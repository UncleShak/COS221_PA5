<?php
// src/Controllers/AgencyController.php
// Handles all HTTP actions for the Agency role.
// Every method begins with an ownership/auth check via requireAgency().

require_once __DIR__ . '/../Models/AgencyModel.php';

class AgencyController
{
    private PDO         $db;
    private AgencyModel $model;

    public function __construct(PDO $db)
    {
        $this->db    = $db;
        $this->model = new AgencyModel($db);
    }

    // ---------------------------------------------------------------
    // AUTH GUARD
    // ---------------------------------------------------------------

    /**
     * Abort with a redirect to /login if the current session is not an agency.
     * Call this at the top of every action method.
     */
    private function requireAgency(): void
    {
        if (
            empty($_SESSION['user_id']) ||
            empty($_SESSION['user_type']) ||
            $_SESSION['user_type'] !== 'agency'
        ) {
            header('Location: /login');
            exit;
        }
    }

    /** Convenience: current agency's user_id from session. */
    private function agencyId(): int
    {
        return (int) $_SESSION['user_id'];
    }

    // ---------------------------------------------------------------
    // DASHBOARD
    // ---------------------------------------------------------------

    /**
     * GET /agency/dashboard
     * Renders the agency dashboard with stats, package list, and recent bookings.
     */
    public function dashboard(): void
    {
        $this->requireAgency();

        $agencyId = $this->agencyId();

        $agency         = $this->model->getAgencyProfile($agencyId);
        $stats          = $this->model->getDashboardStats($agencyId);
        $packages       = $this->model->getPackagesByAgency($agencyId);
        $recentBookings = $this->model->getRecentBookings($agencyId, 5);

        // Flash message (set by savePackage / archivePackage)
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $title = 'Agency Dashboard · Tripistry';

        ob_start();
        require __DIR__ . '/../Views/agency/dashboard.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout.php';
    }

    // ---------------------------------------------------------------
    // PACKAGE FORM (create & edit)
    // ---------------------------------------------------------------

    /**
     * GET /agency/package/create    → blank form
     * GET /agency/package/edit?id=X → pre-filled form
     */
    public function packageForm(): void
    {
        $this->requireAgency();

        $agencyId  = $this->agencyId();
        $packageId = isset($_GET['id']) ? (int) $_GET['id'] : null;

        // Available options for all multi-select dropdowns
        $allDestinations   = $this->model->getAllDestinations();
        $allFlights        = $this->model->getAllFlights();
        $allAccommodations = $this->model->getAllAccommodations();
        $allAttractions    = $this->model->getAllAttractions();
        $allRestaurants    = $this->model->getAllRestaurants();

        if ($packageId !== null) {
            // ── EDIT mode ──────────────────────────────────────────
            $package = $this->model->getPackageById($packageId, $agencyId);

            if (!$package) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Package not found or access denied.'];
                header('Location: /agency/dashboard');
                exit;
            }

            $groupTrip = $this->model->getGroupTrip($packageId);

            // IDs already linked — used to pre-check multi-selects in the view
            $selectedDestinations   = $this->model->getPackageDestinationIds($packageId);
            $selectedFlights        = $this->model->getPackageFlightIds($packageId);
            $selectedAccommodations = $this->model->getPackageAccommodationIds($packageId);
            $selectedAttractions    = $this->model->getPackageAttractionIds($packageId);
            $selectedRestaurants    = $this->model->getPackageRestaurantIds($packageId);

            $formMode = 'edit';
            $title    = 'Edit Package · Tripistry';
        } else {
            // ── CREATE mode ────────────────────────────────────────
            $package   = null;
            $groupTrip = null;

            $selectedDestinations   = [];
            $selectedFlights        = [];
            $selectedAccommodations = [];
            $selectedAttractions    = [];
            $selectedRestaurants    = [];

            $formMode = 'create';
            $title    = 'New Package · Tripistry';
        }

        $errors = $_SESSION['form_errors'] ?? [];
        $old    = $_SESSION['form_old']    ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);

        ob_start();
        require __DIR__ . '/../Views/agency/edit_package.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout.php';
    }

    // ---------------------------------------------------------------
    // SAVE PACKAGE (create or update)
    // ---------------------------------------------------------------

    /**
     * POST /agency/package/save
     * Handles both create (no package_id in POST) and update (package_id present).
     */
    public function savePackage(): void
    {
        $this->requireAgency();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /agency/dashboard');
            exit;
        }

        $agencyId  = $this->agencyId();
        $packageId = !empty($_POST['package_id']) ? (int) $_POST['package_id'] : null;

        // ── Validate core fields ───────────────────────────────────
        $errors = [];

        $title       = trim($_POST['title']           ?? '');
        $description = trim($_POST['description']     ?? '');
        $basePrice   = $_POST['base_price']            ?? '';
        $durationDay = $_POST['duration_days']         ?? '';
        $status      = $_POST['status']                ?? 'draft';
        $coverUrl    = trim($_POST['cover_image_url']  ?? '');
        $maxCapacity = !empty($_POST['max_capacity'])  ? (int) $_POST['max_capacity'] : null;

        if ($title === '') {
            $errors['title'] = 'Title is required.';
        }
        if (!is_numeric($basePrice) || $basePrice < 0) {
            $errors['base_price'] = 'Enter a valid price.';
        }
        if (!is_numeric($durationDay) || $durationDay < 1) {
            $errors['duration_days'] = 'Duration must be at least 1 day.';
        }

        // FIX: status ENUM in DB is ('draft', 'active', 'archived') — not 'published'
        if (!in_array($status, ['draft', 'active', 'archived'], true)) {
            $status = 'draft';
        }

        // ── Group trip validation ──────────────────────────────────
        $isGroupTrip   = !empty($_POST['is_group_trip']);
        $groupTripData = null;

        if ($isGroupTrip) {
            $depDate = trim($_POST['departure_date']    ?? '');
            $retDate = trim($_POST['return_date']       ?? '');
            $minP    = (int) ($_POST['gt_min_participants'] ?? 0);
            $maxP    = (int) ($_POST['gt_max_participants'] ?? 0);
            $meetPt  = trim($_POST['meeting_point']    ?? '');

            if ($depDate === '') {
                $errors['departure_date'] = 'Departure date is required for group trips.';
            }
            // FIX: return_date is NOT NULL in GroupTrips schema
            if ($retDate === '') {
                $errors['return_date'] = 'Return date is required for group trips.';
            }
            if (!empty($depDate) && !empty($retDate) && $retDate <= $depDate) {
                $errors['return_date'] = 'Return date must be after departure date.';
            }
            if ($minP < 1) {
                $errors['gt_min_participants'] = 'Minimum participants must be at least 1.';
            }
            if ($maxP < $minP) {
                $errors['gt_max_participants'] = 'Max participants must be ≥ minimum.';
            }

            $groupTripData = [
                'departure_date'   => $depDate,
                'return_date'      => $retDate,
                'min_participants' => $minP,
                'max_participants' => $maxP,
                'meeting_point'    => $meetPt,
            ];
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $_POST;

            $redirect = $packageId
                ? "/agency/package/edit?id={$packageId}"
                : '/agency/package/create';
            header("Location: {$redirect}");
            exit;
        }

        // ── Persist ───────────────────────────────────────────────
        $data = [
            'title'           => $title,
            'description'     => $description,
            'base_price'      => (float) $basePrice,
            'duration_days'   => (int) $durationDay,
            'status'          => $status,
            'cover_image_url' => $coverUrl ?: null,
            'max_capacity'    => $maxCapacity,  // matches DB column name
        ];

        $this->db->beginTransaction();
        try {
            if ($packageId === null) {
                $packageId = $this->model->createPackage($agencyId, $data);
            } else {
                $affected = $this->model->updatePackage($packageId, $agencyId, $data);
                if ($affected === 0) {
                    throw new RuntimeException('Package not found or access denied.');
                }
            }

            $this->syncJunctions($packageId, $_POST);

            if ($isGroupTrip) {
                $this->model->upsertGroupTrip($packageId, $groupTripData);
            } else {
                $this->model->deleteGroupTrip($packageId);
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Save failed: ' . $e->getMessage()];
            header('Location: /agency/dashboard');
            exit;
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Package saved successfully.'];
        header('Location: /agency/dashboard');
        exit;
    }

    // ---------------------------------------------------------------
    // ARCHIVE (soft-delete)
    // ---------------------------------------------------------------

    /**
     * POST /agency/package/archive
     * Expects POST field: package_id
     */
    public function archivePackage(): void
    {
        $this->requireAgency();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['package_id'])) {
            header('Location: /agency/dashboard');
            exit;
        }

        $agencyId  = $this->agencyId();
        $packageId = (int) $_POST['package_id'];

        $affected = $this->model->archivePackage($packageId, $agencyId);

        $_SESSION['flash'] = $affected > 0
            ? ['type' => 'success', 'message' => 'Package archived.']
            : ['type' => 'error',   'message' => 'Package not found or access denied.'];

        header('Location: /agency/dashboard');
        exit;
    }

    // ---------------------------------------------------------------
    // HELPER: sync all five junction tables in one place
    // ---------------------------------------------------------------

    private function syncJunctions(int $packageId, array $post): void
    {
        $destinations   = array_map('intval', (array) ($post['destinations']   ?? []));
        $flights        = array_map('intval', (array) ($post['flights']        ?? []));
        $accommodations = array_map('intval', (array) ($post['accommodations'] ?? []));
        $attractions    = array_map('intval', (array) ($post['attractions']    ?? []));
        $restaurants    = array_map('intval', (array) ($post['restaurants']    ?? []));

        $this->model->clearPackageDestinations($packageId);
        $this->model->clearPackageFlights($packageId);
        $this->model->clearPackageAccommodations($packageId);
        $this->model->clearPackageAttractions($packageId);
        $this->model->clearPackageRestaurants($packageId);

        if ($destinations)   $this->model->linkDestinations($packageId, $destinations);
        if ($flights)        $this->model->linkFlights($packageId, $flights);
        if ($accommodations) $this->model->linkAccommodations($packageId, $accommodations);
        if ($attractions)    $this->model->linkAttractions($packageId, $attractions);
        if ($restaurants)    $this->model->linkRestaurants($packageId, $restaurants);
    }
}

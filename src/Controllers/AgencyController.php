<?php
// src/Controllers/AgencyController.php

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

    private function requireAgency(): void
    {
        if (
            empty($_SESSION['user_id']) ||
            empty($_SESSION['user_type']) ||
            $_SESSION['user_type'] !== 'agency'
        ) {
            header('Location: index.php?route=login');
            exit;
        }
    }

    private function agencyId(): int
    {
        return (int) $_SESSION['user_id'];
    }

    public function dashboard(): void
    {
        $this->requireAgency();

        $agencyId = $this->agencyId();

        $agency         = $this->model->getAgencyProfile($agencyId);
        $stats          = $this->model->getDashboardStats($agencyId);
        $packages       = $this->model->getPackagesByAgency($agencyId);
        $recentBookings = $this->model->getRecentBookings($agencyId, 5);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $title = 'Agency Dashboard · Tripistry';

        ob_start();
        require __DIR__ . '/../Views/agency/dashboard.php';
        $content = ob_get_clean();
        require __DIR__ . '/../Views/layout.php';
    }

    public function packageForm(): void
    {
        $this->requireAgency();

        $agencyId  = $this->agencyId();
        $packageId = isset($_GET['id']) ? (int) $_GET['id'] : null;

        $allDestinations   = $this->model->getAllDestinations();
        $allFlights        = $this->model->getAllFlights();
        $allAccommodations = $this->model->getAllAccommodations();
        $allAttractions    = $this->model->getAllAttractions();
        $allRestaurants    = $this->model->getAllRestaurants();

        if ($packageId !== null) {
            $package = $this->model->getPackageById($packageId, $agencyId);

            if (!$package) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Package not found or access denied.'];
                header('Location: index.php?route=agency/dashboard');
                exit;
            }

            $groupTrip = $this->model->getGroupTrip($packageId);

            $selectedDestinations   = $this->model->getPackageDestinationIds($packageId);
            $selectedFlights        = $this->model->getPackageFlightIds($packageId);
            $selectedAccommodations = $this->model->getPackageAccommodationIds($packageId);
            $selectedAttractions    = $this->model->getPackageAttractionIds($packageId);
            $selectedRestaurants    = $this->model->getPackageRestaurantIds($packageId);

            $formMode = 'edit';
            $title    = 'Edit Package · Tripistry';
        } else {
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

    public function savePackage(): void
    {
        $this->requireAgency();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?route=agency/dashboard');
            exit;
        }

        $agencyId  = $this->agencyId();
        $packageId = !empty($_POST['package_id']) ? (int) $_POST['package_id'] : null;

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
        if (!in_array($status, ['draft', 'active', 'archived'], true)) {
            $status = 'draft';
        }

        $isGroupTrip   = !empty($_POST['is_group_trip']);
        $groupTripData = null;

        if ($isGroupTrip) {
            $depDate = trim($_POST['departure_date']        ?? '');
            $retDate = trim($_POST['return_date']           ?? '');
            $minP    = (int) ($_POST['gt_min_participants'] ?? 0);
            $maxP    = (int) ($_POST['gt_max_participants'] ?? 0);
            $meetPt  = trim($_POST['meeting_point']         ?? '');

            if ($depDate === '') {
                $errors['departure_date'] = 'Departure date is required for group trips.';
            }
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
                ? "index.php?route=agency/package/edit&id={$packageId}"
                : 'index.php?route=agency/package/create';
            header("Location: {$redirect}");
            exit;
        }

        $data = [
            'title'           => $title,
            'description'     => $description,
            'base_price'      => (float) $basePrice,
            'duration_days'   => (int) $durationDay,
            'status'          => $status,
            'cover_image_url' => $coverUrl ?: null,
            'max_capacity'    => $maxCapacity,
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
            header('Location: index.php?route=agency/dashboard');
            exit;
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Package saved successfully.'];
        header('Location: index.php?route=agency/dashboard');
        exit;
    }

    public function archivePackage(): void
    {
        $this->requireAgency();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['package_id'])) {
            header('Location: index.php?route=agency/dashboard');
            exit;
        }

        $agencyId  = $this->agencyId();
        $packageId = (int) $_POST['package_id'];

        $affected = $this->model->archivePackage($packageId, $agencyId);

        $_SESSION['flash'] = $affected > 0
            ? ['type' => 'success', 'message' => 'Package archived.']
            : ['type' => 'error',   'message' => 'Package not found or access denied.'];

        header('Location: index.php?route=agency/dashboard');
        exit;
    }

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

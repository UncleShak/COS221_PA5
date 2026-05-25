<?php
require_once __DIR__ . '/../Models/BookingModel.php';

class TravellerController {
    
    private $bookingModel;
    private $pdo;

    public function __construct($pdo = null) {
        $this->pdo = $pdo;
        $this->bookingModel = new BookingModel($this->pdo);
    }

    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login"); exit();
        }
        $userId = $_SESSION['user_id'];
        $userSql = "SELECT u.email, t.first_name, t.last_name 
                    FROM users u JOIN travellers t ON u.user_id = t.user_id 
                    WHERE u.user_id = ?";
        $userStmt = $this->pdo->prepare($userSql);
        $userStmt->execute([$userId]);
        $traveller = $userStmt->fetch();
        $allBookings = $this->bookingModel->getTravellerBookings($userId);
        $upcomingTrips = []; $pastTrips = [];
        $currentDate = date('Y-m-d');
        foreach ($allBookings as $booking) {
            if ($booking['status'] === 'cancelled') continue;
            if ($booking['travel_date'] < $currentDate) { $pastTrips[] = $booking; }
            else { $upcomingTrips[] = $booking; }
        }
        require_once __DIR__ . '/../Models/FavouriteModel.php';
        $favModel        = new FavouriteModel($this->pdo);
        $favouritePackages = $favModel->getFavouritePackages($userId);

        $this->render('traveller/dashboard', [
            'title'             => 'Traveller Dashboard | Tripistry',
            'traveller'         => $traveller,
            'upcomingTrips'     => $upcomingTrips,
            'pastTrips'         => $pastTrips,
            'hasPastTrips'      => !empty($pastTrips),
            'totalExpeditions'  => count($allBookings),
            'favouritePackages' => $favouritePackages,
        ]);
    }

    public function details() {
        $packageId = $_GET['id'] ?? null;
        if (!$packageId) { header("Location: /traveller/packages"); exit(); }
        require_once __DIR__ . '/../Models/PackageModel.php';
        $packageModel = new PackageModel($this->pdo);
        $package = $packageModel->getPackageById($packageId);
        if (!$package) { header("Location: /traveller/packages?error=not_found"); exit(); }
        $title = $package['title'] . ' - Tripistry';
        ob_start();
        require_once __DIR__ . '/../Views/traveller/details.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../Views/layout.php';
    }

    private function render($viewPath, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . "/../Views/{$viewPath}.php";
        $content = ob_get_clean();
        require __DIR__ . "/../Views/layout.php";
    }

    public function submitReview() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login"); exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $travellerId = $_SESSION['user_id'];
            $bookingId   = $_POST['booking_id'] ?? null;
            $packageId   = $_POST['package_id'] ?? null;
            $agencyId    = $_POST['agency_id'] ?? null;
            
            $packageRating  = $_POST['package_rating'] ?? null;
            $packageComment = $_POST['package_comment'] ?? '';

            $agencyRating  = $_POST['agency_rating'] ?? null;
            $agencyComment = $_POST['agency_comment'] ?? '';

            require_once __DIR__ . '/../../config/database.php';
            $db = new Database();
            $pdo = $db->getConnection();

            $allSuccess = true;

            if ($bookingId && $packageId && $packageRating) {
                try {
                    $sql = "INSERT INTO packagereviews (traveller_id, package_id, booking_id, rating, comment, created_at) 
                            VALUES (?, ?, ?, ?, ?, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $success = $stmt->execute([
                        $travellerId,
                        $packageId,
                        $bookingId,
                        (int)$packageRating,
                        htmlspecialchars(strip_tags($packageComment))
                    ]);
                    if (!$success) { $allSuccess = false; }
                } catch (PDOException $e) {
                    error_log("Package review error: " . $e->getMessage());
                    $allSuccess = false;
                }
            }

            if ($bookingId && $agencyId && $agencyRating) {
                try {
                    $sql = "INSERT INTO agencyreviews (agency_id, traveller_id, booking_id, rating, comment, created_at)
                            VALUES (?, ?, ?, ?, ?, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $success = $stmt->execute([
                        $agencyId,
                        $travellerId,
                        $bookingId,
                        (int)$agencyRating,
                        htmlspecialchars(strip_tags($agencyComment))
                    ]);
                    if (!$success) { $allSuccess = false; }
                } catch (PDOException $e) {
                    error_log("Agency review error: " . $e->getMessage());
                    $allSuccess = false;
                }
            }

            if ($allSuccess && ($packageRating || $agencyRating)) {
                header("Location: /traveller/dashboard?status=review_submitted");
                exit();
            } else {
                echo "<br><br><a href='/traveller/dashboard' style='color: white; padding: 1rem; background: #333;'>← Go Back</a>";
                die("Review submission failed");
            }
        }
        header("Location: /traveller/dashboard?status=review_failed"); exit();
    }

    public function cancelBooking() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login"); exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['booking_id'] ?? null;
            $travellerId = $_SESSION['user_id'];
            if ($bookingId) {
                require_once __DIR__ . '/../../config/database.php';
                $database = new Database(); $db = $database->getConnection();
                require_once __DIR__ . '/../Models/BookingModel.php';
                $bookingModel = new BookingModel($db);
                if ($bookingModel->cancelBooking($bookingId, $travellerId)) {
                    header("Location: /traveller/dashboard?status=booking_cancelled"); exit();
                }
            }
        }
        header("Location: /traveller/dashboard?error=cancel_failed"); exit();
    }

    public function packages() {
        require_once __DIR__ . '/../Models/PackageModel.php';
        $packageModel = new PackageModel($this->pdo);
        $filters = [
            'search'      => $_GET['search'] ?? '',
            'destination' => $_GET['destination'] ?? '',
            'min_price'   => $_GET['min_price'] ?? '',
            'max_price'   => $_GET['max_price'] ?? '',
            'duration'    => $_GET['duration'] ?? '',
            'min_rating'  => $_GET['min_rating'] ?? '',
        ];
        $currentSort = $_GET['sort'] ?? 'price_asc';
        $perPage     = 12;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $offset      = ($currentPage - 1) * $perPage;
        $packages      = $packageModel->getFilteredPackages($filters, $currentSort, $perPage, $offset);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {

            require_once __DIR__ . '/../Models/FavouriteModel.php';

            $favModel = new FavouriteModel($this->pdo);

            $favouritePackages = $favModel->getFavouritePackages($_SESSION['user_id']);

            $favouriteIds = array_column($favouritePackages, 'favouritable_id');

            foreach ($packages as &$pkg) {
                $pkg['is_favourite'] = in_array($pkg['favouritable_id'], $favouriteIds);
            }

            unset($pkg);
        }

        $totalPackages = $packageModel->getFilteredCount($filters);
        $totalPages    = max(1, (int)ceil($totalPackages / $perPage));
        $filterOptions  = $packageModel->getFilterOptions();
        $currentFilters = $filters;
        $title = 'Explore Packages - Tripistry';
        ob_start();
        require_once __DIR__ . '/../Views/traveller/packages.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function checkout() {
        $packageId = $_GET['package_id'] ?? null;
        if (!$packageId) { header("Location: /traveller/packages"); exit(); }
        require_once __DIR__ . '/../Models/PackageModel.php';
        $packageModel = new PackageModel($this->pdo);
        $package = $packageModel->getPackageById($packageId);
        if (!$package) { header("Location: /traveller/packages?error=not_found"); exit(); }
        $userId = $_SESSION['user_id'] ?? 1;
        $userSql = "SELECT u.email, t.first_name, t.last_name 
                    FROM users u JOIN travellers t ON u.user_id = t.user_id 
                    WHERE u.user_id = ?";
        $userStmt = $this->pdo->prepare($userSql);
        $userStmt->execute([$userId]);
        $traveller = $userStmt->fetch();
        $title = 'Secure Checkout - Tripistry';
        ob_start();
        require_once __DIR__ . '/../Views/traveller/checkout.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function compare() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login"); exit();
        }

        require_once __DIR__ . '/../Models/PackageModel.php';
        $packageModel = new PackageModel($this->pdo);
        $packageOptions = $packageModel->getAllPackages(200);

        $leftId = (int)($_GET['left'] ?? 0);
        $rightId = (int)($_GET['right'] ?? 0);

        $fallbackPackages = $packageModel->getAllPackages(2);

        if ($leftId <= 0 && !empty($fallbackPackages[0]['id'])) {
            $leftId = (int)$fallbackPackages[0]['id'];
        }

        if ($rightId <= 0) {
            if (!empty($fallbackPackages[1]['id'])) {
                $rightId = (int)$fallbackPackages[1]['id'];
            } elseif ($leftId > 0) {
                $rightId = $leftId;
            }
        }

        $leftPackage = $leftId > 0 ? $packageModel->getPackageById($leftId) : null;
        $rightPackage = $rightId > 0 ? $packageModel->getPackageById($rightId) : null;

        if (!$leftPackage || !$rightPackage) {
            header("Location: /traveller/packages?error=compare_not_available"); exit();
        }

        $left = $this->formatComparePackage($leftPackage);
        $right = $this->formatComparePackage($rightPackage);

        $this->render('traveller/compare', [
            'title'   => 'Compare Packages - Tripistry',
            'left'    => $left,
            'right'   => $right,
            'leftId'  => $left['package_id'],
            'rightId' => $right['package_id'],
            'packageOptions' => $packageOptions,
        ]);
    }

    public function groupHub() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login"); exit();
        }
        $groupId = $_GET['id'] ?? null;
        if (!$groupId) { header("Location: /traveller/dashboard"); exit(); }
        require_once __DIR__ . '/../Models/GroupModel.php';
        $groupModel = new GroupModel($this->pdo);
        if (!$groupModel->isUserInGroup($_SESSION['user_id'], $groupId)) {
            header("Location: /traveller/dashboard?error=unauthorized_cluster"); exit();
        }
        $groupDetails = $groupModel->getGroupDetails($groupId);
        $roster = $groupModel->getGroupRoster($groupId);
        $messages = $groupModel->getGroupMessages($groupId);
        $this->render('traveller/group', [
            'title'        => 'Group Cluster - Tripistry',
            'groupDetails' => $groupDetails,
            'roster'       => $roster,
            'messages'     => $messages
        ]);
    }

    public function sendMessage() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login"); exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $travellerId = $_SESSION['user_id'];
            $groupId = $_POST['group_trip_id'] ?? null;
            $messageText = trim($_POST['message_text'] ?? '');
            if ($groupId && !empty($messageText)) {
                require_once __DIR__ . '/../../config/database.php';
                $database = new Database(); $db = $database->getConnection();
                require_once __DIR__ . '/../Models/GroupModel.php';
                $groupModel = new GroupModel($db);
                if ($groupModel->isUserInGroup($travellerId, $groupId)) {
                    $groupModel->saveMessage($groupId, $travellerId, $messageText);
                }
            }
            header("Location: /traveller/group?id=" . $groupId); exit();
        }
    }

    public function destinations() {
        $stmt = $this->pdo->query("SELECT * FROM destinations");
        $destinations = $stmt->fetchAll();
        $this->render('traveller/destinations', [
            'title'        => 'Destinations - Tripistry',
            'destinations' => $destinations,
        ]);
    }

    public function flights() {
        $stmt = $this->pdo->query("SELECT * FROM flights ORDER BY base_price ASC");
        $flights = $stmt->fetchAll();
        $this->render('traveller/flights', [
            'title'   => 'Flights - Tripistry',
            'flights' => $flights,
        ]);
    }

    public function accommodations() {
        $stmt = $this->pdo->query("SELECT * FROM accommodations ORDER BY price_per_night ASC");
        $accommodations = $stmt->fetchAll();
        $this->render('traveller/accommodations', [
            'title'          => 'Accommodations - Tripistry',
            'accommodations' => $accommodations,
        ]);
    }

    public function attractions() {
        $stmt = $this->pdo->query("SELECT * FROM attractions ORDER BY name ASC");
        $attractions = $stmt->fetchAll();
        $this->render('traveller/attractions', [
            'title'       => 'Attractions - Tripistry',
            'attractions' => $attractions,
        ]);
    }

    public function restaurants() {
        $stmt = $this->pdo->query("SELECT * FROM restaurants ORDER BY name ASC");
        $restaurants = $stmt->fetchAll();
        $this->render('traveller/restaurants', [
            'title'       => 'Restaurants - Tripistry',
            'restaurants' => $restaurants,
        ]);
    }

    public function toggleFavourite() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login"); exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $travellerId    = $_SESSION['user_id'];
            $favouritableId = $_POST['favouritable_id'] ?? null;
            $action         = $_POST['action'] ?? 'add'; // 'add' or 'remove'
            $redirect       = $_POST['redirect'] ?? '/traveller/dashboard';

            if ($favouritableId) {
                require_once __DIR__ . '/../Models/FavouriteModel.php';
                $favModel = new FavouriteModel($this->pdo);
                if ($action === 'remove') {
                    $favModel->removeFavourite($travellerId, $favouritableId);
                } else {
                    $favModel->addFavourite($travellerId, $favouritableId);
                }
            }
            header("Location: " . $redirect); exit();
        }
        header("Location: /traveller/dashboard"); exit();
    }

    private function formatComparePackage(array $package): array {
        $highlights = [];
        if (!empty($package['itinerary'])) {
            foreach (array_slice($package['itinerary'], 0, 4) as $day) {
                if (!empty($day['title'])) {
                    $highlights[] = $day['title'];
                }
            }
        }

        if (empty($highlights) && !empty($package['description'])) {
            $highlights[] = $package['description'];
        }

        $includes = [];
        if (!empty($package['inclusions'])) {
            foreach ($package['inclusions'] as $items) {
                foreach ($items as $item) {
                    if (!empty($item['name'])) {
                        $includes[] = $item['name'];
                    }
                }
            }
        }

        if (empty($includes)) {
            $includes[] = 'Standard inclusions apply';
        }

        $includes = array_values(array_unique($includes));

        return [
            'package_id'    => (int)($package['id'] ?? 0),
            'destination'   => $package['destination'] ?? 'Global',
            'title'         => $package['title'] ?? 'Untitled package',
            'agency'        => $package['agency_name'] ?? 'Tripistry Agency',
            'duration_days' => (int)($package['duration_days'] ?? 0),
            'price_pp'      => (float)($package['price'] ?? 0),
            'taxes_fees'    => 0,
            'rating'        => (float)($package['avg_rating'] ?? 0),
            'reviews_count' => isset($package['reviews']) ? count($package['reviews']) : 0,
            'travel_month'   => 'Flexible',
            'highlights'     => $highlights,
            'includes'      => $includes,
        ];
    }
}
?>
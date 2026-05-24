<?php
// Pull in the Model
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
        $this->render('traveller/dashboard', [
            'title'            => 'Traveller Dashboard | Tripistry',
            'traveller'        => $traveller,
            'upcomingTrips'    => $upcomingTrips,
            'pastTrips'        => $pastTrips,
            'hasPastTrips'     => !empty($pastTrips),
            'totalExpeditions' => count($allBookings)
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
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'traveller') {
            header("Location: /login"); exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $travellerId = $_SESSION['user_id'];
            $bookingId   = $_POST['booking_id'] ?? null;
            $packageId   = $_POST['package_id'] ?? null;
            $rating      = $_POST['rating'] ?? null;
            $comment     = $_POST['comment'] ?? '';
            if ($bookingId && $packageId && $rating) {
                require_once __DIR__ . '/../Models/ReviewModel.php';
                require_once __DIR__ . '/../../config/database.php';
                $db = new Database();
                $reviewModel = new ReviewModel($db->getConnection());
                $success = $reviewModel->createReview($travellerId, $packageId, $bookingId, $rating, $comment);
                if ($success) { header("Location: /traveller/dashboard?status=review_submitted"); exit(); }
                else { echo "<br><br><a href='/traveller/dashboard' style='color: white; padding: 1rem; background: #333;'>← Go Back</a>"; die(); }
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
        ];
        $currentSort = $_GET['sort'] ?? 'price_asc';
        $perPage     = 12;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $offset      = ($currentPage - 1) * $perPage;
        $packages      = $packageModel->getFilteredPackages($filters, $currentSort, $perPage, $offset);
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

    // ── Read-Only Entity Browsers ──────────────────────────────────────────

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
}
?>
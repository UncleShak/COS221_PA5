<?php
// Pull in the Model
require_once __DIR__ . '/../Models/BookingModel.php';

class TravellerController {
    
    private $bookingModel;
    private $pdo;

    // 2. Capture the connection when the router creates the controller
    public function __construct($pdo = null) {
        $this->pdo = $pdo;
        $this->bookingModel = new BookingModel($this->pdo);
    }

    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login");
            exit();
        }

        $userId = $_SESSION['user_id'];

        // NEW: Fetch the logged-in traveller's profile data
        $userSql = "SELECT u.email, t.first_name, t.last_name 
                    FROM users u 
                    JOIN travellers t ON u.user_id = t.user_id 
                    WHERE u.user_id = ?";
        $userStmt = $this->pdo->prepare($userSql);
        $userStmt->execute([$userId]);
        $traveller = $userStmt->fetch();

        // 2. Fetch the raw booking data from MariaDB
        $allBookings = $this->bookingModel->getTravellerBookings($userId);

        // 3. Sort the data into states
        $upcomingTrips = [];
        $pastTrips = [];
        $currentDate = date('Y-m-d'); 

        foreach ($allBookings as $booking) {
            if ($booking['status'] === 'cancelled') {
                continue; 
            }

            if ($booking['travel_date'] < $currentDate) {
                $pastTrips[] = $booking;
            } else {
                $upcomingTrips[] = $booking;
            }
        }

        // 4. Pass EVERYTHING (including the $traveller) to your custom render function
        $this->render('traveller/dashboard', [
            'title'            => 'Traveller Dashboard | Tripistry',
            'traveller'        => $traveller, // Passed to the view here!
            'upcomingTrips'    => $upcomingTrips,
            'pastTrips'        => $pastTrips,
            'hasPastTrips'     => !empty($pastTrips),
            'totalExpeditions' => count($allBookings)
        ]);
    }

    public function details() {
        // 1. Grab the ID from the URL (e.g., ?id=1)
        $packageId = $_GET['id'] ?? null;
        
        // If they tampered with the URL, bounce them back to the storefront
        if (!$packageId) {
            header("Location: /traveller/packages");
            exit();
        }

        // 2. Fetch the specific package from MariaDB
        require_once __DIR__ . '/../Models/PackageModel.php';
        $packageModel = new PackageModel($this->pdo);
        $package = $packageModel->getPackageById($packageId);

        // If the package doesn't exist, bounce them back
        if (!$package) {
            header("Location: /traveller/packages?error=not_found");
            exit();
        }

        // 3. Render the view inside the layout shell
        $title = $package['title'] . ' - Tripistry';
        ob_start();
        require_once __DIR__ . '/../Views/traveller/details.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../Views/layout.php';
    }

    /**
     * Helper method to render a view inside the master layout
     */
    private function render($viewPath, $data = []) {
        // This extracts our array keys into real variables for the HTML file
        extract($data);
        
        ob_start();
        require __DIR__ . "/../Views/{$viewPath}.php";
        $content = ob_get_clean();
        
        require __DIR__ . "/../Views/layout.php";
    }

    public function submitReview() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'traveller') {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $travellerId   = $_SESSION['user_id'];
            $bookingId     = !empty($_POST['booking_id']) ? (int)$_POST['booking_id'] : null;
            $packageId     = !empty($_POST['package_id']) ? (int)$_POST['package_id'] : null;

            $pkgRating     = $_POST['package_rating'] ?? null;
            $pkgComment    = trim($_POST['package_comment'] ?? '');
            $agencyRating  = $_POST['agency_rating'] ?? null;
            $agencyComment = trim($_POST['agency_comment'] ?? '');

            if (!$bookingId || !$packageId) {
                header("Location: /traveller/dashboard?status=review_failed");
                exit();
            }

            require_once __DIR__ . '/../../config/database.php';
            $database = new Database();
            $db = $database->getConnection();

            try {
                $debugLog = __DIR__ . '/../../logs/review_debug.log';
                @mkdir(dirname($debugLog), 0755, true);
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] REVIEW DEBUG: POST data - booking_id=$bookingId, package_id=$packageId, pkgRating=$pkgRating, agencyRating=$agencyRating\n", FILE_APPEND);

                $lookupStmt = $db->prepare("SELECT b.package_id, p.agency_id
                                           FROM bookings b
                                           JOIN packages p ON p.package_id = b.package_id
                                           WHERE b.booking_id = :booking_id AND b.traveller_id = :traveller_id
                                           LIMIT 1");
                $lookupStmt->execute([
                    ':booking_id' => $bookingId,
                    ':traveller_id' => $travellerId
                ]);
                $bookingInfo = $lookupStmt->fetch(PDO::FETCH_ASSOC);

                if (!$bookingInfo) {
                    throw new RuntimeException('Booking context could not be resolved.');
                }

                $packageId = (int)$bookingInfo['package_id'];
                $agencyId = (int)$bookingInfo['agency_id'];
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] REVIEW DEBUG: Booking lookup result - packageId=$packageId, agencyId=$agencyId\n", FILE_APPEND);

                if ($pkgRating === null || $pkgRating === '' || $pkgComment === '') {
                    throw new RuntimeException('Package review is required.');
                }

                if ($agencyId === null || $agencyRating === null || $agencyRating === '' || $agencyComment === '') {
                    throw new RuntimeException('Agency review is required.');
                }

                $db->beginTransaction();

                require_once __DIR__ . '/../Models/ReviewModel.php';
                $reviewModel = new ReviewModel($db);

                $packageSql = "INSERT INTO packagereviews (traveller_id, package_id, booking_id, rating, comment, created_at)
                               VALUES (:traveller_id, :package_id, :booking_id, :rating, :comment, NOW())
                               ON DUPLICATE KEY UPDATE
                                   package_id = VALUES(package_id),
                                   rating = VALUES(rating),
                                   comment = VALUES(comment),
                                   created_at = NOW()";
                $packageStmt = $db->prepare($packageSql);
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] REVIEW DEBUG: About to insert package review - traveller_id=$travellerId, package_id=$packageId, booking_id=$bookingId, rating=$pkgRating\n", FILE_APPEND);
                $packageStmt->execute([
                    ':traveller_id' => $travellerId,
                    ':package_id'   => $packageId,
                    ':booking_id'   => $bookingId,
                    ':rating'       => (int)$pkgRating,
                    ':comment'      => htmlspecialchars(strip_tags($pkgComment))
                ]);
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] REVIEW DEBUG: Package review insert completed successfully\n", FILE_APPEND);

                $agencySql = "INSERT INTO agencyreviews (traveller_id, agency_id, booking_id, rating, comment, created_at)
                              VALUES (:traveller_id, :agency_id, :booking_id, :rating, :comment, NOW())
                              ON DUPLICATE KEY UPDATE
                                  agency_id = VALUES(agency_id),
                                  rating = VALUES(rating),
                                  comment = VALUES(comment),
                                  created_at = NOW()";
                $agencyStmt = $db->prepare($agencySql);
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] REVIEW DEBUG: About to insert agency review - traveller_id=$travellerId, agency_id=$agencyId, booking_id=$bookingId, rating=$agencyRating, comment_len=" . strlen($agencyComment) . "\n", FILE_APPEND);
                $agencyStmt->execute([
                    ':traveller_id' => $travellerId,
                    ':agency_id'    => $agencyId,
                    ':booking_id'   => $bookingId,
                    ':rating'       => (int)$agencyRating,
                    ':comment'      => htmlspecialchars(strip_tags($agencyComment))
                ]);
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] REVIEW DEBUG: Agency review insert completed successfully\n", FILE_APPEND);

                $db->commit();
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] REVIEW DEBUG: Transaction committed successfully\n", FILE_APPEND);
                header("Location: /traveller/dashboard?status=review_submitted");
                exit();

            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $debugLog = $debugLog ?? __DIR__ . '/../../logs/review_debug.log';
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] REVIEW DEBUG: Exception caught - Type: " . get_class($e) . " | Message: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine() . "\n", FILE_APPEND);
                file_put_contents($debugLog, "[" . date('Y-m-d H:i:s') . "] REVIEW DEBUG: Full trace - " . $e->getTraceAsString() . "\n", FILE_APPEND);
                header("Location: /traveller/dashboard?error=review_failed");
                exit();
            }
        }
        header("Location: /traveller/dashboard?status=review_failed");
        exit();
    }

    public function cancelBooking() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Grab the booking ID from the hidden form input
            $bookingId = $_POST['booking_id'] ?? null;
            // Grab the traveller ID securely from the session
            $travellerId = $_SESSION['user_id'];

            if ($bookingId) {
                // Safely establish the database connection
                require_once __DIR__ . '/../../config/database.php';
                $database = new Database();
                $db = $database->getConnection();
                
                require_once __DIR__ . '/../Models/BookingModel.php';
                $bookingModel = new BookingModel($db);

                // Pass the variables to the Model to execute the cancellation
                if ($bookingModel->cancelBooking($bookingId, $travellerId)) {
                    header("Location: /traveller/dashboard?status=booking_cancelled");
                    exit();
                }
            }
        }
        
        // Fallback if something fails
        header("Location: /traveller/dashboard?error=cancel_failed");
        exit();
    }

    public function packages() {
        require_once __DIR__ . '/../Models/PackageModel.php';
        $packageModel = new PackageModel($this->pdo);

        // Collect all active filters from GET params
        $filters = [
            'search'      => $_GET['search'] ?? '',
            'destination' => $_GET['destination'] ?? '',
            'min_price'   => $_GET['min_price'] ?? '',
            'max_price'   => $_GET['max_price'] ?? '',
        ];
        $currentSort = $_GET['sort'] ?? 'price_asc';

        // Pagination
        $perPage     = 12;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $offset      = ($currentPage - 1) * $perPage;

        // Fetch packages and total count
        $packages      = $packageModel->getFilteredPackages($filters, $currentSort, $perPage, $offset);
        $totalPackages = $packageModel->getFilteredCount($filters);
        $totalPages    = max(1, (int)ceil($totalPackages / $perPage));

        // Filter options for the sidebar dropdowns
        $filterOptions  = $packageModel->getFilterOptions();
        $currentFilters = $filters;

        // Render the view inside the layout shell
        $title = 'Explore Packages - Tripistry';
        ob_start();
        require_once __DIR__ . '/../Views/traveller/packages.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function checkout() {
        // 1. Grab the ID from the URL
        $packageId = $_GET['package_id'] ?? null;
        
        if (!$packageId) {
            header("Location: /traveller/packages");
            exit();
        }

        // 2. Fetch the specific package data
        require_once __DIR__ . '/../Models/PackageModel.php';
        $packageModel = new PackageModel($this->pdo);
        $package = $packageModel->getPackageById($packageId);

        if (!$package) {
            header("Location: /traveller/packages?error=not_found");
            exit();
        }

        // 3. FETCH THE LOGGED-IN TRAVELLER'S DATA
        // Assuming your session holds 'user_id' after login
        $userId = $_SESSION['user_id'] ?? 1; // Safe fallback to Sarah for testing
        
        $userSql = "SELECT u.email, t.first_name, t.last_name 
                    FROM users u 
                    JOIN travellers t ON u.user_id = t.user_id 
                    WHERE u.user_id = ?";
        $userStmt = $this->pdo->prepare($userSql);
        $userStmt->execute([$userId]);
        $traveller = $userStmt->fetch();

        // 4. Render the checkout view
        $title = 'Secure Checkout - Tripistry';
        ob_start();
        require_once __DIR__ . '/../Views/traveller/checkout.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function groupHub() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login");
            exit();
        }

        $groupId = $_GET['id'] ?? null;
        if (!$groupId) { header("Location: /traveller/dashboard"); exit(); }

        require_once __DIR__ . '/../Models/GroupModel.php';
        $groupModel = new GroupModel($this->pdo);

        // Final Security Check
        if (!$groupModel->isUserInGroup($_SESSION['user_id'], $groupId)) {
            header("Location: /traveller/dashboard?error=unauthorized_cluster");
            exit();
        }

        $groupDetails = $groupModel->getGroupDetails($groupId);
        $roster = $groupModel->getGroupRoster($groupId);
        $messages = $groupModel->getGroupMessages($groupId); // Fetch real messages!

            // Render the private Group Hub view
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
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $travellerId = $_SESSION['user_id'];
            $groupId = $_POST['group_trip_id'] ?? null;
            $messageText = trim($_POST['message_text'] ?? '');

            if ($groupId && !empty($messageText)) {
                // 1. Explicitly connect to the database first
                require_once __DIR__ . '/../../config/database.php';
                $database = new Database();
                $db = $database->getConnection();
                require_once __DIR__ . '/../Models/GroupModel.php';
                $groupModel = new GroupModel($db);

                // Security: Only save if they actually belong to this group
                if ($groupModel->isUserInGroup($travellerId, $groupId)) {
                    $groupModel->saveMessage($groupId, $travellerId, $messageText);
                }
            }
            header("Location: /traveller/group?id=" . $groupId);
            exit();
        }
    }

    
}
?>
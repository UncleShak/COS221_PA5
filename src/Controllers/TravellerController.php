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
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'traveller') {
            header("Location: /login");
            exit();
        }

        // 2. Ensure this is actually a POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $travellerId = $_SESSION['user_id'];
            $bookingId   = $_POST['booking_id'] ?? null;
            $packageId   = $_POST['package_id'] ?? null;
            $rating      = $_POST['rating'] ?? null;
            $comment     = $_POST['comment'] ?? '';

            // 3. Validate mandatory fields
            if ($bookingId && $packageId && $rating) {
                
                require_once __DIR__ . '/../Models/ReviewModel.php';
                require_once __DIR__ . '/../../config/database.php';
                $db = new Database();
                $reviewModel = new ReviewModel($db->getConnection());

                $success = $reviewModel->createReview($travellerId, $packageId, $bookingId, $rating, $comment);

                if ($success) {
                    header("Location: /traveller/dashboard?status=review_submitted");
                    exit();
                } else {
                    // THE FIX: We halt the redirect here so the screen freezes and displays 
                    // the red Trapdoor error from ReviewModel.php
                    echo "<br><br><a href='/traveller/dashboard' style='color: white; padding: 1rem; background: #333;'>← Go Back</a>";
                    die(); 
                }
            }
        }
        header("Location: /traveller/dashboard?status=review_failed");
        exit();
    }

    public function cancelBooking() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
            $bookingId = $_POST['booking_id'];
            $travellerId = $_SESSION['user_id'];

            // Assumes you have a cancelBooking method in your model
            $success = $this->bookingModel->cancelBooking($bookingId, $travellerId);

            if ($success) {
                header("Location: /traveller/dashboard?status=booking_cancelled");
                exit();
            }
        }
        header("Location: /traveller/dashboard?status=cancel_failed");
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

    
}
?>
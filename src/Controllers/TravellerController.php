<?php
// Pull in the Model
require_once __DIR__ . '/../Models/BookingModel.php';

class TravellerController {
    
    private $bookingModel;

    // We need the constructor to receive Prince's database connection
    public function __construct($pdo) {
        $this->bookingModel = new BookingModel($pdo);
    }

    public function dashboard() {
        // 1. Security check (Mocked for testing)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // TEMPORARY TEST DATA
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'traveller';

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'traveller') {
            header("Location: /login");
            exit();
        }

        // 2. Fetch the raw data from MariaDB
        $userId = $_SESSION['user_id'];
        $allBookings = $this->bookingModel->getTravellerBookings($userId);

        // 3. Sort the data into states
        $upcomingTrips = [];
        $pastTrips = [];
        $currentDate = date('Y-m-d'); 

        foreach ($allBookings as $booking) {
            if ($booking['travel_date'] < $currentDate) {
                $pastTrips[] = $booking;
            } 
            else {
                $upcomingTrips[] = $booking;
            }
        }

        // 4. Pass EVERYTHING to your custom render function
        $this->render('traveller/dashboard', [
            'title'            => 'Traveller Dashboard | Tripistry',
            'upcomingTrips'    => $upcomingTrips,
            'pastTrips'        => $pastTrips,
            'hasPastTrips'     => !empty($pastTrips),
            'totalExpeditions' => count($allBookings)
        ]);
    }

    public function details() {
        $this->render('traveller/details', ['title' => 'Package Details | Tripistry']);
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
}
?>
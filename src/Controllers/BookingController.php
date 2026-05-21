<?php
require_once __DIR__ . '/../Models/BookingModel.php';

class BookingController {
    private $bookingModel;

    public function __construct($pdo) {
        $this->bookingModel = new BookingModel($pdo);
    }

    public function processBooking() {
        echo "<h3>Trace Step 1: Controller Reached</h3>";

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // FAKE SESSION
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'traveller';
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'traveller') {
            die("<h2 style='color:red;'>FAILED: Security Check Rejected You.</h2>");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<h3>Trace Step 2: POST Request Confirmed</h3>";

            $travellerId     = $_SESSION['user_id'];
            $packageId       = filter_input(INPUT_POST, 'package_id', FILTER_SANITIZE_NUMBER_INT);
            $travelDate      = trim($_POST['travel_date']);
            $partySize       = filter_input(INPUT_POST, 'party_size', FILTER_SANITIZE_NUMBER_INT);
            $specialRequests = isset($_POST['requests']) ? htmlspecialchars(trim($_POST['requests'])) : null;

            echo "<strong>Data Extracted from Form:</strong><pre>";
            var_dump([
                'package_id' => $packageId, 
                'travel_date' => $travelDate, 
                'party_size' => $partySize,
                'special_requests' => $specialRequests
            ]);
            echo "</pre>";

            if (empty($packageId) || empty($travelDate) || empty($partySize)) {
                die("<h2 style='color:red;'>FAILED: Empty Fields. Did you forget to hardcode package_id=1 in your HTML?</h2>");
            }

            echo "<h3>Trace Step 3: Attempting MariaDB Insert...</h3>";

            $success = $this->bookingModel->createBooking(
                $travellerId, 
                $packageId, 
                $travelDate, 
                $partySize, 
                $specialRequests
            );

            if ($success) {
                die("<h2 style='color:green;'>SUCCESS! The Database Insert Worked. Check VS Code now.</h2>");
            } else {
                die("<h2 style='color:red;'>FAILED: MariaDB Rejected the Insert. Check your BookingModel try/catch logic.</h2>");
            }

        } else {
            die("<h2 style='color:red;'>FAILED: Not a POST Request.</h2>");
        }
    }
}
?>
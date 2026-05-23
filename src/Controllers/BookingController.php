<?php
require_once __DIR__ . '/../Models/BookingModel.php';

class BookingController {
    private $bookingModel;

    public function __construct($pdo) {
        $this->bookingModel = new BookingModel($pdo);
    }

    public function processBooking() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // FIX: Changed 'role' to 'user_type' to match your AuthController
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'traveller') {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $travellerId     = $_SESSION['user_id'];
            
            // FIX: Safely grab package_id and ensure it's not an empty string
            $packageId       = !empty($_POST['package_id']) ? (int)$_POST['package_id'] : null;
            
            if (!$packageId) {
                die("<div style='padding: 2rem; background: #111; color: #ff4b4b; font-family: monospace;'>🚨 Data Drop: package_id is completely missing from the POST submission.</div>");
            }

            $travelDate      = $_POST['travel_date'] ?? date('Y-m-d', strtotime('+1 month')); 
            $partySize       = $_POST['party_size'] ?? 1;
            $specialRequests = $_POST['special_requests'] ?? '';

            $success = $this->bookingModel->processGroupBooking(
                $travellerId, 
                $packageId, 
                $travelDate, 
                $partySize, 
                $specialRequests
            );

            if ($success) {
                header("Location: /traveller/dashboard?status=booking_confirmed");
                exit();
            } else {
                die("<div style='padding: 2rem; background: #111; color: #ff4b4b; font-family: monospace;'>🚨 Database failed to insert the booking. See model trapdoor.</div>");
            }
        }
        
        header("Location: /traveller/packages");
        exit();
    }
}
?>
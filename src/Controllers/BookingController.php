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
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'traveller') {
            header("Location: /login?error=unauthorized_checkout");
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $travellerId     = $_SESSION['user_id'];
            $packageId       = filter_input(INPUT_POST, 'package_id', FILTER_SANITIZE_NUMBER_INT);
            $travelDate      = trim($_POST['travel_date']);
            $partySize       = filter_input(INPUT_POST, 'party_size', FILTER_SANITIZE_NUMBER_INT);
            $specialRequests = isset($_POST['requests']) ? htmlspecialchars(trim($_POST['requests'])) : null;

            if (empty($packageId) || empty($travelDate) || empty($partySize)) {
                header("Location: /traveller/checkout?error=missing_fields");
                exit();
            }

            $success = $this->bookingModel->createBooking(
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
                header("Location: /traveller/checkout?error=system_failure");
                exit();
            }
        } else {
            header("Location: /traveller/dashboard");
            exit();
        }
    }
}
?>
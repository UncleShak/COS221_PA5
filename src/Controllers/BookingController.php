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
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $travellerId     = $_SESSION['user_id'];
            $packageId       = $_POST['package_id'] ?? 1;
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
            }
        }
        header("Location: /traveller/checkout?status=booking_failed");
        exit();
    }
}
?>
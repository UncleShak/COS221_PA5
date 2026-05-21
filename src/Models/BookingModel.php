-- Active: 1779372272824@@127.0.0.1@3306
<?php

class BookingModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createBooking($travellerId, $packageId, $travelDate, $partySize, $specialRequests) {
        try {
            $sql = "INSERT INTO Bookings (traveller_id, package_id, travel_date, num_travellers, special_requests,status) 
                    VALUES (:traveller_id, :package_id, :travel_date, :party_size, :special_requests, 'Confirmed')";
            
            $stmt = $this->pdo->prepare($sql);
            
            // Bind the new parameter securely
            $success = $stmt->execute([
                ':traveller_id'     => $travellerId,
                ':package_id'       => $packageId,
                ':travel_date'      => $travelDate,
                ':num_travellers'       => $partySize,
                ':special_requests' => $specialRequests
            ]);

            return $success;

        } catch (PDOException $e) {
            error_log("Database Booking Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
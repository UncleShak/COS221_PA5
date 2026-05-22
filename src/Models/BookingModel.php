-- Active: 1779372272824@@127.0.0.1@3306
<?php

class BookingModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createBooking($travellerId, $packageId, $travelDate, $partySize, $specialRequests) {
        try {
            $paymentRef = 'TRP-' . strtoupper(substr(uniqid(), -6));
            $sql = "INSERT INTO bookings (
                        traveller_id, package_id, travel_date, num_travellers, 
                        special_requests, status, total_price, currency, payment_reference
                    ) 
                    VALUES (
                        :traveller_id, :package_id, :travel_date, :num_travellers, 
                        :special_requests, 'confirmed', 19700.00, 'ZAR', :payment_reference
                    )";
            
            $stmt = $this->pdo->prepare($sql);
            
            $success = $stmt->execute([
                ':traveller_id'      => $travellerId,
                ':package_id'        => $packageId,
                ':travel_date'       => $travelDate,
                ':num_travellers'    => $partySize, 
                ':special_requests'  => $specialRequests,
                ':payment_reference' => $paymentRef
            ]);

            return $success;

        } catch (PDOException $e) {
            echo "<br><div style='padding: 1rem; background: #ffebee; color: #c62828; border: 2px solid #c62828;'>";
            echo "<strong>MARIADB ERROR:</strong><br>" . $e->getMessage();
            echo "</div><br>";
            return false;
        }
    }

    public function cancelBooking($bookingId, $travellerId) {
        try {
            $sql = "UPDATE bookings 
                    SET status = 'cancelled' 
                    WHERE booking_id = :booking_id AND traveller_id = :traveller_id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':booking_id'   => $bookingId,
                ':traveller_id' => $travellerId
            ]);
        } catch (PDOException $e) {
            error_log("Database Error in cancelBooking: " . $e->getMessage());
            return false;
        }
    }

    public function getTravellerBookings($travellerId) {
        try {
            // THE FIX: Added a LEFT JOIN on the reviews table to pull rating and comment
            $sql = "SELECT 
                        b.*, 
                        p.title AS package_name, 
                        p.duration_days,
                        p.cover_image_url,
                        r.rating,
                        r.comment AS review_comment
                    FROM bookings b
                    JOIN packages p ON b.package_id = p.package_id
                    LEFT JOIN packagereviews r ON b.booking_id = r.booking_id
                    WHERE b.traveller_id = :traveller_id
                    ORDER BY b.travel_date ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':traveller_id' => $travellerId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "<div style='padding: 2rem; background: #111; color: #ff4b4b; border: 2px solid #ff4b4b; font-family: monospace; z-index: 9999; position: relative;'>";
            echo "<h3>MARIADB SELECT ERROR:</h3>";
            echo $e->getMessage();
            echo "</div>";
            die(); 
        }
    }
}
?>
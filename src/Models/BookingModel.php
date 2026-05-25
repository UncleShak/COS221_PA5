<?php

class BookingModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function processGroupBooking($travellerId, $packageId, $travelDate, $partySize, $specialRequests, $totalPrice = null) {
        try {
            $this->pdo->beginTransaction();

            $paymentRef = 'TRP-' . strtoupper(substr(uniqid(), -6));
            $groupTripId = null;

            // 2. THE SEARCH ALGORITHM
            $findSql = "SELECT group_trip_id, current_participants, max_participants 
                        FROM grouptrips 
                        WHERE package_id = :package_id 
                          AND departure_date = :travel_date 
                          AND status = 'open' 
                        LIMIT 1 FOR UPDATE";
            
            $findStmt = $this->pdo->prepare($findSql);
            $findStmt->execute([
                ':package_id' => $packageId,
                ':travel_date' => $travelDate
            ]);
            $group = $findStmt->fetch(PDO::FETCH_ASSOC);

            if ($group && ($group['current_participants'] + $partySize <= $group['max_participants'])) {

                $groupTripId = $group['group_trip_id'];
                
                $updateSql = "UPDATE grouptrips 
                              SET current_participants = current_participants + :party_size 
                              WHERE group_trip_id = :group_trip_id";
                $updateStmt = $this->pdo->prepare($updateSql);
                $updateStmt->execute([
                    ':party_size' => $partySize,
                    ':group_trip_id' => $groupTripId
                ]);

            } else {
                $insertGroupSql = "INSERT INTO grouptrips (
                                      package_id, departure_date, return_date, meeting_point, min_participants, max_participants, 
                                      current_participants, status, created_at
                                   ) VALUES (
                                      :package_id, :departure_date, DATE_ADD(:departure_date, INTERVAL 7 DAY), 'To Be Determined', 2, 10, 
                                      :party_size, 'open', NOW()
                                   )";
                $insertGrpStmt = $this->pdo->prepare($insertGroupSql);
                $insertGrpStmt->execute([
                    ':package_id'     => $packageId,
                    ':departure_date' => $travelDate,
                    ':party_size'     => $partySize
                ]);
                $groupTripId = $this->pdo->lastInsertId();
            }
            $totalPrice = $totalPrice ?? 0;

            $insertBookingSql = "INSERT INTO bookings (
                        traveller_id, package_id, group_trip_id, travel_date, num_travellers, 
                        special_requests, status, total_price, currency, payment_reference
                    ) 
                    VALUES (
                        :traveller_id, :package_id, :group_trip_id, :travel_date, :num_travellers, 
                        :special_requests, 'confirmed', :total_price, 'ZAR', :payment_reference
                    )";
            
            $bookStmt = $this->pdo->prepare($insertBookingSql);
            $success = $bookStmt->execute([
                ':traveller_id'      => $travellerId,
                ':package_id'        => $packageId,
                ':group_trip_id'     => $groupTripId,
                ':travel_date'       => $travelDate,
                ':num_travellers'    => $partySize, 
                ':special_requests'  => $specialRequests,
                ':total_price'       => $totalPrice,
                ':payment_reference' => $paymentRef
            ]);

            // THE FIX: Grab the exact ID of the booking we just created!
            $newBookingId = $this->pdo->lastInsertId();

            $rosterSql = "INSERT INTO grouptripparticipants (traveller_id, group_trip_id, package_id, status, joined_at) 
                          VALUES (:tid, :gid, :pid, 'confirmed', NOW()) 
                          ON DUPLICATE KEY UPDATE status = 'confirmed'";
            
            $rosterStmt = $this->pdo->prepare($rosterSql);
            $rosterStmt->execute([
                ':tid' => $travellerId,
                ':gid' => $groupTripId,
                ':pid' => $packageId
            ]);

            $this->pdo->commit();
            
            // THE FIX: Return the ID instead of just a boolean
            return $newBookingId;

        } catch (Exception $e) {
            // 6. FAILURE: Something broke. Revert all changes instantly.
            $this->pdo->rollBack();
            
            // Trapdoor for Relentless Testing
            echo "<br><div style='padding: 1rem; background: #ffebee; color: #c62828; border: 2px solid #c62828; z-index: 999; position: relative;'>";
            echo "<strong>MATCHING ALGORITHM ERROR:</strong><br>" . $e->getMessage();
            echo "</div><br>";
            die();
        }
    }

    public function cancelBooking($bookingId, $travellerId) {
        try {
            $this->pdo->beginTransaction();

            // 1. Fetch the booking details first so we know which group to update
            $findSql = "SELECT group_trip_id, num_travellers FROM bookings WHERE booking_id = :booking_id AND traveller_id = :traveller_id";
            $findStmt = $this->pdo->prepare($findSql);
            $findStmt->execute([
                ':booking_id' => $bookingId, 
                ':traveller_id' => $travellerId
            ]);
            $booking = $findStmt->fetch(PDO::FETCH_ASSOC);

            if (!$booking) {
                $this->pdo->rollBack();
                return false;
            }

            // 2. Cancel the main booking
            $sql = "UPDATE bookings 
                    SET status = 'cancelled' 
                    WHERE booking_id = :booking_id AND traveller_id = :traveller_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':booking_id'   => $bookingId,
                ':traveller_id' => $travellerId
            ]);

            // 3. Remove them from the active group roster
            if (!empty($booking['group_trip_id'])) {
                $rosterSql = "UPDATE grouptripparticipants 
                              SET status = 'cancelled' 
                              WHERE traveller_id = :traveller_id AND group_trip_id = :group_trip_id";
                $rosterStmt = $this->pdo->prepare($rosterSql);
                $rosterStmt->execute([
                    ':traveller_id' => $travellerId,
                    ':group_trip_id' => $booking['group_trip_id']
                ]);

                // 4. Subtract their party size from the group headcount
                $countSql = "UPDATE grouptrips 
                             SET current_participants = current_participants - :num 
                             WHERE group_trip_id = :group_trip_id";
                $countStmt = $this->pdo->prepare($countSql);
                $countStmt->execute([
                    ':num' => $booking['num_travellers'],
                    ':group_trip_id' => $booking['group_trip_id']
                ]);
            }

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Database Error in cancelBooking: " . $e->getMessage());
            return false;
        }
    }

    public function getTravellerBookings($travellerId) {
        try {
            // THE FIX: Join through grouptrips to find the connection
            $sql = "SELECT 
                        b.*, 
                        p.title AS package_name, 
                        p.duration_days,
                        p.cover_image_url,
                        p.agency_id,
                        r.rating,
                        r.comment AS review_comment,
                        ar.rating AS agency_review_rating,
                        ar.comment AS agency_review_comment,
                        gtp.group_trip_id
                    FROM bookings b
                    JOIN packages p ON b.package_id = p.package_id
                    LEFT JOIN packagereviews r ON b.booking_id = r.booking_id
                    LEFT JOIN agencyreviews ar ON b.booking_id = ar.booking_id
                    LEFT JOIN grouptripparticipants gtp 
                        ON b.group_trip_id = gtp.group_trip_id 
                        AND b.traveller_id = gtp.traveller_id
                    WHERE b.traveller_id = :traveller_id
                    ORDER BY b.travel_date ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':traveller_id' => $travellerId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Keep your existing error handling
            echo "<div style='padding: 2rem; background: #111; color: #ff4b4b; border: 2px solid #ff4b4b;'>";
            echo "<h3>MARIADB SELECT ERROR:</h3>" . $e->getMessage();
            echo "</div>";
            die(); 
        }
    }
}
?>
<?php

class AgencyController{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function createPackage(){
        try{
            // Gets all flights by joining locations for readable cities
            $flightQuery = "SELECT f.flight_id, f.airline_name, f.flight_number, f.flight_class, f.base_price,
                                    dep.city AS departure_city, arr.city AS arrival_city
                            FROM flights f
                            JOIN locations dep ON f.departure_location_id = dep.location_id
                            JOIN locations arr ON f.arrival_location_id = arr.location_id";

            $stmt = $this->conn->prepare($flightQuery);
            $stmt->execute();
            $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // do the same for accoms and also join locations for city names
            $accQuery= "SELECT a.accommodation_id, a.name, a.type, a.star_rating, a.price_per_night, l.city
             FROM accommodations a
             JOIN locations l ON a.location_id = l.location_id";

             $stmt= $this->conn->prepare($accQuery);
             $stmt->execute();
             $accommodations = $stmt->fetchAll(PDO::FETCH_ASSOC);

             // Skyf the new mats to the view
             $title = 'Forge New Package - Tripistry';
            ob_start();
            require_once __DIR__ . '/../Views/agency/create_package.php';
            $content = ob_get_clean();
            require_once __DIR__ . '/../Views/layout.php';
        } catch(PDOException $e){
            error_log("Failed to fetch raw materials: " . $e->getMessage());
            echo "A database error occurred. Check the terminal logs.";
        }
    }

    public function storePackage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Start the Transaction
                $this->conn->beginTransaction();

                // 1. Insert the Base Package
                // 1. Insert the Base Package (Columns and tokens perfectly matched)
                $packageQuery = "INSERT INTO packages (agency_id, title, description, base_price, duration_days, max_capacity, cover_image_url) 
                                 VALUES (:agency_id, :title, :description, :base_price, :duration, :capacity, :cover_image_url)";
                $stmt = $this->conn->prepare($packageQuery);
                
                $stmt->execute([
                    ':agency_id'   => $_SESSION['user_id'], 
                    ':title'       => htmlspecialchars($_POST['title']),
                    ':description' => htmlspecialchars($_POST['description']),
                    ':base_price'  => (float)$_POST['base_price'],
                    ':duration'    => (int)$_POST['duration_days'],
                    ':capacity'    => (int)$_POST['max_capacity'],
                    ':cover_image_url' => !empty($_POST['cover_image']) ? filter_var($_POST['cover_image'], FILTER_SANITIZE_URL) : null
                ]);

                // 2. Grab the new Package ID that MariaDB just auto-generated
                $packageId = $this->conn->lastInsertId();

                // 3. Link the Flight (if one was selected)
                if (!empty($_POST['flight_id'])) {
                    $flightQuery = "INSERT INTO packageflights (package_id, flight_id) VALUES (:pkg_id, :flt_id)";
                    $flightStmt = $this->conn->prepare($flightQuery);
                    $flightStmt->execute([
                        ':pkg_id' => $packageId,
                        ':flt_id' => (int)$_POST['flight_id']
                    ]);
                }

                // 4. Link the Accommodation (if one was selected)
                if (!empty($_POST['accommodation_id'])) {
                    $accQuery = "INSERT INTO packageaccommodations (package_id, accommodation_id) VALUES (:pkg_id, :acc_id)";
                    $accStmt = $this->conn->prepare($accQuery);
                    $accStmt->execute([
                        ':pkg_id' => $packageId,
                        ':acc_id' => (int)$_POST['accommodation_id']
                    ]);
                }

                // If we made it here without errors, lock it all in!
                $this->conn->commit();
                
                // Redirect back to the forge with a success flag
                header("Location: /agency/package/create?success=package_forged");
                exit;

            } catch (PDOException $e) {
                // If anything fails, roll back the entire transaction
                $this->conn->rollBack();
                error_log("Package Forge Error: " . $e->getMessage());
                header("Location: /agency/package/create?error=forge_failed");
                exit;
            }
        }
    }
}

?>
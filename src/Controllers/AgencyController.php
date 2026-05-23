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
             FROM accomodations a
             JOIN locations l ON a.loaction_id = l.location_id";

             $stmt= $this->conn->prepare($accQuery);
             $stmt->execute();
             $accomodations = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
}

?>
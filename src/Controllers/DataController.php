<?php

class DataController{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }
    

    public function handleUpload(){
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $uploadType = $_POST['upload_type'] ?? '';

            try{
                if($uploadType === 'flight'){
                    $query = "INSERT INTO flights (airline_name, flight_number, departure_airport, arrival_airport, 
                               departure_location_id, arrival_location_id, flight_class, 
                               duration_minutes, base_price) 
                               VALUES (:airline, :flight_no, :dep_air, :arr_air, :dep_loc, :arr_loc, :class, :duration, :price)";

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute([
                        ':airline'  => htmlspecialchars($_POST['airline_name']),
                        ':flight_no'=> htmlspecialchars($_POST['flight_number']),
                        ':dep_air'  => strtoupper(htmlspecialchars($_POST['departure_airport'])), 
                        ':arr_air'  => strtoupper(htmlspecialchars($_POST['arrival_airport'])),
                        ':dep_loc'  => (int)$_POST['departure_location_id'],
                        ':arr_loc'  => (int)$_POST['arrival_location_id'],
                        ':class'    => $_POST['flight_class'], 
                        ':duration' => (int)$_POST['duration_minutes'],
                        ':price'    => (float)$_POST['base_price']
                    ]);
                    header("Location: /manage-data?success=flight_added");
                    exit;
                }
                elseif ($uploadType === 'accommodation') {
                    $query = "INSERT INTO accommodations 
                              (name, location_id, type, star_rating, price_per_night, description, address) 
                              VALUES 
                              (:name, :loc_id, :type, :rating, :price, :desc, :address)";
                    
                    $stmt = $this->conn->prepare($query);
                    
                    $stmt->execute([
                        ':name'   => htmlspecialchars($_POST['name']),
                        ':loc_id' => (int)$_POST['location_id'],
                        ':type'   => $_POST['type'],
                        ':rating' => !empty($_POST['star_rating']) ? (int)$_POST['star_rating'] : null,
                        ':price'  => (float)$_POST['price_per_night'],
                        ':desc'   => htmlspecialchars($_POST['description']),
                        ':address'=> htmlspecialchars($_POST['address'])
                    ]);

                    header("Location: /manage-data?success=accommodation_added");
                    exit;
                }

            } catch(PDOException $e){
                error_log("Data Upload Error: " . $e->getMessage());
                header("Location: /manage-data?error=upload_failed");
                exit;
            }
        }
        
    }
}

?>
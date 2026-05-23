<?php 

class UserModel{
    private $conn;
    private $table_name = "users";

    public function __construct($db){
        $this->conn= $db;
    }

    public function verifyCredentials($email, $password){
        //1. Prep ts
        $query = "SELECT user_id, email, password_hash, user_type 
                  FROM " . $this->table_name . " 
                  WHERE email = :email AND is_active = 1 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        //2. Bind the email
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        // 3. Execute and fetch
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // 4. Verify the password against the stored hash
            if (password_verify($password, $user['password_hash'])) {
                // Remove the hash before returning the data to the controller for safety
                unset($user['password_hash']);
                return $user;
            }
        }
        return false;
    }
}

?>
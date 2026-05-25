<?php 

class UserModel{
    private $conn;
    private $table_name = "users";

    public function __construct($db){
        $this->conn = $db;
    }

    public function verifyCredentials($email, $password){
        $query = "SELECT user_id, email, password_hash, user_type 
                  FROM " . $this->table_name . " 
                  WHERE email = :email AND is_active = 1 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password_hash'])) {
                unset($user['password_hash']);
                return ['status' => 'success', 'user' => $user];
            } else {
                return ['status' => 'wrong_password'];
            }
        }
        return ['status' => 'not_found'];
    }
}
?>
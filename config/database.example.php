<?php 

class Database {
    private $host = '127.0.0.1';
    private $db_name= 'tripistry';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection(){
        $this->conn = null;

        try{
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            
            $this->conn->setAttribute(PDO::ATR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully";

        }catch(PDOException $e){
            echo "Connection failed: " .  $e->getMessage();
        }
        return $this->conn;
    } 
}

?>
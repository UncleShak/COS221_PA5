<?php 

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $envPath = __DIR__ . '/../.env';
        
        if (file_exists($envPath)) {
            $envVars = parse_ini_file($envPath);
            
            $this->host     = $envVars['DB_HOST'] ?? '127.0.0.1';
            $this->db_name  = $envVars['DB_NAME'] ?? 'tripistry';
            $this->username = $envVars['DB_USER'] ?? 'root';
            $this->password = $envVars['DB_PASS'] ?? '';
        } else {
            die("CRITICAL ERROR: .env file missing. Please create one from .env.example");
        }
    }

    public function getConnection(){
        $this->conn = null;

        try{
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo "Connection failed: " .  $e->getMessage();
        }
        return $this->conn;
    } 
}

?>
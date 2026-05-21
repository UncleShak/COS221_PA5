<?php
require_once '../config/database.php';
// require_once '../src/Models/UserModel.php'; 

class AuthController{
    public function showLogin(){ // self explanatory
        $title ='Sign In - Tripistry';
        ob_start();
        require_once '../src/Views/auth/login.php';
        $content = ob_get_clean();
        require_once '../src/Views/layout.php';
    }

    public function login(){
        if($_SERVER["REQUEST_METHOD"]== "POST"){
            $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

            $password = trim($_POST['password']);

            if(empty($email) || empty($password)){
                header("Location: /login?error=empty_fields");
                exit;
            }

            $database = new Database();
            $db= $database->getConnection();

            echo "<div style='padding: 2rem; text-align: center; font-family: sans-serif;'>";
            echo "<h2 style='color: #10b981;'>Tactical Link Established!</h2>";
            echo "<p>Email captured: " . htmlspecialchars($email) . "</p>";
            echo "<p>Ready to hand off to UserModel tomorrow.</p>";
            echo "</div>";
        }
    }
}
?>
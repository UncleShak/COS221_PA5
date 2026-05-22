<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/UserModel.php'; 

class AuthController{
    public function showLogin(){ // self explanatory
        $title ='Sign In - Tripistry';
        ob_start();
        require_once __DIR__ . '/../Views/auth/login.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../Views/layout.php';
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

            $userModel = new UserModel($db);
            $user = $userModel->verifyCredentials($email, $password);

            if($user){
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_type']=$user['user_type'];

                if($user['user_type']==='traveller'){
                    header("Location: /traveller/dashboard");
                }else{
                    header("Location: /agency/dashboard");
                }
                exit;
            }else{
                header("Location: /login?error=invalid_credentials");
            }
        }
    }

    public static function checkAuth(){
        if(!isset($_SESSION['user_id'])){
            header("Location: /login?error=auth_required");
            exit;
        }
    }

    public static function checkRole($requiredRole){
        self::checkAuth();

        if($_SESSION['user_type'] !== $requiredRole){
            http_response_code(403);
            echo "<div style='text-align:center; padding: 5rem; font-family: sans-serif;'>";
            echo "<h1 style='color: var(--danger, #ef4444);'>403 - Forbidden Access</h1>";
            echo "<p>Your account type does not have permission to view this destination.</p>";
            echo "<a href='/login'>Return to Safety</a>";
            echo "</div>";
            exit;
        }
    }
}
?>
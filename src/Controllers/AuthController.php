<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/UserModel.php'; 

class AuthController{
    
    public function showLogin(){ 
        $title ='Sign In - Tripistry';
        ob_start();
        require_once __DIR__ . '/../Views/auth/login.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function login(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if($_SERVER["REQUEST_METHOD"]== "POST"){
            
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_attempt_time'] = time();
            }

            if ($_SESSION['login_attempts'] >= 5) {
                if (time() - $_SESSION['last_attempt_time'] < 300) {
                    header("Location: /login?error=account_locked");
                    exit;
                } else {
                    $_SESSION['login_attempts'] = 0;
                }
            }

            $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
            $password = trim($_POST['password']);
            $redirect = $_POST['redirect'] ?? ''; 

            if(empty($email) || empty($password)){
                header("Location: /login?error=empty_fields");
                exit;
            }

            $database = new Database();
            $db= $database->getConnection();

            $userModel = new UserModel($db);
            $result = $userModel->verifyCredentials($email, $password);

            if($result['status'] === 'success'){
                $user = $result['user'];
                session_regenerate_id(true); 
                $_SESSION['login_attempts'] = 0; 
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_type'] = $user['user_type'];

                if($user['user_type']==='traveller'){
                    if (!empty($redirect) && strpos($redirect, '/') === 0) {
                        header("Location: " . $redirect);
                    } else {
                        header("Location: /traveller/packages");
                    }
                }else{
                    header("Location: /agency/dashboard");
                }
                exit;
            } else if ($result['status'] === 'not_found') {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                header("Location: /login?error=not_found");
                exit;
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                header("Location: /login?error=wrong_password");
                exit;
            }
        }
    }

    // THE FIX: Much more forgiving regex that allows ANY special character
    public static function isPasswordStrong($password) {
        // Requires: 8+ chars, 1 uppercase, 1 lowercase, 1 number, 1 special character (ANY non-alphanumeric)
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/';
        return preg_match($pattern, $password);
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

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();  
        header("Location: /login?status=logged_out");
        exit();
    }
}
?>
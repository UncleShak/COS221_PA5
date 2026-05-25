<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/AuthController.php';

class RegistrationController{
    public function register(){
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $dbInstance = new Database();
            $conn = $dbInstance->getConnection();

            $firstName = htmlspecialchars(trim($_POST['first_name'] ?? ''));
            $lastName = htmlspecialchars(trim($_POST['last_name'] ?? ''));
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
                header("Location: /register?error=empty_fields");
                exit;
            }

            if ($password !== $confirmPassword) {
                header("Location: /register?error=password_mismatch");
                exit;
            }

            if (!AuthController::isPasswordStrong($password)) {
                header("Location: /register?error=weak_password");
                exit;
            }

            $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
            $checkStmt->execute([':email' => $email]);
            if ($checkStmt->fetch()) {
                header("Location: /register?error=email_taken");
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                $conn->beginTransaction();

                $userQuery = "INSERT INTO users (email, password_hash, user_type) 
                              VALUES (:email, :password_hash, 'traveller')";
                $userStmt = $conn->prepare($userQuery);
                $userStmt->execute([
                    ':email'         => $email,
                    ':password_hash' => $hashedPassword
                ]);

                $newUserId = $conn->lastInsertId();

                $travellerQuery = "INSERT INTO travellers (user_id, first_name, last_name) 
                                   VALUES (:user_id, :first_name, :last_name)";
                $travellerStmt = $conn->prepare($travellerQuery);
                $travellerStmt->execute([
                    ':user_id'    => $newUserId,
                    ':first_name' => $firstName,
                    ':last_name'  => $lastName
                ]);

                $conn->commit();

                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['user_type'] = 'traveller';

                header("Location: /traveller/packages?success=account_created");
                exit;

            } catch (PDOException $e) {
                $conn->rollBack();
                error_log("Registration Error: " . $e->getMessage());
                header("Location: /register?error=server_error");
                exit;
            }

        } else {
            $title = 'Register - Tripistry';
            ob_start();
            require_once __DIR__ . '/../Views/auth/register.php';
            $content = ob_get_clean();
            require_once __DIR__ . '/../Views/layout.php';
        }
    }
}
?>
<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/AuthController.php';

class RegistrationController{
    public function register(){
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $dbInstance = new Database();
            $conn = $dbInstance->getConnection();

            // 1. Sanitize the data
            $firstName = htmlspecialchars(trim($_POST['first_name'] ?? ''));
            $lastName = htmlspecialchars(trim($_POST['last_name'] ?? ''));
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // 2. Basic Validation
            if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
                header("Location: /register?error=empty_fields");
                exit;
            }

            if ($password !== $confirmPassword) {
                header("Location: /register?error=password_mismatch");
                exit;
            }

            // 3. SECURITY FEATURE: Enforce Strict Password Complexity
            if (!AuthController::isPasswordStrong($password)) {
                header("Location: /register?error=weak_password");
                exit;
            }

            // 4. Check for duplicate emails
            $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
            $checkStmt->execute([':email' => $email]);
            if ($checkStmt->fetch()) {
                header("Location: /register?error=email_taken");
                exit;
            }

            // 5. SECURITY FEATURE: Secure Password Hashing
            // PASSWORD_DEFAULT automatically uses the strongest available algorithm (usually BCRYPT)
            // It also automatically generates a cryptographic salt.
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 6. Insert the new user into the database
            try {
                // Start transaction because we are writing to TWO tables
                $conn->beginTransaction();

                // Step A: Create the core auth record in the `users` table
                $userQuery = "INSERT INTO users (email, password_hash, user_type) 
                              VALUES (:email, :password_hash, 'traveller')";
                $userStmt = $conn->prepare($userQuery);
                
                $userStmt->execute([
                    ':email'         => $email,
                    ':password_hash' => $hashedPassword // Matches the DB column name perfectly
                ]);

                // Grab the auto-generated ID for the new user
                $newUserId = $conn->lastInsertId();

                // Step B: Create the profile record in the `travellers` table
                $travellerQuery = "INSERT INTO travellers (user_id, first_name, last_name) 
                                   VALUES (:user_id, :first_name, :last_name)";
                $travellerStmt = $conn->prepare($travellerQuery);
                
                $travellerStmt->execute([
                    ':user_id'    => $newUserId,
                    ':first_name' => $firstName,
                    ':last_name'  => $lastName
                ]);

                // Both inserts succeeded, lock it in!
                $conn->commit();

                // Success! Send them to the login page
                header("Location: /login?success=account_created");
                exit;

            } catch (PDOException $e) {
                // If either insert fails, roll it all back safely
                $conn->rollBack();
                error_log("Registration Error: " . $e->getMessage());
                header("Location: /register?error=server_error");
                exit;
            }

        } else {
            // If it is just a normal GET request, show the HTML form
            $title = 'Register - Tripistry';
            ob_start();
            require_once __DIR__ . '/../Views/auth/register.php';
            $content = ob_get_clean();
            require_once __DIR__ . '/../Views/layout.php';
        }
    }
}

?>
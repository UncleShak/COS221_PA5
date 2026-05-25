<?php
?>
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem; padding-top: 120px;">
    
    <div style="width: 100%; max-width: 460px;">
        
        <div class="modal-demo glass-heavy" style="width: 100%;">
            <h2 class="modal-title">Join Tripistry</h2>
            <p style="font-size: 0.95rem; color: var(--text-soft); margin-bottom: 1.5rem;">
                Create an account to start your journey.
            </p>

            <?php if (isset($_GET['error'])): ?>
                <div style="color: var(--danger, #ef4444); font-size: 0.9rem; font-weight: 600; margin-bottom: 1.5rem; text-align: left; line-height: 1.4;">
                    <?php 
                        switch ($_GET['error']) {
                            case 'empty_fields':
                                echo "Please fill in all required fields.";
                                break;
                            case 'password_mismatch':
                                echo "The passwords you entered do not match.";
                                break;
                            case 'weak_password':
                                echo "Password must be at least 8 characters and include an uppercase letter, a number, and a special character.";
                                break;
                            case 'email_taken':
                                echo "An account with this email already exists.";
                                break;
                            case 'server_error':
                                echo "A server error occurred. Please try again later.";
                                break;
                            default:
                                echo "Registration failed. Please try again.";
                        }
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="/register" method="POST">
                <div class="input-stack" style="text-align: left;">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="input-group">
                            <label class="input-label" for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" class="input-field" required>
                        </div>
                        <div class="input-group">
                            <label class="input-label" for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" class="input-field" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="input-field" required>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label" for="password">Password</label>
                        <?php $pwdError = (isset($_GET['error']) && in_array($_GET['error'], ['weak_password', 'password_mismatch'])); ?>
                        <input type="password" id="password" name="password" class="input-field <?= $pwdError ? 'error' : '' ?>" placeholder="••••••••" required>
                        <div class="input-hint">At least 8 characters, 1 uppercase, 1 number, 1 symbol</div>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="input-field <?= $pwdError ? 'error' : '' ?>" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1.5rem;">Create Account</button>
                </div>
            </form>
            
            <div style="margin-top: 2rem; font-size: 0.85rem; color: var(--text-muted);">
                Already have an account? 
                <a href="/login" style="color: var(--coral); font-weight: 600; text-decoration: none; transition: color 0.2s;">
                    Sign in here
                </a>
            </div>
        </div>
    </div>
</div>
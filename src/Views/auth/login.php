<?php
// src/Views/auth/login.php
// Note: No <html>, <head>, or background orbs here. layout.php handles that.
?>
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem; padding-top: 120px;">
    
    <div style="width: 100%; max-width: 420px;">
        
        <?php if (isset($_GET['error']) && $_GET['error'] === 'account_locked'): ?>
            <div class="toast-stack" style="margin-bottom: 2rem; width: 100%;">
                <div class="toast t-warn" style="animation: fadeUp 0.4s var(--ease) both;">
                    <div>
                        <div class="toast-title" style="color: var(--danger);">Account Temporarily Locked</div>
                        <div class="toast-msg">To protect your security, we've locked this account after 5 failed attempts. Please wait 5 minutes before trying again.</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="modal-demo glass-heavy" style="width: 100%;">
            <h2 class="modal-title">Welcome to Tripistry</h2>
            <p style="font-size: 0.95rem; color: var(--text-soft); margin-bottom: 2.5rem;">
                Sign in to continue your journey.
            </p>
            
            <form action="/login" method="POST">
                <div class="input-stack" style="text-align: left;">
                    
                    <div class="input-group">
                        <label class="input-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="input-field" placeholder="wanderer@tripistry.com" required>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label" for="password">Password</label>
                        
                        <?php 
                            $isInvalid = (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'); 
                        ?>
                        
                        <input type="password" id="password" name="password" class="input-field <?= $isInvalid ? 'error' : '' ?>" placeholder="••••••••" required>
                        
                        <?php if ($isInvalid): ?>
                            <div class="input-hint" style="color: var(--danger); font-weight: 600; margin-top: 0.4rem; animation: fadeUp 0.3s var(--ease) both;">
                                Invalid credentials. Please verify your email and password.
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Sign In</button>
                </div>
            </form>
            
            <div style="margin-top: 2rem; font-size: 0.85rem; color: var(--text-muted);">
                Don't have an account? 
                <a href="/register" style="color: var(--coral); font-weight: 600; text-decoration: none; transition: color 0.2s;">
                    Register here
                </a>
            </div>
        </div>
    </div>
</div>
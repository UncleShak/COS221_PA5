<?php
// src/Views/auth/login.php
// Note: No <html>, <head>, or background orbs here. layout.php handles that.
?>
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem;padding-top: 120px;">
    
    <div class="modal-demo glass-heavy" style="width: 100%; max-width: 420px;">
        <h2 class="modal-title">Welcome to Tripistry</h2>
        <p style="font-size: 0.95rem; color: var(--text-soft); margin-bottom: 2.5rem;">
            Sign in to continue your journey.
        </p>
        
        <form action="/traveller/dashboard" method="GET">
            <div class="input-stack" style="text-align: left;">
                
                <div class="input-group">
                    <label class="input-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="input-field" placeholder="wanderer@tripistry.com" required>
                </div>
                
                <div class="input-group">
                    <label class="input-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="input-field" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1rem;">Sign In</button>
            </div>
        </form>
        
        <div style="margin-top: 2rem; font-size: 0.85rem; color: var(--text-muted);">
            Don't have an account? 
            <a href="/register" style="color: var(--ocean); font-weight: 600; text-decoration: none; transition: color 0.2s;">
                Register here
            </a>
        </div>
    </div>
</div>
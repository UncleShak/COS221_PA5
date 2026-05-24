<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Tripistry Resort' ?></title>
    
    <link rel="stylesheet" href="/css/StyleGuide.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@1,9..144,300&family=Inter:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="ambient-bg"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <nav class="sg-nav">
      <a href="/home" class="sg-nav-logo" style="text-decoration: none; color: inherit;">
         TRIPISTRY
      </a>
      
      <ul class="sg-nav-links">
        <li><a href="/traveller/packages">Explore Packages</a></li>
      </ul>

      <div style="display: flex; align-items: center; gap: 1.5rem;">
        
        <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/logout" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none;">Sign Out</a>
            <a href="/traveller/dashboard" style="text-decoration: none; font-size: 1.4rem; transition: transform 0.2s var(--ease);" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Your Itinerary">
                👤
            </a>
            <span class="sg-nav-pill">Traveller View</span>
        <?php else: ?>
            <a href="/login" style="text-decoration: none; font-size: 1.4rem; transition: transform 0.2s var(--ease);" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Sign In">
                🔒
            </a>
            <span class="sg-nav-pill" style="background: rgba(255, 111, 97, 0.1); color: var(--coral);">Guest Mode</span>
        <?php endif; ?>
        
      </div>
    </nav>

    <div class="content">
        <?= $content ?>
    </div>
</body>
</html>
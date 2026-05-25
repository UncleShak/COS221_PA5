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
        <?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
        <?php if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] === 'traveller'): ?>
            <li><a href="/traveller/packages">Packages</a></li>
            <li><a href="/traveller/compare">Compare</a></li>
            <li><a href="/traveller/destinations">Destinations</a></li>
            <li><a href="/traveller/flights">Flights</a></li>
            <li><a href="/traveller/accommodations">Accommodations</a></li>
            <li><a href="/traveller/attractions">Attractions</a></li>
            <li><a href="/traveller/restaurants">Restaurants</a></li>
        <?php endif; ?>
      </ul>

      <div style="display: flex; align-items: center; gap: 1.5rem;">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/logout" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none;">Sign Out</a>
            <?php if ($_SESSION['user_type'] === 'traveller'): ?>
                <a href="/traveller/dashboard" class="btn-primary" style="padding: 0.5rem 1.2rem; font-size: 0.85rem; border-radius: 99px; text-decoration: none;">My Hub</a>
            <?php else: ?>
                <a href="/agency/dashboard" class="btn-primary" style="padding: 0.5rem 1.2rem; font-size: 0.85rem; border-radius: 99px; text-decoration: none;">Command Center</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="/login" style="text-decoration: none; font-size: 0.9rem; font-weight: 600; color: var(--text-main);">Sign In</a>
            <a href="/register" class="btn-primary" style="padding: 0.5rem 1.2rem; font-size: 0.85rem; border-radius: 99px; text-decoration: none;">Get Started</a>
        <?php endif; ?>
      </div>
    </nav>

    <div class="content">
        <?= $content ?>
    </div>
</body>
</html>
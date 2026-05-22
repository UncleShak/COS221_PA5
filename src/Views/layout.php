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
      <div class="sg-nav-logo"><span class="logo-dot"></span> TRIPISTRY</div>
      <ul class="sg-nav-links">
        <li><a href="/traveller/dashboard">Dashboard</a></li>
        <li><a href="/traveller/details">Packages</a></li>
      </ul>
      <span class="sg-nav-pill">Traveller View</span>
    </nav>

    <div class="content">
        <?= $content ?>
    </div>
</body>
</html>
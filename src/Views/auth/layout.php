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
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

<div style="position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); z-index: 999; width: 90%; max-width: 1700px;">
        <nav class="navbar-demo glass-clear" style="border-radius: var(--r-pill); padding: 0.5rem 1rem;">
            <div class="navbar-inner" style="padding: 0.5rem 1rem;">
                
                <div class="nb-logo" style="color: var(--text-main);">
                  TRIPISTRY
                </div>
                
                <ul class="nb-links" style="margin: 0; padding: 0;">
                    <li><a href="/traveller/dashboard" style="color: var(--ocean); font-weight: 600;">Dashboard</a></li>
                    <li><a href="/traveller/details">Packages</a></li>
                </ul>
                
                <div class="nb-actions">
                    <div class="nb-icon-btn" style="background: rgba(255,255,255,0.4); color: var(--ocean);">
                        👤
                    </div>
                </div>
                
            </div>
        </nav>
    </div>

    <div class="content">
        <?= $content ?>
    </div>
</body>
</html>
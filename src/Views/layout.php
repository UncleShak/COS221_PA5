<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Tripistry Resort' ?></title>

    <!-- Apply saved theme BEFORE CSS loads to avoid flashing -->
    <script>
      (function () {
        try {
          const stored = localStorage.getItem('tripistry-theme');
          const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
          const theme = stored || (prefersDark ? 'dark' : 'light');
          document.documentElement.setAttribute('data-theme', theme);
        } catch (e) {
          // If storage is blocked, fall back to default.
        }
      })();
    </script>

    <link rel="stylesheet" href="/css/StyleGuide.css">

    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@1,9..144,300&family=Inter:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="ambient-bg"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <nav class="sg-nav">
      <a href="/home" class="sg-nav-logo" style="text-decoration: none;">TRIPISTRY</a>
        
        <ul class="nb-links" style="margin: 0; padding: 0;">
              <?php if (($_SESSION['user_type'] ?? '') === 'agency'): ?>
                  <li><a href="index.php?route=agency/dashboard"
                        style="color:var(--ocean); font-weight:600;">Dashboard</a></li>
              <?php else: ?>
                  <li><a href="index.php?route=traveller/dashboard"
                        style="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard') ? 'color:var(--ocean);font-weight:600;' : '' ?>">
                      Dashboard</a></li>
                  <li><a href="index.php?route=traveller/packages"
                        style="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'packages') ? 'color:var(--ocean);font-weight:600;' : '' ?>">
                      Packages</a></li>
                  <li><a href="index.php?route=traveller/browse"
                        style="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'browse') ? 'color:var(--ocean);font-weight:600;' : '' ?>">
                      Browse</a></li>
              <?php endif; ?>
      </ul>

      <div class="sg-nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
          <?php $dashboardUrl = $_SESSION['user_type'] === 'agency' ? '/agency/dashboard' : '/traveller/dashboard'; ?>
          <a href="<?= $dashboardUrl ?>" class="btn-secondary" style="padding: 0.55rem 1.2rem; font-size: 0.85rem; text-decoration: none;" onclick="window.location.href=this.href; return false;">Dashboard</a>
          <a href="/logout" class="btn-ghost" style="padding: 0.55rem 1.2rem; font-size: 0.85rem; text-decoration: none;">Logout</a>
        <?php else: ?>
          <a href="/login" class="btn-secondary" style="padding: 0.55rem 1.2rem; font-size: 0.85rem; text-decoration: none;">Log In</a>
          <a href="/register" class="btn-primary" style="padding: 0.55rem 1.2rem; font-size: 0.85rem; text-decoration: none;">Sign Up</a>
        <?php endif; ?>

        <button
          type="button"
          class="theme-toggle"
          id="themeToggle"
          aria-pressed="false"
          aria-label="Toggle light and dark mode"
          title="Toggle light/dark mode"
        >
          <span class="theme-toggle-icon" aria-hidden="true"></span>
        </button>

      </div>
    </nav>

    <div class="content">
        <?= $content ?>
    </div>

    <script>
      (function () {
        const btn = document.getElementById('themeToggle');
        if (!btn) return;

        function applyTheme(theme) {
          document.documentElement.setAttribute('data-theme', theme);
          try { localStorage.setItem('tripistry-theme', theme); } catch (e) {}

          const pressed = theme === 'dark';
          btn.setAttribute('aria-pressed', pressed ? 'true' : 'false');

          const icon = btn.querySelector('.theme-toggle-icon');
          if (icon) {
            icon.innerHTML = pressed
              ? '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M20.25 14.5A7.5 7.5 0 0 1 9.5 3.75a9 9 0 1 0 10.75 10.75Z" fill="currentColor"/></svg>'
              : '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="4.75" fill="currentColor"/><path d="M12 1.75V4.5M12 19.5v2.75M4.5 12H1.75M22.25 12H19.5M6.1 6.1 4.16 4.16M19.84 19.84 17.9 17.9M17.9 6.1l1.94-1.94M4.16 19.84 6.1 17.9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
          }
        }

        // Initialize from current html attribute (already set by the head script)
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        applyTheme(current);

        btn.addEventListener('click', function () {
          const now = document.documentElement.getAttribute('data-theme') || 'light';
          applyTheme(now === 'dark' ? 'light' : 'dark');
        });
      })();
    </script>
</body>
</html>
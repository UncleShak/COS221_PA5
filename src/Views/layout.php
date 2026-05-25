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
      <div class="sg-nav-logo"><span class="logo-dot"></span> TRIPISTRY</div>

      <ul class="sg-nav-links">
<<<<<<< HEAD
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
=======
        <li><a href="/traveller/dashboard">Dashboard</a></li>
        <li><a href="/traveller/details">Packages</a></li>
>>>>>>> 950266797b58517d273de54fda275636a324dc3b
      </ul>

      <div class="sg-nav-right">
        <button
          type="button"
          class="theme-toggle"
          id="themeToggle"
          aria-pressed="false"
          title="Toggle light/dark mode"
        >
          <span class="theme-toggle-icon" aria-hidden="true"></span>
          <span class="theme-toggle-text">Theme</span>
        </button>

        <span class="sg-nav-pill">Traveller View</span>
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
          if (icon) icon.textContent = pressed ? '🌙' : '☀️';
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
<?php
/**
 * src/Views/Landing.php
 * -----------------------------------------------------------------------
 * Tripistry · Public Landing Page
 *
 * HOW THIS FILE IS USED:
 *   This view is loaded by a controller (or directly from public/index.php)
 *   via output buffering. The resulting HTML string is injected into
 *   src/Views/layout.php as $content.
 *
 *   Example in public/index.php (switch block):
 *       case '/':
 *       case '/home':
 *           // ── DATABASE QUERIES ────────────────────────────────────────
 *           // TOP-RATED PACKAGES (highest average rating, limit 5)
 *           // TODO: replace the empty arrays below with real DB queries.
 *           //
 *           //  $topRated = $db->query("
 *           //      SELECT p.id, p.name, p.destination, p.price_per_person,
 *           //             p.image_filename, ROUND(AVG(r.stars), 1) AS avg_rating
 *           //      FROM   packages p
 *           //      LEFT JOIN reviews r ON r.package_id = p.id
 *           //      GROUP  BY p.id
 *           //      ORDER  BY avg_rating DESC
 *           //      LIMIT  5
 *           //  ")->fetchAll(PDO::FETCH_ASSOC);
 *           //
 *           // MOST POPULAR PACKAGES (most bookings, limit 5)
 *           //  $mostPopular = $db->query("
 *           //      SELECT p.id, p.name, p.destination, p.price_per_person,
 *           //             p.image_filename, COUNT(b.id) AS booking_count
 *           //      FROM   packages p
 *           //      LEFT JOIN bookings b ON b.package_id = p.id
 *           //      GROUP  BY p.id
 *           //      ORDER  BY booking_count DESC
 *           //      LIMIT  5
 *           //  ")->fetchAll(PDO::FETCH_ASSOC);
 *           //
 *           // Then pass those arrays to this view before requiring it:
 *           //  $topRated    = $topRated    ?? [];
 *           //  $mostPopular = $mostPopular ?? [];
 *           //
 *           ob_start();
 *           require __DIR__ . '/../src/Views/Landing.php';
 *           $content = ob_get_clean();
 *           require __DIR__ . '/../src/Views/layout.php';
 *           break;
 *
 * -----------------------------------------------------------------------
 * IMPORTANT NOTE FOR TK (layout.php integration):
 *   layout.php already injects the <nav>, ambient orbs, StyleGuide.css link,
 *   and wraps everything in <body>. This file therefore contains ONLY the
 *   page-specific content that goes inside <?= $content ?>.
 *
 *   HOWEVER — the hero section needs a full-bleed background image, which
 *   means the hero must NOT be constrained by any layout.php wrapper padding.
 *   Make sure layout.php's content wrapper has no horizontal padding/margin
 *   on the hero, or apply `margin: 0 -Xrem` to override it.
 *
 *   The nav rendered by layout.php is HIDDEN on this page via CSS
 *   (.landing-page .sg-nav { display: none }) because the landing page
 *   uses its own minimal hero-nav with just Log In / Sign Up.
 * -----------------------------------------------------------------------
 */

// ── PLACEHOLDER DATA (remove once DB queries above are wired up) ─────────
// Each package array needs: id, name, destination, price_per_person,
// image_filename (relative to /images/), avg_rating (optional).
// Replace with real $topRated / $mostPopular from the controller.

$topRated = $topRated ?? [
    ['id'=>1, 'name'=>'Drakensberg Escape',  'destination'=>'South Africa',  'price_per_person'=>18500, 'image_filename'=>'pkg-drakensberg.jpg',  'avg_rating'=>4.9],
    ['id'=>2, 'name'=>'Kruger Safari',       'destination'=>'South Africa',  'price_per_person'=>38000, 'image_filename'=>'pkg-kruger.jpg',       'avg_rating'=>4.8],
    ['id'=>3, 'name'=>'Mozambique Coast',    'destination'=>'Mozambique',    'price_per_person'=>12200, 'image_filename'=>'pkg-mozambique.jpg',   'avg_rating'=>4.8],
    ['id'=>4, 'name'=>'Zanzibar Retreat',    'destination'=>'Tanzania',      'price_per_person'=>8400,  'image_filename'=>'pkg-zanzibar.jpg',     'avg_rating'=>4.7],
    ['id'=>5, 'name'=>'Santorini Escape',    'destination'=>'Greece',        'price_per_person'=>24000, 'image_filename'=>'pkg-santorini.jpg',    'avg_rating'=>4.7],
];

$mostPopular = $mostPopular ?? [
    ['id'=>6,  'name'=>'Bali Adventure',     'destination'=>'Indonesia',     'price_per_person'=>11200, 'image_filename'=>'pkg-bali.jpg',         'booking_count'=>312],
    ['id'=>2,  'name'=>'Kruger Safari',      'destination'=>'South Africa',  'price_per_person'=>38000, 'image_filename'=>'pkg-kruger.jpg',       'booking_count'=>287],
    ['id'=>7,  'name'=>'Lisbon City Break',  'destination'=>'Portugal',      'price_per_person'=>19800, 'image_filename'=>'pkg-lisbon.jpg',       'booking_count'=>241],
    ['id'=>1,  'name'=>'Drakensberg Escape', 'destination'=>'South Africa',  'price_per_person'=>18500, 'image_filename'=>'pkg-drakensberg.jpg',  'booking_count'=>198],
    ['id'=>8,  'name'=>'Maldives Luxury',    'destination'=>'Maldives',      'price_per_person'=>32000, 'image_filename'=>'pkg-maldives.jpg',     'booking_count'=>175],
];

// ── GRADIENT FALLBACKS (shown if package image is missing) ───────────────
$gradients = [
    'linear-gradient(135deg,#e8614a,#c9516e,#8f6fa8)',
    'linear-gradient(160deg,#3d4a7a,#6b7db3,#c3cde6)',
    'linear-gradient(135deg,#d97b3a,#e8614a,#c9516e)',
    'linear-gradient(135deg,#8f6fa8,#3d4a7a)',
    'linear-gradient(135deg,#c9516e,#d97b3a)',
];
?>

<?php /* ═══════════════════════════════════════════════════════════════
   PAGE-SCOPED STYLES
   These styles are landing-page-only and supplement StyleGuide.css.
   They do NOT override any existing class — they extend for this page.
   ═══════════════════════════════════════════════════════════════ */ ?>
<style>
/* ── Hide the global nav on this page (landing uses its own hero-nav) ── */
.sg-nav { display: none !important; }

/* ── Reset layout.php wrapper so hero can be full-bleed ── */
/* If layout.php wraps $content in a div with padding, add:
   .content, .sg-page { padding: 0 !important; } */

/* ════════════════════════════════════════
   HERO
   ════════════════════════════════════════ */
.lp-full-hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Background image — SunsetImage.jpg from /images/ folder */
.lp-full-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url('/images/SunsetImage.jpg');
    background-size: cover;
    background-position: center 40%;
    z-index: 0;
}

/* Gradient overlay so text stays legible over the photo */
.lp-full-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    /* Removed the dark gradient overlay so the hero image displays normally */
    background: none;
    z-index: 1;
}

/* ── Hero nav (Log In / Sign Up only) ── */
.lp-hero-nav {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.6rem 3rem;
    background: rgba(42, 31, 53, 0.18);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}

.lp-hero-brand {
    font-family: var(--font-display);
    font-weight: 700;
    font-style: italic;
    font-size: 1.55rem;
    color: #fff;
    text-decoration: none;
    letter-spacing: -0.01em;
}

.lp-hero-nav-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

/* Log In — ghost/outline style */
.btn-hero-login {
    background: transparent;
    color: #fff;
    border: 1.5px solid rgba(255, 255, 255, 0.55);
    padding: 0.6rem 1.6rem;
    border-radius: var(--r-pill);
    font-family: var(--font-body);
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s var(--ease);
    display: inline-block;
}
.btn-hero-login:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.85);
}

/* Sign Up — solid coral */
.btn-hero-signup {
    background: var(--gradient-warm);
    color: #fff;
    border: none;
    padding: 0.6rem 1.6rem;
    border-radius: var(--r-pill);
    font-family: var(--font-body);
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s var(--ease);
    display: inline-block;
    box-shadow: 0 6px 20px rgba(232, 97, 74, 0.38);
}
.btn-hero-signup:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 10px 28px rgba(232, 97, 74, 0.50);
}

/* ── Hero centre content ── */
.lp-hero-body {
    position: relative;
    z-index: 10;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 4rem 2rem 6rem;
    gap: 2rem;
}

.lp-hero-title {
    font-family: var(--font-display);
    font-size: clamp(4.5rem, 13vw, 9rem);
    font-weight: 700;
    font-style: italic;
    line-height: 0.95;
    color: #fff;
    text-shadow: 0 4px 40px rgba(0, 0, 0, 0.35);
    animation: fadeUp 0.9s var(--ease) both;
}

.lp-hero-tagline {
    font-family: var(--font-body);
    font-size: clamp(1rem, 2vw, 1.2rem);
    color: rgba(255, 255, 255, 0.82);
    max-width: 480px;
    line-height: 1.7;
    animation: fadeUp 1s 0.15s var(--ease) both;
}

/* Inline search bar */
.lp-search-bar {
    display: flex;
    align-items: center;
    gap: 0;
    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border: 1px solid rgba(255, 255, 255, 0.30);
    border-radius: var(--r-pill);
    padding: 0.5rem 0.5rem 0.5rem 1.5rem;
    animation: fadeUp 1s 0.25s var(--ease) both;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
}

.lp-search-bar-field {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0 1.2rem;
    color: rgba(255, 255, 255, 0.88);
    font-size: 0.9rem;
    white-space: nowrap;
}

.lp-search-bar-field:not(:last-of-type) {
    border-right: 1px solid rgba(255, 255, 255, 0.25);
}

/* Scroll cue */
.lp-scroll-cue {
    position: absolute;
    bottom: 2.5rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.55);
    font-size: 0.7rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    animation: bob 2.5s ease-in-out infinite;
    cursor: pointer;
    text-decoration: none;
}
.lp-scroll-line {
    width: 1px;
    height: 34px;
    background: linear-gradient(to bottom, rgba(255,255,255,0.6), transparent);
}

/* ════════════════════════════════════════
   PACKAGES SECTION
   ════════════════════════════════════════ */
.lp-packages-section {
    padding: 6rem 3rem;
    background: var(--bg-main);
    position: relative;
}

.lp-section-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 2.2rem;
}

.lp-section-header-left .section-eyebrow {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--coral);
    margin-bottom: 0.35rem;
}

.lp-section-header-left h2 {
    font-family: var(--font-display);
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    font-weight: 700;
    font-style: italic;
    color: var(--text-main);
    line-height: 1.1;
    margin: 0;
}

/* ── "View All" button ── */
/*
 * TODO: update the href below to the correct packages listing route.
 * Based on the router in public/index.php, the route will be something like:
 *   href="/packages"   (if you add a 'packages' case in the switch)
 * Ask the controller owner what route handles the full packages page.
 */
.btn-view-all {
    background: transparent;
    color: var(--coral);
    border: 1.5px solid rgba(232, 97, 74, 0.35);
    padding: 0.55rem 1.4rem;
    border-radius: var(--r-pill);
    font-family: var(--font-body);
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.btn-view-all:hover {
    background: rgba(232, 97, 74, 0.07);
    border-color: var(--coral);
}

/* ── Carousel wrapper ── */
.carousel-outer {
    position: relative;
}

.carousel-track-wrapper {
    overflow: hidden;
    border-radius: var(--r-xl);
}

.carousel-track {
    display: flex;
    gap: 1.4rem;
    transition: transform 0.45s cubic-bezier(0.22, 1, 0.36, 1);
    will-change: transform;
}

/* Each card takes up 1/3 of the visible area (3 visible at a time) */
.carousel-card {
    flex: 0 0 calc((100% - 2 * 1.4rem) / 3);
    min-width: 0;
    border-radius: var(--r-xl);
    overflow: hidden;
    background: var(--surface-1);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid rgba(42, 31, 53, 0.07);
    box-shadow: var(--glass-shadow);
    cursor: pointer;
    transition: transform 0.35s var(--ease), box-shadow 0.35s var(--ease);
    text-decoration: none;
    display: block;
    color: inherit;
}
.carousel-card, .carousel-card * { color: inherit; }
.carousel-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 45px rgba(42, 31, 53, 0.11);
}

html[data-theme="dark"] .carousel-card {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 18px 45px rgba(0, 0, 0, 0.35);
}

.carousel-card-img {
    height: 175px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.carousel-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Fallback gradient shown when no image is available */
.carousel-card-img .img-fallback {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
}

.carousel-card-badge {
    position: absolute;
    top: 0.9rem;
    right: 0.9rem;
    background: rgba(255, 255, 255, 0.92);
    color: var(--coral);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 0.28rem 0.75rem;
    border-radius: var(--r-pill);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.carousel-card-body {
    padding: 1.3rem 1.4rem 1.4rem;
}

.carousel-card-dest {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--coral);
    margin-bottom: 0.3rem;
}

.carousel-card-name {
    font-family: var(--font-display);
    font-style: italic;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text-main);
    line-height: 1.2;
    margin-bottom: 0.7rem;
}

.carousel-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 0.85rem;
    border-top: 1px solid rgba(42, 31, 53, 0.06);
}

.carousel-card-price {
    font-family: var(--font-display);
    font-style: italic;
    font-weight: 700;
    font-size: 1.15rem;
    color: var(--coral);
}
.carousel-card-price span {
    font-size: 0.78rem;
    font-weight: 400;
    font-style: normal;
    font-family: var(--font-body);
    color: var(--text-muted);
}

.carousel-card-meta {
    font-size: 0.78rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

html[data-theme="dark"] .carousel-card-name,
html[data-theme="dark"] .carousel-card-price,
html[data-theme="dark"] .carousel-card-meta,
html[data-theme="dark"] .carousel-card-price span {
    color: var(--text-main);
}

html[data-theme="dark"] .carousel-card-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.10);
}

/* ── Arrow buttons ── */
.carousel-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 5;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #fff;
    border: 1px solid rgba(42, 31, 53, 0.1);
    box-shadow: 0 4px 16px rgba(42, 31, 53, 0.10);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.1rem;
    color: var(--text-main);
    transition: all 0.2s;
    user-select: none;
}
.carousel-btn:hover {
    background: var(--coral);
    color: #fff;
    border-color: var(--coral);
    box-shadow: 0 6px 20px rgba(232, 97, 74, 0.30);
}
.carousel-btn.prev { left: -22px; }
.carousel-btn.next { right: -22px; }
.carousel-btn:disabled {
    opacity: 0.35;
    cursor: default;
    pointer-events: none;
}

/* ── Dot indicators ── */
.carousel-dots {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
}
.carousel-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: rgba(42, 31, 53, 0.15);
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    padding: 0;
}
.carousel-dot.active {
    width: 22px;
    border-radius: 4px;
    background: var(--coral);
}

/* Spacer between the two carousels */
.carousel-spacer { height: 4rem; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .lp-packages-section { padding: 4rem 1.5rem; }
    .lp-hero-nav { padding: 1.2rem 1.5rem; }
    .carousel-card {
        flex: 0 0 calc((100% - 1.4rem) / 2);
    }
}
@media (max-width: 600px) {
    .lp-hero-title { font-size: clamp(3.5rem, 18vw, 5.5rem); }
    .carousel-card { flex: 0 0 85%; }
    .lp-section-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    .carousel-btn.prev { left: -10px; }
    .carousel-btn.next { right: -10px; }
}
</style>

<?php /* ═══════════════════════════════════════════════════
   SECTION 1 — FULL-BLEED HERO
   ═══════════════════════════════════════════════════ */ ?>

<?php /* ═══════════════════════════════════════════════════
    SECTION 1 — FULL-BLEED HERO
    ═══════════════════════════════════════════════════ */ ?>
<section class="lp-full-hero" id="hero">

    <?php /* ── HERO NAV (Log In / Sign Up only) ── */ ?>
    <nav class="lp-hero-nav">
        <a href="/" class="lp-hero-brand">Tripistry</a>
        <div class="lp-hero-nav-actions">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <?php $dashboardUrl = $_SESSION['user_type'] === 'agency' ? '/agency/dashboard' : '/traveller/dashboard'; ?>
                <a href="<?= $dashboardUrl ?>" class="btn-hero-signup" onclick="window.location.href=this.href; return false;">View Dashboard</a>
                <a href="/logout" class="btn-hero-login">Logout</a>
            <?php else: ?>
                <a href="/login"  class="btn-hero-login">Log In</a>
                <a href="/register" class="btn-hero-signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <?php /* ── HERO BODY ── */ ?>
    <div class="lp-hero-body">

        <h1 class="lp-hero-title">Tripistry</h1>

        <p class="lp-hero-tagline">
            Discover handpicked travel packages from South Africa's best agencies.
            Your next adventure starts here.
        </p>

    </div>

    <?php /* Scroll indicator — jumps to the packages section */ ?>
    <a href="#packages" class="lp-scroll-cue">
        <div class="lp-scroll-line"></div>
        Scroll
    </a>

</section>


<?php /* ═══════════════════════════════════════════════════
   SECTION 2 — PACKAGES CAROUSELS
   ═══════════════════════════════════════════════════ */ ?>
<section class="lp-packages-section" id="packages">

    <?php /* ──────────────────────────────────────────────────
       CAROUSEL 1 — TOP-RATED PACKAGES
    ────────────────────────────────────────────────── */ ?>
    <div class="lp-section-header">
        <div class="lp-section-header-left">
            <div class="section-eyebrow">Highest Rated</div>
            <h2>Top-Rated Packages</h2>
        </div>
        <?php /*
         * TODO: Update href to the correct packages listing route.
         * e.g. href="/packages?sort=rating" or just href="/packages"
         * depending on how the packages controller handles sorting.
         */ ?>
        <a href="/traveller/packages" class="btn-view-all">View All →</a>
    </div>

    <div class="carousel-outer" id="carousel-top-rated">
        <button class="carousel-btn prev" aria-label="Previous" onclick="carouselPrev('top-rated')">‹</button>
        <div class="carousel-track-wrapper">
            <div class="carousel-track" id="track-top-rated">

                <?php
                /*
                 * ── DATA SOURCE ──────────────────────────────────────────
                 * $topRated is populated at the top of this file (placeholder)
                 * OR passed in from the controller (real data). It is an
                 * array of up to 5 associative arrays, each with:
                 *   id, name, destination, price_per_person,
                 *   image_filename, avg_rating
                 * ─────────────────────────────────────────────────────────
                 */
                foreach ($topRated as $i => $pkg):
                    $grad = $gradients[$i % count($gradients)];
                    $imgPath = htmlspecialchars($pkg['image_filename'] ?? '');
                    $rating  = isset($pkg['avg_rating']) ? number_format($pkg['avg_rating'], 1) : '—';
                ?>
                    <?php /*
                     * TODO: update href to the individual package detail route.
                     * e.g. href="/packages/<?= $pkg['id'] ?>"
                     * (add a 'packages/{id}' case in public/index.php's switch)
                     */ ?>
                    <a href="/traveller/details?id=<?= (int)$pkg['id'] ?>" class="carousel-card">
                        <div class="carousel-card-img" style="background:<?= $grad ?>;">
                            <?php if (!empty($pkg['image_filename'])): ?>
                                <img src="<?= $imgPath ?>"
                                     alt="<?= htmlspecialchars($pkg['name']) ?>"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.style.display='none'">
                            <?php endif; ?>
                            <span class="carousel-card-badge">★ <?= $rating ?></span>
                        </div>
                        <div class="carousel-card-body">
                            <div class="carousel-card-dest"><?= htmlspecialchars($pkg['destination']) ?></div>
                            <div class="carousel-card-name"><?= htmlspecialchars($pkg['name']) ?></div>
                            <div class="carousel-card-footer">
                                <div class="carousel-card-price">
                                    R <?= number_format($pkg['price_per_person']) ?>
                                    <span>/ person</span>
                                </div>
                                <div class="carousel-card-meta">★ <?= $rating ?></div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>

            </div>
        </div>
        <button class="carousel-btn next" aria-label="Next" onclick="carouselNext('top-rated')">›</button>

        <div class="carousel-dots" id="dots-top-rated">
            <?php /* Dots: 5 packages, 3 visible → 3 positions (0, 1, 2) */ ?>
            <button class="carousel-dot active" onclick="carouselGo('top-rated', 0)"></button>
            <button class="carousel-dot"        onclick="carouselGo('top-rated', 1)"></button>
            <button class="carousel-dot"        onclick="carouselGo('top-rated', 2)"></button>
        </div>
    </div>


    <div class="carousel-spacer"></div>


    <?php /* ──────────────────────────────────────────────────
       CAROUSEL 2 — MOST POPULAR PACKAGES
    ────────────────────────────────────────────────── */ ?>
    <div class="lp-section-header">
        <div class="lp-section-header-left">
            <div class="section-eyebrow">Most Booked</div>
            <h2>Most Popular Packages</h2>
        </div>
        <?php /*
         * todo: Update href to packages listing sorted by popularity.
         * e.g. href="/packages?sort=popular" or href="/packages"
         */ ?>
        <a href="/traveller/packages" class="btn-view-all">View All →</a>
    </div>

    <div class="carousel-outer" id="carousel-most-popular">
        <button class="carousel-btn prev" aria-label="Previous" onclick="carouselPrev('most-popular')">‹</button>
        <div class="carousel-track-wrapper">
            <div class="carousel-track" id="track-most-popular">

                <?php
                /*
                 * ── DATA SOURCE ──────────────────────────────────────────
                 * $mostPopular is populated at the top of this file (placeholder)
                 * OR passed in from the controller (real data). It is an
                 * array of up to 5 associative arrays, each with:
                 *   id, name, destination, price_per_person,
                 *   image_filename, booking_count
                 * ─────────────────────────────────────────────────────────
                 */
                foreach ($mostPopular as $i => $pkg):
                    $grad  = $gradients[$i % count($gradients)];
                    $imgPath = htmlspecialchars($pkg['image_filename'] ?? '');
                    $count   = isset($pkg['booking_count']) ? number_format($pkg['booking_count']) : '—';
                ?>
                    <a href="/traveller/details?id=<?= (int)$pkg['id'] ?>" class="carousel-card">
                        <div class="carousel-card-img" style="background:<?= $grad ?>;">
                            <?php if (!empty($pkg['image_filename'])): ?>
                                <img src="<?= $imgPath ?>"
                                     alt="<?= htmlspecialchars($pkg['name']) ?>"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.style.display='none'">
                            <?php endif; ?>
                            <span class="carousel-card-badge"><?= $count ?> booked</span>
                        </div>
                        <div class="carousel-card-body">
                            <div class="carousel-card-dest"><?= htmlspecialchars($pkg['destination']) ?></div>
                            <div class="carousel-card-name"><?= htmlspecialchars($pkg['name']) ?></div>
                            <div class="carousel-card-footer">
                                <div class="carousel-card-price">
                                    R <?= number_format($pkg['price_per_person']) ?>
                                    <span>/ person</span>
                                </div>
                                <div class="carousel-card-meta">🔥 <?= $count ?> bookings</div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>

            </div>
        </div>
        <button class="carousel-btn next" aria-label="Next" onclick="carouselNext('most-popular')">›</button>

        <div class="carousel-dots" id="dots-most-popular">
            <button class="carousel-dot active" onclick="carouselGo('most-popular', 0)"></button>
            <button class="carousel-dot"        onclick="carouselGo('most-popular', 1)"></button>
            <button class="carousel-dot"        onclick="carouselGo('most-popular', 2)"></button>
        </div>
    </div>

</section>


<?php /* ═══════════════════════════════════════════════════
   CAROUSEL JAVASCRIPT
   ─ Pure vanilla JS, no dependencies.
   ─ Shows 3 cards at a time; each "step" moves by 1 card.
   ─ Max position = total cards − 3 (so last 3 fill the row).
   ═══════════════════════════════════════════════════ */ ?>
<script>
(function () {
    // State: current index per carousel id
    const state = {};

    /**
     * Calculate how many pixels to translate the track
     * so that `index` is the first visible card.
     */
    function getOffset(trackId, index) {
        const track = document.getElementById('track-' + trackId);
        if (!track) return 0;
        const cards = track.querySelectorAll('.carousel-card');
        if (!cards.length) return 0;

        // Gap between cards (matches CSS gap: 1.4rem)
        const gap = parseFloat(getComputedStyle(track).gap) || 22;
        const cardWidth = cards[0].getBoundingClientRect().width;
        return index * (cardWidth + gap);
    }

    function maxIndex(trackId) {
        const track = document.getElementById('track-' + trackId);
        if (!track) return 0;
        const total = track.querySelectorAll('.carousel-card').length;
        // Show 3 at a time; max start index = total - 3
        return Math.max(0, total - 3);
    }

    function updateCarousel(id) {
        const idx   = state[id] || 0;
        const track = document.getElementById('track-' + id);
        const dots  = document.querySelectorAll('#dots-' + id + ' .carousel-dot');
        const btnPrev = document.querySelector('#carousel-' + id + ' .carousel-btn.prev');
        const btnNext = document.querySelector('#carousel-' + id + ' .carousel-btn.next');

        if (track) {
            track.style.transform = `translateX(-${getOffset(id, idx)}px)`;
        }

        // Update dots (dot 0 = index 0, dot 1 = index 1, dot 2 = index 2+)
        dots.forEach((dot, i) => {
            dot.classList.toggle('active',
                i === Math.min(idx, dots.length - 1)
            );
        });

        // Disable arrows at boundaries
        if (btnPrev) btnPrev.disabled = (idx === 0);
        if (btnNext) btnNext.disabled = (idx >= maxIndex(id));
    }

    window.carouselPrev = function (id) {
        state[id] = Math.max(0, (state[id] || 0) - 1);
        updateCarousel(id);
    };

    window.carouselNext = function (id) {
        state[id] = Math.min(maxIndex(id), (state[id] || 0) + 1);
        updateCarousel(id);
    };

    window.carouselGo = function (id, idx) {
        state[id] = Math.min(maxIndex(id), Math.max(0, idx));
        updateCarousel(id);
    };

    // Initialise both carousels on load
    document.addEventListener('DOMContentLoaded', function () {
        ['top-rated', 'most-popular'].forEach(function (id) {
            state[id] = 0;
            updateCarousel(id);
        });

        // Smooth scroll for the scroll-cue arrow
        const scrollCue = document.querySelector('.lp-scroll-cue');
        if (scrollCue) {
            scrollCue.addEventListener('click', function (e) {
                e.preventDefault();
                document.getElementById('packages')?.scrollIntoView({ behavior: 'smooth' });
            });
        }
    });

    // Re-calculate on resize (card widths change)
    window.addEventListener('resize', function () {
        ['top-rated', 'most-popular'].forEach(function (id) {
            // Clamp state to new maxIndex in case viewport shrank
            state[id] = Math.min(state[id] || 0, maxIndex(id));
            updateCarousel(id);
        });
    });
})();
</script>
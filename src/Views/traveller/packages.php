<?php
$currentFilters = $currentFilters ?? ['search' => '', 'min_price' => '', 'max_price' => '', 'duration' => '', 'min_rating' => ''];
$filterOptions = $filterOptions ?? [
    'destinations' => ['Bali', 'Paris', 'Tokyo', 'Rome'], 
    'durations' => [3, 5, 7, 10, 14], 
    'min_price' => 0, 
    'max_price' => 20000
];
$currentSort = $currentSort ?? 'price_asc';
$totalPackages = $totalPackages ?? 0;
$packages = $packages ?? [];
$totalPages = $totalPages ?? 1;
$currentPage = $currentPage ?? 1;
?>

<main class="container" style="padding: 100px 2rem 4rem; width: 100%; max-width: 1800px; margin: 0 auto;">
    <section class="hero" style="margin-bottom: 3rem; text-align: center; max-width: 800px; margin-left: auto; margin-right: auto;">
        <h1 class="sg-section-title">Discover Your Next Adventure</h1>
        <p class="sg-section-sub" style="margin: 0 auto 2rem;">Explore thousands of travel packages from trusted agencies worldwide.</p>
        
        <form id="search-form" class="hero-search" method="GET" action="/traveller/packages" style="width: 100%; max-width: 700px; margin: 0 auto;">
            <div class="glass-clear" style="display: flex; padding: 0.5rem; border-radius: var(--r-pill); box-shadow: var(--glass-shadow-hi); border: 1px solid var(--glass-border); backdrop-filter: var(--glass-blur); -webkit-backdrop-filter: var(--glass-blur); background: var(--surface-1);">
                <input type="text" name="search" id="search-input" placeholder="Where do you want to go?" value="<?= htmlspecialchars($currentFilters['search'] ?? '') ?>" style="flex: 1; border: none; outline: none; background: transparent; padding: 0.5rem 1.5rem; font-family: var(--font-body); font-size: 1rem; color: var(--text-main);">
                <button type="submit" class="btn-primary" style="padding: 0.8rem 2.5rem;">Search</button>
            </div>
        </form>
    </section>
    
    <div class="dashboard-layout" style="display: grid; grid-template-columns: minmax(0, 1fr) 300px; grid-template-areas: 'main sidebar'; gap: 2rem;">
        <div class="packages-main" style="grid-area: main;">
            <div class="results-header" style="margin-bottom: 1.5rem;">
                <h2>Available Packages</h2>
                <p id="results-count" style="color: var(--text-soft);"><?= $totalPackages ?> packages found</p>
            </div>

            <div id="packages-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
                <?php if (empty($packages)): ?>
                    <div class="glass-clear" style="padding: 2rem; text-align: center; grid-column: 1 / -1; color: var(--text-muted);">
                        <p>No packages match your filters. Try adjusting your search criteria!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($packages as $pkg): ?>
                    <div class="pkg-card glass-frosted" onclick="window.location.href='/traveller/details?id=<?= htmlspecialchars($pkg['id']) ?>'">
                        <div class="pkg-card-img" style="<?= !empty($pkg['image_url']) ? "background-image: url('" . htmlspecialchars($pkg['image_url']) . "'); background-size: cover; background-position: center;" : "" ?>">
                            <?php if (!empty($pkg['image_url'])): ?>
                                <img src="<?= htmlspecialchars($pkg['image_url']) ?>"
                                     alt="<?= htmlspecialchars($pkg['title']) ?>"
                                     loading="lazy"
                                     decoding="async"
                                     style="width: 100%; height: 100%; object-fit: cover; display: block;"
                                     onerror="this.style.display='none'">
                            <?php else: ?>
                                    No image
                            <?php endif; ?>
                                <?php if ($pkg['avg_rating'] > 0): ?>
                                    <span class="pkg-card-badge">Rating <?= number_format($pkg['avg_rating'], 1) ?> (<?= $pkg['review_count'] ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="pkg-card-body">
                            <div class="pkg-card-dest"><?= htmlspecialchars($pkg['destination'] ?? 'Global') ?></div>
                            <div class="pkg-card-name"><?= htmlspecialchars($pkg['title']) ?></div>
                            <div class="pkg-card-meta">
                                    <span>Duration: <?= htmlspecialchars($pkg['duration_days']) ?> days</span>
                                    <span>Agency: <?= htmlspecialchars($pkg['agency_name']) ?></span>
                            </div>
                            <div class="pkg-card-footer">
                                <div class="pkg-card-price">
                                    R <?= number_format($pkg['price'], 2) ?> <span>/ person</span>
                                </div>
                                <?php if (!empty($pkg['favouritable_id']) && isset($_SESSION['user_id'])): ?>

                                <form action="/traveller/favourite" method="POST" onclick="event.stopPropagation();" style="margin: 0;">

                                    <input type="hidden" name="favouritable_id" value="<?= htmlspecialchars($pkg['favouritable_id']) ?>">

                                    <input type="hidden"
                                            name="action" 
                                            value="<?= !empty($pkg['is_favourite']) ? 'remove' : 'add' ?>">

                                    <input type="hidden" name="redirect" value="/traveller/packages">

                                    <button type="submit"
                                        title="<?= !empty($pkg['is_favourite']) ? 'Remove from favourites' : 'Save to favourites' ?>"
                                        style="
                                            background: none;
                                            border: none;
                                            cursor: pointer;
                                            font-size: 1.2rem;
                                            color: <?= !empty($pkg['is_favourite']) ? 'var(--coral)' : 'var(--text-muted)' ?>;
                                            padding: 0.2rem;
                                        ">

                                        <?= !empty($pkg['is_favourite']) ? '♥' : '♡' ?>

                                    </button>

                                </form>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination" style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2.5rem; flex-wrap: wrap;">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php
                        $pageParams = array_merge($_GET, ['page' => $p]);
                        // THE FIX: Links directly to the clean router path
                        $pageUrl = '/traveller/packages?' . http_build_query($pageParams);
                    ?>
                    <a href="<?= htmlspecialchars($pageUrl) ?>" class="<?= $p === $currentPage ? 'btn-primary' : 'btn-secondary' ?>" style="padding: 0.5rem 1rem; min-width: 2.5rem; text-align: center;">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>

        <aside class="filters-sidebar glass-clear" style="grid-area: sidebar; padding: 1.5rem; position: sticky; top: 120px; align-self: start; max-height: calc(100vh - 140px); overflow-y: auto;">
            <h3>Filter Packages</h3>
            <form id="filter-form" method="GET" action="/traveller/packages">
                <div class="filter-group" style="margin-bottom: 1rem;">
                    <label for="destination" class="input-label">Destination</label>
                    <select name="destination" id="destination" class="input-field">
                        <option value="">All Destinations</option>
                        <?php foreach ($filterOptions['destinations'] as $dest): ?>
                            <option value="<?= htmlspecialchars($dest) ?>" <?= (isset($currentFilters['destination']) && $currentFilters['destination'] == $dest) ? 'selected' : '' ?>><?= htmlspecialchars($dest) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group" style="margin-bottom: 1rem;">
                    <label for="duration" class="input-label">Duration (days)</label>
                    <select name="duration" id="duration" class="input-field">
                        <option value="">Any</option>
                        <?php foreach ($filterOptions['durations'] as $d): ?>
                            <option value="<?= htmlspecialchars((string)$d) ?>" <?= (isset($currentFilters['duration']) && (string)$currentFilters['duration'] === (string)$d) ? 'selected' : '' ?>><?= htmlspecialchars($d) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group" style="margin-bottom: 1rem;">
                    <label for="min_rating" class="input-label">Minimum Rating</label>
                    <select name="min_rating" id="min_rating" class="input-field">
                        <option value="">Any</option>
                        <?php for ($r = 5; $r >= 1; $r--): ?>
                            <option value="<?= $r ?>" <?= (isset($currentFilters['min_rating']) && (string)$currentFilters['min_rating'] === (string)$r) ? 'selected' : '' ?>><?= $r ?>+ stars</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="filter-group" style="margin-bottom: 1.5rem;">
                    <label for="sort" class="input-label">Sort By</label>
                    <select name="sort" id="sort" class="input-field">
                        <option value="price_asc" <?= ($currentSort == 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= ($currentSort == 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="rating_desc" <?= ($currentSort == 'rating_desc') ? 'selected' : '' ?>>Rating: High to Low</option>
                        <option value="rating_asc" <?= ($currentSort == 'rating_asc') ? 'selected' : '' ?>>Rating: Low to High</option>
                        <option value="duration_asc" <?= ($currentSort == 'duration_asc') ? 'selected' : '' ?>>Duration: Short to Long</option>
                        <option value="duration_desc" <?= ($currentSort == 'duration_desc') ? 'selected' : '' ?>>Duration: Long to Short</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%; margin-bottom: 0.5rem;">Apply Filters</button>
                <a href="/traveller/packages" class="btn-secondary" style="width: 100%; text-align: center; display: inline-block;">Reset All</a>
            </form>
    </div>
</main>
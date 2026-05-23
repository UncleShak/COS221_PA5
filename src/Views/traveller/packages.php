<?php
$currentFilters = $currentFilters ?? ['search' => '', 'min_price' => '', 'max_price' => ''];
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
            
            <div style="display: flex; background: rgba(255, 255, 255, 0.95); padding: 0.5rem; border-radius: var(--r-pill); box-shadow: var(--glass-shadow-hi); border: 1px solid var(--glass-border);">
                
                <input type="text" 
                       name="search" 
                       id="search-input" 
                       placeholder="Where do you want to go?" 
                       value="<?= htmlspecialchars($currentFilters['search'] ?? '') ?>"
                       style="flex: 1; border: none; outline: none; background: transparent; padding: 0.5rem 1.5rem; font-family: var(--font-body); font-size: 1rem; color: var(--text-main);">
                
                <button type="submit" class="btn-primary" style="padding: 0.8rem 2.5rem;">Search</button>
            </div>
        </form>
    </section>
    
    <div class="dashboard-layout" style="display: grid; grid-template-columns: 300px 1fr; gap: 2rem;">
        <aside class="filters-sidebar glass-clear" style="padding: 1.5rem; position: sticky; top: 120px; align-self: start; max-height: calc(100vh - 140px); overflow-y: auto;">
            <h3>Filter Packages</h3>
            <form id="filter-form" method="GET" action="/traveller/packages">
                
                <div class="filter-group" style="margin-bottom: 1rem;">
                    <label for="destination" class="input-label">Destination</label>
                    <select name="destination" id="destination" class="input-field">
                        <option value="">All Destinations</option>
                        <?php foreach ($filterOptions['destinations'] as $dest): ?>
                            <option value="<?= htmlspecialchars($dest) ?>" 
                                <?= (isset($currentFilters['destination']) && $currentFilters['destination'] == $dest) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dest) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group" style="margin-bottom: 1.5rem;">
                    <label for="sort" class="input-label">Sort By</label>
                    <select name="sort" id="sort" class="input-field">
                        <option value="price_asc" <?= ($currentSort == 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= ($currentSort == 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="rating_desc" <?= ($currentSort == 'rating_desc') ? 'selected' : '' ?>>Rating: High to Low</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%; margin-bottom: 0.5rem;">Apply Filters</button>
                <a href="/traveller/packages" class="btn-secondary" style="width: 100%; text-align: center; display: inline-block;">Reset All</a>
            </form>
        </aside>
        
        <div class="packages-main">
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
                            <?php if (empty($pkg['image_url'])): ?>
                                🌴 <?php endif; ?>
                            
                            <?php if ($pkg['avg_rating'] > 0): ?>
                                <span class="pkg-card-badge">★ <?= number_format($pkg['avg_rating'], 1) ?> (<?= $pkg['review_count'] ?>)</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pkg-card-body">
                            <div class="pkg-card-dest"><?= htmlspecialchars($pkg['destination'] ?? 'Global') ?></div>
                            <div class="pkg-card-name"><?= htmlspecialchars($pkg['title']) ?></div>
                            
                            <div class="pkg-card-meta">
                                <span>🗓️ <?= htmlspecialchars($pkg['duration_days']) ?> Days</span>
                                <span>🏢 <?= htmlspecialchars($pkg['agency_name']) ?></span>
                            </div>
                            
                            <div class="pkg-card-footer">
                                <div class="pkg-card-price">
                                    R <?= number_format($pkg['price'], 2) ?> <span>/ person</span>
                                </div>
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
                        $pageUrl = 'index.php?' . http_build_query($pageParams);
                    ?>
                    <a href="<?= htmlspecialchars($pageUrl) ?>"
                       class="<?= $p === $currentPage ? 'btn-primary' : 'btn-secondary' ?>"
                       style="padding: 0.5rem 1rem; min-width: 2.5rem; text-align: center;">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</main>
<?php
// src/Views/traveller/dashboard.php
// Owner: Aeron - Traveller browsing and filtering interface

$page_title = 'Discover Travel Packages - Tripistry';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../layout.php'; ?>
    
    <main class="container">
        <!-- Hero Section -->
        <section class="hero">
            <h1>Discover Your Next Adventure</h1>
            <p>Explore thousands of travel packages from trusted agencies worldwide</p>
            
            <form id="search-form" class="hero-search" method="GET" action="index.php">
                <input type="hidden" name="route" value="traveller/dashboard">
                <input type="text" 
                       name="search" 
                       id="search-input" 
                       placeholder="Where do you want to go? (e.g., Paris, Tokyo, New York)" 
                       value="<?php echo htmlspecialchars($currentFilters['search'] ?? ''); ?>">
                <button type="submit" class="btn-primary">Search</button>
            </form>
        </section>
        
        <div class="dashboard-layout">
            <!-- Sidebar Filters -->
            <aside class="filters-sidebar">
                <h3>Filter Packages</h3>
                
                <form id="filter-form" method="GET" action="index.php">
                    <input type="hidden" name="route" value="traveller/dashboard">
                    
                    <!-- Destination Filter -->
                    <div class="filter-group">
                        <label for="destination">Destination</label>
                        <select name="destination" id="destination">
                            <option value="">All Destinations</option>
                            <?php foreach ($filterOptions['destinations'] as $dest): ?>
                                <option value="<?php echo htmlspecialchars($dest); ?>" 
                                    <?php echo (isset($currentFilters['destination']) && $currentFilters['destination'] == $dest) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dest); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="filter-group">
                        <label>Price Range (USD)</label>
                        <div class="price-range-inputs">
                            <input type="number" 
                                   name="min_price" 
                                   id="min_price" 
                                   placeholder="Min" 
                                   value="<?php echo $currentFilters['min_price'] ?? ''; ?>"
                                   min="<?php echo $filterOptions['min_price']; ?>"
                                   max="<?php echo $filterOptions['max_price']; ?>">
                            <span>to</span>
                            <input type="number" 
                                   name="max_price" 
                                   id="max_price" 
                                   placeholder="Max" 
                                   value="<?php echo $currentFilters['max_price'] ?? ''; ?>"
                                   min="<?php echo $filterOptions['min_price']; ?>"
                                   max="<?php echo $filterOptions['max_price']; ?>">
                        </div>
                    </div>
                    
                    <!-- Duration Filter -->
                    <div class="filter-group">
                        <label for="duration">Duration (Days)</label>
                        <select name="duration" id="duration">
                            <option value="">Any</option>
                            <?php foreach ($filterOptions['durations'] as $days): ?>
                                <option value="<?php echo $days; ?>"
                                    <?php echo (isset($currentFilters['duration']) && $currentFilters['duration'] == $days) ? 'selected' : ''; ?>>
                                    <?php echo $days; ?> days
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Rating Filter -->
                    <div class="filter-group">
                        <label for="min_rating">Minimum Rating</label>
                        <select name="min_rating" id="min_rating">
                            <option value="">Any rating</option>
                            <option value="4.5" <?php echo (isset($currentFilters['min_rating']) && $currentFilters['min_rating'] == 4.5) ? 'selected' : ''; ?>>4.5+ ★★★★★</option>
                            <option value="4.0" <?php echo (isset($currentFilters['min_rating']) && $currentFilters['min_rating'] == 4.0) ? 'selected' : ''; ?>>4.0+ ★★★★☆</option>
                            <option value="3.5" <?php echo (isset($currentFilters['min_rating']) && $currentFilters['min_rating'] == 3.5) ? 'selected' : ''; ?>>3.5+ ★★★☆☆</option>
                            <option value="3.0" <?php echo (isset($currentFilters['min_rating']) && $currentFilters['min_rating'] == 3.0) ? 'selected' : ''; ?>>3.0+ ★★★☆☆</option>
                        </select>
                    </div>
                    
                    <!-- Sort Options -->
                    <div class="filter-group">
                        <label for="sort">Sort By</label>
                        <select name="sort" id="sort">
                            <option value="price_asc" <?php echo ($currentSort == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_desc" <?php echo ($currentSort == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="rating_desc" <?php echo ($currentSort == 'rating_desc') ? 'selected' : ''; ?>>Rating: High to Low</option>
                            <option value="duration_asc" <?php echo ($currentSort == 'duration_asc') ? 'selected' : ''; ?>>Duration: Shortest</option>
                            <option value="duration_desc" <?php echo ($currentSort == 'duration_desc') ? 'selected' : ''; ?>>Duration: Longest</option>
                            <option value="newest" <?php echo ($currentSort == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-primary btn-block">Apply Filters</button>
                    <a href="index.php?route=traveller/dashboard" class="btn-secondary btn-block">Reset All</a>
                </form>
            </aside>
            
            <!-- Main Content -->
            <div class="packages-main">
                <div class="results-header">
                    <h2>Available Packages</h2>
                    <p id="results-count"><?php echo $totalPackages; ?> packages found</p>
                </div>
                
                <!-- Loading Spinner -->
                <div id="loading-spinner" class="spinner hidden">
                    <div class="loader"></div>
                    <p>Loading packages...</p>
                </div>
                
                <!-- Packages Grid -->
                <div id="packages-grid" class="grid-3col">
                    <?php if (empty($packages)): ?>
                        <div class="no-results">
                            <p>No packages match your filters. Try adjusting your search criteria!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($packages as $package): ?>
                            <div class="package-card" data-package-id="<?php echo $package['id']; ?>">
                                <div class="package-image">
                                    <img src="<?php echo htmlspecialchars($package['image_url'] ?? '/images/placeholder.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($package['title']); ?>"
                                         loading="lazy"
                                         onerror="this.src='/images/placeholder.jpg'">
                                    <?php if ($package['avg_rating'] > 0): ?>
                                        <span class="rating-badge">
                                            ★ <?php echo number_format($package['avg_rating'], 1); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="package-info">
                                    <h3><?php echo htmlspecialchars($package['title']); ?></h3>
                                    <p class="destination">
                                        📍 <?php echo htmlspecialchars($package['destination']); ?>
                                    </p>
                                    <div class="package-meta">
                                        <span class="duration">🗓️ <?php echo $package['duration_days']; ?> days</span>
                                        <span class="agency">🏢 <?php echo htmlspecialchars($package['agency_name']); ?></span>
                                    </div>
                                    <p class="price">
                                        $<?php echo number_format($package['price']); ?>
                                        <span class="per-person">per person</span>
                                    </p>
                                    <?php if ($package['review_count'] > 0): ?>
                                        <p class="reviews">
                                            <?php echo $package['review_count']; ?> review<?php echo $package['review_count'] != 1 ? 's' : ''; ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="card-actions">
                                        <a href="index.php?route=traveller/details&id=<?php echo $package['id']; ?>" 
                                           class="btn-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="?route=traveller/dashboard&page=<?php echo $currentPage - 1; ?>&<?php echo http_build_query(array_filter($currentFilters)); ?>&sort=<?php echo urlencode($currentSort); ?>" class="page-link">← Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= min(5, $totalPages); $i++): ?>
                            <a href="?route=traveller/dashboard&page=<?php echo $i; ?>&<?php echo http_build_query(array_filter($currentFilters)); ?>&sort=<?php echo urlencode($currentSort); ?>" 
                               class="page-link <?php echo ($currentPage == $i) ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?route=traveller/dashboard&page=<?php echo $currentPage + 1; ?>&<?php echo http_build_query(array_filter($currentFilters)); ?>&sort=<?php echo urlencode($currentSort); ?>" class="page-link">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- For You Section (Bonus Task) -->
        <section class="for-you-section">
            <div class="section-header">
                <h2>🌟 For You</h2>
                <p>Personalized recommendations based on your travel history</p>
            </div>
            <div id="for-you-grid" class="grid-4col">
                <div class="loading-placeholder">Loading recommendations...</div>
            </div>
        </section>
    </main>
    
    <script src="/js/validation.js"></script>
    <script>
        // Load For You recommendations
        async function loadForYouRecommendations() {
            const container = document.getElementById('for-you-grid');
            
            try {
                const response = await fetch('index.php?route=traveller/for-you', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const packages = await response.json();
                
                if (packages.length === 0) {
                    container.innerHTML = '<p class="no-results">Sign in to see personalized recommendations!</p>';
                    return;
                }
                
                container.innerHTML = packages.map(pkg => `
                    <div class="package-card-small">
                        <h4>${escapeHtml(pkg.title)}</h4>
                        <p>📍 ${escapeHtml(pkg.destination)}</p>
                        <p class="price">$${Number(pkg.price).toLocaleString()}</p>
                        <a href="index.php?route=traveller/details&id=${pkg.id}" class="btn-small">View</a>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Failed to load recommendations:', error);
                container.innerHTML = '<p class="no-results">Unable to load recommendations.</p>';
            }
        }
        
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        
        // Load on page ready
        document.addEventListener('DOMContentLoaded', loadForYouRecommendations);
    </script>
</body>
</html>
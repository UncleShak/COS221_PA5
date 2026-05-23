<?php

?>

<main class="container" style="padding: 100px 2rem 4rem; width: 100%; max-width: 1200px; margin: 0 auto;">
    <div class="package-detail">
        
        <div class="breadcrumb" style="margin-bottom: 2rem; font-family: var(--font-code); font-size: 0.85rem;">
            <a href="/traveller/packages" style="color: var(--coral); text-decoration: none;">Explore</a> / 
            <span style="color: var(--text-muted);"><?= htmlspecialchars($package['title'] ?? 'Package Details') ?></span>
        </div>
        
        <div class="detail-header" style="margin-bottom: 3rem;">
            <h1 class="sg-section-title"><?= htmlspecialchars($package['title'] ?? 'Unknown Package') ?></h1>
            <div class="agency-info" style="display: flex; gap: 1rem; align-items: center; color: var(--text-soft);">
                <span class="agency-name">by <?= htmlspecialchars($package['agency_name'] ?? 'Tripistry Agency') ?></span>
                <?php if (isset($package['verified']) && $package['verified']): ?>
                    <span class="badge badge-green" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.85rem;">✓ Verified Agency</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="detail-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem;">
            
            <div class="detail-left">
                
                <div class="main-image" style="margin-bottom: 3rem; border-radius: var(--r-xl); overflow: hidden; box-shadow: var(--glass-shadow-hi);">
                    <img src="<?= htmlspecialchars($package['image_url'] ?? '/images/placeholder-large.jpg') ?>" 
                         alt="<?= htmlspecialchars($package['title'] ?? 'Image') ?>"
                         style="width: 100%; height: 400px; object-fit: cover;"
                         onerror="this.src='/images/placeholder-large.jpg'">
                </div>
                
                <div class="itinerary-section glass-clear" style="padding: 2.5rem; margin-bottom: 3rem; border-radius: var(--r-xl);">
                    <h3 style="font-family: var(--font-display); font-style: italic; margin-bottom: 2rem; font-size: 1.8rem;">The Journey</h3>
                    
                    <?php if (empty($package['itinerary'])): ?>
                        <p style="color: var(--text-muted);">Detailed daily itinerary data is currently being curated by the agency.</p>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 2rem;">
                            <?php foreach ($package['itinerary'] as $day): ?>
                                <div style="border-left: 2px solid var(--coral); padding-left: 1.5rem; position: relative;">
                                    <div style="position: absolute; left: -6px; top: 0; width: 10px; height: 10px; border-radius: 50%; background: var(--coral); box-shadow: 0 0 10px var(--coral);"></div>
                                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--coral); letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 0.3rem;">
                                        Day <?= (int)$day['day_number'] ?>
                                    </div>
                                    <h4 style="font-size: 1.15rem; color: var(--text-main); margin-bottom: 0.5rem; font-family: var(--font-display);"><?= htmlspecialchars($day['title']) ?></h4>
                                    <p style="color: var(--text-soft); font-size: 0.95rem; line-height: 1.6; margin: 0;"><?= htmlspecialchars($day['description']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="reviews-section" style="margin-bottom: 3rem;">
                    <h3 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; font-size: 1.8rem;">Traveler Reviews</h3>
                    
                    <?php if (empty($package['reviews'])): ?>
                        <div class="glass-clear" style="padding: 1.5rem; border-radius: var(--r-md); color: var(--text-muted); text-align: center;">No reviews yet. Be the first to review this adventure!</div>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <?php foreach (array_slice($package['reviews'], 0, 4) as $review): ?>
                                <div class="glass-clear" style="padding: 1.5rem; border-radius: var(--r-md); display: flex; gap: 2rem; align-items: center;">
                                    <div style="min-width: 140px; border-right: 1px solid rgba(42,31,53,0.1); padding-right: 1rem;">
                                        <strong style="display: block; color: var(--text-main); font-size: 0.95rem;"><?= htmlspecialchars(explode('@', $review['username'])[0]) ?></strong>
                                        <span style="color: #fbbf24; font-size: 0.9rem;">★ <?= number_format($review['rating'], 1) ?></span>
                                    </div>
                                    <p style="color: var(--text-soft); font-size: 0.95rem; font-style: italic; margin: 0; flex: 1;">"<?= htmlspecialchars($review['comment']) ?>"</p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="detail-right" style="position: sticky; top: 100px; align-self: start;">
                
                <div class="price-card glass-frosted" style="padding: 2.5rem; text-align: center; border-radius: var(--r-xl); margin-bottom: 2rem;">
                    <div class="duration-info" style="color: var(--text-soft); margin-bottom: 1rem; font-weight: 600;">
                        🗓️ <?= htmlspecialchars($package['duration_days'] ?? 0) ?> Days of Exploration
                    </div>
                    
                    <div class="price-large" style="font-family: var(--font-display); font-style: italic; font-size: 2.8rem; color: var(--coral); margin-bottom: 1.5rem; line-height: 1;">
                        R <?= number_format($package['price'] ?? 0) ?>
                        <div style="font-family: var(--font-body); font-size: 0.9rem; font-style: normal; color: var(--text-muted); margin-top: 0.5rem;">per person</div>
                    </div>
                    
                    <div class="rating-summary" style="margin-bottom: 2rem; color: #fbbf24; font-size: 1.2rem; letter-spacing: 2px;">
                        ★ <?= number_format($package['avg_rating'] ?? 0, 1) ?>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'traveller'): ?>
                        <form action="/traveller/checkout" method="GET">
                            <input type="hidden" name="package_id" value="<?= htmlspecialchars($package['id'] ?? '') ?>">
                            <button type="submit" class="btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; border-radius: var(--r-md); cursor: pointer; border: none;">Secure Your Spot →</button>
                        </form>
                    <?php else: ?>
                        <a href="/login" class="btn-secondary" style="display: block; width: 100%; padding: 1rem; text-decoration: none; border-radius: var(--r-md); text-align: center;">Login to Book</a>
                    <?php endif; ?>
                </div>
                
                <div class="inclusions-section glass-clear" style="padding: 2rem; border-radius: var(--r-xl);">
                    <h3 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; font-size: 1.4rem;">What's Included</h3>
                    
                    <?php if (empty($package['inclusions'])): ?>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Standard inclusions apply. Details syncing.</p>
                    <?php else: ?>
                        <?php 
                        // Loop through specific categories in order for the UI
                        $categories = ['hotel', 'flight', 'restaurant', 'attraction'];
                        foreach ($categories as $type): 
                            if (!empty($package['inclusions'][$type])): 
                        ?>
                                <div style="margin-bottom: 1.5rem;">
                                    <h4 style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.5rem;"><?= htmlspecialchars(strtoupper($type)) ?></h4>
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        <?php foreach ($package['inclusions'][$type] as $item): ?>
                                            <li style="font-size: 0.95rem; color: var(--text-main); margin-bottom: 0.3rem;">✓ <?= htmlspecialchars($item['name']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                        <?php 
                            endif; 
                        endforeach; 
                        ?>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
</main>
<?php
// src/Views/traveller/details.php
// Owner: Aeron - Individual package detail page

$page_title = $package['title'] . ' - Tripistry';
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
        <div class="package-detail">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="index.php?route=traveller/dashboard">Home</a> / 
                <span><?php echo htmlspecialchars($package['title']); ?></span>
            </div>
            
            <!-- Package Header -->
            <div class="detail-header">
                <h1><?php echo htmlspecialchars($package['title']); ?></h1>
                <div class="agency-info">
                    <span class="agency-name">by <?php echo htmlspecialchars($package['agency_name']); ?></span>
                    <?php if ($package['agency_rating']): ?>
                        <span class="agency-rating">★ <?php echo number_format($package['agency_rating'], 1); ?></span>
                    <?php endif; ?>
                    <?php if ($package['verified']): ?>
                        <span class="verified-badge">✓ Verified Agency</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="detail-grid">
                <!-- Left Column: Images & Itinerary -->
                <div class="detail-left">
                    <div class="main-image">
                        <img src="<?php echo htmlspecialchars($package['image_url'] ?? '/images/placeholder-large.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($package['title']); ?>"
                             onerror="this.src='/images/placeholder-large.jpg'">
                    </div>
                    
                    <div class="itinerary-section">
                        <h3>Your Itinerary</h3>
                        <?php if (empty($package['itinerary'])): ?>
                            <p>Itinerary details coming soon!</p>
                        <?php else: ?>
                            <?php foreach ($package['itinerary'] as $day): ?>
                                <div class="itinerary-day">
                                    <div class="day-number">Day <?php echo (int)$day['day_number']; ?></div>
                                    <div class="day-content">
                                        <h4><?php echo htmlspecialchars($day['title']); ?></h4>
                                        <p><?php echo htmlspecialchars($day['description']); ?></p>
                                        <?php if (!empty($day['activity_type'])): ?>
                                            <span class="activity-tag"><?php echo htmlspecialchars($day['activity_type']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Right Column: Info & Booking -->
                <div class="detail-right">
                    <div class="price-card">
                        <div class="price-large">
                            $<?php echo number_format($package['price']); ?>
                            <span class="per-person">per person</span>
                        </div>
                        <div class="duration-info">
                            🗓️ <?php echo $package['duration_days']; ?> days / 
                            <?php echo $package['duration_days'] - 1; ?> nights
                        </div>
                        <div class="rating-summary">
                            ★ <?php echo number_format($package['avg_rating'], 1); ?> / 5
                        </div>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'traveller'): ?>
                            <a href="index.php?route=booking/checkout&id=<?php echo $package['id']; ?>" 
                               class="btn-primary btn-large btn-block">Book This Trip →</a>
                        <?php else: ?>
                            <a href="index.php?route=auth/login" class="btn-primary btn-large btn-block">
                                Login to Book
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="inclusions-section">
                        <h3>What's Included</h3>
                        
                        <?php if (!empty($package['inclusions']['flight'])): ?>
                            <div class="inclusion-category">
                                <h4>✈️ Flights</h4>
                                <?php foreach ($package['inclusions']['flight'] as $flight): ?>
                                    <p><strong><?php echo htmlspecialchars($flight['name']); ?></strong></p>
                                    <?php if (!empty($flight['description'])): ?>
                                        <p class="small"><?php echo htmlspecialchars($flight['description']); ?></p>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($package['inclusions']['hotel'])): ?>
                            <div class="inclusion-category">
                                <h4>🏨 Accommodation</h4>
                                <?php foreach ($package['inclusions']['hotel'] as $hotel): ?>
                                    <p><strong><?php echo htmlspecialchars($hotel['name']); ?></strong></p>
                                    <?php if (!empty($hotel['description'])): ?>
                                        <p class="small"><?php echo htmlspecialchars($hotel['description']); ?></p>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($package['inclusions']['attraction'])): ?>
                            <div class="inclusion-category">
                                <h4>🎯 Attractions & Activities</h4>
                                <?php foreach ($package['inclusions']['attraction'] as $attraction): ?>
                                    <p>• <?php echo htmlspecialchars($attraction['name']); ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($package['inclusions'])): ?>
                            <p>Inclusion details coming soon!</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="reviews-section">
                        <h3>Traveler Reviews</h3>
                        <?php if (empty($package['reviews'])): ?>
                            <p class="no-reviews">No reviews yet. Be the first to review this package!</p>
                        <?php else: ?>
                            <?php foreach (array_slice($package['reviews'], 0, 5) as $review): ?>
                                <div class="review-card">
                                    <div class="review-header">
                                        <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                                        <span class="review-rating">★ <?php echo number_format($review['rating'], 1); ?></span>
                                    </div>
                                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                    <small><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'traveller'): ?>
                            <a href="index.php?route=booking/write-review&id=<?php echo $package['id']; ?>" 
                               class="btn-secondary btn-block">Write a Review</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Similar Packages -->
            <?php if (!empty($similarPackages)): ?>
                <div class="similar-section">
                    <h3>You Might Also Like</h3>
                    <div class="grid-4col">
                        <?php foreach ($similarPackages as $similar): ?>
                            <?php if ($similar['id'] != $package['id']): ?>
                                <div class="package-card-small">
                                    <h4><?php echo htmlspecialchars($similar['title']); ?></h4>
                                    <p>📍 <?php echo htmlspecialchars($similar['destination']); ?></p>
                                    <p class="price">$<?php echo number_format($similar['price']); ?></p>
                                    <a href="index.php?route=traveller/details&id=<?php echo $similar['id']; ?>" 
                                       class="btn-small">View Details</a>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="/js/validation.js"></script>
</body>
</html>
<?php $restaurants = $restaurants ?? []; ?>

<main class="container" style="padding: 100px 2rem 4rem; width: 100%; max-width: 1400px; margin: 0 auto;">
    <section style="margin-bottom: 3rem; text-align: center;">
        <h1 class="sg-section-title">Browse Restaurants</h1>
        <p class="sg-section-sub" style="margin: 0 auto;">Discover the best dining experiences at every destination.</p>
    </section>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        <?php if (empty($restaurants)): ?>
            <div class="glass-clear" style="padding: 2rem; text-align: center; grid-column: 1 / -1; color: var(--text-muted);">
                <p>No restaurants found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($restaurants as $r): ?>
            <div class="glass-frosted" style="border-radius: var(--r-xl); overflow: hidden;">
                <div style="height: 180px; background: <?= !empty($r['image_url']) ? "url('" . htmlspecialchars($r['image_url']) . "') center/cover" : "linear-gradient(135deg, var(--amber), var(--coral))" ?>; display: flex; align-items: flex-end; padding: 1rem; justify-content: space-between;">
                    <?php if (empty($r['image_url'])): ?>
                        <span style="font-size: 3rem; margin: auto 0;">🍽️</span>
                    <?php endif; ?>
                    <?php if (!empty($r['price_range'])): ?>
                        <span style="background: rgba(0,0,0,0.5); color: #fff; padding: 0.2rem 0.7rem; border-radius: var(--r-pill); font-size: 0.8rem; text-transform: capitalize; margin-left: auto;">
                            <?= htmlspecialchars(str_replace('-', ' ', $r['price_range'])) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div style="padding: 1.5rem;">
                    <?php if (!empty($r['cuisine_type'])): ?>
                        <div style="font-size: 0.8rem; color: var(--coral); margin-bottom: 0.3rem; text-transform: uppercase; letter-spacing: 0.05em;"><?= htmlspecialchars($r['cuisine_type']) ?></div>
                    <?php endif; ?>
                    <h3 style="margin: 0 0 0.5rem; font-family: var(--font-display); font-style: italic;"><?= htmlspecialchars($r['name']) ?></h3>
                    <?php if (!empty($r['address'])): ?>
                        <p style="margin: 0 0 0.5rem; color: var(--text-muted); font-size: 0.85rem;">📍 <?= htmlspecialchars($r['address']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($r['description'])): ?>
                        <p style="margin: 0 0 1rem; color: var(--text-soft); font-size: 0.9rem; line-height: 1.6;">
                            <?= htmlspecialchars(substr($r['description'], 0, 100)) ?>...
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($r['average_rating'])): ?>
                        <div style="font-size: 0.9rem; color: var(--text-soft);">
                            ★ <?= number_format($r['average_rating'], 1) ?> <span style="color: var(--text-muted);">avg rating</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
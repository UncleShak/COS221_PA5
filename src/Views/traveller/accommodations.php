<?php $accommodations = $accommodations ?? []; ?>

<main class="container" style="padding: 100px 2rem 4rem; width: 100%; max-width: 1400px; margin: 0 auto;">
    <section style="margin-bottom: 3rem; text-align: center;">
        <h1 class="sg-section-title">Browse Accommodations</h1>
        <p class="sg-section-sub" style="margin: 0 auto;">Find the perfect place to stay for your next adventure.</p>
    </section>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        <?php if (empty($accommodations)): ?>
            <div class="glass-clear" style="padding: 2rem; text-align: center; grid-column: 1 / -1; color: var(--text-muted);">
                <p>No accommodations found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($accommodations as $a): ?>
            <div class="glass-frosted" style="border-radius: var(--r-xl); overflow: hidden;">
                <div style="height: 180px; background: <?= !empty($a['image_url']) ? "url('" . htmlspecialchars($a['image_url']) . "') center/cover" : "linear-gradient(135deg, var(--blush), var(--amber))" ?>; display: flex; align-items: flex-start; padding: 1rem; justify-content: space-between;">
                    <?php if (empty($a['image_url'])): ?>
                        <span style="font-size: 1rem; margin: auto; color: var(--text-muted);">No image</span>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>
                    <?php if (!empty($a['star_rating'])): ?>
                        <span style="background: rgba(0,0,0,0.5); color: #ffd700; padding: 0.2rem 0.6rem; border-radius: var(--r-pill); font-size: 0.85rem;">
                            Rating <?= htmlspecialchars((string)$a['star_rating']) ?>/5
                        </span>
                    <?php endif; ?>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="font-size: 0.8rem; text-transform: capitalize; color: var(--coral); margin-bottom: 0.3rem;"><?= htmlspecialchars($a['type']) ?></div>
                    <h3 style="margin: 0 0 0.5rem; font-family: var(--font-display); font-style: italic;"><?= htmlspecialchars($a['name']) ?></h3>
                    <?php if (!empty($a['address'])): ?>
                        <p style="margin: 0 0 1rem; color: var(--text-muted); font-size: 0.85rem;">Address: <?= htmlspecialchars($a['address']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($a['description'])): ?>
                        <p style="margin: 0 0 1rem; color: var(--text-soft); font-size: 0.9rem; line-height: 1.6;">
                            <?= htmlspecialchars(substr($a['description'], 0, 100)) ?>...
                        </p>
                    <?php endif; ?>
                    <div style="font-size: 1.1rem; font-weight: 700; color: var(--coral);">
                        R <?= number_format($a['price_per_night'], 2) ?> <span style="font-size: 0.8rem; font-weight: 400; color: var(--text-muted);">/ night</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
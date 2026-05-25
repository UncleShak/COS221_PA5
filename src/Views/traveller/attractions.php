<?php $attractions = $attractions ?? []; ?>

<main class="container" style="padding: 100px 2rem 4rem; width: 100%; max-width: 1400px; margin: 0 auto;">
    <section style="margin-bottom: 3rem; text-align: center;">
        <h1 class="sg-section-title">Tourist Attractions</h1>
        <p class="sg-section-sub" style="margin: 0 auto;">Discover must-see attractions at every destination.</p>
    </section>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        <?php if (empty($attractions)): ?>
            <div class="glass-clear" style="padding: 2rem; text-align: center; grid-column: 1 / -1; color: var(--text-muted);">
                <p>No attractions found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($attractions as $a): ?>
            <div class="glass-frosted" style="border-radius: var(--r-xl); overflow: hidden;">
                <div style="height: 180px; background: <?= !empty($a['image_url']) ? "url('" . htmlspecialchars($a['image_url']) . "') center/cover" : "linear-gradient(135deg, var(--ocean), var(--blush))" ?>; display: flex; align-items: flex-end; padding: 1rem;">
                    <?php if (empty($a['image_url'])): ?>
                        <span style="font-size: 1rem; margin: auto 0; color: var(--text-muted);">No image</span>
                    <?php endif; ?>
                    <span style="background: rgba(0,0,0,0.5); color: #fff; padding: 0.2rem 0.7rem; border-radius: var(--r-pill); font-size: 0.8rem; text-transform: capitalize; margin-left: auto;">
                        <?= htmlspecialchars(str_replace('_', ' ', $a['category'])) ?>
                    </span>
                </div>
                <div style="padding: 1.5rem;">
                    <h3 style="margin: 0 0 0.5rem; font-family: var(--font-display); font-style: italic;"><?= htmlspecialchars($a['name']) ?></h3>
                    <?php if (!empty($a['opening_hours'])): ?>
                        <p style="margin: 0 0 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Hours: <?= htmlspecialchars($a['opening_hours']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($a['description'])): ?>
                        <p style="margin: 0 0 1rem; color: var(--text-soft); font-size: 0.9rem; line-height: 1.6;">
                            <?= htmlspecialchars(substr($a['description'], 0, 100)) ?>...
                        </p>
                    <?php endif; ?>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 1rem; font-weight: 700; color: var(--coral);">
                            <?= $a['entry_fee'] > 0 ? 'R ' . number_format($a['entry_fee'], 2) . ' entry' : 'Free Entry' ?>
                        </div>
                        <?php if (!empty($a['website_url'])): ?>
                            <a href="<?= htmlspecialchars($a['website_url']) ?>" target="_blank" style="font-size: 0.8rem; color: var(--ocean); text-decoration: none;">Visit site →</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
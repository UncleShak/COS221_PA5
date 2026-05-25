<?php $destinations = $destinations ?? []; ?>

<main class="container" style="padding: 100px 2rem 4rem; width: 100%; max-width: 1400px; margin: 0 auto;">
    <section style="margin-bottom: 3rem; text-align: center;">
        <h1 class="sg-section-title">Browse Destinations</h1>
        <p class="sg-section-sub" style="margin: 0 auto;">Explore all the beautiful destinations available on Tripistry.</p>
    </section>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        <?php if (empty($destinations)): ?>
            <div class="glass-clear" style="padding: 2rem; text-align: center; grid-column: 1 / -1; color: var(--text-muted);">
                <p>No destinations found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($destinations as $d): ?>
            <div class="glass-frosted" style="border-radius: var(--r-xl); overflow: hidden;">
                <div style="height: 180px; background: <?= !empty($d['image_url']) ? "url('" . htmlspecialchars($d['image_url']) . "') center/cover" : "linear-gradient(135deg, var(--ocean), var(--ice))" ?>; display: flex; align-items: flex-end;">
                    <?php if (empty($d['image_url'])): ?>
                        <span style="font-size: 1rem; margin: auto; color: var(--text-muted);">No image</span>
                    <?php endif; ?>
                </div>
                <div style="padding: 1.5rem;">
                    <h3 style="margin: 0 0 0.5rem; font-family: var(--font-display); font-style: italic;"><?= htmlspecialchars($d['name']) ?></h3>
                    <?php if (!empty($d['best_season'])): ?>
                        <span style="font-size: 0.8rem; background: rgba(232,97,74,0.12); color: var(--coral); padding: 0.2rem 0.7rem; border-radius: var(--r-pill); text-transform: capitalize;">
                            Best: <?= htmlspecialchars($d['best_season']) ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($d['average_temperature_celsius'])): ?>
                        <span style="font-size: 0.8rem; background: rgba(100,180,255,0.12); color: var(--ocean); padding: 0.2rem 0.7rem; border-radius: var(--r-pill); margin-left: 0.4rem;">
                            Avg temperature: <?= htmlspecialchars($d['average_temperature_celsius']) ?>°C
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($d['description'])): ?>
                        <p style="margin: 1rem 0 0; color: var(--text-soft); font-size: 0.9rem; line-height: 1.6;">
                            <?= htmlspecialchars(substr($d['description'], 0, 120)) ?>...
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
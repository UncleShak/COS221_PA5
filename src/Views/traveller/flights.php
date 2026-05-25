<?php $flights = $flights ?? []; ?>

<main class="container" style="padding: 100px 2rem 4rem; width: 100%; max-width: 1400px; margin: 0 auto;">
    <section style="margin-bottom: 3rem; text-align: center;">
        <h1 class="sg-section-title">Browse Flights</h1>
        <p class="sg-section-sub" style="margin: 0 auto;">View all available flights across our travel packages.</p>
    </section>

    <div style="display: flex; flex-direction: column; gap: 1.2rem;">
        <?php if (empty($flights)): ?>
            <div class="glass-clear" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                <p>No flights found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($flights as $f): ?>
            <div class="glass-frosted" style="padding: 1.5rem 2rem; border-radius: var(--r-xl); display: grid; grid-template-columns: 1fr auto auto auto; align-items: center; gap: 2rem;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;"><?= htmlspecialchars($f['airline_name']) ?> · <?= htmlspecialchars($f['flight_number']) ?></div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 1.4rem; font-weight: 700; font-family: var(--font-display);"><?= htmlspecialchars($f['departure_airport']) ?></div>
                            <div style="font-size: 0.85rem; color: var(--text-soft);"><?= htmlspecialchars($f['departure_time'] ?? '--') ?></div>
                        </div>
                        <div style="flex: 1; text-align: center; color: var(--text-muted); font-size: 0.8rem;">
                            Flight time: <?= $f['duration_minutes'] ? floor($f['duration_minutes']/60).'h '.($f['duration_minutes']%60).'m' : '' ?>
                            <div style="border-top: 1px dashed var(--glass-border); margin: 0.3rem 0;"></div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.4rem; font-weight: 700; font-family: var(--font-display);"><?= htmlspecialchars($f['arrival_airport']) ?></div>
                            <div style="font-size: 0.85rem; color: var(--text-soft);"><?= htmlspecialchars($f['arrival_time'] ?? '--') ?></div>
                        </div>
                    </div>
                </div>
                <span style="font-size: 0.8rem; background: rgba(100,180,255,0.12); color: var(--ocean); padding: 0.3rem 0.8rem; border-radius: var(--r-pill); text-transform: capitalize;">
                    <?= htmlspecialchars($f['flight_class']) ?>
                </span>
                <div style="text-align: right;">
                    <div style="font-size: 1.2rem; font-weight: 700; color: var(--coral);">R <?= number_format($f['base_price'], 2) ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">per person</div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
<?php
function hsc(mixed $val): string
{
    return htmlspecialchars((string) $val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>

<div class="sg-page" style="min-height: 100vh; padding: 120px 2rem 4rem; background: transparent;">
  <div style="max-width: 1200px; margin: 0 auto; width: 100%;">

    <?php if ($flash): ?>
      <div class="toast <?= $flash['type'] === 'success' ? 't-success' : '' ?>" style="margin-bottom: 1.5rem; max-width: 100%;">
        <div>
          <div class="toast-title"><?= $flash['type'] === 'success' ? '✓ Success' : '⚠ Error' ?></div>
          <div class="toast-msg"><?= hsc($flash['message']) ?></div>
        </div>
      </div>
    <?php endif; ?>

    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:3rem; flex-wrap:wrap; gap:1.5rem;">
      <div>
        <p class="sg-section-label" style="margin-bottom:0.5rem;">Agency Portal</p>
        <h2 class="sg-section-title" style="margin:0; font-size:clamp(2rem,4vw,3rem); line-height:1.1;">
          Welcome back...
        </h2>
      </div>
      <a href="/agency/package/create" class="btn-primary" style="border-radius:var(--r-pill); padding:0.8rem 2rem; text-decoration:none;">+ New Package</a>
    </div>

    <div class="stat-row" style="display:grid; grid-template-columns:repeat(4,1fr); gap:1.5rem; margin-bottom:3rem;">
      <div class="glass-heavy" style="border-radius:var(--r-xl); padding:1.8rem;">
        <div style="font-size:0.8rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:.6rem;">Total Packages</div>
        <div style="font-size:2.4rem; font-weight:700; color:var(--ocean); line-height:1;"><?= (int) ($stats['total_packages'] ?? 0) ?></div>
        <div style="font-size:0.82rem; color:var(--text-muted); margin-top:.4rem;"><?= (int) ($stats['published'] ?? 0) ?> active · <?= (int) ($stats['drafts'] ?? 0) ?> drafts</div>
      </div>
      <div class="glass-heavy" style="border-radius:var(--r-xl); padding:1.8rem;">
        <div style="font-size:0.8rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:.6rem;">Bookings</div>
        <div style="font-size:2.4rem; font-weight:700; color:var(--teal); line-height:1;"><?= (int) ($stats['total_bookings'] ?? 0) ?></div>
        <div style="font-size:0.82rem; color:var(--text-muted); margin-top:.4rem;">confirmed &amp; pending</div>
      </div>
      <div class="glass-heavy" style="border-radius:var(--r-xl); padding:1.8rem;">
        <div style="font-size:0.8rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:.6rem;">Revenue</div>
        <div style="font-size:2.4rem; font-weight:700; color:var(--deep-blue); line-height:1;">R <?= number_format((float) ($stats['total_revenue'] ?? 0), 0) ?></div>
        <div style="font-size:0.82rem; color:var(--text-muted); margin-top:.4rem;">all-time gross</div>
      </div>
      <div class="glass-heavy" style="border-radius:var(--r-xl); padding:1.8rem;">
        <div style="font-size:0.8rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:.6rem;">Avg Rating</div>
        <div style="font-size:2.4rem; font-weight:700; color:var(--warning); line-height:1;"><?= $stats['avg_rating'] ? hsc($stats['avg_rating']) : '–' ?></div>
        <div style="font-size:0.82rem; color:var(--text-muted); margin-top:.4rem;">from <?= (int) ($stats['review_count'] ?? 0) ?> review<?= $stats['review_count'] != 1 ? 's' : '' ?></div>
      </div>
    </div>

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:2rem; align-items:start;">
      <div class="glass-clear" style="border-radius:var(--r-xl); overflow:hidden;">
        <div style="padding:1.5rem 1.8rem; border-bottom:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:center;">
          <h3 style="margin:0; font-size:1.05rem; font-weight:600;">Your Packages</h3>
          <span style="font-size:0.82rem; color:var(--text-muted);"><?= count($packages) ?> total</span>
        </div>

        <?php if (empty($packages)): ?>
          <div style="padding:3rem; text-align:center; color:var(--text-muted);">
            No packages yet. <a href="/agency/package/create" style="color:var(--ocean); font-weight:600;">Create your first →</a>
          </div>
        <?php else: ?>
          <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
              <thead>
                <tr style="background:rgba(255,255,255,0.3);">
                  <th style="padding:.9rem 1.2rem; text-align:left; font-size:.8rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em;">Title</th>
                  <th style="padding:.9rem 1.2rem; text-align:left; font-size:.8rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em;">Price</th>
                  <th style="padding:.9rem 1.2rem; text-align:left; font-size:.8rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em;">Status</th>
                  <th style="padding:.9rem 1.2rem; text-align:left; font-size:.8rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em;">Bookings</th>
                  <th style="padding:.9rem 1.2rem; text-align:center; font-size:.8rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($packages as $i => $pkg): ?>
                  <?php
                    $statusColor = match ($pkg['status']) { 'active' => 'var(--success)', 'draft' => 'var(--warning)', 'archived' => 'var(--danger)', default => 'var(--text-muted)' };
                    $statusLabel = match ($pkg['status']) { 'active' => 'Active', 'draft' => 'Draft', 'archived' => 'Archived', default => ucfirst(hsc($pkg['status'])) };
                  ?>
                  <tr style="border-top:1px solid rgba(255,255,255,0.3); <?= $i % 2 ? 'background:rgba(255,255,255,0.08)' : '' ?>">
                    <td style="padding:.9rem 1.2rem;">
                      <div style="font-weight:600; font-size:.95rem;"><?= hsc($pkg['title']) ?></div>
                    </td>
                    <td style="padding:.9rem 1.2rem; white-space:nowrap;">R <?= number_format((float) $pkg['base_price'], 0) ?></td>
                    <td style="padding:.9rem 1.2rem;">
                      <span style="display:inline-flex; align-items:center; gap:.4rem; font-size:.82rem; font-weight:600; color:<?= $statusColor ?>;">
                        <span style="width:7px; height:7px; border-radius:50%; background:<?= $statusColor ?>; display:inline-block;"></span><?= $statusLabel ?>
                      </span>
                    </td>
                    <td style="padding:.9rem 1.2rem; font-size:.95rem;"><?= (int) $pkg['booking_count'] ?></td>
                    
                    <td style="padding:.9rem 1.2rem; text-align:center; white-space:nowrap;">
                      <a href="/agency/package/edit?id=<?= (int) $pkg['package_id'] ?>" class="btn-secondary" style="padding:.35rem .9rem; border-radius:var(--r-pill); font-size:.82rem; text-decoration:none; margin-right:.4rem;">Edit</a>
                      
                      <?php if ($pkg['status'] !== 'archived'): ?>
                        <form method="POST" action="/agency/package/archive" style="display:inline;" onsubmit="return confirm('Archive this package?')">
                          <input type="hidden" name="package_id" value="<?= (int) $pkg['package_id'] ?>">
                          <button type="submit" style="background:none; border:1px solid var(--danger); color:var(--danger); border-radius:var(--r-pill); padding:.35rem .9rem; font-size:.82rem; cursor:pointer;">Archive</button>
                        </form>
                      <?php else: ?>
                        <form method="POST" action="/agency/package/activate" style="display:inline;" onsubmit="return confirm('Restore this package to active status?')">
                          <input type="hidden" name="package_id" value="<?= (int) $pkg['package_id'] ?>">
                          <button type="submit" style="background:none; border:1px solid var(--success); color:var(--success); border-radius:var(--r-pill); padding:.35rem .9rem; font-size:.82rem; cursor:pointer;">Make Active</button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <div class="glass-clear" style="border-radius:var(--r-xl); overflow:hidden;">
        <div style="padding:1.5rem 1.8rem; border-bottom:1px solid var(--glass-border);">
          <h3 style="margin:0; font-size:1.05rem; font-weight:600;">Recent Bookings</h3>
        </div>
        <?php if (empty($recentBookings)): ?>
          <div style="padding:2rem; text-align:center; color:var(--text-muted); font-size:.9rem;">No bookings yet.</div>
        <?php else: ?>
          <div style="padding:.5rem 0;">
            <?php foreach ($recentBookings as $booking): ?>
              <?php $bStatusColor = match ($booking['status']) { 'confirmed' => 'var(--success)', 'pending' => 'var(--warning)', 'cancelled' => 'var(--danger)', default => 'var(--text-muted)' }; ?>
              <div style="padding:1rem 1.5rem; border-bottom:1px solid rgba(255,255,255,0.2);">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:.5rem;">
                  <div>
                    <div style="font-size:.88rem; font-weight:600;"><?= hsc($booking['traveller_name']) ?></div>
                    <div style="font-size:.78rem; color:var(--text-muted); margin-top:.15rem;"><?= hsc($booking['package_title']) ?></div>
                  </div>
                  <span style="font-size:.75rem; font-weight:600; color:<?= $bStatusColor ?>; white-space:nowrap;"><?= ucfirst(hsc($booking['status'])) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>
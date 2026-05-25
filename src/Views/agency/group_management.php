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
          Group Trip
          <em style="font-style:italic; color:var(--ocean);">Management</em>
        </h2>
        <p style="margin-top:0.75rem; color:var(--text-soft); max-width:56rem;">
          Review every group trip created for your packages and remove confirmed participants when needed.
        </p>
      </div>
      <a href="/agency/dashboard" class="btn-secondary" style="border-radius:var(--r-pill); padding:0.8rem 2rem; text-decoration:none;">Back to Dashboard</a>
    </div>

    <?php if (empty($groupTrips)): ?>
      <div class="glass-clear" style="border-radius:var(--r-xl); padding:3rem; text-align:center; color:var(--text-muted);">
        No group trips have been created for your packages yet.
      </div>
    <?php else: ?>
      <div style="display:grid; gap:1rem;">
        <?php foreach ($groupTrips as $groupTrip): ?>
          <?php
            $groupStatusColor = match ($groupTrip['status']) { 'open' => 'var(--success)', 'full' => 'var(--warning)', 'departed' => 'var(--deep-blue)', 'cancelled' => 'var(--danger)', default => 'var(--text-muted)' };
            $participantCount = (int) ($groupTrip['current_participants'] ?? 0);
            $maxParticipants = (int) ($groupTrip['max_participants'] ?? 0);
          ?>
          <div class="glass-clear" style="border-radius:var(--r-xl); overflow:hidden;">
            <div style="padding:1.5rem 1.8rem; border-bottom:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
              <div>
                <p class="sg-section-label" style="margin-bottom:0.35rem;">Package</p>
                <h3 style="margin:0; font-size:1.25rem; font-weight:700; color:var(--text-main);"><?= hsc($groupTrip['package_title']) ?></h3>
                <p style="margin:0.4rem 0 0; color:var(--text-muted); font-size:0.9rem;">
                  Group #<?= str_pad((string) $groupTrip['group_trip_id'], 4, '0', STR_PAD_LEFT) ?> · <?= hsc($groupTrip['departure_date']) ?> to <?= hsc($groupTrip['return_date']) ?>
                </p>
              </div>
              <span style="display:inline-flex; align-items:center; gap:.4rem; font-size:.8rem; font-weight:600; color:<?= $groupStatusColor ?>; white-space:nowrap;">
                <span style="width:7px; height:7px; border-radius:50%; background:<?= $groupStatusColor ?>; display:inline-block;"></span><?= ucfirst(hsc($groupTrip['status'])) ?>
              </span>
            </div>

            <div style="padding:1.2rem 1.8rem; display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:1rem;">
              <div style="padding:0.9rem 1rem; background:rgba(255,255,255,0.08); border-radius:var(--r-md);">
                <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); margin-bottom:.25rem;">Participants</div>
                <div style="font-size:1.2rem; font-weight:700; color:var(--text-main);"><?= $participantCount ?> / <?= $maxParticipants ?></div>
              </div>
              <div style="padding:0.9rem 1rem; background:rgba(255,255,255,0.08); border-radius:var(--r-md);">
                <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); margin-bottom:.25rem;">Minimum</div>
                <div style="font-size:1.2rem; font-weight:700; color:var(--text-main);"><?= (int) $groupTrip['min_participants'] ?></div>
              </div>
              <div style="padding:0.9rem 1rem; background:rgba(255,255,255,0.08); border-radius:var(--r-md);">
                <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); margin-bottom:.25rem;">Meeting Point</div>
                <div style="font-size:0.95rem; font-weight:600; color:var(--text-main);"><?= hsc($groupTrip['meeting_point'] ?? 'TBA') ?></div>
              </div>
              <div style="padding:0.9rem 1rem; background:rgba(255,255,255,0.08); border-radius:var(--r-md);">
                <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); margin-bottom:.25rem;">Base Price</div>
                <div style="font-size:1.2rem; font-weight:700; color:var(--text-main);">R <?= number_format((float) $groupTrip['base_price'], 0) ?></div>
              </div>
            </div>

            <div style="padding:0 1.8rem 1.8rem;">
              <div style="font-size:0.76rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:.75rem;">Confirmed Participants</div>
              <?php if (empty($groupTrip['participants'])): ?>
                <div style="padding:0.9rem 1rem; border:1px dashed var(--glass-border); border-radius:var(--r-md); color:var(--text-muted); font-size:0.9rem;">
                  No confirmed participants yet.
                </div>
              <?php else: ?>
                <div style="display:grid; gap:0.7rem;">
                  <?php foreach ($groupTrip['participants'] as $participant): ?>
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; padding:0.9rem 1rem; border:1px solid rgba(255,255,255,0.18); border-radius:var(--r-md); background:rgba(255,255,255,0.05);">
                      <div>
                        <div style="font-weight:600; color:var(--text-main);"><?= hsc($participant['first_name'] . ' ' . $participant['last_name']) ?></div>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.2rem;">Joined <?= hsc($participant['joined_at']) ?></div>
                      </div>
                      <form method="POST" action="/agency/group/remove-participant" onsubmit="return confirm('Remove this traveller from the group?')">
                        <input type="hidden" name="group_trip_id" value="<?= (int) $groupTrip['group_trip_id'] ?>">
                        <input type="hidden" name="traveller_id" value="<?= (int) $participant['traveller_id'] ?>">
                        <button type="submit" class="btn-secondary" style="padding:.4rem .85rem; border-radius:var(--r-pill); font-size:.8rem;">Remove</button>
                      </form>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

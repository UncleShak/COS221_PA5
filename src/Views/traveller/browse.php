<?php
// src/Views/traveller/browse.php
// Five read-only entity browsers in a single tabbed page.
// Variables available: $destinations, $flights, $accommodations,
//                      $attractions, $restaurants, $activeTab

function bsc(mixed $val): string {
    return htmlspecialchars((string)$val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function starRating(int|null $rating): string {
    if (!$rating) return '—';
    return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
}
?>

<div class="sg-page" style="min-height:100vh; padding:120px 2rem 4rem; background:transparent;">
<div style="max-width:1200px; margin:0 auto; width:100%;">

  <!-- ── Page Header ──────────────────────────────────────── -->
  <div style="margin-bottom:2.5rem;">
    <p class="sg-section-label" style="margin-bottom:0.5rem;">Explore Tripistry</p>
    <h2 class="sg-section-title" style="margin:0; font-size:clamp(2rem,4vw,3rem); line-height:1.1;">
      Browse the World.
    </h2>
  </div>

  <!-- ── Tab Bar ──────────────────────────────────────────── -->
  <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:2.5rem; border-bottom:1px solid var(--glass-border); padding-bottom:0;">

    <?php
    $tabs = [
        'destinations'   => ['label' => 'Destinations',   'icon' => '🗺'],
        'flights'        => ['label' => 'Flights',         'icon' => '✈'],
        'accommodations' => ['label' => 'Accommodations',  'icon' => '🏨'],
        'attractions'    => ['label' => 'Attractions',     'icon' => '🎡'],
        'restaurants'    => ['label' => 'Restaurants',     'icon' => '🍽'],
    ];
    foreach ($tabs as $key => $tab): ?>
      <a href="index.php?route=traveller/browse&tab=<?= $key ?>"
         style="
           display:inline-flex; align-items:center; gap:0.4rem;
           padding:0.6rem 1.2rem; border-radius:var(--r-pill) var(--r-pill) 0 0;
           font-size:0.88rem; font-weight:600; text-decoration:none;
           transition:all 0.2s;
           <?= $activeTab === $key
               ? 'background:var(--ocean); color:#fff; border:1px solid var(--ocean); border-bottom:1px solid transparent;'
               : 'background:rgba(255,255,255,0.15); color:var(--text-soft); border:1px solid var(--glass-border);' ?>
         ">
        <?= $tab['icon'] ?> <?= $tab['label'] ?>
      </a>
    <?php endforeach; ?>

  </div>

  <!-- ══════════════════════════════════════════════════════
       TAB: DESTINATIONS
  ═══════════════════════════════════════════════════════ -->
  <?php if ($activeTab === 'destinations'): ?>

  <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:1.5rem;">
    <?php if (empty($destinations)): ?>
      <p style="color:var(--text-muted);">No destinations found.</p>
    <?php else: foreach ($destinations as $dest): ?>

    <div class="glass-heavy" style="border-radius:var(--r-xl); overflow:hidden;">
      <?php if ($dest['image_url']): ?>
        <img src="<?= bsc($dest['image_url']) ?>"
             alt="<?= bsc($dest['name']) ?>"
             style="width:100%; height:200px; object-fit:cover; display:block;"
             onerror="this.style.display='none'">
      <?php else: ?>
        <div style="width:100%; height:200px; background:linear-gradient(135deg, var(--ocean), var(--teal)); display:flex; align-items:center; justify-content:center; font-size:3rem;">🗺</div>
      <?php endif; ?>

      <div style="padding:1.4rem;">
        <div style="font-size:0.75rem; font-weight:600; color:var(--ocean); text-transform:uppercase; letter-spacing:.06em; margin-bottom:0.3rem;">
          <?= bsc($dest['city']) ?>, <?= bsc($dest['country']) ?>
        </div>
        <div style="font-size:1.1rem; font-weight:700; margin-bottom:0.6rem; color:var(--text-main);">
          <?= bsc($dest['name']) ?>
        </div>
        <?php if ($dest['description']): ?>
          <p style="font-size:0.85rem; color:var(--text-soft); margin:0 0 0.8rem; line-height:1.5;">
            <?= bsc(mb_strimwidth($dest['description'], 0, 120, '…')) ?>
          </p>
        <?php endif; ?>
        <div style="display:flex; gap:1rem; font-size:0.8rem; color:var(--text-muted);">
          <?php if ($dest['best_season']): ?>
            <span>🌤 Best: <?= bsc(ucfirst($dest['best_season'])) ?></span>
          <?php endif; ?>
          <?php if ($dest['average_temperature_celsius']): ?>
            <span>🌡 <?= bsc($dest['average_temperature_celsius']) ?>°C</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php endforeach; endif; ?>
  </div>

  <!-- ══════════════════════════════════════════════════════
       TAB: FLIGHTS
  ═══════════════════════════════════════════════════════ -->
  <?php elseif ($activeTab === 'flights'): ?>

  <div class="glass-clear" style="border-radius:var(--r-xl); overflow:hidden;">
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr style="background:rgba(255,255,255,0.25);">
          <?php foreach (['Airline', 'Flight No.', 'Route', 'Departure', 'Arrival', 'Duration', 'Class', 'Base Price'] as $h): ?>
            <th style="padding:0.9rem 1rem; text-align:left; font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; white-space:nowrap;">
              <?= $h ?>
            </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($flights)): ?>
          <tr><td colspan="8" style="padding:2rem; text-align:center; color:var(--text-muted);">No flights found.</td></tr>
        <?php else: foreach ($flights as $i => $fl): ?>
        <tr style="border-top:1px solid rgba(255,255,255,0.2); <?= $i % 2 ? 'background:rgba(255,255,255,0.06)' : '' ?>">
          <td style="padding:0.8rem 1rem; font-weight:600; font-size:0.9rem;"><?= bsc($fl['airline_name']) ?></td>
          <td style="padding:0.8rem 1rem; font-family:var(--font-mono, monospace); font-size:0.85rem; color:var(--ocean);"><?= bsc($fl['flight_number']) ?></td>
          <td style="padding:0.8rem 1rem; font-size:0.85rem;">
            <span style="font-weight:600;"><?= bsc($fl['departure_airport']) ?></span>
            <span style="color:var(--text-muted); margin:0 0.3rem;">→</span>
            <span style="font-weight:600;"><?= bsc($fl['arrival_airport']) ?></span>
            <div style="font-size:0.75rem; color:var(--text-muted);"><?= bsc($fl['departure_city']) ?> → <?= bsc($fl['arrival_city']) ?></div>
          </td>
          <td style="padding:0.8rem 1rem; font-size:0.85rem; white-space:nowrap;"><?= bsc(substr($fl['departure_time'], 0, 5)) ?></td>
          <td style="padding:0.8rem 1rem; font-size:0.85rem; white-space:nowrap;"><?= bsc(substr($fl['arrival_time'], 0, 5)) ?></td>
          <td style="padding:0.8rem 1rem; font-size:0.85rem; white-space:nowrap;">
            <?php
              $mins = (int)$fl['duration_minutes'];
              echo $mins ? floor($mins/60).'h '.($mins%60).'m' : '—';
            ?>
          </td>
          <td style="padding:0.8rem 1rem;">
            <span style="
              display:inline-block; padding:0.2rem 0.6rem;
              border-radius:var(--r-pill); font-size:0.75rem; font-weight:600;
              <?= match($fl['flight_class']) {
                'business' => 'background:rgba(99,102,241,0.15); color:#818cf8;',
                'first'    => 'background:rgba(251,191,36,0.15); color:#fbbf24;',
                default    => 'background:rgba(255,255,255,0.1); color:var(--text-soft);'
              } ?>
            "><?= bsc(ucfirst($fl['flight_class'])) ?></span>
          </td>
          <td style="padding:0.8rem 1rem; font-weight:700; color:var(--ocean); white-space:nowrap;">
            R <?= number_format((float)$fl['base_price'], 0) ?>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <!-- ══════════════════════════════════════════════════════
       TAB: ACCOMMODATIONS
  ═══════════════════════════════════════════════════════ -->
  <?php elseif ($activeTab === 'accommodations'): ?>

  <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:1.5rem;">
    <?php if (empty($accommodations)): ?>
      <p style="color:var(--text-muted);">No accommodations found.</p>
    <?php else: foreach ($accommodations as $acc): ?>

    <div class="glass-heavy" style="border-radius:var(--r-xl); overflow:hidden;">
      <?php if ($acc['image_url']): ?>
        <img src="<?= bsc($acc['image_url']) ?>"
             alt="<?= bsc($acc['name']) ?>"
             style="width:100%; height:190px; object-fit:cover; display:block;"
             onerror="this.style.display='none'">
      <?php else: ?>
        <div style="width:100%; height:190px; background:linear-gradient(135deg,var(--deep-blue),var(--ocean)); display:flex; align-items:center; justify-content:center; font-size:3rem;">🏨</div>
      <?php endif; ?>

      <div style="padding:1.3rem;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.4rem;">
          <div>
            <div style="font-size:0.75rem; color:var(--ocean); font-weight:600; text-transform:uppercase; letter-spacing:.06em;">
              <?= bsc($acc['city']) ?>, <?= bsc($acc['country']) ?>
            </div>
            <div style="font-size:1rem; font-weight:700; color:var(--text-main); margin-top:0.2rem;">
              <?= bsc($acc['name']) ?>
            </div>
          </div>
          <span style="font-size:0.75rem; background:rgba(255,255,255,0.12); padding:0.2rem 0.5rem; border-radius:var(--r-pill); color:var(--text-soft); white-space:nowrap; margin-left:0.5rem;">
            <?= bsc(ucfirst($acc['type'])) ?>
          </span>
        </div>

        <div style="color:var(--warning); font-size:1rem; margin-bottom:0.6rem;">
          <?= starRating($acc['star_rating'] ? (int)$acc['star_rating'] : null) ?>
        </div>

        <?php if ($acc['description']): ?>
          <p style="font-size:0.82rem; color:var(--text-soft); margin:0 0 0.8rem; line-height:1.5;">
            <?= bsc(mb_strimwidth($acc['description'], 0, 110, '…')) ?>
          </p>
        <?php endif; ?>

        <?php if ($acc['amenities']): ?>
          <div style="margin-bottom:0.8rem; display:flex; flex-wrap:wrap; gap:0.3rem;">
            <?php foreach (explode(', ', $acc['amenities']) as $amenity): ?>
              <span style="font-size:0.72rem; background:rgba(var(--ocean-rgb,14,165,233),0.12); color:var(--ocean); padding:0.15rem 0.5rem; border-radius:var(--r-pill);">
                <?= bsc(ucfirst($amenity)) ?>
              </span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--glass-border); padding-top:0.8rem; margin-top:0.2rem;">
          <span style="font-size:0.8rem; color:var(--text-muted);">Per night</span>
          <span style="font-size:1.1rem; font-weight:700; color:var(--ocean);">
            R <?= number_format((float)$acc['price_per_night'], 0) ?>
          </span>
        </div>
      </div>
    </div>

    <?php endforeach; endif; ?>
  </div>

  <!-- ══════════════════════════════════════════════════════
       TAB: ATTRACTIONS
  ═══════════════════════════════════════════════════════ -->
  <?php elseif ($activeTab === 'attractions'): ?>

  <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:1.5rem;">
    <?php if (empty($attractions)): ?>
      <p style="color:var(--text-muted);">No attractions found.</p>
    <?php else: foreach ($attractions as $attr): ?>

    <div class="glass-heavy" style="border-radius:var(--r-xl); overflow:hidden;">
      <?php if ($attr['image_url']): ?>
        <img src="<?= bsc($attr['image_url']) ?>"
             alt="<?= bsc($attr['name']) ?>"
             style="width:100%; height:180px; object-fit:cover; display:block;"
             onerror="this.style.display='none'">
      <?php else: ?>
        <div style="width:100%; height:180px; background:linear-gradient(135deg,var(--teal),var(--deep-blue)); display:flex; align-items:center; justify-content:center; font-size:3rem;">🎡</div>
      <?php endif; ?>

      <div style="padding:1.3rem;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
          <div>
            <div style="font-size:0.75rem; color:var(--ocean); font-weight:600; text-transform:uppercase; letter-spacing:.06em;">
              <?= bsc($attr['city']) ?>, <?= bsc($attr['country']) ?>
            </div>
            <div style="font-size:1rem; font-weight:700; color:var(--text-main); margin-top:0.2rem;">
              <?= bsc($attr['name']) ?>
            </div>
          </div>
          <span style="font-size:0.72rem; background:rgba(255,255,255,0.1); color:var(--text-soft); padding:0.2rem 0.5rem; border-radius:var(--r-pill); white-space:nowrap; margin-left:0.5rem; text-transform:capitalize;">
            <?= bsc(str_replace('_', ' ', $attr['category'])) ?>
          </span>
        </div>

        <?php if ($attr['description']): ?>
          <p style="font-size:0.82rem; color:var(--text-soft); margin:0 0 0.8rem; line-height:1.5;">
            <?= bsc(mb_strimwidth($attr['description'], 0, 110, '…')) ?>
          </p>
        <?php endif; ?>

        <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.82rem; color:var(--text-muted);">
          <?php if ($attr['opening_hours']): ?>
            <span>🕐 <?= bsc($attr['opening_hours']) ?></span>
          <?php endif; ?>
          <span style="font-weight:700; color:<?= (float)$attr['entry_fee'] > 0 ? 'var(--ocean)' : 'var(--success)' ?>; white-space:nowrap;">
            <?= (float)$attr['entry_fee'] > 0 ? 'R '.number_format((float)$attr['entry_fee'], 0) : 'Free Entry' ?>
          </span>
        </div>
      </div>
    </div>

    <?php endforeach; endif; ?>
  </div>

  <!-- ══════════════════════════════════════════════════════
       TAB: RESTAURANTS
  ═══════════════════════════════════════════════════════ -->
  <?php elseif ($activeTab === 'restaurants'): ?>

  <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:1.5rem;">
    <?php if (empty($restaurants)): ?>
      <p style="color:var(--text-muted);">No restaurants found.</p>
    <?php else: foreach ($restaurants as $rest): ?>

    <div class="glass-heavy" style="border-radius:var(--r-xl); overflow:hidden;">
      <?php if ($rest['image_url']): ?>
        <img src="<?= bsc($rest['image_url']) ?>"
             alt="<?= bsc($rest['name']) ?>"
             style="width:100%; height:180px; object-fit:cover; display:block;"
             onerror="this.style.display='none'">
      <?php else: ?>
        <div style="width:100%; height:180px; background:linear-gradient(135deg,#f97316,#dc2626); display:flex; align-items:center; justify-content:center; font-size:3rem;">🍽</div>
      <?php endif; ?>

      <div style="padding:1.3rem;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.4rem;">
          <div>
            <div style="font-size:0.75rem; color:var(--ocean); font-weight:600; text-transform:uppercase; letter-spacing:.06em;">
              <?= bsc($rest['city']) ?>, <?= bsc($rest['country']) ?>
            </div>
            <div style="font-size:1rem; font-weight:700; color:var(--text-main); margin-top:0.2rem;">
              <?= bsc($rest['name']) ?>
            </div>
          </div>
          <?php if ($rest['price_range']): ?>
            <span style="font-size:0.72rem; background:rgba(255,255,255,0.1); color:var(--text-soft); padding:0.2rem 0.5rem; border-radius:var(--r-pill); white-space:nowrap; margin-left:0.5rem; text-transform:capitalize;">
              <?= bsc(str_replace('-', ' ', $rest['price_range'])) ?>
            </span>
          <?php endif; ?>
        </div>

        <?php if ($rest['cuisine_type']): ?>
          <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:0.5rem;">
            🍴 <?= bsc($rest['cuisine_type']) ?> cuisine
          </div>
        <?php endif; ?>

        <?php if ($rest['description']): ?>
          <p style="font-size:0.82rem; color:var(--text-soft); margin:0 0 0.8rem; line-height:1.5;">
            <?= bsc(mb_strimwidth($rest['description'], 0, 110, '…')) ?>
          </p>
        <?php endif; ?>

        <?php if ($rest['average_rating']): ?>
          <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.85rem;">
            <span style="color:var(--warning);">
              <?php
                $r = round((float)$rest['average_rating']);
                echo str_repeat('★', $r) . str_repeat('☆', 5 - $r);
              ?>
            </span>
            <span style="color:var(--text-muted);"><?= bsc(number_format((float)$rest['average_rating'], 1)) ?></span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <?php endforeach; endif; ?>
  </div>

  <?php endif; ?>

</div>
</div>
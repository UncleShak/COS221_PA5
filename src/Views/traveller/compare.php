<?php

$leftId  = isset($leftId) ? (int)$leftId : (int)($_GET['left'] ?? 1);
$rightId = isset($rightId) ? (int)$rightId : (int)($_GET['right'] ?? 2);
$packageOptions = $packageOptions ?? [];

if (empty($left) || empty($right)) {
  $left = $left ?? [
    'package_id'     => $leftId,
    'destination'    => 'Bali, Indonesia',
    'title'          => 'Jungle Retreat',
    'agency'         => 'Wanderlust Travel',
    'duration_days'  => 8,
    'price_pp'       => 18500,
    'taxes_fees'     => 1200,
    'rating'         => 4.9,
    'reviews_count'  => 128,
    'travel_month'   => 'June',
    'highlights'     => [
      'Ubud rainforest villa stay',
      'Seminyak beach resort',
      'Guided temple tour',
      '60-minute spa treatment'
    ],
    'includes'       => [
      'Return Flights (Economy)',
      'Luxury Villa Accommodation',
      'Airport Transfers',
      'Daily Breakfast'
    ],
  ];

  $right = $right ?? [
    'package_id'     => $rightId,
    'destination'    => 'Tokyo, Japan',
    'title'          => 'December Explorer',
    'agency'         => 'MetroVoyage',
    'duration_days'  => 7,
    'price_pp'       => 20900,
    'taxes_fees'     => 1500,
    'rating'         => 4.7,
    'reviews_count'  => 96,
    'travel_month'   => 'December',
    'highlights'     => [
      'Day 3 temple visit',
      'Street food night market',
      'City skyline observatory',
      'Cultural etiquette primer'
    ],
    'includes'       => [
      'Return Flights (Economy)',
      'Boutique Hotel',
      'Airport Transfers',
      'Metro Pass'
    ],
  ];
}

function moneyZAR($amount) {
  return 'R ' . number_format((float)$amount, 0, '.', ',');
}

function stars($ratingOutOf5) {
  $full = (int)floor($ratingOutOf5);
  $half = ($ratingOutOf5 - $full) >= 0.5 ? 1 : 0;
  $out = '';
  for ($i=1; $i<=5; $i++) {
    if ($i <= $full) $out .= '★';
    else if ($half && $i === $full + 1) $out .= '★';
    else $out .= '<span style="color: rgba(0,0,0,0.12);">★</span>';
  }
  return $out;
}
?>

<div class="sg-page" style="min-height: 100vh; padding: 100px 2rem 4rem; width: 100%; max-width: 1800px; margin: 0 auto;">
  
  <div style="margin-bottom: 2.5rem;">
    <p class="sg-section-label">Compare</p>
    <h2 class="sg-section-title">Packages, side-by-side.</h2>
  </div>

  <div class="glass-clear" style="padding: 1.5rem; margin-bottom: 2.5rem;">
    <div class="demo-cell-label" style="margin-bottom: 1rem;">Compare Inputs</div>
    <form method="GET" action="" class="input-stack" style="max-width: 900px;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem;">
        <div class="input-group">
          <label class="input-label">Left package</label>
          <select class="input-field" name="left">
            <?php foreach ($packageOptions as $packageOption): ?>
              <option value="<?= htmlspecialchars((string)$packageOption['id']) ?>" <?= ((int)$packageOption['id'] === $leftId) ? 'selected' : '' ?>>
                <?= htmlspecialchars($packageOption['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="input-hint">Choose the first package to compare</div>
        </div>
        <div class="input-group">
          <label class="input-label">Right package</label>
          <select class="input-field" name="right">
            <?php foreach ($packageOptions as $packageOption): ?>
              <option value="<?= htmlspecialchars((string)$packageOption['id']) ?>" <?= ((int)$packageOption['id'] === $rightId) ? 'selected' : '' ?>>
                <?= htmlspecialchars($packageOption['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="input-hint">Choose the second package to compare</div>
        </div>
      </div>

      <div style="display:flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
        <button type="submit" class="btn-primary">Compare</button>
        <a class="btn-secondary" href="/traveller/packages">Back to Packages</a>
      </div>
    </form>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start; width: 100%;">
    
    <div class="glass-frosted" style="padding: 2rem;">
      <div style="display:flex; justify-content: space-between; gap: 1rem; align-items: flex-start; margin-bottom: 1.2rem;">
        <div>
          <div class="pkg-card-dest" style="margin-bottom: 0.2rem;"><?= htmlspecialchars($left['destination']) ?></div>
          <div class="pkg-card-name" style="font-size: 2rem;"><?= htmlspecialchars($left['title']) ?></div>
          <div style="color: var(--text-soft); font-size: 0.95rem;">
            Hosted by <strong style="color: var(--text-main);"><?= htmlspecialchars($left['agency']) ?></strong>
          </div>
        </div>
      </div>

      <div class="glass-clear" style="padding: 1.4rem; margin-bottom: 1.5rem;">
        <div class="demo-cell-label" style="margin-bottom: 0.9rem;">Key Stats</div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div>
            <div class="input-label">Price (pp)</div>
            <div class="pkg-card-price" style="font-size: 2rem; color: var(--text-main);">
              <?= moneyZAR($left['price_pp']) ?>
            </div>
            <div class="input-hint">Excludes taxes & fees</div>
          </div>
          <div>
            <div class="input-label">Duration</div>
            <div style="font-size: 1.2rem; font-weight: 600; color: var(--text-main);">
              <?= htmlspecialchars((string)$left['duration_days']) ?> nights
            </div>
            <div class="input-hint">Package length</div>
          </div>

          <div>
            <div class="input-label">Taxes & Fees</div>
            <div style="font-size: 1.2rem; font-weight: 600; color: var(--text-main);">
              <?= moneyZAR($left['taxes_fees']) ?>
            </div>
          </div>

          <div>
            <div class="input-label">Rating</div>
            <div style="color: var(--warning); font-weight: 700; font-size: 1.1rem; letter-spacing: 2px;">
              <?= stars((float)$left['rating']) ?>
              <span style="letter-spacing: 0; color: var(--text-soft); font-weight: 500; font-size: 0.9rem;">
                <?= htmlspecialchars(number_format((float)$left['rating'], 1)) ?> (<?= (int)$left['reviews_count'] ?>)
              </span>
            </div>
          </div>
        </div>

        <hr style="border: 0; height: 1px; background: rgba(0,0,0,0.08); margin: 1.2rem 0;">

        <div style="display:flex; justify-content: space-between; align-items: center;">
          <div style="color: var(--text-soft); font-size: 0.95rem;">
            Estimated total (1 traveller)
          </div>
          <div style="font-family: var(--font-display); font-style: italic; font-size: 1.6rem; font-weight: 700; color: var(--text-main);">
            <?= moneyZAR($left['price_pp'] + $left['taxes_fees']) ?>
          </div>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: 1fr; gap: 1.2rem;">
        <div class="glass-clear" style="padding: 1.4rem;">
          <div class="demo-cell-label" style="margin-bottom: 0.9rem;">Highlights</div>
          <div class="input-stack">
            <?php foreach ($left['highlights'] as $h): ?>
              <div class="sm-item" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
               <?= htmlspecialchars($h) ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="glass-clear" style="padding: 1.4rem;">
          <div class="demo-cell-label" style="margin-bottom: 0.9rem;">What's Included</div>
          <div style="display:flex; flex-wrap: wrap; gap: 0.7rem;">
            <?php foreach ($left['includes'] as $inc): ?>
              <span class="status-pill"> <?= htmlspecialchars($inc) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div style="display:flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem;">
        <a href="/traveller/details?id=<?= htmlspecialchars((string)$left['package_id']) ?>" class="btn-secondary">View Package</a>
        <a href="/traveller/checkout?package_id=<?= htmlspecialchars((string)$left['package_id']) ?>" class="btn-primary">Book Left</a>
      </div>
    </div>

    <div class="glass-frosted" style="padding: 2rem;">
      <div style="display:flex; justify-content: space-between; gap: 1rem; align-items: flex-start; margin-bottom: 1.2rem;">
        <div>
          <div class="pkg-card-dest" style="margin-bottom: 0.2rem;"><?= htmlspecialchars($right['destination']) ?></div>
          <div class="pkg-card-name" style="font-size: 2rem;"><?= htmlspecialchars($right['title']) ?></div>
          <div style="color: var(--text-soft); font-size: 0.95rem;">
            Hosted by <strong style="color: var(--text-main);"><?= htmlspecialchars($right['agency']) ?></strong>
          </div>
        </div>
      </div>

      <div class="glass-clear" style="padding: 1.4rem; margin-bottom: 1.5rem;">
        <div class="demo-cell-label" style="margin-bottom: 0.9rem;">Key Stats</div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div>
            <div class="input-label">Price (pp)</div>
            <div class="pkg-card-price" style="font-size: 2rem; color: var(--text-main);">
              <?= moneyZAR($right['price_pp']) ?>
            </div>
            <div class="input-hint">Excludes taxes & fees</div>
          </div>
          <div>
            <div class="input-label">Duration</div>
            <div style="font-size: 1.2rem; font-weight: 600; color: var(--text-main);">
              <?= htmlspecialchars((string)$right['duration_days']) ?> nights
            </div>
            <div class="input-hint">Package length</div>
          </div>

          <div>
            <div class="input-label">Taxes & Fees</div>
            <div style="font-size: 1.2rem; font-weight: 600; color: var(--text-main);">
              <?= moneyZAR($right['taxes_fees']) ?>
            </div>
          </div>

          <div>
            <div class="input-label">Rating</div>
            <div style="color: var(--warning); font-weight: 700; font-size: 1.1rem; letter-spacing: 2px;">
              <?= stars((float)$right['rating']) ?>
              <span style="letter-spacing: 0; color: var(--text-soft); font-weight: 500; font-size: 0.9rem;">
                <?= htmlspecialchars(number_format((float)$right['rating'], 1)) ?> (<?= (int)$right['reviews_count'] ?>)
              </span>
            </div>
          </div>
        </div>

        <hr style="border: 0; height: 1px; background: rgba(0,0,0,0.08); margin: 1.2rem 0;">

        <div style="display:flex; justify-content: space-between; align-items: center;">
          <div style="color: var(--text-soft); font-size: 0.95rem;">
            Estimated total (1 traveller)
          </div>
          <div style="font-family: var(--font-display); font-style: italic; font-size: 1.6rem; font-weight: 700; color: var(--text-main);">
            <?= moneyZAR($right['price_pp'] + $right['taxes_fees']) ?>
          </div>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: 1fr; gap: 1.2rem;">
        <div class="glass-clear" style="padding: 1.4rem;">
          <div class="demo-cell-label" style="margin-bottom: 0.9rem;">Highlights</div>
          <div class="input-stack">
            <?php foreach ($right['highlights'] as $h): ?>
              <div class="sm-item" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                 <?= htmlspecialchars($h) ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="glass-clear" style="padding: 1.4rem;">
          <div class="demo-cell-label" style="margin-bottom: 0.9rem;">What's Included</div>
          <div style="display:flex; flex-wrap: wrap; gap: 0.7rem;">
            <?php foreach ($right['includes'] as $inc): ?>
              <span class="status-pill"><?= htmlspecialchars($inc) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div style="display:flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem;">
        <a href="/traveller/details?id=<?= htmlspecialchars((string)$right['package_id']) ?>" class="btn-secondary">View Package</a>
        <a href="/traveller/checkout?package_id=<?= htmlspecialchars((string)$right['package_id']) ?>" class="btn-primary">Book Right</a>
      </div>
    </div>

  </div>

</div>
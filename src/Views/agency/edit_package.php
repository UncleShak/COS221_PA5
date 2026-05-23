<?php
// src/Views/agency/edit_package.php
// Package create / edit form.
// Variables supplied by AgencyController::packageForm():
//   $formMode              — 'create' | 'edit'
//   $package               — array|null  (null when creating)
//   $groupTrip             — array|null  (existing GroupTrips row, or null)
//   $allDestinations       — array of available rows
//   $allFlights            — array of available rows
//   $allAccommodations     — array of available rows
//   $allAttractions        — array of available rows
//   $allRestaurants        — array of available rows
//   $selectedDestinations  — int[]  (IDs already linked)
//   $selectedFlights       — int[]
//   $selectedAccommodations— int[]
//   $selectedAttractions   — int[]
//   $selectedRestaurants   — int[]
//   $errors                — assoc array of validation messages (may be empty)
//   $old                   — assoc array of previous POST values (for re-fill after error)

// Use $old to re-populate fields after a failed save; fall back to $package for edits.
$v = fn(string $key, mixed $default = '') => $old[$key] ?? $package[$key] ?? $default;

// Group trip checkbox: re-tick if $old had it, or if an existing GroupTrip record exists.
$showGroupTrip = !empty($old['is_group_trip']) || $groupTrip !== null;
?>

<div class="sg-page" style="min-height:100vh; padding:120px 2rem 4rem; background:transparent;">
<div style="max-width:860px; margin:0 auto; width:100%;">

  <!-- ── Page Header ──────────────────────────────────────────── -->
  <div style="margin-bottom:2.5rem;">
    <a href="/agency/dashboard"
       style="font-size:.85rem; color:var(--ocean); text-decoration:none; font-weight:600;">
      ← Back to Dashboard
    </a>
    <h2 class="sg-section-title"
        style="margin:.8rem 0 0; font-size:clamp(1.8rem,3vw,2.6rem); line-height:1.1;">
      <?= $formMode === 'edit' ? 'Edit Package' : 'New Package' ?>
    </h2>
  </div>

  <!-- ── Form ─────────────────────────────────────────────────── -->
  <form method="POST" action="/agency/package/save" novalidate>

    <!-- Hidden fields -->
    <?php if ($formMode === 'edit'): ?>
      <input type="hidden" name="package_id" value="<?= (int) $package['package_id'] ?>">
    <?php endif; ?>

    <!-- ── Section 1: Core Details ──────────────────────────── -->
    <div class="glass-clear" style="border-radius:var(--r-xl); padding:2rem; margin-bottom:1.5rem;">
      <h3 style="margin:0 0 1.5rem; font-size:1rem; font-weight:600; color:var(--text-soft);
                 text-transform:uppercase; letter-spacing:.08em;">Package Details</h3>

      <div class="input-stack">

        <!-- Title -->
        <div class="input-group">
          <label class="input-label" for="title">Title *</label>
          <input type="text" id="title" name="title" class="input-field <?= isset($errors['title']) ? 'error' : '' ?>"
                 placeholder="e.g. Bali Jungle Retreat"
                 value="<?= htmlspecialchars((string) $v('title'), ENT_QUOTES, 'UTF-8') ?>"
                 required>
          <?php if (isset($errors['title'])): ?>
            <span class="input-hint" style="color:var(--danger);"><?= htmlspecialchars($errors['title']) ?></span>
          <?php endif; ?>
        </div>

        <!-- Description -->
        <div class="input-group">
          <label class="input-label" for="description">Description</label>
          <textarea id="description" name="description" class="input-field"
                    rows="4" placeholder="Describe the package experience…"
                    style="resize:vertical;"><?= htmlspecialchars((string) $v('description'), ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <!-- Price + Duration (two columns) -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.2rem;">

          <div class="input-group">
            <label class="input-label" for="base_price">Base Price (R) *</label>
            <input type="number" id="base_price" name="base_price"
                   class="input-field <?= isset($errors['base_price']) ? 'error' : '' ?>"
                   min="0" step="0.01" placeholder="0.00"
                   value="<?= htmlspecialchars((string) $v('base_price'), ENT_QUOTES, 'UTF-8') ?>"
                   required>
            <?php if (isset($errors['base_price'])): ?>
              <span class="input-hint" style="color:var(--danger);"><?= htmlspecialchars($errors['base_price']) ?></span>
            <?php endif; ?>
          </div>

          <div class="input-group">
            <label class="input-label" for="duration_days">Duration (days) *</label>
            <input type="number" id="duration_days" name="duration_days"
                   class="input-field <?= isset($errors['duration_days']) ? 'error' : '' ?>"
                   min="1" step="1" placeholder="7"
                   value="<?= htmlspecialchars((string) $v('duration_days'), ENT_QUOTES, 'UTF-8') ?>"
                   required>
            <?php if (isset($errors['duration_days'])): ?>
              <span class="input-hint" style="color:var(--danger);"><?= htmlspecialchars($errors['duration_days']) ?></span>
            <?php endif; ?>
          </div>

        </div>

        <!-- Max Capacity + Cover Image (two columns) -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.2rem;">

          <div class="input-group">
            <label class="input-label" for="max_capacity">Max Capacity</label>
            <input type="number" id="max_capacity" name="max_capacity"
                   class="input-field" min="1" step="1" placeholder="Leave blank for unlimited"
                   value="<?= htmlspecialchars((string) $v('max_capacity'), ENT_QUOTES, 'UTF-8') ?>">
            <span class="input-hint">Optional overall cap on bookings for this package.</span>
          </div>

          <div class="input-group">
            <label class="input-label" for="cover_image_url">Cover Image URL</label>
            <input type="url" id="cover_image_url" name="cover_image_url"
                   class="input-field" placeholder="https://…"
                   value="<?= htmlspecialchars((string) $v('cover_image_url'), ENT_QUOTES, 'UTF-8') ?>">
          </div>

        </div>

        <!-- Status -->
        <div class="input-group">
          <label class="input-label" for="status">Status *</label>
          <select id="status" name="status" class="input-field">
            <?php
              // FIX: ENUM values are draft / active / archived — not 'published'
              $currentStatus = $v('status', 'draft');
              foreach (['draft' => 'Draft', 'active' => 'Active (Published)', 'archived' => 'Archived'] as $val => $label):
            ?>
              <option value="<?= $val ?>" <?= $currentStatus === $val ? 'selected' : '' ?>>
                <?= $label ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>
    </div>

    <!-- ── Section 2: Group Trip Toggle ─────────────────────── -->
    <div class="glass-clear" style="border-radius:var(--r-xl); padding:2rem; margin-bottom:1.5rem;">
      <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem;">
        <input type="checkbox" id="is_group_trip" name="is_group_trip" value="1"
               <?= $showGroupTrip ? 'checked' : '' ?>
               style="width:18px; height:18px; accent-color:var(--ocean); cursor:pointer;">
        <label for="is_group_trip"
               style="font-size:1rem; font-weight:600; cursor:pointer; color:var(--text-main);">
          This is a Group Trip
        </label>
      </div>

      <div id="group-trip-fields" style="<?= $showGroupTrip ? '' : 'display:none;' ?>">
        <div class="input-stack">

          <!-- Departure + Return date -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.2rem;">

            <div class="input-group">
              <label class="input-label" for="departure_date">Departure Date *</label>
              <input type="date" id="departure_date" name="departure_date"
                     class="input-field <?= isset($errors['departure_date']) ? 'error' : '' ?>"
                     value="<?= htmlspecialchars((string) ($old['departure_date'] ?? $groupTrip['departure_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <?php if (isset($errors['departure_date'])): ?>
                <span class="input-hint" style="color:var(--danger);"><?= htmlspecialchars($errors['departure_date']) ?></span>
              <?php endif; ?>
            </div>

            <div class="input-group">
              <label class="input-label" for="return_date">Return Date *</label>
              <input type="date" id="return_date" name="return_date"
                     class="input-field <?= isset($errors['return_date']) ? 'error' : '' ?>"
                     value="<?= htmlspecialchars((string) ($old['return_date'] ?? $groupTrip['return_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <?php if (isset($errors['return_date'])): ?>
                <span class="input-hint" style="color:var(--danger);"><?= htmlspecialchars($errors['return_date']) ?></span>
              <?php endif; ?>
            </div>

          </div>

          <!-- Min + Max participants -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.2rem;">

            <div class="input-group">
              <label class="input-label" for="gt_min_participants">Min Participants *</label>
              <input type="number" id="gt_min_participants" name="gt_min_participants"
                     class="input-field <?= isset($errors['gt_min_participants']) ? 'error' : '' ?>"
                     min="1" step="1" placeholder="2"
                     value="<?= htmlspecialchars((string) ($old['gt_min_participants'] ?? $groupTrip['min_participants'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <?php if (isset($errors['gt_min_participants'])): ?>
                <span class="input-hint" style="color:var(--danger);"><?= htmlspecialchars($errors['gt_min_participants']) ?></span>
              <?php endif; ?>
            </div>

            <div class="input-group">
              <label class="input-label" for="gt_max_participants">Max Participants *</label>
              <input type="number" id="gt_max_participants" name="gt_max_participants"
                     class="input-field <?= isset($errors['gt_max_participants']) ? 'error' : '' ?>"
                     min="1" step="1" placeholder="20"
                     value="<?= htmlspecialchars((string) ($old['gt_max_participants'] ?? $groupTrip['max_participants'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <?php if (isset($errors['gt_max_participants'])): ?>
                <span class="input-hint" style="color:var(--danger);"><?= htmlspecialchars($errors['gt_max_participants']) ?></span>
              <?php endif; ?>
            </div>

          </div>

          <!-- Meeting point -->
          <div class="input-group">
            <label class="input-label" for="meeting_point">Meeting Point</label>
            <input type="text" id="meeting_point" name="meeting_point"
                   class="input-field" placeholder="e.g. OR Tambo International, Departure Hall"
                   value="<?= htmlspecialchars((string) ($old['meeting_point'] ?? $groupTrip['meeting_point'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
          </div>

        </div>
      </div>
    </div>

    <!-- ── Section 3: Components (multi-selects) ────────────── -->
    <div class="glass-clear" style="border-radius:var(--r-xl); padding:2rem; margin-bottom:1.5rem;">
      <h3 style="margin:0 0 1.5rem; font-size:1rem; font-weight:600; color:var(--text-soft);
                 text-transform:uppercase; letter-spacing:.08em;">Package Components</h3>
      <p style="font-size:.88rem; color:var(--text-muted); margin-bottom:1.8rem;">
        Select from the items already in the database. Hold <kbd>Ctrl</kbd> (or <kbd>Cmd</kbd>) to pick multiple.
      </p>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">

        <!-- Destinations -->
        <div class="input-group">
          <label class="input-label" for="destinations">Destinations</label>
          <select id="destinations" name="destinations[]" multiple class="input-field"
                  style="height:160px;">
            <?php foreach ($allDestinations as $dest): ?>
              <option value="<?= (int) $dest['destination_id'] ?>"
                <?= in_array($dest['destination_id'], $selectedDestinations) ? 'selected' : '' ?>>
                <?= htmlspecialchars("{$dest['name']} — {$dest['city']}, {$dest['country']}", ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Flights -->
        <div class="input-group">
          <label class="input-label" for="flights">Flights</label>
          <select id="flights" name="flights[]" multiple class="input-field"
                  style="height:160px;">
            <?php foreach ($allFlights as $flight): ?>
              <option value="<?= (int) $flight['flight_id'] ?>"
                <?= in_array($flight['flight_id'], $selectedFlights) ? 'selected' : '' ?>>
                <?= htmlspecialchars(
                    "{$flight['airline_name']} {$flight['flight_number']} · {$flight['origin_city']} → {$flight['dest_city']}",
                    ENT_QUOTES, 'UTF-8'
                ) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Accommodations -->
        <div class="input-group">
          <label class="input-label" for="accommodations">Accommodations</label>
          <select id="accommodations" name="accommodations[]" multiple class="input-field"
                  style="height:160px;">
            <?php foreach ($allAccommodations as $acc): ?>
              <option value="<?= (int) $acc['accommodation_id'] ?>"
                <?= in_array($acc['accommodation_id'], $selectedAccommodations) ? 'selected' : '' ?>>
                <?= htmlspecialchars(
                    "{$acc['name']} · {$acc['star_rating']}★ · {$acc['city']}, {$acc['country']}",
                    ENT_QUOTES, 'UTF-8'
                ) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Attractions -->
        <div class="input-group">
          <label class="input-label" for="attractions">Attractions</label>
          <select id="attractions" name="attractions[]" multiple class="input-field"
                  style="height:160px;">
            <?php foreach ($allAttractions as $attr): ?>
              <option value="<?= (int) $attr['attraction_id'] ?>"
                <?= in_array($attr['attraction_id'], $selectedAttractions) ? 'selected' : '' ?>>
                <?= htmlspecialchars(
                    "{$attr['name']} · {$attr['category']} · {$attr['city']}, {$attr['country']}",
                    ENT_QUOTES, 'UTF-8'
                ) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Restaurants (full width) -->
        <div class="input-group" style="grid-column:1 / -1;">
          <label class="input-label" for="restaurants">Restaurants</label>
          <select id="restaurants" name="restaurants[]" multiple class="input-field"
                  style="height:140px;">
            <?php foreach ($allRestaurants as $rest): ?>
              <option value="<?= (int) $rest['restaurant_id'] ?>"
                <?= in_array($rest['restaurant_id'], $selectedRestaurants) ? 'selected' : '' ?>>
                <?= htmlspecialchars(
                    "{$rest['name']} · {$rest['cuisine_type']} · {$rest['city']}, {$rest['country']}",
                    ENT_QUOTES, 'UTF-8'
                ) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>
    </div>

    <!-- ── Submit ───────────────────────────────────────────── -->
    <div style="display:flex; gap:1rem; justify-content:flex-end;">
      <a href="/agency/dashboard" class="btn-secondary"
         style="border-radius:var(--r-pill); padding:.8rem 2rem; text-decoration:none;">
        Cancel
      </a>
      <button type="submit" class="btn-primary"
              style="border-radius:var(--r-pill); padding:.8rem 2.5rem;">
        <?= $formMode === 'edit' ? 'Save Changes' : 'Create Package' ?>
      </button>
    </div>

  </form>

</div>
</div>

<!-- ── Group Trip toggle JS ───────────────────────────────────── -->
<script>
  (function () {
    const checkbox = document.getElementById('is_group_trip');
    const fields   = document.getElementById('group-trip-fields');

    function toggle() {
      fields.style.display = checkbox.checked ? '' : 'none';
    }

    checkbox.addEventListener('change', toggle);
  })();
</script>

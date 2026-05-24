<main class="sg-page">
    <section class="sg-section" style="max-width: 900px; margin: 0 auto;">
        
        <div class="sg-section-label">Inventory Management</div>
        <h1 class="sg-section-title"><?= isset($package) ? 'Edit Package' : 'Create New Package' ?></h1>
        <p class="sg-section-sub">Bundle flights and accommodations into a master package for travellers.</p>

        <div class="glass-frosted" style="padding: 3rem;">
            <form action="/agency/package/save" method="POST" class="input-stack">
                
                <?php if (isset($package)): ?>
                    <input type="hidden" name="package_id" value="<?= htmlspecialchars($package['package_id']) ?>">
                <?php endif; ?>

                <div style="margin-bottom: 2.5rem;">
                    <h2 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; color: var(--text-main);">1. Core Details</h2>
                    
                    <div class="input-stack">
                        <div class="input-group">
                            <label class="input-label">Package Title</label>
                            <input type="text" name="title" class="input-field" placeholder="e.g., Tokyo Explorer" value="<?= htmlspecialchars($package['title'] ?? '') ?>" required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                            <div class="input-group">
                                <label class="input-label">Duration (Days)</label>
                                <input type="number" name="duration_days" class="input-field" min="1" value="<?= htmlspecialchars($package['duration_days'] ?? '') ?>" required>
                            </div>

                            <div class="input-group">
                                <label class="input-label">Max Capacity</label>
                                <input type="number" name="max_capacity" class="input-field" min="1" value="<?= htmlspecialchars($package['max_capacity'] ?? '') ?>" required>
                            </div>

                            <div class="input-group">
                                <label class="input-label">Base Price (ZAR)</label>
                                <input type="number" name="base_price" class="input-field" step="0.01" min="0" value="<?= htmlspecialchars($package['base_price'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="input-group" style="margin-top: 1.5rem;">
                            <label class="input-label">Cover Image URL</label>
                            <input type="url" name="cover_image_url" class="input-field" placeholder="https://example.com/tokyo-hero.jpg" value="<?= htmlspecialchars($package['cover_image_url'] ?? '') ?>">
                        </div>

                        <div class="input-group">
                            <label class="input-label">Description</label>
                            <textarea name="description" class="input-field" rows="4"><?= htmlspecialchars($package['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; color: var(--text-main);">2. Attach Inventory (Check to Select)</h2>
                    
                    <div class="input-stack">
                        
                        <div class="input-group">
                            <label class="input-label">Destinations</label>
                            <div style="height: 140px; overflow-y: auto; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--r-md); padding: 0.75rem;">
                                <?php foreach ($allDestinations ?? [] as $dest): ?>
                                    <?php $isChecked = in_array($dest['destination_id'], $selectedDestinations ?? []) ? 'checked' : ''; ?>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: var(--text-main); font-size: 0.85rem; cursor: pointer;">
                                        <input type="checkbox" name="destinations[]" value="<?= $dest['destination_id'] ?>" <?= $isChecked ?> style="accent-color: var(--coral);">
                                        <?= htmlspecialchars($dest['name']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="input-group">
                                <label class="input-label">Flights</label>
                                <div style="height: 140px; overflow-y: auto; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--r-md); padding: 0.75rem;">
                                    <?php foreach ($allFlights ?? [] as $flight): ?>
                                        <?php $isChecked = in_array($flight['flight_id'], $selectedFlights ?? []) ? 'checked' : ''; ?>
                                        <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: var(--text-main); font-size: 0.85rem; cursor: pointer;">
                                            <input type="checkbox" name="flights[]" value="<?= $flight['flight_id'] ?>" <?= $isChecked ?> style="accent-color: var(--coral);">
                                            <?= htmlspecialchars($flight['airline_name']) ?> <?= htmlspecialchars($flight['flight_number']) ?> 
                                            (<?= htmlspecialchars($flight['departure_airport']) ?> → <?= htmlspecialchars($flight['arrival_airport']) ?>)
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="input-group">
                                <label class="input-label">Accommodations</label>
                                <div style="height: 140px; overflow-y: auto; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--r-md); padding: 0.75rem;">
                                    <?php foreach ($allAccommodations ?? [] as $acc): ?>
                                        <?php $isChecked = in_array($acc['accommodation_id'], $selectedAccommodations ?? []) ? 'checked' : ''; ?>
                                        <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: var(--text-main); font-size: 0.85rem; cursor: pointer;">
                                            <input type="checkbox" name="accommodations[]" value="<?= $acc['accommodation_id'] ?>" <?= $isChecked ?> style="accent-color: var(--coral);">
                                            <?= htmlspecialchars($acc['name']) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="input-group">
                                <label class="input-label">Attractions</label>
                                <div style="height: 140px; overflow-y: auto; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--r-md); padding: 0.75rem;">
                                    <?php foreach ($allAttractions ?? [] as $attr): ?>
                                        <?php $isChecked = in_array($attr['attraction_id'], $selectedAttractions ?? []) ? 'checked' : ''; ?>
                                        <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: var(--text-main); font-size: 0.85rem; cursor: pointer;">
                                            <input type="checkbox" name="attractions[]" value="<?= $attr['attraction_id'] ?>" <?= $isChecked ?> style="accent-color: var(--coral);">
                                            <?= htmlspecialchars($attr['name']) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="input-group">
                                <label class="input-label">Restaurants</label>
                                <div style="height: 140px; overflow-y: auto; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--r-md); padding: 0.75rem;">
                                    <?php foreach ($allRestaurants ?? [] as $rest): ?>
                                        <?php $isChecked = in_array($rest['restaurant_id'], $selectedRestaurants ?? []) ? 'checked' : ''; ?>
                                        <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: var(--text-main); font-size: 0.85rem; cursor: pointer;">
                                            <input type="checkbox" name="restaurants[]" value="<?= $rest['restaurant_id'] ?>" <?= $isChecked ?> style="accent-color: var(--coral);">
                                            <?= htmlspecialchars($rest['name']) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2.5rem; border-top: 1px solid rgba(42,31,53,0.1); padding-top: 1.5rem; text-align: right;">
                    <button type="submit" class="btn-primary"><?= isset($package) ? 'Update Package' : 'Forge Package' ?></button>
                </div>
            </form>
        </div>
    </section>
</main>
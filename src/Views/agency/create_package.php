<main class="sg-page">
    <section class="sg-section" style="max-width: 900px; margin: 0 auto;">
        
        <div class="sg-section-label">Inventory Management</div>
        <h1 class="sg-section-title">Create New Package</h1>
        <p class="sg-section-sub">Bundle flights and accommodations into a master package for travellers.</p>

        <div class="glass-frosted" style="padding: 3rem;">
            <form action="/agency/package/save" method="POST" class="input-stack">
                
                <div style="margin-bottom: 2.5rem;">
                    <h2 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; color: var(--text-main);">1. Core Details</h2>
                    
                    <div class="input-stack">
                        <div class="input-group">
                            <label class="input-label">Package Title</label>
                            <input type="text" name="title" class="input-field" placeholder="e.g., Tokyo Explorer" required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                            <div class="input-group">
                                <label class="input-label">Duration (Days)</label>
                                <input type="number" name="duration_days" class="input-field" min="1" required>
                            </div>

                            <div class="input-group">
                                <label class="input-label">Max Capacity</label>
                                <input type="number" name="max_capacity" class="input-field" min="1" required>
                            </div>

                            <div class="input-group">
                                <label class="input-label">Base Price (ZAR)</label>
                                <input type="number" name="base_price" class="input-field" step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="input-group" style="margin-top: 1.5rem;">
                            <label class="input-label">Cover Image URL</label>
                            <input type="url" name="cover_image_url" class="input-field" placeholder="https://example.com/tokyo-hero.jpg">
                        </div>

                        <div class="input-group">
                            <label class="input-label">Description</label>
                            <textarea name="description" class="input-field" rows="4"></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; color: var(--text-main);">2. Attach Inventory (Hold Ctrl/Cmd to select multiple)</h2>
                    
                    <div class="input-stack">
                        <div class="input-group">
                            <label class="input-label">Destinations</label>
                            <select name="destinations[]" class="input-field" multiple style="height: 100px;">
                                <?php foreach ($allDestinations ?? [] as $dest): ?>
                                    <option value="<?= $dest['destination_id'] ?>"><?= htmlspecialchars($dest['name']) ?> (<?= htmlspecialchars($dest['country']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="input-group">
                                <label class="input-label">Flights</label>
                                <select name="flights[]" class="input-field" multiple style="height: 100px;">
                                    <?php foreach ($allFlights ?? [] as $flight): ?>
                                        <option value="<?= $flight['flight_id'] ?>"><?= htmlspecialchars($flight['airline_name']) ?> (<?= htmlspecialchars($flight['origin_city']) ?> → <?= htmlspecialchars($flight['dest_city']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="input-group">
                                <label class="input-label">Accommodations</label>
                                <select name="accommodations[]" class="input-field" multiple style="height: 100px;">
                                    <?php foreach ($allAccommodations ?? [] as $acc): ?>
                                        <option value="<?= $acc['accommodation_id'] ?>"><?= htmlspecialchars($acc['name']) ?> (<?= htmlspecialchars($acc['city']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="input-group">
                                <label class="input-label">Attractions</label>
                                <select name="attractions[]" class="input-field" multiple style="height: 100px;">
                                    <?php foreach ($allAttractions ?? [] as $attr): ?>
                                        <option value="<?= $attr['attraction_id'] ?>"><?= htmlspecialchars($attr['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="input-group">
                                <label class="input-label">Restaurants</label>
                                <select name="restaurants[]" class="input-field" multiple style="height: 100px;">
                                    <?php foreach ($allRestaurants ?? [] as $rest): ?>
                                        <option value="<?= $rest['restaurant_id'] ?>"><?= htmlspecialchars($rest['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2.5rem; border-top: 1px solid rgba(42,31,53,0.1); padding-top: 1.5rem; text-align: right;">
                    <button type="submit" class="btn-primary">Forge Package</button>
                </div>
            </form>
        </div>
    </section>
</main>
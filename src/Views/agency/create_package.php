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
                            <input type="url" name="cover_image" class="input-field" placeholder="https://example.com/tokyo-hero.jpg">
                        </div>

                        <div class="input-group">
                            <label class="input-label">Description</label>
                            <textarea name="description" class="input-field" rows="4"></textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; color: var(--text-main);">2. Attach Inventory</h2>
                    
                    <div class="input-stack">
                        <div class="input-group">
                            <label class="input-label">Select Outbound Flight</label>
                            <select name="flight_id" class="input-field" required>
                                <option value="">-- Choose a Flight --</option>
                                <?php foreach ($flights as $flight): ?>
                                    <option value="<?= $flight['flight_id'] ?>">
                                        <?= htmlspecialchars($flight['airline_name'] . ' ' . $flight['flight_number']) ?> 
                                        (<?= htmlspecialchars($flight['departure_city']) ?> to <?= htmlspecialchars($flight['arrival_city']) ?>) 
                                        - [<?= ucfirst(htmlspecialchars($flight['flight_class'])) ?>]
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input-group">
                            <label class="input-label">Select Accommodation</label>
                            <select name="accommodation_id" class="input-field" required>
                                <option value="">-- Choose a Hotel --</option>
                                <?php foreach ($accommodations as $acc): ?>
                                    <option value="<?= $acc['accommodation_id'] ?>">
                                        <?= htmlspecialchars($acc['name']) ?> in <?= htmlspecialchars($acc['city']) ?> 
                                        (<?= $acc['star_rating'] ?> Star <?= ucfirst(htmlspecialchars($acc['type'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
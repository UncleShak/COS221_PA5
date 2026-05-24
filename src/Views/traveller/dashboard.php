<?php
$userName = ($traveller && isset($traveller['first_name'])) 
    ? htmlspecialchars($traveller['first_name'] . ' ' . $traveller['last_name']) 
    : 'Verified Traveller';
    
$userEmail = ($traveller && isset($traveller['email'])) 
    ? htmlspecialchars($traveller['email']) 
    : 'traveller@tripistry.com';
?>

<div class="sg-page" style="min-height: 100vh; padding: 100px 2rem 4rem; width: 100%; max-width: 1800px; margin: 0 auto;">

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'booking_cancelled'): ?>
        <div class="glass-heavy" style="border-left: 4px solid #ff3b30; padding: 1rem 2rem; display: flex; align-items: center; gap: 1rem; margin-bottom: 3rem;">
            <div style="width: 10px; height: 10px; border-radius: 50%; background: #ff3b30; box-shadow: 0 0 10px #ff3b30;"></div>
            <div style="color: var(--text-main);">
                <strong style="color: #ff3b30;">Expedition Aborted:</strong> Your booking has been successfully cancelled.
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized_cluster'): ?>
        <div class="glass-heavy" style="border-left: 4px solid #ff3b30; padding: 1rem 2rem; display: flex; align-items: center; gap: 1rem; margin-bottom: 3rem;">
            <div style="width: 10px; height: 10px; border-radius: 50%; background: #ff3b30; box-shadow: 0 0 10px #ff3b30;"></div>
            <div style="color: var(--text-main);">
                <strong style="color: #ff3b30;">Access Denied:</strong> You are not authorized to view this private transmission channel.
            </div>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 3rem;">
        <p class="sg-section-label">Traveller Hub</p>
        <h2 class="sg-section-title">Your Itinerary.</h2>
    </div>

    <div style="display: flex; gap: 2rem; align-items: stretch; width: 100%; flex-wrap: wrap;">

        <div class="glass-frosted" style="flex: 1; min-width: 300px; padding: 3rem 2rem; display: flex; flex-direction: column; text-align: center;">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--gradient-main); margin: 0 auto 1.5rem; padding: 4px; box-shadow: var(--glass-shadow-hi);">
                <div style="width: 100%; height: 100%; border-radius: 50%; background: var(--glass-bg-hi); display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                    🌴
                </div>
            </div>
            
            <h2 style="font-family: var(--font-display); font-weight: 300; font-style: italic; font-size: 2.2rem; color: var(--text-main); margin-bottom: 0.2rem;"><?= $userName ?></h2>
            <p style="color: var(--ocean); font-size: 0.85rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;"><?= $userEmail ?></p>

            <div style="margin-top: 3rem; text-align: left; border-top: 1px dashed var(--glass-border); padding-top: 2rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1.2rem;">
                    <span class="input-label">Total Expeditions</span>
                    <strong style="color: var(--teal); font-size: 1.2rem;"><?= $totalExpeditions ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 2rem;">
                    <span class="input-label">Account Status</span>
                    <span class="status-pill"><div class="status-dot"></div> Active</span>
                </div>
                <button class="btn-secondary" style="width: 100%;">
                    Edit Profile
                </button>
            </div>
        </div>

        <div style="flex: 1; min-width: 350px; display: flex; flex-direction: column;">
            <p class="sg-section-label" style="margin-bottom: 1.5rem;">The adventure that awaits...</p>
            
            <?php if (empty($upcomingTrips)): ?>
                <div class="glass-clear" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                    No upcoming journeys scheduled. Head to the packages page to book your next adventure.
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                <?php foreach ($upcomingTrips as $trip): ?>
                
                <div class="glass-active" style="display: flex; flex-direction: column;">
                    <div style="padding: 2.5rem; border-bottom: 2px dashed rgba(0, 166, 199, 0.2);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                            <span class="badge badge-green">CONFIRMED</span>
                            <span style="font-family: var(--font-code); color: var(--text-muted); font-size: 0.9rem;">REF: <?= htmlspecialchars($trip['payment_reference'] ?? 'PENDING') ?></span>
                        </div>
                        
                        <h3 style="font-family: var(--font-display); font-size: 2.8rem; font-weight: 300; font-style: italic; color: var(--text-main); line-height: 1.1; margin-bottom: 0.5rem;">
                            <?= htmlspecialchars($trip['package_name']) ?>
                        </h3>
                        <p style="color: var(--text-soft); font-size: 1.1rem;"><?= htmlspecialchars($trip['duration_days']) ?> Days of Exploration</p>
                    </div>

                    <div style="padding: 2.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <div class="input-label" style="margin-bottom: 0.4rem;">DEPARTURE DATE</div>
                            <div style="font-size: 1.3rem; font-weight: 500; color: var(--text-main);"><?= htmlspecialchars($trip['travel_date']) ?></div>
                        </div>
                        <div>
                            <div class="input-label" style="margin-bottom: 0.4rem;">PARTY SIZE</div>
                            <div style="font-size: 1.3rem; font-weight: 500; color: var(--text-main);"><?= htmlspecialchars($trip['num_travellers']) ?> Travellers</div>
                        </div>
                    </div>

                    <div style="padding: 1.5rem 2.5rem; background: rgba(255, 255, 255, 0.4); display: flex; justify-content: space-between; align-items: center; border-radius: 0 0 var(--r-lg) var(--r-lg); gap: 1rem; overflow: hidden;">
                        
                        <div style="flex-grow: 1; min-width: 0;">
                            <?php if (!empty($trip['group_trip_id'])): ?>
                                <a href="/traveller/group?id=<?= htmlspecialchars($trip['group_trip_id']) ?>" 
                                   class="btn-primary" 
                                   style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; max-width: 100%;">
                                    🌐 Access Hub #<?= htmlspecialchars($trip['group_trip_id']) ?>
                                </a>
                            <?php else: ?>
                                <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.9rem; white-space: nowrap;">
                                    <div class="status-dot" style="background: var(--ocean);"></div> 
                                    Auto-Matching...
                                </div>
                            <?php endif; ?>
                        </div>

                        <button class="btn-danger" 
                                style="flex-shrink: 0; white-space: nowrap;"
                                data-booking="<?= htmlspecialchars($trip['booking_id']) ?>"
                                data-name="<?= htmlspecialchars($trip['package_name']) ?>"
                                onclick="openCancelModal(this)">
                            Cancel
                        </button>
                    </div>
                </div>

                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($hasPastTrips): ?>
        <div style="flex: 1; min-width: 350px; display: flex; flex-direction: column;">
            <p class="sg-section-label" style="margin-bottom: 1.5rem; color: var(--text-muted);">Past adventures</p>
            
            <div class="input-stack" style="overflow-y: auto; max-height: 700px; padding-right: 0.5rem;">
                
                <?php foreach ($pastTrips as $pastTrip): ?>
                <div class="glass-clear" style="padding: 1.8rem; transition: all 0.3s var(--ease);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                        <div>
                            <div style="font-family: var(--font-code); font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.4rem;">
                                <?= htmlspecialchars($pastTrip['travel_date']) ?>
                            </div>
                            <div style="font-family: var(--font-display); font-size: 1.5rem; font-style: italic; color: var(--text-main);">
                                <?= htmlspecialchars($pastTrip['package_name']) ?>
                            </div>
                        </div>
                        <span class="badge badge-blue">COMPLETED</span>
                    </div>
                    
                    <?php if (!empty($pastTrip['rating'])): ?>
                        <div style="background: rgba(255,255,255,0.3); padding: 1rem 1.2rem; border-radius: var(--r-md); border-left: 3px solid var(--ocean);">
                            <div style="color: #fbbf24; font-size: 1.2rem; margin-bottom: 0.5rem; letter-spacing: 2px;">
                                <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $pastTrip['rating'] ? '★' : '<span style="color: rgba(0,0,0,0.1);">★</span>';
                                    }
                                ?>
                            </div>
                            <p style="font-size: 0.9rem; color: var(--text-soft); font-style: italic; line-height: 1.5; margin: 0;">
                                "<?= htmlspecialchars($pastTrip['review_comment']) ?>"
                            </p>
                        </div>
                    <?php else: ?>
                        <button class="btn-primary" style="width: 100%;" 
                                data-booking="<?= htmlspecialchars($pastTrip['booking_id']) ?>" 
                                data-package="<?= htmlspecialchars($pastTrip['package_id']) ?>"
                                data-name="<?= htmlspecialchars($pastTrip['package_name']) ?>"
                                onclick="openReviewModal(this)">
                            Submit Review
                        </button>
                    <?php endif; ?>

                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px dashed var(--glass-border);">
                        <p style="font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-soft);">Rate the Agency for this trip:</p>
                        
                        <form action="/traveller/submit-agency-review" method="POST" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                            <input type="hidden" name="agency_id" value="<?= (int)$pastTrip['agency_id'] ?>">
                            <input type="hidden" name="booking_id" value="<?= (int)$pastTrip['booking_id'] ?>">
                            
                            <select name="rating" class="input-field" style="width: 140px; padding: 0.4rem; font-size: 0.9rem;" required>
                                <option value="" disabled selected>Select Rating...</option>
                                <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                <option value="4">⭐⭐⭐⭐ (4)</option>
                                <option value="3">⭐⭐⭐ (3)</option>
                                <option value="2">⭐⭐ (2)</option>
                                <option value="1">⭐ (1)</option>
                            </select>
                            
                            <input type="text" name="comment" class="input-field" placeholder="Leave a short comment..." style="flex-grow: 1; min-width: 200px; padding: 0.4rem; font-size: 0.9rem;">
                            
                            <button type="submit" class="btn-primary" style="padding: 0.4rem 1.2rem; font-size: 0.9rem;">Submit</button>
                        </form>
                    </div>

                </div>
                <?php endforeach; ?>

            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div id="reviewModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(11, 43, 51, 0.6); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div class="glass-heavy" style="padding: 3rem 2.5rem; border-radius: var(--r-xl); width: 100%; max-width: 500px; position: relative; animation: fadeUp 0.3s ease-out forwards;">
            
            <button onclick="closeReviewModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: transparent; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer;">&times;</button>
            
            <h3 style="font-family: var(--font-display); font-size: 2rem; font-style: italic; font-weight: 300; color: var(--text-main); margin-bottom: 0.5rem;">Mission Log</h3>
            <p style="color: var(--text-soft); font-size: 0.95rem; margin-bottom: 2rem;">Submitting telemetry for <strong id="modalTripName" style="color: var(--ocean);">[Trip]</strong></p>

            <form action="/traveller/submit-review" method="POST" class="input-stack">
                <input type="hidden" name="booking_id" id="modalBookingId">
                <input type="hidden" name="package_id" id="modalPackageId">

                <div class="input-group">
                    <label class="input-label">Rating (1-5)</label>
                    <select name="rating" class="input-field" required>
                        <option value="5">5 - Flawless Execution</option>
                        <option value="4">4 - Minor Anomalies</option>
                        <option value="3">3 - Acceptable</option>
                        <option value="2">2 - Suboptimal</option>
                        <option value="1">1 - Critical Failure</option>
                    </select>
                </div>

                <div class="input-group" style="margin-bottom: 1.5rem;">
                    <label class="input-label">Expedition Notes</label>
                    <textarea name="comment" class="input-field" rows="4" placeholder="Detail your experience..." required></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Transmit Log</button>
            </form>
        </div>
    </div>

    <div id="cancelModalOverlay" style="display: none; position: fixed; inset: 0; background: rgba(11, 43, 51, 0.6); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
        <div class="glass-heavy" style="padding: 3rem 2.5rem; border-radius: var(--r-xl); width: 100%; max-width: 450px; position: relative; animation: fadeUp 0.3s ease-out forwards; text-align: center;">
            
            <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(255, 59, 48, 0.1); color: #ff3b30; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.5rem;">
                ⚠️
            </div>
            
            <h3 style="font-family: var(--font-display); font-size: 2rem; font-style: italic; font-weight: 300; color: var(--text-main); margin-bottom: 0.5rem;">Cancel Adventure?</h3>
            <p style="color: var(--text-soft); font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.5;">Are you sure you wan to cancel your trip? <strong id="modalCancelTripName" style="color: var(--coral);">[Trip]</strong>? This sequence cannot be reversed.</p>

            <form action="/traveller/cancel-booking" method="POST" style="display: flex; gap: 1rem; flex-direction: column;">
                <input type="hidden" name="booking_id" id="modalCancelBookingId">
                <button type="submit" class="btn-danger" style="width: 100%; padding: 1rem; border: none; cursor: pointer; border-radius: var(--r-md); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Yes, Terminate Booking</button>
                <button type="button" class="btn-secondary" onclick="closeCancelModal()" style="width: 100%; padding: 1rem; border: none; cursor: pointer; border-radius: var(--r-md);">Go Back</button>
            </form>
        </div>
    </div>

    <script>
        function openReviewModal(button) {
            const bookingId = button.getAttribute('data-booking');
            const packageId = button.getAttribute('data-package');
            const tripName = button.getAttribute('data-name');

            document.getElementById('modalBookingId').value = bookingId;
            document.getElementById('modalPackageId').value = packageId;
            document.getElementById('modalTripName').innerText = tripName;

            document.getElementById('reviewModalOverlay').style.display = 'flex';
        }

        function closeReviewModal() {
            document.getElementById('reviewModalOverlay').style.display = 'none';
        }

        // Cancel Modal Logic
        function openCancelModal(button) {
            const bookingId = button.getAttribute('data-booking');
            const tripName = button.getAttribute('data-name');

            document.getElementById('modalCancelBookingId').value = bookingId;
            document.getElementById('modalCancelTripName').innerText = tripName;

            document.getElementById('cancelModalOverlay').style.display = 'flex';
        }

        function closeCancelModal() {
            document.getElementById('cancelModalOverlay').style.display = 'none';
        }
    </script>
</div>
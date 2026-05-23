<?php
// Safely extract group variables
$title = $groupDetails['package_name'] ?? 'Classified Expedition';
$destination = $groupDetails['destination'] ?? 'Unknown Vector';
$departureDate = $groupDetails['departure_date'] ?? 'TBA';
$returnDate = $groupDetails['return_date'] ?? 'TBA';
$currentParticipants = $groupDetails['current_participants'] ?? 0;
$maxParticipants = $groupDetails['max_participants'] ?? 0;
?>

<div class="sg-page" style="min-height: 100vh; padding: 100px 2rem 4rem; width: 100%; max-width: 1400px; margin: 0 auto;">

    <div style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 0.5rem;">
                <p class="sg-section-label" style="margin: 0;">Active Cluster</p>
                <span class="badge badge-blue">ID: #<?= str_pad($groupDetails['group_trip_id'], 4, '0', STR_PAD_LEFT) ?></span>
            </div>
            <h2 class="sg-section-title" style="margin-bottom: 0;"><?= htmlspecialchars($title) ?></h2>
            <p style="color: var(--text-soft); font-size: 1.1rem; margin-top: 0.5rem; font-family: var(--font-code);">
                Vector: <?= htmlspecialchars($destination) ?> | <?= htmlspecialchars($departureDate) ?> to <?= htmlspecialchars($returnDate) ?>
            </p>
        </div>
        
        <div style="text-align: right;">
            <div style="font-size: 2.5rem; color: var(--coral); font-family: var(--font-display); font-style: italic; line-height: 1;">
                <?= $currentParticipants ?> <span style="font-size: 1.2rem; color: var(--text-muted);">/ <?= $maxParticipants ?></span>
            </div>
            <div class="input-label" style="margin-top: 0.5rem;">Travellers Synced</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem;">
        
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <div class="glass-clear" style="padding: 2rem;">
                <h3 style="font-family: var(--font-display); font-style: italic; font-size: 1.5rem; color: var(--ocean); margin-bottom: 1.5rem;">The Roster</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php if (empty($roster)): ?>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Awaiting additional traveller synchronizations...</p>
                    <?php else: ?>
                        <?php foreach ($roster as $member): ?>
                            <div style="display: flex; align-items: center; gap: 1rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <div style="width: 45px; height: 45px; border-radius: 50%; background: var(--gradient-main); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 0 10px rgba(0, 166, 199, 0.2);">
                                    👤
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-main); font-size: 1rem;">
                                        <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                                    </div>
                                    <div style="color: var(--success); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-top: 0.2rem;">
                                        ✓ Confirmed
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="glass-frosted" style="padding: 2rem;">
                <h3 style="font-family: var(--font-display); font-style: italic; font-size: 1.5rem; color: var(--text-main); margin-bottom: 1.5rem;">Logistics</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1.2rem;">
                    <div>
                        <div class="input-label" style="font-size: 0.75rem;">Meeting Point</div>
                        <div style="color: var(--text-main); font-weight: 500;"><?= htmlspecialchars($groupDetails['meeting_point'] ?? 'TBA by Agency') ?></div>
                    </div>
                    <div>
                        <div class="input-label" style="font-size: 0.75rem;">Status</div>
                        <span class="badge badge-green" style="margin-top: 0.3rem; display: inline-block;">
                            <?= htmlspecialchars(strtoupper($groupDetails['status'] ?? 'OPEN')) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-heavy" style="display: flex; flex-direction: column; height: 700px; border-radius: var(--r-xl); overflow: hidden;">
            
            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--glass-border); background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-family: var(--font-code); font-size: 1.2rem; color: var(--text-main); letter-spacing: 2px; margin: 0;">TRANSMISSION_LOG</h3>
                <div class="status-dot" style="background: var(--success); box-shadow: 0 0 8px var(--success);"></div>
            </div>

            <div id="chat-messages" style="flex: 1; padding: 2rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1.5rem;">
                
                <div style="text-align: center; margin-bottom: 1rem;">
                    <span style="background: rgba(255,255,255,0.05); padding: 0.3rem 1rem; border-radius: 999px; font-family: var(--font-code); font-size: 0.75rem; color: var(--text-muted);">
                        Secure channel established. End-to-end encryption active.
                    </span>
                </div>

                <div style="display: flex; gap: 1rem; max-width: 80%;">
                    <div style="width: 35px; height: 35px; border-radius: 50%; background: rgba(0, 166, 199, 0.2); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0;">
                        👤
                    </div>
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 1rem 1.2rem; border-radius: 0 var(--r-md) var(--r-md) var(--r-md);">
                        <div style="font-size: 0.8rem; color: var(--ocean); margin-bottom: 0.4rem; font-weight: 600;">Sarah Johnson</div>
                        <div style="color: var(--text-main); line-height: 1.5; font-size: 0.95rem;">
                            Transmission test: Is anyone else packing heavy winter gear for this expedition?
                        </div>
                        <div style="font-family: var(--font-code); font-size: 0.7rem; color: var(--text-muted); margin-top: 0.5rem; text-align: right;">
                            10:42 AM
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; max-width: 80%; align-self: flex-end; flex-direction: row-reverse;">
                    <div style="background: rgba(255, 111, 97, 0.1); border: 1px solid rgba(255, 111, 97, 0.2); padding: 1rem 1.2rem; border-radius: var(--r-md) 0 var(--r-md) var(--r-md);">
                        <div style="color: var(--text-main); line-height: 1.5; font-size: 0.95rem;">
                            I'm bringing a heavy coat just in case. Better safe than freezing!
                        </div>
                        <div style="font-family: var(--font-code); font-size: 0.7rem; color: var(--text-muted); margin-top: 0.5rem; text-align: right;">
                            10:45 AM
                        </div>
                    </div>
                </div>

            </div>

            <div style="padding: 1.5rem 2rem; border-top: 1px solid var(--glass-border); background: rgba(0,0,0,0.2);">
                <form id="chat-form" style="display: flex; gap: 1rem;">
                    <input type="text" id="chat-input" placeholder="Broadcast to cluster..." style="flex: 1; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: var(--r-pill); padding: 1rem 1.5rem; color: var(--text-main); outline: none; font-family: var(--font-body); font-size: 1rem;">
                    <button type="submit" class="btn-primary" style="border-radius: var(--r-pill); padding: 0 2rem; white-space: nowrap;">
                        Send ↗
                    </button>
                </form>
            </div>
            
        </div>
    </div>
</div>
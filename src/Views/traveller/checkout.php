<?php
$basePrice = $package['price'] ?? 0;
$taxRate = 0.06;
$taxes = $basePrice * $taxRate;
$initialTotal = $basePrice + $taxes;

if ($traveller) {
    $userName = htmlspecialchars($traveller['first_name'] . ' ' . $traveller['last_name']);
    $userEmail = htmlspecialchars($traveller['email']);
} else {
    $userName = 'Verified Traveller';
    $userEmail = 'traveller@tripistry.com';
}
?>
<div class="sg-page">
  <section class="sg-section" style="padding-top: 2rem;">
    
    <div style="max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 1.5fr 1fr; gap: 3rem;">
      
      <div>
        <div class="demo-cell-label">Secure Checkout</div>
        <h2 class="sg-section-title" style="font-size: 2.5rem; margin-bottom: 2rem;">Finalize Journey</h2>
        
        <form action="/traveller/process_booking" method="POST" id="checkout-form" class="input-stack">
          <input type="hidden" name="package_id" value="<?= htmlspecialchars($package['id'] ?? '') ?>">

          <div class="demo-cell glass-clear" style="padding: 2rem; margin-bottom: 1.5rem;">
            <h3 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; color: var(--ocean);">1. Traveller Profile</h3>
            
            <div style="display: flex; gap: 1.5rem; align-items: center; padding: 1.5rem; background: rgba(255,255,255,0.03); border-radius: var(--r-md); border: 1px solid var(--glass-border);">
               <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--coral), #ff8a66); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 0 15px rgba(255, 111, 97, 0.3);">
                   👤
               </div>
               <div>
                  <div style="font-weight: 600; color: var(--text-main); font-size: 1.1rem; letter-spacing: 0.02em;"><?= htmlspecialchars($userName) ?></div>
                  <div style="color: var(--text-soft); font-size: 0.9rem; margin-top: 0.2rem;"><?= htmlspecialchars($userEmail) ?></div>
               </div>
               <div style="margin-left: auto;">
                   <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.4rem 0.8rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">✓ Authenticated</span>
               </div>
            </div>
          </div>

          <div class="demo-cell glass-clear" style="padding: 2rem; margin-bottom: 1.5rem;">
            <h3 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; color: var(--ocean);">2. Travel Details</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
              <div class="input-group">
                <label class="input-label">Departure Date</label>
                <input type="date" name="travel_date" class="input-field" required>
              </div>
              <div class="input-group">
                <label class="input-label">Party Size</label>
                <input type="number" name="party_size" id="party-size" class="input-field" min="1" max="<?= htmlspecialchars($package['max_capacity'] ?? 10) ?>" value="1" required>
              </div>
            </div>

            <div class="input-group">
              <label class="input-label">Special Requests (Optional)</label>
              <textarea name="requests" class="input-field" rows="3" placeholder="Dietary requirements, accessibility needs, etc."></textarea>
            </div>
          </div>

          <div class="demo-cell glass-clear" style="padding: 2rem;">
            <h3 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; color: var(--ocean);">3. Payment Method</h3>
            
            <div class="input-group" style="margin-bottom: 1.5rem;">
              <label class="input-label">Name on Card</label>
              <input type="text" name="card_name" class="input-field" value="<?= htmlspecialchars($userName) ?>" required>
            </div>

            <div class="input-group" style="margin-bottom: 1.5rem;">
              <label class="input-label">Card Number</label>
              <input type="text" name="card_number" class="input-field" placeholder="0000 0000 0000 0000" maxlength="19" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
              <div class="input-group">
                <label class="input-label">Expiry Date</label>
                <input type="text" name="card_expiry" class="input-field" placeholder="MM/YY" maxlength="5" required>
              </div>
              <div class="input-group">
                <label class="input-label">CVV</label>
                <input type="password" name="card_cvv" class="input-field" placeholder="123" maxlength="3" required>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-primary" style="width: 100%; margin-top: 2rem; font-size: 1.1rem; padding: 1rem; border: none; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Complete Booking</button>
        </form>
      </div>

      <div style="position: sticky; top: 100px; align-self: start;">
        <div class="pkg-card glass-frosted" style="cursor: default; transform: none;">
          
          <div class="pkg-card-img" style="<?= !empty($package['image_url']) ? "background-image: url('" . htmlspecialchars($package['image_url']) . "'); background-size: cover; background-position: center;" : "" ?>">
             <?php if (empty($package['image_url'])): ?>
                 ✈️
             <?php endif; ?>
          </div>
          
          <div class="pkg-card-body">
            <div class="demo-cell-label" style="margin-bottom: 1rem;">Order Summary</div>
            
            <div class="pkg-card-dest"><?= htmlspecialchars($package['destination'] ?? 'Global Exploration') ?></div>
            <div class="pkg-card-name" style="font-size: 1.4rem;"><?= htmlspecialchars($package['title'] ?? 'Unknown Journey') ?></div>
            
            <hr style="border: 0; height: 1px; background: rgba(0,0,0,0.1); margin: 1.5rem 0;">
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--text-soft);">
              <span>Price per person</span>
              <span>R <?= number_format($basePrice, 2) ?></span>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--text-soft);">
              <span>Party Multiplier</span>
              <span id="summary-multiplier">x 1</span>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; color: var(--text-soft);">
              <span>Taxes & Fees (6%)</span>
              <span id="summary-taxes">R <?= number_format($taxes, 2) ?></span>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 2px dashed rgba(0,0,0,0.1);">
              <span style="font-weight: 600; color: var(--text-main);">Total</span>
              <span class="pkg-card-price" id="summary-total" style="font-size: 1.8rem; color: var(--ocean);">R <?= number_format($initialTotal, 2) ?></span>
            </div>
            
            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.8rem; color: var(--text-muted);">
              🔒 Secure 256-bit SSL Encryption
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const partyInput = document.getElementById('party-size');
    const multiplierDisplay = document.getElementById('summary-multiplier');
    const taxesDisplay = document.getElementById('summary-taxes');
    const totalDisplay = document.getElementById('summary-total');
    
    // Inject PHP values directly into the JS variables
    const basePrice = <?= json_encode($basePrice) ?>;
    const taxRate = <?= json_encode($taxRate) ?>;

    // Currency formatter
    const formatCurrency = (amount) => {
        return 'R ' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    partyInput.addEventListener('input', (e) => {
        let size = parseInt(e.target.value);
        if (isNaN(size) || size < 1) size = 1;
        
        const subtotal = basePrice * size;
        const currentTaxes = subtotal * taxRate;
        const currentTotal = subtotal + currentTaxes;

        // Update the UI
        multiplierDisplay.textContent = 'x ' + size;
        taxesDisplay.textContent = formatCurrency(currentTaxes);
        totalDisplay.textContent = formatCurrency(currentTotal);
    });
});
</script>
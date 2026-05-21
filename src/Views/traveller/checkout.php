<div class="sg-page">
  <section class="sg-section" style="padding-top: 2rem;">
    
    <div style="max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 1.5fr 1fr; gap: 3rem;">
      
      <div>
        <div class="demo-cell-label">Secure Checkout</div>
        <h2 class="sg-section-title" style="font-size: 2.5rem; margin-bottom: 2rem;">Finalize Journey</h2>
        
        <form action="/traveller/process_booking" method="POST" id="validate-me" class="input-stack">
          <input type="hidden" name="package_id" value="">

          <div class="demo-cell glass-clear" style="padding: 2rem; margin-bottom: 1.5rem;">
            <h3 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; color: var(--ocean);">1. Travel Details</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
              <div class="input-group">
                <label class="input-label">Departure Date</label>
                <input type="date" name="travel_date" class="input-field" required>
              </div>
              <div class="input-group">
                <label class="input-label">Party Size</label>
                <input type="number" name="party_size" class="input-field" min="1" value="1" required>
              </div>
            </div>

            <div class="input-group">
              <label class="input-label">Special Requests (Optional)</label>
              <textarea name="requests" class="input-field" rows="3" placeholder="Dietary requirements, accessibility needs, etc."></textarea>
            </div>
          </div>

          <div class="demo-cell glass-clear" style="padding: 2rem;">
            <h3 style="font-family: var(--font-display); font-style: italic; margin-bottom: 1.5rem; color: var(--ocean);">2. Payment Method</h3>
            
            <div class="input-group" style="margin-bottom: 1.5rem;">
              <label class="input-label">Name on Card</label>
              <input type="text" class="input-field" placeholder="John Doe" required>
            </div>

            <div class="input-group" style="margin-bottom: 1.5rem;">
              <label class="input-label">Card Number</label>
              <input type="text" class="input-field" placeholder="0000 0000 0000 0000" maxlength="19" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
              <div class="input-group">
                <label class="input-label">Expiry Date</label>
                <input type="text" class="input-field" placeholder="MM/YY" maxlength="5" required>
              </div>
              <div class="input-group">
                <label class="input-label">CVV</label>
                <input type="password" class="input-field" placeholder="123" maxlength="3" required>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-primary" style="width: 100%; margin-top: 2rem; font-size: 1.1rem; padding: 1rem;">Complete Booking</button>
        </form>
      </div>

      <div style="position: sticky; top: 100px; align-self: start;">
        <div class="pkg-card glass-frosted" style="cursor: default; transform: none;">
          <div class="pkg-card-img">
            🌊 
          </div>
          
          <div class="pkg-card-body">
            <div class="demo-cell-label" style="margin-bottom: 1rem;">Order Summary</div>
            
            <div class="pkg-card-dest">Bali, Indonesia</div>
            <div class="pkg-card-name" style="font-size: 1.4rem;">Jungle Retreat</div>
            
            <hr style="border: 0; height: 1px; background: rgba(0,0,0,0.1); margin: 1.5rem 0;">
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--text-soft);">
              <span>Price per person</span>
              <span>R 18,500</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; color: var(--text-soft);">
              <span>Taxes & Fees</span>
              <span>R 1,200</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 2px dashed rgba(0,0,0,0.1);">
              <span style="font-weight: 600; color: var(--text-main);">Total</span>
              <span class="pkg-card-price" style="font-size: 1.8rem; color: var(--ocean);">R 19,700</span>
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
<div class="sg-page">
  <section class="sg-section">
    <div class="demo-cell" style="max-width: 600px; margin: 0 auto;">
      <div class="demo-cell-label">Complete Your Booking</div>
      <h2 class="sg-section-title" style="font-size: 2rem;">Finalize Journey</h2>
      
      <form action="index.php?action=process_booking" method="POST" id="validate-me" class="input-stack">
        
        <input type="hidden" name="package_id" value="">
        
        <div class="input-group">
          <label class="input-label">Travel Date</label>
          <input type="date" name="travel_date" class="input-field" required>
        </div>
        
        <div class="input-group">
          <label class="input-label">Party Size</label>
          <input type="number" name="party_size" class="input-field" min="1" value="1" required>
          <div class="input-hint">Number of people travelling</div>
        </div>
        
        <button type="submit" class="btn-primary" style="margin-top: 1.5rem;">Confirm Booking</button>
      </form>
    </div>
  </section>
</div>
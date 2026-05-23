<div class="dashboard-container">
    <h1>Raw Data Management</h1>
    <p>Upload new inventory to the global Tripistry database.</p>

    <section class="upload-section">
        <h2>Add New Flight</h2>
        <form action="/manage-data" method="POST">
            <input type="hidden" name="upload_type" value="flight">

            <div class="form-group">
                <label>Airline Name</label>
                <input type="text" name="airline_name" required>
            </div>

            <div class="form-group">
                <label>Flight Number</label>
                <input type="text" name="flight_number" required>
            </div>

            <div class="form-group">
                <label>Departure Airport (IATA Code)</label>
                <input type="text" name="departure_airport" maxlength="10" placeholder="e.g., JNB" required>
            </div>

            <div class="form-group">
                <label>Arrival Airport (IATA Code)</label>
                <input type="text" name="arrival_airport" maxlength="10" placeholder="e.g., CPT" required>
            </div>

            <div class="form-group">
                <label>Departure Location ID</label>
                <input type="number" name="departure_location_id" required>
            </div>

            <div class="form-group">
                <label>Arrival Location ID</label>
                <input type="number" name="arrival_location_id" required>
            </div>

            <div class="form-group">
                <label>Flight Class</label>
                <select name="flight_class" required>
                    <option value="economy">Economy</option>
                    <option value="business">Business</option>
                    <option value="first">First Class</option>
                </select>
            </div>

            <div class="form-group">
                <label>Duration (Minutes)</label>
                <input type="number" name="duration_minutes" min="1" required>
            </div>

            <div class="form-group">
                <label>Base Price (ZAR)</label>
                <input type="number" name="base_price" step="0.01" min="0" required>
            </div>

            <button type="submit">Upload Flight</button>
        </form>
    </section>

    <hr>

    <section class="upload-section">
        <h2>Add New Accommodation</h2>
        <form action="/manage-data" method="POST">
            <input type="hidden" name="upload_type" value="accommodation">

            <div class="form-group">
                <label>Accommodation Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Location ID</label>
                <input type="number" name="location_id" required>
            </div>

            <div class="form-group">
                <label>Property Type</label>
                <select name="type" required>
                    <option value="hotel">Hotel</option>
                    <option value="guesthouse">Guesthouse</option>
                    <option value="resort">Resort</option>
                    <option value="hostel">Hostel</option>
                    <option value="apartment">Apartment</option>
                    <option value="villa">Villa</option>
                </select>
            </div>

            <div class="form-group">
                <label>Star Rating (1-5)</label>
                <input type="number" name="star_rating" min="1" max="5">
            </div>

            <div class="form-group">
                <label>Price Per Night (ZAR)</label>
                <input type="number" name="price_per_night" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address">
            </div>

            <button type="submit">Upload Accommodation</button>
        </form>
    </section>
</div>
# Tripistry

Tripistry is a PHP MVC travel booking application for browsing packages, confirming bookings, and viewing trip intel on the traveller dashboard.

## What It Does
- Browse curated travel packages from the landing page and traveller views.
- Book a package and generate trip intel containing an itinerary, required gear, and local protocols.
- Fall back to a local Cape Town-aware itinerary if the Gemini request fails or returns unusable data.
- Manage traveller and agency dashboards, reviews, favourites, and group trips.

## Requirements
- PHP 7.4+ with PDO
- MySQL or MariaDB
- A web server such as Apache/Nginx, or the PHP built-in server for local testing

`mbstring` is optional. The current booking fallback code works even when it is not installed.

## Configuration
1. Set up the database connection in `config/database.php`.
2. Provide your Gemini key through the `GEMINI_API_KEY` environment variable.
3. If you want to use a `.env` file, you must load it yourself first. PHP does not read `.env` files automatically.

Example shell session:

```bash
export GEMINI_API_KEY="your_real_key_here"
```

## Database Setup
Import the provided schema or sample data into your MySQL database:

```bash
mysql -u youruser -p yourdatabase < Tripistry_dump.sql
# or
mysql -u youruser -p yourdatabase < PopulateData.sql
```

## Run Locally
From the project root, start the PHP built-in server from the `public/` folder:

```bash
cd public
php -S localhost:8000
```

Then open `http://localhost:8000` in your browser.

If you are using Apache or Nginx, point the document root to `public/` so `public/index.php` acts as the front controller.

## Booking And AI Flow
When a traveller confirms a booking, `src/Controllers/BookingController.php`:
- creates the booking,
- asks Gemini for trip intel,
- stores the result in `bookings.prep_notes`,
- falls back to a locally generated itinerary if the API call fails.

The traveller dashboard reads `prep_notes` and displays:
- Trip Itinerary
- Required Gear
- Local Protocols

## Troubleshooting
- If the API key was leaked or revoked, Gemini requests will fail with `403` and the app will use the fallback itinerary.
- If the dashboard shows no trip intel, confirm that `prep_notes` is being written for the booking and that the traveller is viewing the booking on the dashboard.
- If a booking cancellation message appears and you do not want it, the dashboard no longer shows the cancelled-booking alert.

## Full Folder Structure
```
PopulateData.sql
README.md
StyleGuide.html
Tripistry_dump.sql
config/
    database.php
    secrets.php
logs/
public/
    index.php
    test.php
    assets/
        icons/
            building.png:Zone.Identifier
            global-icon.png:Zone.Identifier
    css/
        StyleGuide.css
    images/
        SunsetImage.jpg:Zone.Identifier
    js/
src/
    Controllers/
        AgencyController.php
        AuthController.php
        BookingController.php
        DataController.php
        HomeController.php
        RegistrationController.php
        TravellerController.php
    Models/
        AgencyModel.php
        BookingModel.php
        FavouriteModel.php
        GroupModel.php
        PackageModel.php
        ReviewModel.php
        UserModel.php
    Views/
        Landing.php
        layout.php
        loading.php
        agency/
            create_package.html
            create_package.php
            dashboard.php
            edit_package.php
            group_management.php
            manage_data.php
        auth/
            login.php
            register.php
        traveller/
            accommodations.php
            attractions.php
            checkout.php
            compare.php
            dashboard.php
            destinations.php
            details.php
            flights.php
            forYou.php
            group.php
            packages.php
            restaurants.php
```

## Notes For Markers
- The landing page and layout use custom CSS in `public/css/StyleGuide.css` and page-scoped styles in `src/Views/Landing.php`.
- AI output is intentionally resilient: if Gemini is unavailable, the booking still succeeds and saves a fallback itinerary.
- The dashboard itinerary display has been simplified to match the style used for Required Gear.

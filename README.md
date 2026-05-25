# Tripistry

Tripistry is a lightweight PHP MVC web application for browsing and booking curated travel packages. This repository contains the backend controllers, models, views, and public assets for a demo travel marketplace.

## Quick Overview
- PHP-based MVC pattern
- Views under `src/Views`, controllers under `src/Controllers`, models under `src/Models`
- Public webroot: `public/` (contains `index.php`, assets, css, js)
- Database schema and sample data: `PopulateData.sql`, `Tripistry_dump.sql`

## Prerequisites
- PHP 7.4+ with PDO and common extensions (mbstring, json, gd)
- MySQL / MariaDB
- PHP built-in server for local testing

## Setup (Local)
1. Copy configuration templates:

   - Edit `config/database.php` to set your DB connection.
   - Store secrets in `config/secrets.php` (this file may be environment-specific and is not committed).

2. Import the database schema and sample data:

```bash
mysql -u youruser -p yourdatabase < Tripistry_dump.sql
# or
mysql -u youruser -p yourdatabase < PopulateData.sql
```

3. Ensure the `public/` directory is your web root. For quick local testing you can run:

```bash
cd public
php -S localhost:8000 -t public/       
```

## Build & Execute
1. Install prerequisites on your system (example for Debian/Ubuntu):

```bash
sudo apt update
sudo apt install -y php php-mbstring php-xml php-gd php-pdo php-mysql mysql-client mysql-server
```
2. Configure the application database connection:

- Edit `config/database.php` and set the DSN/credentials to point to your local MySQL instance.
- If `config/secrets.php` exists in the repo, check it for any environment-specific overrides.

3. Create the database and import sample data:

```bash
mysql -u root -p -e "CREATE DATABASE tripistry;"
mysql -u root -p tripistry < Tripistry_dump.sql
```

4. Serve the app using PHP built-in server (quick test):
```bash
cd /path/to/Tripistry/public
php -S localhost:8000
```
Open `http://localhost:8000` in a browser. The homepage (`/`) should render the landing page.

5. Alternative: configure your local Apache/Nginx to use the `public/` folder as the document root and enable rewrites so `public/index.php` is the front controller.

6. Quick validation commands

```bash
php -l src/Views/Landing.php

php -S localhost:8000
```

## File Structure 
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
# Tripistry Project

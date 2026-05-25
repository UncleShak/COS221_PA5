<?php
// src/Models/BrowseModel.php
// Read-only SELECT queries for the five entity browsers.
// Used by TravellerController::browse()

class BrowseModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getDestinations(): array {
        $stmt = $this->db->query("
            SELECT
                d.destination_id,
                d.name,
                d.description,
                d.image_url,
                d.best_season,
                d.average_temperature_celsius,
                l.city,
                l.country
            FROM Destinations d
            JOIN Locations l ON d.location_id = l.location_id
            ORDER BY l.country, d.name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFlights(): array {
        $stmt = $this->db->query("
            SELECT
                f.flight_id,
                f.airline_name,
                f.flight_number,
                f.departure_airport,
                f.arrival_airport,
                f.departure_time,
                f.arrival_time,
                f.duration_minutes,
                f.flight_class,
                f.base_price,
                dep.city AS departure_city,
                dep.country AS departure_country,
                arr.city AS arrival_city,
                arr.country AS arrival_country
            FROM Flights f
            JOIN Locations dep ON f.departure_location_id = dep.location_id
            JOIN Locations arr ON f.arrival_location_id = arr.location_id
            ORDER BY f.airline_name, f.departure_airport
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAccommodations(): array {
        $stmt = $this->db->query("
            SELECT
                a.accommodation_id,
                a.name,
                a.type,
                a.star_rating,
                a.description,
                a.image_url,
                a.price_per_night,
                a.address,
                l.city,
                l.country,
                GROUP_CONCAT(am.amenity ORDER BY am.amenity SEPARATOR ', ')
                    AS amenities
            FROM Accommodations a
            JOIN Locations l ON a.location_id = l.location_id
            LEFT JOIN AccommodationAmenities am
                ON a.accommodation_id = am.accommodation_id
            GROUP BY a.accommodation_id
            ORDER BY l.city, a.star_rating DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttractions(): array {
        $stmt = $this->db->query("
            SELECT
                at.attraction_id,
                at.name,
                at.category,
                at.description,
                at.image_url,
                at.entry_fee,
                at.opening_hours,
                l.city,
                l.country
            FROM Attractions at
            JOIN Locations l ON at.location_id = l.location_id
            ORDER BY l.city, at.name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRestaurants(): array {
        $stmt = $this->db->query("
            SELECT
                r.restaurant_id,
                r.name,
                r.cuisine_type,
                r.price_range,
                r.description,
                r.image_url,
                r.address,
                r.average_rating,
                l.city,
                l.country
            FROM Restaurants r
            JOIN Locations l ON r.location_id = l.location_id
            ORDER BY l.city, r.name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
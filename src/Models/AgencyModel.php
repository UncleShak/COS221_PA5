<?php
// src/Models/AgencyModel.php
// Handles all database queries for the Agency role.
// All queries are scoped to the authenticated agency's own data.

class AgencyModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // ---------------------------------------------------------------
    // AGENCY PROFILE
    // ---------------------------------------------------------------

    /**
     * Fetch agency profile (joined with Users table).
     * Agencies PK is user_id (not agency_id); column is website_url and is_verified.
     */
    public function getAgencyProfile(int $agencyId): array|false
    {
        $stmt = $this->db->prepare('
            SELECT a.user_id,
                   a.agency_name,
                   a.description,
                   a.logo_url,
                   a.website_url,
                   a.is_verified,
                   u.email,
                   u.created_at
            FROM   Agencies a
            JOIN   Users u ON u.user_id = a.user_id
            WHERE  a.user_id = :id
        ');
        $stmt->execute([':id' => $agencyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ---------------------------------------------------------------
    // DASHBOARD STATS
    // ---------------------------------------------------------------

    /**
     * Returns aggregate statistics for the agency dashboard.
     * Status ENUM uses 'active' not 'published'.
     */
    public function getDashboardStats(int $agencyId): array
    {
        $stmtPkg = $this->db->prepare('
            SELECT COUNT(*) AS total_packages,
                   SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) AS published,
                   SUM(CASE WHEN status = "draft"  THEN 1 ELSE 0 END) AS drafts
            FROM   Packages
            WHERE  agency_id = :id
        ');
        $stmtPkg->execute([':id' => $agencyId]);
        $pkgStats = $stmtPkg->fetch(PDO::FETCH_ASSOC);

        $stmtBook = $this->db->prepare('
            SELECT COUNT(*) AS total_bookings,
                   COALESCE(SUM(total_price), 0) AS total_revenue
            FROM   Bookings b
            JOIN   Packages p ON p.package_id = b.package_id
            WHERE  p.agency_id = :id
              AND  b.status != "cancelled"
        ');
        $stmtBook->execute([':id' => $agencyId]);
        $bookStats = $stmtBook->fetch(PDO::FETCH_ASSOC);

        $stmtRating = $this->db->prepare('
            SELECT ROUND(AVG(rating), 1) AS avg_rating,
                   COUNT(*)             AS review_count
            FROM   AgencyReviews
            WHERE  agency_id = :id
        ');
        $stmtRating->execute([':id' => $agencyId]);
        $ratingStats = $stmtRating->fetch(PDO::FETCH_ASSOC);

        return array_merge($pkgStats, $bookStats, $ratingStats);
    }

    // ---------------------------------------------------------------
    // PACKAGES — READ
    // ---------------------------------------------------------------

    /**
     * List all packages owned by this agency (summary for dashboard table).
     */
    public function getPackagesByAgency(int $agencyId): array
    {
        $stmt = $this->db->prepare('
            SELECT p.package_id,
                   p.title,
                   p.base_price,
                   p.status,
                   p.average_rating,
                   p.duration_days,
                   p.updated_at,
                   (SELECT COUNT(*)
                    FROM   Bookings b
                    WHERE  b.package_id = p.package_id
                      AND  b.status != "cancelled") AS booking_count,
                   (SELECT 1
                    FROM   GroupTrips gt
                    WHERE  gt.package_id = p.package_id
                    LIMIT  1)                       AS is_group_trip
            FROM   Packages p
            WHERE  p.agency_id = :id
            ORDER BY p.updated_at DESC
        ');
        $stmt->execute([':id' => $agencyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single package with ownership check.
     * Returns false if not found or not owned by this agency.
     */
    public function getPackageById(int $packageId, int $agencyId): array|false
    {
        $stmt = $this->db->prepare('
            SELECT * FROM Packages
            WHERE  package_id = :pkg_id
              AND  agency_id  = :agency_id
        ');
        $stmt->execute([':pkg_id' => $packageId, ':agency_id' => $agencyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ---------------------------------------------------------------
    // PACKAGES — CREATE / UPDATE / DELETE
    // ---------------------------------------------------------------

    /**
     * Insert a new package. Returns the new package_id.
     * Column is max_capacity (not max_participants) in the Packages table.
     */
    public function createPackage(int $agencyId, array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO Packages
                (agency_id, title, description, base_price, duration_days,
                 max_capacity, status, cover_image_url)
            VALUES
                (:agency_id, :title, :description, :base_price, :duration_days,
                 :max_capacity, :status, :cover_image_url)
        ');
        $stmt->execute([
            ':agency_id'       => $agencyId,
            ':title'           => $data['title'],
            ':description'     => $data['description'],
            ':base_price'      => $data['base_price'],
            ':duration_days'   => $data['duration_days'],
            ':max_capacity'    => $data['max_capacity'] ?? null,
            ':status'          => $data['status'] ?? 'draft',
            ':cover_image_url' => $data['cover_image_url'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing package. Ownership is verified via agency_id.
     * Returns affected row count (0 = not found / not owned).
     */
    public function updatePackage(int $packageId, int $agencyId, array $data): int
    {
        $stmt = $this->db->prepare('
            UPDATE Packages
            SET    title            = :title,
                   description      = :description,
                   base_price       = :base_price,
                   duration_days    = :duration_days,
                   max_capacity     = :max_capacity,
                   status           = :status,
                   cover_image_url  = :cover_image_url
            WHERE  package_id = :pkg_id
              AND  agency_id  = :agency_id
        ');
        $stmt->execute([
            ':title'           => $data['title'],
            ':description'     => $data['description'],
            ':base_price'      => $data['base_price'],
            ':duration_days'   => $data['duration_days'],
            ':max_capacity'    => $data['max_capacity'] ?? null,
            ':status'          => $data['status'] ?? 'draft',
            ':cover_image_url' => $data['cover_image_url'] ?? null,
            ':pkg_id'          => $packageId,
            ':agency_id'       => $agencyId,
        ]);
        return $stmt->rowCount();
    }

    /**
     * Soft-delete: set status to 'archived'. Ownership enforced.
     */
    public function archivePackage(int $packageId, int $agencyId): int
    {
        $stmt = $this->db->prepare('
            UPDATE Packages
            SET    status = "archived"
            WHERE  package_id = :pkg_id
              AND  agency_id  = :agency_id
        ');
        $stmt->execute([':pkg_id' => $packageId, ':agency_id' => $agencyId]);
        return $stmt->rowCount();
    }

    // ---------------------------------------------------------------
    // JUNCTION TABLES — link/unlink components to a package
    // ---------------------------------------------------------------

    public function clearPackageDestinations(int $packageId): void
    {
        $this->db->prepare('DELETE FROM PackageDestinations WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    public function linkDestinations(int $packageId, array $destinationIds): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO PackageDestinations (package_id, destination_id)
            VALUES (:pkg, :dest)
        ');
        foreach ($destinationIds as $destId) {
            $stmt->execute([':pkg' => $packageId, ':dest' => (int) $destId]);
        }
    }

    public function clearPackageFlights(int $packageId): void
    {
        $this->db->prepare('DELETE FROM PackageFlights WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    public function linkFlights(int $packageId, array $flightIds): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO PackageFlights (package_id, flight_id) VALUES (:pkg, :flt)
        ');
        foreach ($flightIds as $id) {
            $stmt->execute([':pkg' => $packageId, ':flt' => (int) $id]);
        }
    }

    public function clearPackageAccommodations(int $packageId): void
    {
        $this->db->prepare('DELETE FROM PackageAccommodations WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    public function linkAccommodations(int $packageId, array $accomIds): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO PackageAccommodations (package_id, accommodation_id) VALUES (:pkg, :acc)
        ');
        foreach ($accomIds as $id) {
            $stmt->execute([':pkg' => $packageId, ':acc' => (int) $id]);
        }
    }

    public function clearPackageAttractions(int $packageId): void
    {
        $this->db->prepare('DELETE FROM PackageAttractions WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    public function linkAttractions(int $packageId, array $attrIds): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO PackageAttractions (package_id, attraction_id) VALUES (:pkg, :att)
        ');
        foreach ($attrIds as $id) {
            $stmt->execute([':pkg' => $packageId, ':att' => (int) $id]);
        }
    }

    public function clearPackageRestaurants(int $packageId): void
    {
        $this->db->prepare('DELETE FROM PackageRestaurants WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    public function linkRestaurants(int $packageId, array $restIds): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO PackageRestaurants (package_id, restaurant_id) VALUES (:pkg, :res)
        ');
        foreach ($restIds as $id) {
            $stmt->execute([':pkg' => $packageId, ':res' => (int) $id]);
        }
    }

    // ---------------------------------------------------------------
    // GROUP TRIPS
    // ---------------------------------------------------------------

    public function getGroupTrip(int $packageId): array|false
    {
        $stmt = $this->db->prepare('
            SELECT * FROM GroupTrips WHERE package_id = :id LIMIT 1
        ');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Upsert group trip record.
     * GroupTrips has a composite PK (group_trip_id AUTO_INCREMENT, package_id),
     * so ON DUPLICATE KEY UPDATE would never trigger on re-edit — each INSERT
     * generates a new group_trip_id and never conflicts. We check for an existing
     * record first, then UPDATE or INSERT explicitly.
     * Also includes return_date which is NOT NULL in the schema.
     */
    public function upsertGroupTrip(int $packageId, array $data): void
    {
        $existing = $this->getGroupTrip($packageId);

        if ($existing) {
            $stmt = $this->db->prepare('
                UPDATE GroupTrips
                SET    departure_date   = :dep_date,
                       return_date      = :ret_date,
                       min_participants = :min_p,
                       max_participants = :max_p,
                       meeting_point    = :meet
                WHERE  package_id      = :pkg
            ');
        } else {
            $stmt = $this->db->prepare('
                INSERT INTO GroupTrips
                    (package_id, departure_date, return_date,
                     min_participants, max_participants, meeting_point)
                VALUES
                    (:pkg, :dep_date, :ret_date, :min_p, :max_p, :meet)
            ');
        }

        $stmt->execute([
            ':pkg'      => $packageId,
            ':dep_date' => $data['departure_date'],
            ':ret_date' => $data['return_date'],
            ':min_p'    => (int) $data['min_participants'],
            ':max_p'    => (int) $data['max_participants'],
            ':meet'     => $data['meeting_point'] ?? null,
        ]);
    }

    public function deleteGroupTrip(int $packageId): void
    {
        $this->db->prepare('DELETE FROM GroupTrips WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    // ---------------------------------------------------------------
    // AVAILABLE COMPONENTS (dropdowns / multi-selects for the form)
    // ---------------------------------------------------------------

    public function getAllDestinations(): array
    {
        return $this->db->query('
            SELECT d.destination_id, d.name,
                   l.city, l.country
            FROM   Destinations d
            JOIN   Locations l ON l.location_id = d.location_id
            ORDER BY l.country, d.name
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Correct column names: airline_name, base_price,
     * departure_location_id, arrival_location_id.
     */
    public function getAllFlights(): array
    {
        return $this->db->query('
            SELECT f.flight_id,
                   f.airline_name,
                   f.flight_number,
                   f.flight_class,
                   f.base_price,
                   f.departure_time,
                   f.arrival_time,
                   dep.city AS origin_city,
                   arr.city AS dest_city
            FROM   Flights f
            JOIN   Locations dep ON dep.location_id = f.departure_location_id
            JOIN   Locations arr ON arr.location_id = f.arrival_location_id
            ORDER BY f.airline_name, f.departure_time
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Correct column name: star_rating (not stars).
     */
    public function getAllAccommodations(): array
    {
        return $this->db->query('
            SELECT a.accommodation_id, a.name, a.type, a.star_rating,
                   a.price_per_night, l.city, l.country
            FROM   Accommodations a
            JOIN   Locations l ON l.location_id = a.location_id
            ORDER BY l.country, a.name
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAttractions(): array
    {
        return $this->db->query('
            SELECT a.attraction_id, a.name, a.category,
                   l.city, l.country
            FROM   Attractions a
            JOIN   Locations l ON l.location_id = a.location_id
            ORDER BY l.country, a.name
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRestaurants(): array
    {
        return $this->db->query('
            SELECT r.restaurant_id, r.name, r.cuisine_type,
                   l.city, l.country
            FROM   Restaurants r
            JOIN   Locations l ON l.location_id = r.location_id
            ORDER BY l.country, r.name
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

    // ---------------------------------------------------------------
    // SELECTED COMPONENTS for a package (used to pre-fill edit form)
    // ---------------------------------------------------------------

    public function getPackageDestinationIds(int $packageId): array
    {
        $stmt = $this->db->prepare('SELECT destination_id FROM PackageDestinations WHERE package_id = :id');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPackageFlightIds(int $packageId): array
    {
        $stmt = $this->db->prepare('SELECT flight_id FROM PackageFlights WHERE package_id = :id');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPackageAccommodationIds(int $packageId): array
    {
        $stmt = $this->db->prepare('SELECT accommodation_id FROM PackageAccommodations WHERE package_id = :id');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPackageAttractionIds(int $packageId): array
    {
        $stmt = $this->db->prepare('SELECT attraction_id FROM PackageAttractions WHERE package_id = :id');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPackageRestaurantIds(int $packageId): array
    {
        $stmt = $this->db->prepare('SELECT restaurant_id FROM PackageRestaurants WHERE package_id = :id');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // ---------------------------------------------------------------
    // RECENT BOOKINGS for dashboard activity feed
    // ---------------------------------------------------------------

    /**
     * b.created_at aliased as booking_date (schema has no booking_date column).
     * JOIN Travellers on t.user_id (Travellers PK), not t.traveller_id.
     */
    public function getRecentBookings(int $agencyId, int $limit = 5): array
    {
        $stmt = $this->db->prepare('
            SELECT b.booking_id,
                   b.created_at    AS booking_date,
                   b.total_price,
                   b.status,
                   p.title         AS package_title,
                   CONCAT(t.first_name, " ", t.last_name) AS traveller_name
            FROM   Bookings b
            JOIN   Packages   p ON p.package_id = b.package_id
            JOIN   Travellers t ON t.user_id    = b.traveller_id
            WHERE  p.agency_id = :id
            ORDER BY b.created_at DESC
            LIMIT  :lim
        ');
        $stmt->bindValue(':id',  $agencyId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

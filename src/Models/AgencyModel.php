<?php

class AgencyModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


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
            FROM   agencies a
            JOIN   users u ON u.user_id = a.user_id
            WHERE  a.user_id = :id
        ');
        $stmt->execute([':id' => $agencyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getDashboardStats(int $agencyId): array
    {
        $stmtPkg = $this->db->prepare('
            SELECT COUNT(*) AS total_packages,
                   SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) AS published,
                   SUM(CASE WHEN status = "draft"  THEN 1 ELSE 0 END) AS drafts
            FROM   packages
            WHERE  agency_id = :id
        ');
        $stmtPkg->execute([':id' => $agencyId]);
        $pkgStats = $stmtPkg->fetch(PDO::FETCH_ASSOC);

        $stmtBook = $this->db->prepare('
            SELECT COUNT(*) AS total_bookings,
                   COALESCE(SUM(total_price), 0) AS total_revenue
            FROM   bookings b
            JOIN   packages p ON p.package_id = b.package_id
            WHERE  p.agency_id = :id
              AND  b.status != "cancelled"
        ');
        $stmtBook->execute([':id' => $agencyId]);
        $bookStats = $stmtBook->fetch(PDO::FETCH_ASSOC);

        $stmtRating = $this->db->prepare('
            SELECT ROUND(AVG(pr.rating), 1) AS avg_rating,
                   COUNT(pr.rating)         AS review_count
            FROM   packagereviews pr
            JOIN   packages p ON p.package_id = pr.package_id
            WHERE  p.agency_id = :id
        ');
        $stmtRating->execute([':id' => $agencyId]);
        $ratingStats = $stmtRating->fetch(PDO::FETCH_ASSOC);

        $pkgStats = $pkgStats ?: ['total_packages' => 0, 'published' => 0, 'drafts' => 0];
        $bookStats = $bookStats ?: ['total_bookings' => 0, 'total_revenue' => 0];
        $ratingStats = $ratingStats ?: ['avg_rating' => null, 'review_count' => 0];

        return array_merge($pkgStats, $bookStats, $ratingStats);
    }


    public function getPackagesByAgency(int $agencyId): array
    {
        $stmt = $this->db->prepare('
            SELECT p.package_id,
                   p.title,
                   p.base_price,
                   p.status,
                   (SELECT ROUND(AVG(pr.rating), 1)
                    FROM   packagereviews pr
                    WHERE  pr.package_id = p.package_id) AS average_rating,
                   p.duration_days,
                   p.updated_at,
                   (SELECT COUNT(*)
                    FROM   bookings b
                    WHERE  b.package_id = p.package_id
                      AND  b.status != "cancelled") AS booking_count,
                   (SELECT 1
                    FROM   grouptrips gt
                    WHERE  gt.package_id = p.package_id
                    LIMIT  1)                       AS is_group_trip
            FROM   packages p
            WHERE  p.agency_id = :id
            ORDER BY p.updated_at DESC
        ');
        $stmt->execute([':id' => $agencyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPackageById(int $packageId, int $agencyId): array|false
    {
        $stmt = $this->db->prepare('
            SELECT * FROM packages
            WHERE  package_id = :pkg_id
              AND  agency_id  = :agency_id
        ');
        $stmt->execute([':pkg_id' => $packageId, ':agency_id' => $agencyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


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


    public function clearPackageDestinations(int $packageId): void
    {
        $this->db->prepare('DELETE FROM packagedestinations WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    public function linkDestinations(int $packageId, array $destinationIds): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO packagedestinations (package_id, destination_id)
            VALUES (:pkg, :dest)
        ');
        foreach ($destinationIds as $destId) {
            $stmt->execute([':pkg' => $packageId, ':dest' => (int) $destId]);
        }
    }

    public function clearPackageFlights(int $packageId): void
    {
        $this->db->prepare('DELETE FROM packageflights WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    public function linkFlights(int $packageId, array $flightIds): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO packageflights (package_id, flight_id) VALUES (:pkg, :flt)
        ');
        foreach ($flightIds as $id) {
            $stmt->execute([':pkg' => $packageId, ':flt' => (int) $id]);
        }
    }

    public function clearPackageAccommodations(int $packageId): void
    {
        $this->db->prepare('DELETE FROM packageaccommodations WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    public function linkAccommodations(int $packageId, array $accomIds): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO packageaccommodations (package_id, accommodation_id) VALUES (:pkg, :acc)
        ');
        foreach ($accomIds as $id) {
            $stmt->execute([':pkg' => $packageId, ':acc' => (int) $id]);
        }
    }

    public function clearPackageAttractions(int $packageId): void
    {
        $this->db->prepare('DELETE FROM packageattractions WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    public function linkAttractions(int $packageId, array $attrIds): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO packageattractions (package_id, attraction_id) VALUES (:pkg, :att)
        ');
        foreach ($attrIds as $id) {
            $stmt->execute([':pkg' => $packageId, ':att' => (int) $id]);
        }
    }

    public function clearPackageRestaurants(int $packageId): void
    {
        $this->db->prepare('DELETE FROM packagerestaurants WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }

    public function linkRestaurants(int $packageId, array $restIds): void
    {
        $stmt = $this->db->prepare('
            INSERT IGNORE INTO packagerestaurants (package_id, restaurant_id) VALUES (:pkg, :res)
        ');
        foreach ($restIds as $id) {
            $stmt->execute([':pkg' => $packageId, ':res' => (int) $id]);
        }
    }


    public function getGroupTrip(int $packageId): array|false
    {
        $stmt = $this->db->prepare('
            SELECT * FROM grouptrips WHERE package_id = :id LIMIT 1
        ');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function upsertGroupTrip(int $packageId, array $data): void
    {
        $existing = $this->getGroupTrip($packageId);

        if ($existing) {
            $stmt = $this->db->prepare('
                UPDATE grouptrips
                SET    departure_date   = :dep_date,
                       return_date      = :ret_date,
                       min_participants = :min_p,
                       max_participants = :max_p,
                       meeting_point    = :meet
                WHERE  package_id      = :pkg
            ');
        } else {
            $stmt = $this->db->prepare('
                INSERT INTO grouptrips
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
        $this->db->prepare('DELETE FROM grouptrips WHERE package_id = :id')
                 ->execute([':id' => $packageId]);
    }


    public function getAllDestinations(): array
    {
        return $this->db->query('
            SELECT d.destination_id, d.name,
                   l.city, l.country
            FROM   destinations d
            JOIN   locations l ON l.location_id = d.location_id
            ORDER BY l.country, d.name
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

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
            FROM   flights f
            JOIN   locations dep ON dep.location_id = f.departure_location_id
            JOIN   locations arr ON arr.location_id = f.arrival_location_id
            ORDER BY f.airline_name, f.departure_time
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAccommodations(): array
    {
        return $this->db->query('
            SELECT a.accommodation_id, a.name, a.type, a.star_rating,
                   a.price_per_night, l.city, l.country
            FROM   accommodations a
            JOIN   locations l ON l.location_id = a.location_id
            ORDER BY l.country, a.name
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAttractions(): array
    {
        return $this->db->query('
            SELECT a.attraction_id, a.name, a.category,
                   l.city, l.country
            FROM   attractions a
            JOIN   locations l ON l.location_id = a.location_id
            ORDER BY l.country, a.name
        ')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRestaurants(): array
    {
        return $this->db->query('
            SELECT r.restaurant_id, r.name, r.cuisine_type,
                   l.city, l.country
            FROM   restaurants r
            JOIN   locations l ON l.location_id = r.location_id
            ORDER BY l.country, r.name
        ')->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getPackageDestinationIds(int $packageId): array
    {
        $stmt = $this->db->prepare('SELECT destination_id FROM packagedestinations WHERE package_id = :id');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPackageFlightIds(int $packageId): array
    {
        $stmt = $this->db->prepare('SELECT flight_id FROM packageflights WHERE package_id = :id');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPackageAccommodationIds(int $packageId): array
    {
        $stmt = $this->db->prepare('SELECT accommodation_id FROM packageaccommodations WHERE package_id = :id');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPackageAttractionIds(int $packageId): array
    {
        $stmt = $this->db->prepare('SELECT attraction_id FROM packageattractions WHERE package_id = :id');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPackageRestaurantIds(int $packageId): array
    {
        $stmt = $this->db->prepare('SELECT restaurant_id FROM packagerestaurants WHERE package_id = :id');
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }


    public function getRecentBookings(int $agencyId, int $limit = 5): array
    {
        $stmt = $this->db->prepare('
            SELECT b.booking_id,
                   b.created_at    AS booking_date,
                   b.total_price,
                   b.status,
                   p.title         AS package_title,
                   CONCAT(t.first_name, " ", t.last_name) AS traveller_name
            FROM   bookings b
            JOIN   packages   p ON p.package_id = b.package_id
            JOIN   travellers t ON t.user_id    = b.traveller_id
            WHERE  p.agency_id = :id
            ORDER BY b.created_at DESC
            LIMIT  :lim
        ');
        $stmt->bindValue(':id',  $agencyId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getGroupTripsByAgency(int $agencyId): array
    {
        $stmt = $this->db->prepare('
            SELECT gt.group_trip_id,
                   gt.package_id,
                   gt.departure_date,
                   gt.return_date,
                   gt.min_participants,
                   gt.max_participants,
                   gt.current_participants,
                   gt.meeting_point,
                   gt.status,
                   gt.created_at,
                   p.title AS package_title,
                   p.base_price
            FROM   grouptrips gt
            JOIN   packages p ON p.package_id = gt.package_id
            WHERE  p.agency_id = :id
            ORDER BY gt.departure_date ASC, gt.created_at DESC
        ');
        $stmt->execute([':id' => $agencyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGroupTripParticipants(int $groupTripId): array
    {
        $stmt = $this->db->prepare('
            SELECT gp.traveller_id,
                   gp.status,
                   gp.joined_at,
                   t.first_name,
                   t.last_name,
                   t.profile_picture_url
            FROM   grouptripparticipants gp
            JOIN   travellers t ON t.user_id = gp.traveller_id
            WHERE  gp.group_trip_id = :group_trip_id
            ORDER BY gp.joined_at ASC
        ');
        $stmt->execute([':group_trip_id' => $groupTripId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeGroupParticipant(int $agencyId, int $groupTripId, int $travellerId): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare('
                SELECT gt.group_trip_id,
                       gt.current_participants,
                       gt.max_participants,
                       gp.status AS participant_status
                FROM   grouptrips gt
                JOIN   packages p ON p.package_id = gt.package_id
                LEFT JOIN grouptripparticipants gp
                       ON gp.group_trip_id = gt.group_trip_id
                      AND gp.package_id = gt.package_id
                      AND gp.traveller_id = :traveller_id
                WHERE  gt.group_trip_id = :group_trip_id
                  AND  p.agency_id = :agency_id
                LIMIT 1
                FOR UPDATE
            ');
            $stmt->execute([
                ':traveller_id' => $travellerId,
                ':group_trip_id' => $groupTripId,
                ':agency_id' => $agencyId,
            ]);
            $group = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$group || ($group['participant_status'] ?? null) !== 'confirmed') {
                $this->db->rollBack();
                return false;
            }

            $deleteStmt = $this->db->prepare('
                UPDATE grouptripparticipants
                SET    status = "cancelled"
                WHERE  group_trip_id = :group_trip_id
                  AND  traveller_id = :traveller_id
                  AND  status = "confirmed"
            ');
            $deleteStmt->execute([
                ':group_trip_id' => $groupTripId,
                ':traveller_id' => $travellerId,
            ]);

            $newCount = max(0, ((int) $group['current_participants']) - 1);
            $status = $newCount >= (int) $group['max_participants'] ? 'full' : 'open';

            $updateStmt = $this->db->prepare('
                UPDATE grouptrips
                SET    current_participants = :current_participants,
                       status = :status
                WHERE  group_trip_id = :group_trip_id
            ');
            $updateStmt->execute([
                ':current_participants' => $newCount,
                ':status' => $status,
                ':group_trip_id' => $groupTripId,
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Remove group participant error: ' . $e->getMessage());
            return false;
        }
    }
}

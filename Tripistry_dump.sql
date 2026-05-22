-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: tripistry
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accommodationamenities`
--
USE tripistry;

DROP TABLE IF EXISTS `accommodationamenities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accommodationamenities` (
  `accommodation_id` int NOT NULL,
  `amenity` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`accommodation_id`,`amenity`),
  CONSTRAINT `fk_amenities_accommodation` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodations` (`accommodation_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accommodationamenities`
--

LOCK TABLES `accommodationamenities` WRITE;
/*!40000 ALTER TABLE `accommodationamenities` DISABLE KEYS */;
/*!40000 ALTER TABLE `accommodationamenities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accommodations`
--

DROP TABLE IF EXISTS `accommodations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accommodations` (
  `accommodation_id` int NOT NULL AUTO_INCREMENT,
  `location_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('hotel','guesthouse','resort','hostel','apartment','villa') COLLATE utf8mb4_unicode_ci NOT NULL,
  `star_rating` tinyint DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`accommodation_id`),
  KEY `fk_accommodations_location` (`location_id`),
  CONSTRAINT `fk_accommodations_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_accommodations_price` CHECK ((`price_per_night` >= 0)),
  CONSTRAINT `chk_accommodations_star_rating` CHECK (((`star_rating` is null) or (`star_rating` between 1 and 5)))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accommodations`
--

LOCK TABLES `accommodations` WRITE;
/*!40000 ALTER TABLE `accommodations` DISABLE KEYS */;
INSERT INTO `accommodations` VALUES (1,1,'Hotel Le Marais','hotel',4,'Charming boutique hotel in central Paris',NULL,1200.00,'15 Rue des Archives, Paris',NULL),(2,2,'Cape Grace Hotel','hotel',5,'Luxury waterfront hotel at the V&A Waterfront',NULL,3500.00,'West Quay Road, Cape Town',NULL),(3,3,'Shibuya Sky Hotel','hotel',3,'Modern hotel near Shibuya Crossing',NULL,800.00,'2-1 Shibuya, Tokyo',NULL);
/*!40000 ALTER TABLE `accommodations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agencies`
--

DROP TABLE IF EXISTS `agencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agencies` (
  `user_id` int NOT NULL,
  `agency_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `favouritable_id` int DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_agencies_name` (`agency_name`),
  UNIQUE KEY `uq_agencies_registration` (`registration_number`),
  UNIQUE KEY `uq_agencies_favouritable` (`favouritable_id`),
  CONSTRAINT `fk_agencies_favouritable` FOREIGN KEY (`favouritable_id`) REFERENCES `favouritable` (`favouritable_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_agencies_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_agencies_is_verified` CHECK ((`is_verified` in (0,1)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agencies`
--

LOCK TABLES `agencies` WRITE;
/*!40000 ALTER TABLE `agencies` DISABLE KEYS */;
INSERT INTO `agencies` VALUES (3,'Global Travel Co.','Worldwide package holidays since 2005',NULL,NULL,NULL,'+27-11-555-0100',1,NULL),(4,'Adventure Tours SA','Specializing in African safari and adventure packages',NULL,NULL,NULL,'+27-21-555-0200',1,NULL);
/*!40000 ALTER TABLE `agencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agencyreviews`
--

DROP TABLE IF EXISTS `agencyreviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agencyreviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `traveller_id` int NOT NULL,
  `agency_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  UNIQUE KEY `uq_agency_reviews_booking` (`booking_id`),
  KEY `fk_ar_traveller` (`traveller_id`),
  KEY `idx_agency_reviews_agency` (`agency_id`),
  CONSTRAINT `fk_ar_agency` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ar_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ar_traveller` FOREIGN KEY (`traveller_id`) REFERENCES `travellers` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_ar_rating` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agencyreviews`
--

LOCK TABLES `agencyreviews` WRITE;
/*!40000 ALTER TABLE `agencyreviews` DISABLE KEYS */;
INSERT INTO `agencyreviews` VALUES (1,1,3,1,5,'Amazing trip, everything was perfectly organized!','2026-05-13 10:26:01');
/*!40000 ALTER TABLE `agencyreviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attractions`
--

DROP TABLE IF EXISTS `attractions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attractions` (
  `attraction_id` int NOT NULL AUTO_INCREMENT,
  `location_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('museum','park','landmark','theme_park','beach','historical','entertainment','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_fee` decimal(8,2) NOT NULL DEFAULT '0.00',
  `opening_hours` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`attraction_id`),
  KEY `fk_attractions_location` (`location_id`),
  CONSTRAINT `fk_attractions_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_attractions_entry_fee` CHECK ((`entry_fee` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attractions`
--

LOCK TABLES `attractions` WRITE;
/*!40000 ALTER TABLE `attractions` DISABLE KEYS */;
INSERT INTO `attractions` VALUES (1,1,'Eiffel Tower','landmark','Iconic iron lattice tower',NULL,25.00,'9:00 AM - 11:45 PM',NULL),(2,2,'Table Mountain','landmark','Cable car to the top of Cape Town',NULL,395.00,'8:00 AM - 6:00 PM',NULL),(3,3,'Meiji Shrine','historical','Shinto shrine surrounded by forest',NULL,0.00,'Sunrise to sunset',NULL);
/*!40000 ALTER TABLE `attractions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `traveller_id` int NOT NULL,
  `package_id` int NOT NULL,
  `group_trip_id` int DEFAULT NULL,
  `travel_date` date NOT NULL,
  `num_travellers` int NOT NULL DEFAULT '1',
  `total_price` decimal(10,2) NOT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ZAR',
  `status` enum('pending','confirmed','cancelled','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_requests` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`),
  KEY `fk_bookings_group_trip` (`group_trip_id`,`package_id`),
  KEY `idx_bookings_traveller_status` (`traveller_id`,`status`),
  KEY `idx_bookings_package` (`package_id`),
  CONSTRAINT `fk_bookings_group_trip` FOREIGN KEY (`group_trip_id`, `package_id`) REFERENCES `grouptrips` (`group_trip_id`, `package_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_traveller` FOREIGN KEY (`traveller_id`) REFERENCES `travellers` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_bookings_currency` CHECK ((char_length(`currency`) = 3)),
  CONSTRAINT `chk_bookings_num_travellers` CHECK ((`num_travellers` > 0)),
  CONSTRAINT `chk_bookings_price` CHECK ((`total_price` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,1,1,NULL,'2026-06-15',2,8900.00,'ZAR','confirmed','PAY-001',NULL,'2026-05-13 10:25:49'),(2,2,3,NULL,'2026-07-20',1,7500.00,'ZAR','pending','PAY-002',NULL,'2026-05-13 10:25:49');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `destinations`
--

DROP TABLE IF EXISTS `destinations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `destinations` (
  `destination_id` int NOT NULL AUTO_INCREMENT,
  `location_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `best_season` enum('spring','summer','autumn','winter','year-round') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `average_temperature_celsius` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`destination_id`),
  KEY `idx_destinations_location` (`location_id`),
  CONSTRAINT `fk_destinations_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `destinations`
--

LOCK TABLES `destinations` WRITE;
/*!40000 ALTER TABLE `destinations` DISABLE KEYS */;
INSERT INTO `destinations` VALUES (1,1,'Paris City Break','Romantic getaway in the City of Light',NULL,'spring',15.50),(2,2,'Cape Town Adventure','Explore Table Mountain and beaches',NULL,'summer',22.00),(3,3,'Tokyo Discovery','Modern city meets ancient traditions',NULL,'autumn',18.00);
/*!40000 ALTER TABLE `destinations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favouritable`
--

DROP TABLE IF EXISTS `favouritable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favouritable` (
  `favouritable_id` int NOT NULL AUTO_INCREMENT,
  `target_type` enum('agency','package') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`favouritable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favouritable`
--

LOCK TABLES `favouritable` WRITE;
/*!40000 ALTER TABLE `favouritable` DISABLE KEYS */;
/*!40000 ALTER TABLE `favouritable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favourites`
--

DROP TABLE IF EXISTS `favourites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favourites` (
  `favourite_id` int NOT NULL AUTO_INCREMENT,
  `traveller_id` int NOT NULL,
  `favouritable_id` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`favourite_id`),
  UNIQUE KEY `uq_favourites_traveller_item` (`traveller_id`,`favouritable_id`),
  KEY `fk_fav_favouritable` (`favouritable_id`),
  CONSTRAINT `fk_fav_favouritable` FOREIGN KEY (`favouritable_id`) REFERENCES `favouritable` (`favouritable_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_fav_traveller` FOREIGN KEY (`traveller_id`) REFERENCES `travellers` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favourites`
--

LOCK TABLES `favourites` WRITE;
/*!40000 ALTER TABLE `favourites` DISABLE KEYS */;
/*!40000 ALTER TABLE `favourites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flights`
--

DROP TABLE IF EXISTS `flights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flights` (
  `flight_id` int NOT NULL AUTO_INCREMENT,
  `airline_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flight_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departure_airport` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `arrival_airport` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departure_location_id` int NOT NULL,
  `arrival_location_id` int NOT NULL,
  `departure_time` time DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `duration_minutes` int DEFAULT NULL,
  `flight_class` enum('economy','business','first') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'economy',
  `base_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`flight_id`),
  KEY `fk_flights_departure_location` (`departure_location_id`),
  KEY `fk_flights_arrival_location` (`arrival_location_id`),
  KEY `idx_flights_airports` (`departure_airport`,`arrival_airport`),
  CONSTRAINT `fk_flights_arrival_location` FOREIGN KEY (`arrival_location_id`) REFERENCES `locations` (`location_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_flights_departure_location` FOREIGN KEY (`departure_location_id`) REFERENCES `locations` (`location_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_flights_different_airports` CHECK ((`departure_airport` <> `arrival_airport`)),
  CONSTRAINT `chk_flights_duration` CHECK (((`duration_minutes` is null) or (`duration_minutes` > 0))),
  CONSTRAINT `chk_flights_price` CHECK ((`base_price` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flights`
--

LOCK TABLES `flights` WRITE;
/*!40000 ALTER TABLE `flights` DISABLE KEYS */;
INSERT INTO `flights` VALUES (1,'Air France','AF123','JNB','CDG',2,1,NULL,NULL,660,'economy',4500.00),(2,'Emirates','EK456','JNB','DXB',2,5,NULL,NULL,480,'business',8500.00),(3,'Japan Airlines','JL789','JNB','NRT',2,3,NULL,NULL,900,'economy',6200.00);
/*!40000 ALTER TABLE `flights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grouptripparticipants`
--

DROP TABLE IF EXISTS `grouptripparticipants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grouptripparticipants` (
  `traveller_id` int NOT NULL,
  `group_trip_id` int NOT NULL,
  `package_id` int NOT NULL,
  `joined_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('waitlist','confirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waitlist',
  PRIMARY KEY (`traveller_id`,`group_trip_id`,`package_id`),
  KEY `fk_gtp_group_trip` (`group_trip_id`,`package_id`),
  CONSTRAINT `fk_gtp_group_trip` FOREIGN KEY (`group_trip_id`, `package_id`) REFERENCES `grouptrips` (`group_trip_id`, `package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_gtp_traveller` FOREIGN KEY (`traveller_id`) REFERENCES `travellers` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grouptripparticipants`
--

LOCK TABLES `grouptripparticipants` WRITE;
/*!40000 ALTER TABLE `grouptripparticipants` DISABLE KEYS */;
/*!40000 ALTER TABLE `grouptripparticipants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grouptrips`
--

DROP TABLE IF EXISTS `grouptrips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grouptrips` (
  `group_trip_id` int NOT NULL AUTO_INCREMENT,
  `package_id` int NOT NULL,
  `departure_date` date NOT NULL,
  `return_date` date NOT NULL,
  `min_participants` int NOT NULL DEFAULT '2',
  `max_participants` int NOT NULL,
  `current_participants` int NOT NULL DEFAULT '0',
  `meeting_point` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('open','full','departed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_trip_id`,`package_id`),
  KEY `fk_group_trips_package` (`package_id`),
  KEY `idx_group_trips_status_date` (`status`,`departure_date`),
  CONSTRAINT `fk_group_trips_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_group_trips_dates` CHECK ((`return_date` > `departure_date`)),
  CONSTRAINT `chk_group_trips_participants` CHECK (((`min_participants` > 0) and (`max_participants` >= `min_participants`) and (`current_participants` >= 0) and (`current_participants` <= `max_participants`)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grouptrips`
--

LOCK TABLES `grouptrips` WRITE;
/*!40000 ALTER TABLE `grouptrips` DISABLE KEYS */;
/*!40000 ALTER TABLE `grouptrips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `location_id` int NOT NULL AUTO_INCREMENT,
  `city` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timezone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  CONSTRAINT `chk_country_code` CHECK ((char_length(`country_code`) = 2)),
  CONSTRAINT `chk_latitude` CHECK (((`latitude` is null) or (`latitude` between -(90.0) and 90.0))),
  CONSTRAINT `chk_longitude` CHECK (((`longitude` is null) or (`longitude` between -(180.0) and 180.0)))
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
INSERT INTO `locations` VALUES (1,'Paris','France','FR','Europe/Paris',48.8566000,2.3522000),(2,'Cape Town','South Africa','ZA','Africa/Johannesburg',-33.9249000,18.4241000),(3,'Tokyo','Japan','JP','Asia/Tokyo',35.6762000,139.6503000),(4,'New York','United States','US','America/New_York',40.7128000,-74.0060000),(5,'Dubai','United Arab Emirates','AE','Asia/Dubai',25.2048000,55.2708000),(6,'Paris','France','FR','Europe/Paris',48.8566000,2.3522000),(7,'Cape Town','South Africa','ZA','Africa/Johannesburg',-33.9249000,18.4241000),(8,'Tokyo','Japan','JP','Asia/Tokyo',35.6762000,139.6503000),(9,'New York','United States','US','America/New_York',40.7128000,-74.0060000),(10,'Dubai','United Arab Emirates','AE','Asia/Dubai',25.2048000,55.2708000);
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packageaccommodations`
--

DROP TABLE IF EXISTS `packageaccommodations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packageaccommodations` (
  `package_id` int NOT NULL,
  `accommodation_id` int NOT NULL,
  `num_nights` int NOT NULL DEFAULT '1',
  `check_in_day` int DEFAULT NULL,
  PRIMARY KEY (`package_id`,`accommodation_id`),
  KEY `fk_pa_accommodation` (`accommodation_id`),
  CONSTRAINT `fk_pa_accommodation` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodations` (`accommodation_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pa_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_pa_check_in_day` CHECK (((`check_in_day` is null) or (`check_in_day` > 0))),
  CONSTRAINT `chk_pa_num_nights` CHECK ((`num_nights` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packageaccommodations`
--

LOCK TABLES `packageaccommodations` WRITE;
/*!40000 ALTER TABLE `packageaccommodations` DISABLE KEYS */;
INSERT INTO `packageaccommodations` VALUES (1,1,3,1),(2,3,5,1),(3,2,4,1);
/*!40000 ALTER TABLE `packageaccommodations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packageattractions`
--

DROP TABLE IF EXISTS `packageattractions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packageattractions` (
  `package_id` int NOT NULL,
  `attraction_id` int NOT NULL,
  `visit_day` int DEFAULT NULL,
  `is_included` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`package_id`,`attraction_id`),
  KEY `fk_pat_attraction` (`attraction_id`),
  CONSTRAINT `fk_pat_attraction` FOREIGN KEY (`attraction_id`) REFERENCES `attractions` (`attraction_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pat_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_pat_is_included` CHECK ((`is_included` in (0,1))),
  CONSTRAINT `chk_pat_visit_day` CHECK (((`visit_day` is null) or (`visit_day` > 0)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packageattractions`
--

LOCK TABLES `packageattractions` WRITE;
/*!40000 ALTER TABLE `packageattractions` DISABLE KEYS */;
INSERT INTO `packageattractions` VALUES (1,1,2,1),(2,3,3,1),(3,2,2,1);
/*!40000 ALTER TABLE `packageattractions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packagedestinations`
--

DROP TABLE IF EXISTS `packagedestinations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packagedestinations` (
  `package_id` int NOT NULL,
  `destination_id` int NOT NULL,
  `day_number` int DEFAULT NULL,
  `visit_order` int DEFAULT NULL,
  PRIMARY KEY (`package_id`,`destination_id`),
  KEY `fk_pd_destination` (`destination_id`),
  CONSTRAINT `fk_pd_destination` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`destination_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pd_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_pd_day_number` CHECK (((`day_number` is null) or (`day_number` > 0))),
  CONSTRAINT `chk_pd_visit_order` CHECK (((`visit_order` is null) or (`visit_order` > 0)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packagedestinations`
--

LOCK TABLES `packagedestinations` WRITE;
/*!40000 ALTER TABLE `packagedestinations` DISABLE KEYS */;
INSERT INTO `packagedestinations` VALUES (1,1,1,NULL),(2,3,1,NULL),(3,2,1,NULL);
/*!40000 ALTER TABLE `packagedestinations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packageflights`
--

DROP TABLE IF EXISTS `packageflights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packageflights` (
  `package_id` int NOT NULL,
  `flight_id` int NOT NULL,
  `is_outbound` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`package_id`,`flight_id`),
  KEY `fk_pf_flight` (`flight_id`),
  CONSTRAINT `fk_pf_flight` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`flight_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pf_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_pf_is_outbound` CHECK ((`is_outbound` in (0,1)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packageflights`
--

LOCK TABLES `packageflights` WRITE;
/*!40000 ALTER TABLE `packageflights` DISABLE KEYS */;
INSERT INTO `packageflights` VALUES (1,1,1),(2,3,1),(3,2,1);
/*!40000 ALTER TABLE `packageflights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packagerestaurants`
--

DROP TABLE IF EXISTS `packagerestaurants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packagerestaurants` (
  `package_id` int NOT NULL,
  `restaurant_id` int NOT NULL,
  `meal_type` enum('breakfast','lunch','dinner','all') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visit_day` int DEFAULT NULL,
  PRIMARY KEY (`package_id`,`restaurant_id`),
  KEY `fk_pr_restaurant` (`restaurant_id`),
  CONSTRAINT `fk_pr_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pr_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`restaurant_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_pr_visit_day` CHECK (((`visit_day` is null) or (`visit_day` > 0)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packagerestaurants`
--

LOCK TABLES `packagerestaurants` WRITE;
/*!40000 ALTER TABLE `packagerestaurants` DISABLE KEYS */;
INSERT INTO `packagerestaurants` VALUES (1,1,'dinner',1),(2,3,'lunch',2),(3,2,'dinner',3);
/*!40000 ALTER TABLE `packagerestaurants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packagereviews`
--

DROP TABLE IF EXISTS `packagereviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packagereviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `traveller_id` int NOT NULL,
  `package_id` int NOT NULL,
  `booking_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  UNIQUE KEY `uq_package_reviews_booking` (`booking_id`),
  KEY `fk_packagereviews_traveller` (`traveller_id`),
  KEY `idx_package_reviews_package` (`package_id`),
  CONSTRAINT `fk_packagereviews_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_packagereviews_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_packagereviews_traveller` FOREIGN KEY (`traveller_id`) REFERENCES `travellers` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_pr_rating` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packagereviews`
--

LOCK TABLES `packagereviews` WRITE;
/*!40000 ALTER TABLE `packagereviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `packagereviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packages` (
  `package_id` int NOT NULL AUTO_INCREMENT,
  `agency_id` int NOT NULL,
  `favouritable_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `base_price` decimal(10,2) NOT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ZAR',
  `duration_days` int NOT NULL,
  `max_capacity` int DEFAULT NULL,
  `status` enum('draft','active','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `cover_image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancellation_policy` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`package_id`),
  UNIQUE KEY `uq_packages_favouritable` (`favouritable_id`),
  KEY `idx_packages_status_price` (`status`,`base_price`),
  KEY `idx_packages_agency` (`agency_id`),
  KEY `idx_packages_duration` (`duration_days`),
  CONSTRAINT `fk_packages_agency` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_packages_favouritable` FOREIGN KEY (`favouritable_id`) REFERENCES `favouritable` (`favouritable_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chk_packages_capacity` CHECK (((`max_capacity` is null) or (`max_capacity` > 0))),
  CONSTRAINT `chk_packages_currency` CHECK ((char_length(`currency`) = 3)),
  CONSTRAINT `chk_packages_duration` CHECK ((`duration_days` > 0)),
  CONSTRAINT `chk_packages_price` CHECK ((`base_price` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
INSERT INTO `packages` VALUES (1,3,NULL,'Romantic Paris Escape','3 nights in Paris including flights and hotel',8900.00,'ZAR',4,2,'active','2026-05-13 10:19:39',NULL,NULL,NULL),(2,3,NULL,'Tokyo Explorer','5 nights in Tokyo with city tours',12500.00,'ZAR',6,4,'active','2026-05-13 10:19:39',NULL,NULL,NULL),(3,4,NULL,'Cape Town Adventure','4 nights in Cape Town with Table Mountain tour',7500.00,'ZAR',5,6,'active','2026-05-13 10:19:39',NULL,NULL,NULL);
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurants`
--

DROP TABLE IF EXISTS `restaurants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `restaurants` (
  `restaurant_id` int NOT NULL AUTO_INCREMENT,
  `location_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuisine_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_range` enum('budget','mid-range','fine-dining') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `average_rating` decimal(3,2) DEFAULT NULL,
  PRIMARY KEY (`restaurant_id`),
  KEY `fk_restaurants_location` (`location_id`),
  CONSTRAINT `fk_restaurants_location` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_restaurants_rating` CHECK (((`average_rating` is null) or (`average_rating` between 0.00 and 5.00)))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurants`
--

LOCK TABLES `restaurants` WRITE;
/*!40000 ALTER TABLE `restaurants` DISABLE KEYS */;
INSERT INTO `restaurants` VALUES (1,1,'Le Petit Bistro','French','mid-range','Classic French dishes in a cozy setting',NULL,NULL,4.50),(2,2,'The Test Kitchen','Fusion','fine-dining','Award-winning fine dining experience',NULL,NULL,4.80),(3,3,'Ichiran Ramen','Japanese','budget','Famous solo ramen dining experience',NULL,NULL,4.30);
/*!40000 ALTER TABLE `restaurants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `travellers`
--

DROP TABLE IF EXISTS `travellers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travellers` (
  `user_id` int NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `profile_picture_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_currency` char(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ZAR',
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_travellers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_travellers_currency` CHECK ((char_length(`preferred_currency`) = 3))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `travellers`
--

LOCK TABLES `travellers` WRITE;
/*!40000 ALTER TABLE `travellers` DISABLE KEYS */;
INSERT INTO `travellers` VALUES (1,'Sarah','Johnson','+27-82-123-4567','1995-06-15',NULL,'ZAR'),(2,'John','Smith','+27-83-987-6543','1992-03-22',NULL,'ZAR');
/*!40000 ALTER TABLE `travellers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` enum('traveller','agency') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_users_email` (`email`),
  CONSTRAINT `chk_users_is_active` CHECK ((`is_active` in (0,1)))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'sarah@email.com',NULL,'$2y$10$hashedpassword1','traveller','2026-05-13 10:18:11',1),(2,'john@email.com',NULL,'$2y$10$hashedpassword2','traveller','2026-05-13 10:18:11',1),(3,'globaltravel@agency.com',NULL,'$2y$10$hashedpassword3','agency','2026-05-13 10:18:11',1),(4,'adventuretours@agency.com',NULL,'$2y$10$hashedpassword4','agency','2026-05-13 10:18:11',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-20 10:25:19


UPDATE users SET password_hash = '$2y$10$Fqv8q.lUyQT82cgoca1f5eclkk.CLqGhfYq8CN6Vp8nzynB9ruqFW' WHERE email = 'sarah@email.com';

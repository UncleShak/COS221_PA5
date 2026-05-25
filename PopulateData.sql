-- ============================================================
-- Tripistry Full Seed Data
-- Run after database.sql
-- Uses INSERT IGNORE to safely skip existing records
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- ============================================================
-- 1. LOCATIONS
-- ============================================================

INSERT IGNORE INTO locations (location_id, city, country, country_code, timezone, latitude, longitude) VALUES
(1, 'Paris', 'France', 'FR', 'Europe/Paris', 48.8566, 2.3522),
(2, 'Cape Town', 'South Africa', 'ZA', 'Africa/Johannesburg', -33.9249, 18.4241),
(3, 'Tokyo', 'Japan', 'JP', 'Asia/Tokyo', 35.6762, 139.6503),
(4, 'Johannesburg', 'South Africa', 'ZA', 'Africa/Johannesburg', -26.2041, 28.0473),
(5, 'Dubai', 'UAE', 'AE', 'Asia/Dubai', 25.2048, 55.2708),
(6, 'Nairobi', 'Kenya', 'KE', 'Africa/Nairobi', -1.2921, 36.8219),
(7, 'New York', 'USA', 'US', 'America/New_York', 40.7128, -74.0060),
(8, 'Zanzibar', 'Tanzania', 'TZ', 'Africa/Dar_es_Salaam', -6.1659, 39.2026),
(9, 'Mauritius', 'Mauritius', 'MU', 'Indian/Mauritius', -20.3484, 57.5522),
(10, 'Durban', 'South Africa', 'ZA', 'Africa/Johannesburg', -29.8587, 31.0218),
(11, 'Amsterdam', 'Netherlands', 'NL', 'Europe/Amsterdam', 52.3676, 4.9041),
(12, 'Bangkok', 'Thailand', 'TH', 'Asia/Bangkok', 13.7563, 100.5018),
(13, 'Sydney', 'Australia', 'AU', 'Australia/Sydney', -33.8688, 151.2093),
(14, 'Marrakech', 'Morocco', 'MA', 'Africa/Casablanca', 31.6295, -7.9811),
(15, 'Lisbon', 'Portugal', 'PT', 'Europe/Lisbon', 38.7169, -9.1395);

-- ============================================================
-- 2. USERS
-- Password for all demo accounts: Password123!
-- ============================================================

INSERT IGNORE INTO users (user_id, email, username, password_hash, user_type, created_at, is_active) VALUES
(5, 'amara.nkosi@email.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveller', NOW(), 1),
(6, 'luca.ferrari@email.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveller', NOW(), 1),
(7, 'priya.sharma@email.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveller', NOW(), 1),
(8, 'james.oduya@email.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveller', NOW(), 1),
(9, 'mei.zhang@email.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveller', NOW(), 1),
(10, 'sunsettravel@agency.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agency', NOW(), 1),
(11, 'luxeescapes@agency.com', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agency', NOW(), 1);

-- ============================================================
-- 3. TRAVELLERS
-- ============================================================

INSERT IGNORE INTO travellers (user_id, first_name, last_name, phone_number, date_of_birth, profile_picture_url, preferred_currency) VALUES
(1, 'Sarah', 'Johnson', '+27-82-123-4567', '1995-06-15', NULL, 'ZAR'),
(2, 'John', 'Smith', '+27-83-987-6543', '1992-03-22', NULL, 'ZAR'),
(5, 'Amara', 'Nkosi', '+27-71-234-5678', '1998-11-02', NULL, 'ZAR'),
(6, 'Luca', 'Ferrari', '+39-02-1234-5678', '1990-04-17', NULL, 'EUR'),
(7, 'Priya', 'Sharma', '+91-98-7654-3210', '1996-08-30', NULL, 'USD'),
(8, 'James', 'Oduya', '+254-72-345-6789', '1988-01-25', NULL, 'USD'),
(9, 'Mei', 'Zhang', '+86-138-0013-8000', '2000-12-05', NULL, 'USD');

-- ============================================================
-- 4. AGENCIES
-- ============================================================

INSERT IGNORE INTO agencies (user_id, agency_name, description, logo_url, website_url, registration_number, contact_phone, is_verified) VALUES
(3, 'Global Travel Co.', 'Worldwide package holidays since 2005', NULL, NULL, 'REG-2005-001', '+27-11-555-0100', 1),
(4, 'Adventure Tours SA', 'Specializing in African safari and adventure packages', NULL, NULL, 'REG-2009-004', '+27-21-555-0200', 1),
(10, 'Sunset Travel', 'Luxury beach and island escapes for the discerning traveller', NULL, NULL, 'REG-2015-010', '+27-31-555-0300', 1),
(11, 'LuxeEscapes', 'High-end curated travel experiences worldwide', NULL, NULL, 'REG-2018-011', '+27-12-555-0400', 1);

-- ============================================================
-- 5. DESTINATIONS
-- ============================================================

INSERT IGNORE INTO destinations (destination_id, location_id, name, description, image_url, best_season, average_temperature_celsius) VALUES
(1, 1, 'Paris City Centre', 'The heart of Paris with the Eiffel Tower, Louvre, and world-class cuisine', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800', 'spring', 16.0),
(2, 2, 'Cape Town Waterfront', 'The iconic V&A Waterfront with Table Mountain views and award-winning restaurants', 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800', 'summer', 22.0),
(3, 3, 'Tokyo City', 'Bustling metropolis blending ultra-modern and traditional Japanese culture', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', 'spring', 15.0),
(4, 5, 'Dubai Downtown', 'Futuristic skyline, luxury malls, and desert adventures', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800', 'winter', 28.0),
(5, 6, 'Nairobi & Maasai Mara', 'Gateway to iconic savanna wildlife and authentic Maasai culture', 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=800', 'summer', 20.0),
(6, 7, 'New York City', 'The city that never sleeps — Times Square, Central Park, and Broadway', 'https://images.unsplash.com/photo-1485871981521-5b1fd3805795?w=800', 'autumn', 13.0),
(7, 8, 'Zanzibar Island', 'Pristine white-sand beaches, spice farms, and Stone Town UNESCO site', 'https://images.unsplash.com/photo-1586861635167-e5223aadc9fe?w=800', 'summer', 28.0),
(8, 9, 'Mauritius Beaches', 'Crystal-clear lagoons, coral reefs, and lush tropical landscape', 'https://images.unsplash.com/photo-1544551763-77ef2d0cfc6c?w=800', 'winter', 26.0),
(9, 11, 'Amsterdam Canals', 'Charming canal city with world-class museums and cycling culture', 'https://images.unsplash.com/photo-1534351590666-13e3e96b5017?w=800', 'spring', 12.0),
(10, 12, 'Bangkok', 'Vibrant street food, ornate temples, and thrilling nightlife', 'https://images.unsplash.com/photo-1563492065599-3520f775eeed?w=800', 'winter', 32.0),
(11, 13, 'Sydney Harbour', 'Iconic Opera House, Bondi Beach, and the Great Barrier Reef day trips', 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800', 'summer', 22.0),
(12, 14, 'Marrakech Medina', 'Labyrinthine souks, Djemaa el-Fna square, and Atlas Mountain excursions', 'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?w=800', 'spring', 20.0),
(13, 10, 'Durban Golden Mile', 'South Africa\'s warm-water beach capital with a vibrant Indian cultural scene', 'https://images.unsplash.com/photo-1551244072-5d12893278bc?w=800', 'summer', 25.0),
(14, 15, 'Lisbon Old Town', 'Pastel-coloured trams, historic Alfama district, and legendary pastéis de nata', 'https://images.unsplash.com/photo-1548707309-dcebeab9ea9b?w=800', 'spring', 18.0),
(15, 4, 'Johannesburg & Soweto', 'Cultural heartbeat of South Africa with vibrant arts and Apartheid Museum', 'https://images.unsplash.com/photo-1577717907211-b0ee4cd2bb17?w=800', 'year-round', 19.0);

-- ============================================================
-- 6. FLIGHTS
-- ============================================================

INSERT IGNORE INTO flights (flight_id, airline_name, flight_number, departure_airport, arrival_airport, departure_location_id, arrival_location_id, departure_time, arrival_time, duration_minutes, flight_class, base_price) VALUES
(1, 'South African Airways', 'SA201', 'JNB', 'CDG', 4, 1, '20:00:00', '06:30:00', 750, 'economy', 4200.00),
(2, 'South African Airways', 'SA202', 'CDG', 'JNB', 1, 4, '09:00:00', '21:30:00', 750, 'economy', 4200.00),
(3, 'South African Airways', 'SA301', 'JNB', 'NRT', 4, 3, '18:00:00', '16:00:00', 1320, 'economy', 7800.00),
(4, 'South African Airways', 'SA302', 'NRT', 'JNB', 3, 4, '09:00:00', '19:30:00', 1290, 'economy', 7800.00),
(5, 'FlySafair', 'FA101', 'JNB', 'CPT', 4, 2, '07:00:00', '09:15:00', 135, 'economy', 890.00),
(6, 'FlySafair', 'FA102', 'CPT', 'JNB', 2, 4, '18:00:00', '20:15:00', 135, 'economy', 890.00),
(7, 'Emirates', 'EK762', 'JNB', 'DXB', 4, 5, '23:55:00', '07:30:00', 455, 'economy', 3200.00),
(8, 'Emirates', 'EK763', 'DXB', 'JNB', 5, 4, '10:00:00', '17:30:00', 450, 'economy', 3200.00),
(9, 'Kenya Airways', 'KQ100', 'JNB', 'NBO', 4, 6, '09:00:00', '13:45:00', 285, 'economy', 2400.00),
(10, 'Kenya Airways', 'KQ101', 'NBO', 'JNB', 6, 4, '15:00:00', '19:45:00', 285, 'economy', 2400.00),
(11, 'Turkish Airlines', 'TK770', 'JNB', 'JFK', 4, 7, '21:30:00', '07:00:00', 990, 'economy', 9500.00),
(12, 'Turkish Airlines', 'TK771', 'JFK', 'JNB', 7, 4, '23:00:00', '19:30:00', 990, 'economy', 9500.00),
(13, 'Air Tanzania', 'TC445', 'DAR', 'ZNZ', 4, 8, '09:00:00', '09:50:00', 50, 'economy', 800.00),
(14, 'Air Mauritius', 'MK002', 'JNB', 'MRU', 4, 9, '08:00:00', '12:30:00', 270, 'economy', 3600.00),
(15, 'KLM', 'KL591', 'JNB', 'AMS', 4, 11, '20:00:00', '07:00:00', 660, 'economy', 5800.00),
(16, 'South African Airways', 'SA201', 'JNB', 'CDG', 4, 1, '20:00:00', '06:30:00', 750, 'business', 9800.00),
(17, 'Emirates', 'EK762', 'JNB', 'DXB', 4, 5, '23:55:00', '07:30:00', 455, 'business', 7500.00),
(18, 'FlySafair', 'FA103', 'JNB', 'DUR', 4, 10, '06:30:00', '07:45:00', 75, 'economy', 650.00),
(19, 'FlySafair', 'FA104', 'DUR', 'JNB', 10, 4, '19:00:00', '20:15:00', 75, 'economy', 650.00),
(20, 'TAP Portugal', 'TP261', 'JNB', 'LIS', 4, 15, '20:15:00', '06:45:00', 630, 'economy', 5200.00);

-- ============================================================
-- 7. ACCOMMODATIONS
-- ============================================================

INSERT IGNORE INTO accommodations (accommodation_id, location_id, name, type, star_rating, description, image_url, price_per_night, address, website_url) VALUES
(1, 1, 'Hotel Le Marais', 'hotel', 4, 'Charming boutique hotel in the heart of Paris Marais district', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800', 1200.00, '15 Rue des Archives, Paris', NULL),
(2, 2, 'Cape Grace Hotel', 'hotel', 5, 'Luxury waterfront hotel with Table Mountain views', 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800', 3500.00, 'West Quay Road, Cape Town', NULL),
(3, 3, 'Shibuya Sky Hotel', 'hotel', 3, 'Modern hotel with rooftop views over Shibuya Crossing', 'https://images.unsplash.com/photo-1455587734955-081b22074882?w=800', 800.00, '2-1 Shibuya, Tokyo', NULL),
(4, 5, 'Burj Al Arab Jumeirah', 'hotel', 5, 'The world\'s most luxurious hotel, shaped like a sail over the sea', 'https://images.unsplash.com/photo-1512632578888-169bbbc64f33?w=800', 12000.00, 'Jumeirah Beach Road, Dubai', NULL),
(5, 6, 'Hemingways Nairobi', 'hotel', 5, 'Award-winning boutique hotel in the Karen suburb of Nairobi', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800', 2800.00, 'Karen Road, Nairobi', NULL),
(6, 7, 'Park Hyatt New York', 'hotel', 5, 'Iconic luxury hotel steps from Central Park and Carnegie Hall', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800', 6500.00, '153 West 57th Street, New York', NULL),
(7, 8, 'Zanzibar Beach Resort', 'resort', 4, 'Stunning beachfront resort with overwater bungalows and coral reef', 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800', 2200.00, 'Kendwa Beach, Zanzibar', NULL),
(8, 9, 'Constance Belle Mare', 'resort', 5, 'Ultra-luxe resort on Mauritius\' finest lagoon with spa and golf', 'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=800', 8500.00, 'Belle Mare, Mauritius', NULL),
(9, 11, 'Pulitzer Amsterdam', 'hotel', 5, 'A collection of 25 Golden Age canal houses turned iconic hotel', 'https://images.unsplash.com/photo-1541123437800-1bb1317badc2?w=800', 3200.00, 'Prinsengracht 315, Amsterdam', NULL),
(10, 12, 'Mandarin Oriental Bangkok', 'hotel', 5, 'Legendary luxury on the banks of the Chao Phraya river since 1876', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800', 4200.00, '48 Oriental Avenue, Bangkok', NULL),
(11, 13, 'Park Hyatt Sydney', 'hotel', 5, 'Unrivalled views of the Opera House and Harbour Bridge', 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800', 5800.00, '7 Hickson Road, Sydney', NULL),
(12, 14, 'La Mamounia Marrakech', 'hotel', 5, 'Legendary palace hotel in magnificent Moorish gardens', 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800', 4500.00, 'Avenue Bab Jdid, Marrakech', NULL),
(13, 10, 'The Oyster Box Durban', 'hotel', 5, 'Iconic colonial-style hotel on Umhlanga Rocks beach', 'https://images.unsplash.com/photo-1615880484746-a134be9a6ecf?w=800', 3200.00, '2 Lighthouse Road, Umhlanga', NULL),
(14, 15, 'Bairro Alto Hotel Lisbon', 'hotel', 5, 'Contemporary luxury in the historic bohemian Bairro Alto district', 'https://images.unsplash.com/photo-1621293954908-907159247fc8?w=800', 2800.00, 'Praça Luis de Camões 2, Lisbon', NULL),
(15, 4, 'The Saxon Boutique Hotel', 'hotel', 5, 'Where Nelson Mandela wrote Long Walk to Freedom, ultra-private', 'https://images.unsplash.com/photo-1551918120-9739cb430c6d?w=800', 4800.00, '36 Saxon Road, Sandhurst, JHB', NULL),
(16, 2, 'Kempinski Camps Bay', 'hotel', 5, 'Beachfront luxury with mountain and ocean panoramas in Camps Bay', 'https://images.unsplash.com/photo-1573052905904-34ad8c27f0cc?w=800', 4200.00, 'Victoria Road, Camps Bay', NULL),
(17, 8, 'Matemwe Lodge Zanzibar', 'villa', 4, 'Intimate clifftop lodge with private plunge pools and ocean views', 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?w=800', 3100.00, 'Matemwe Beach, North Zanzibar', NULL),
(18, 9, 'Dinarobin Beachcomber', 'resort', 5, 'Exclusive resort on the tranquil southwestern peninsula of Mauritius', 'https://images.unsplash.com/photo-1540202404-a2f29016b523?w=800', 6200.00, 'Le Morne Peninsula, Mauritius', NULL),
(19, 1, 'Hotel Eiffel Blomet', 'hotel', 3, 'Cosy affordable hotel in the 15th arrondissement, Metro accessible', 'https://images.unsplash.com/photo-1522798514-97ceb8c4f1c8?w=800', 650.00, '78 Rue Blomet, Paris', NULL),
(20, 5, 'Rove Downtown Dubai', 'hotel', 3, 'Stylish mid-range hotel in the heart of Downtown Dubai', 'https://images.unsplash.com/photo-1560347876-aeef00ee58a1?w=800', 900.00, 'Sheikh Mohammed bin Rashid Blvd', NULL);

-- ============================================================
-- 8. ACCOMMODATION AMENITIES
-- ============================================================

INSERT IGNORE INTO accommodationamenities (accommodation_id, amenity) VALUES
(1, 'wifi'), (1, 'breakfast'),
(2, 'pool'), (2, 'wifi'), (2, 'spa'), (2, 'gym'), (2, 'breakfast'),
(3, 'wifi'), (3, 'gym'),
(4, 'pool'), (4, 'wifi'), (4, 'spa'), (4, 'gym'), (4, 'breakfast'), (4, 'parking'),
(5, 'pool'), (5, 'wifi'), (5, 'gym'), (5, 'breakfast'), (5, 'parking'),
(6, 'wifi'), (6, 'gym'), (6, 'spa'),
(7, 'pool'), (7, 'wifi'), (7, 'breakfast'),
(8, 'pool'), (8, 'wifi'), (8, 'spa'), (8, 'gym'), (8, 'breakfast'),
(9, 'wifi'), (9, 'breakfast'),
(10, 'pool'), (10, 'wifi'), (10, 'spa'), (10, 'gym'), (10, 'breakfast'),
(11, 'pool'), (11, 'wifi'), (11, 'spa'), (11, 'gym'),
(12, 'pool'), (12, 'wifi'), (12, 'spa'), (12, 'gym'), (12, 'breakfast'), (12, 'parking'),
(13, 'pool'), (13, 'wifi'), (13, 'breakfast'), (13, 'parking'),
(14, 'wifi'), (14, 'breakfast'), (14, 'spa'),
(15, 'pool'), (15, 'wifi'), (15, 'spa'), (15, 'gym'), (15, 'parking'),
(16, 'pool'), (16, 'wifi'), (16, 'spa'), (16, 'gym'), (16, 'breakfast'),
(17, 'pool'), (17, 'wifi'), (17, 'breakfast'),
(18, 'pool'), (18, 'wifi'), (18, 'spa'), (18, 'breakfast'),
(19, 'wifi'),
(20, 'wifi'), (20, 'gym'), (20, 'pool');

-- ============================================================
-- 9. ATTRACTIONS
-- ============================================================

INSERT IGNORE INTO attractions (attraction_id, location_id, name, category, description, image_url, entry_fee, opening_hours, website_url) VALUES
(1, 1, 'Eiffel Tower', 'landmark', 'Iconic iron lattice tower on the Champ de Mars, symbol of France', 'https://images.unsplash.com/photo-1511739001486-6bfe10ce785f?w=800', 28.30, 'Mon-Sun 09:00-23:00', NULL),
(2, 1, 'Louvre Museum', 'museum', 'World\'s largest art museum and home of the Mona Lisa', 'https://images.unsplash.com/photo-1565099824688-0e4f3d7f72dc?w=800', 22.00, 'Mon,Thu,Sat,Sun 09:00-18:00', NULL),
(3, 2, 'Table Mountain', 'landmark', 'Flat-topped mountain offering panoramic views over Cape Town', 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=800', 0.00, 'Mon-Sun Sunrise-Sunset', NULL),
(4, 2, 'Robben Island', 'historical', 'UNESCO World Heritage site where Nelson Mandela was imprisoned', 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800', 650.00, 'Tours at 09:00, 11:00, 13:00', NULL),
(5, 3, 'Senso-ji Temple', 'historical', 'Tokyo\'s oldest temple in Asakusa, a spiritual and cultural icon', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', 0.00, 'Mon-Sun 06:00-17:00', NULL),
(6, 3, 'Shibuya Crossing', 'landmark', 'The world\'s busiest pedestrian crossing and ultimate Tokyo experience', 'https://images.unsplash.com/photo-1542051841857-5f90071e7989?w=800', 0.00, 'Open 24hrs', NULL),
(7, 5, 'Burj Khalifa', 'landmark', 'The world\'s tallest building at 828m with observation decks', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800', 149.00, 'Mon-Sun 08:00-23:00', NULL),
(8, 5, 'Dubai Desert Safari', 'entertainment', 'Dune bashing, camel riding, and traditional Bedouin camp dinner', 'https://images.unsplash.com/photo-1452080353490-4b7f4e4f4f3c?w=800', 280.00, 'Tours at 15:00 daily', NULL),
(9, 6, 'Maasai Mara Safari', 'park', 'World-famous game reserve and site of the Great Migration', 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=800', 200.00, 'Open daily', NULL),
(10, 7, 'Times Square', 'entertainment', 'The commercial and entertainment crossroads of the world', 'https://images.unsplash.com/photo-1485871981521-5b1fd3805795?w=800', 0.00, 'Open 24hrs', NULL),
(11, 7, 'The Metropolitan Museum', 'museum', 'One of the world\'s greatest art museums with 5,000 years of history', 'https://images.unsplash.com/photo-1538965501903-8b09ed5412e3?w=800', 30.00, 'Mon-Thu,Sun 10:00-17:00 Fri-Sat 10:00-21:00', NULL),
(12, 8, 'Stone Town', 'historical', 'UNESCO World Heritage old town with Swahili and Arab architecture', 'https://images.unsplash.com/photo-1586861635167-e5223aadc9fe?w=800', 0.00, 'Open 24hrs', NULL),
(13, 9, 'Blue Bay Marine Park', 'beach', 'Pristine marine reserve with snorkelling and glass-bottom boat tours', 'https://images.unsplash.com/photo-1544551763-77ef2d0cfc6c?w=800', 0.00, 'Mon-Sun 08:00-17:00', NULL),
(14, 11, 'Anne Frank House', 'historical', 'The hiding place of Anne Frank during WWII, now a moving museum', 'https://images.unsplash.com/photo-1534351590666-13e3e96b5017?w=800', 14.00, 'Mon-Sun 09:00-22:00', NULL),
(15, 12, 'Grand Palace Bangkok', 'historical', 'Dazzling royal complex with Wat Phra Kaew (Temple of the Emerald Buddha)', 'https://images.unsplash.com/photo-1563492065599-3520f775eeed?w=800', 500.00, 'Mon-Sun 08:30-15:30', NULL),
(16, 13, 'Sydney Opera House', 'landmark', 'UNESCO-listed architectural masterpiece on Sydney Harbour', 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800', 45.00, 'Mon-Sun 09:00-17:00', NULL),
(17, 14, 'Djemaa el-Fna Square', 'entertainment', 'The beating heart of Marrakech with musicians, storytellers, and food', 'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?w=800', 0.00, 'Open 24hrs', NULL),
(18, 10, 'uShaka Marine World', 'theme_park', 'Africa\'s largest marine theme park on Durban\'s Golden Mile', 'https://images.unsplash.com/photo-1551244072-5d12893278bc?w=800', 280.00, 'Mon-Sun 09:00-17:00', NULL),
(19, 15, 'Jerónimos Monastery', 'historical', 'Magnificent Manueline Gothic monastery and UNESCO World Heritage site', 'https://images.unsplash.com/photo-1548707309-dcebeab9ea9b?w=800', 10.00, 'Tue-Sun 10:00-18:00', NULL),
(20, 4, 'Apartheid Museum', 'museum', 'Powerful museum documenting the rise and fall of apartheid in South Africa', 'https://images.unsplash.com/photo-1577717907211-b0ee4cd2bb17?w=800', 180.00, 'Tue-Sun 09:00-17:00', NULL);

-- ============================================================
-- 10. RESTAURANTS
-- ============================================================

INSERT IGNORE INTO restaurants (restaurant_id, location_id, name, cuisine_type, price_range, description, image_url, address, average_rating) VALUES
(1, 1, 'Le Petit Bistro', 'French', 'mid-range', 'Classic French dishes in a cozy Left Bank setting', 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800', 'Rue de Buci, Paris', 4.50),
(2, 2, 'The Test Kitchen', 'Fusion', 'fine-dining', 'Award-winning fine dining from South Africa\'s most celebrated chef', 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800', 'Old Biscuit Mill, Cape Town', 4.80),
(3, 3, 'Ichiran Ramen', 'Japanese', 'budget', 'Famous solo-booth ramen dining — the authentic Tokyo experience', 'https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=800', '1-22-7 Jinnan, Shibuya, Tokyo', 4.30),
(4, 5, 'Nobu Dubai', 'Japanese', 'fine-dining', 'World-renowned Nobu restaurant in the Atlantis Palm', 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=800', 'Atlantis The Palm, Dubai', 4.70),
(5, 6, 'Carnivore Nairobi', 'African', 'mid-range', 'Africa\'s greatest party and restaurant — wild game barbecue', 'https://images.unsplash.com/photo-1544025162-d76538b2a681?w=800', 'Langata Road, Nairobi', 4.40),
(6, 7, 'Katz\'s Delicatessen', 'American', 'budget', 'Legendary NYC deli since 1888 — home of the pastrami on rye', 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800', '205 E Houston St, New York', 4.60),
(7, 8, 'The Rock Restaurant', 'Seafood', 'fine-dining', 'Famous restaurant built on a rock in the Indian Ocean, Zanzibar', 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800', 'Michamvi Pingwe, Zanzibar', 4.90),
(8, 9, 'Plantation Restaurant', 'Mauritian', 'fine-dining', 'Authentic Mauritian cuisine with ocean views at its finest', 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800', 'Grand Baie, Mauritius', 4.60),
(9, 11, 'Moeders Amsterdam', 'Dutch', 'mid-range', 'Legendary comfort food restaurant with mismatched crockery and charm', 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800', 'Rozengracht 251, Amsterdam', 4.50),
(10, 12, 'Gaggan Anand', 'Indian', 'fine-dining', 'Asia\'s best restaurant — progressive Indian cuisine in Bangkok', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=800', '68/1 Soi Langsuan, Bangkok', 4.90),
(11, 13, 'Quay Restaurant Sydney', 'Australian', 'fine-dining', 'World-class restaurant with Sydney Harbour and Opera House views', 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800', 'Upper Level Overseas Passenger Terminal', 4.80),
(12, 14, 'Nomad Marrakech', 'Moroccan', 'mid-range', 'Modern Moroccan rooftop restaurant in the heart of the Medina', 'https://images.unsplash.com/photo-1560053608-13721b0a1f2a?w=800', 'Derb Aarjan, Marrakech', 4.50),
(13, 10, 'Berea Road Restaurant', 'South African', 'mid-range', 'Celebrating the best of KwaZulu-Natal cuisine with ocean views', 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800', 'Marine Parade, Durban', 4.30),
(14, 15, 'Taberna da Rua das Flores', 'Portuguese', 'mid-range', 'Authentic Lisbon tavern with traditional petiscos and natural wines', 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800', 'Rua das Flores 103, Lisbon', 4.70),
(15, 4, 'Marble Johannesburg', 'South African', 'fine-dining', 'Rooftop wood-fired restaurant at the top of Melrose Arch', 'https://images.unsplash.com/photo-1544025162-d76538b2a681?w=800', 'Melrose Arch, Johannesburg', 4.80);

-- ============================================================
-- 11. PACKAGES
-- ============================================================

INSERT IGNORE INTO packages (package_id, agency_id, favouritable_id, title, description, base_price, currency, duration_days, max_capacity, status, created_at, cover_image_url, cancellation_policy) VALUES
(1, 3, NULL, 'Romantic Paris Escape', '3 nights in Paris including flights and hotel', 8900.00, 'ZAR', 4, 2, 'active', '2026-05-13 10:19:39', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800', 'Free cancellation up to 14 days before departure'),
(2, 3, NULL, 'Tokyo Explorer', '5 nights in Tokyo with city tours and cultural experiences', 12500.00, 'ZAR', 6, 4, 'active', '2026-05-13 10:19:39', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800', 'Free cancellation up to 21 days before departure'),
(3, 4, NULL, 'Cape Town Adventure', '4 nights in Cape Town with Table Mountain tour and wine tasting', 7500.00, 'ZAR', 5, 6, 'active', '2026-05-13 10:19:39', 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800', '50% refund up to 7 days before departure'),
(4, 4, NULL, 'Maasai Mara Safari Special', 'Authentic Kenyan safari with bush camp, game drives and culture', 18500.00, 'ZAR', 7, 8, 'active', NOW(), 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=800', 'Non-refundable — travel insurance strongly recommended'),
(5, 10, NULL, 'Zanzibar Beach Getaway', 'Idyllic Zanzibar with Stone Town, spice farm and beach resort', 14200.00, 'ZAR', 7, 6, 'active', NOW(), 'https://images.unsplash.com/photo-1586861635167-e5223aadc9fe?w=800', 'Free cancellation up to 10 days before departure'),
(6, 10, NULL, 'Mauritius Paradise', 'Luxury Mauritius resort stay with snorkelling and spa treatments', 22000.00, 'ZAR', 8, 4, 'active', NOW(), 'https://images.unsplash.com/photo-1544551763-77ef2d0cfc6c?w=800', 'Free cancellation up to 21 days before departure'),
(7, 11, NULL, 'Dubai Luxury Weekend', 'Premium Dubai experience with Burj Khalifa and desert safari', 16800.00, 'ZAR', 5, 4, 'active', NOW(), 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800', '50% refund up to 14 days before departure'),
(8, 11, NULL, 'New York City Break', 'Explore the Big Apple — Times Square, Central Park and Broadway', 28500.00, 'ZAR', 6, 6, 'active', NOW(), 'https://images.unsplash.com/photo-1485871981521-5b1fd3805795?w=800', 'Free cancellation up to 30 days before departure'),
(9, 3, NULL, 'Amsterdam Canal Discovery', 'Explore Amsterdam by canal boat, bicycle, and on foot', 15600.00, 'ZAR', 5, 8, 'active', NOW(), 'https://images.unsplash.com/photo-1534351590666-13e3e96b5017?w=800', 'Free cancellation up to 14 days before departure'),
(10, 4, NULL, 'Durban Beach Escape', 'Sun, surf, and soul food on South Africa\'s warm east coast', 4800.00, 'ZAR', 4, 10, 'active', NOW(), 'https://images.unsplash.com/photo-1551244072-5d12893278bc?w=800', 'Free cancellation up to 7 days before departure'),
(11, 10, NULL, 'Marrakech Mystique', 'Experience the magic of Morocco — souks, riads, and the Atlas', 11200.00, 'ZAR', 6, 6, 'active', NOW(), 'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?w=800', '50% refund up to 10 days before departure'),
(12, 11, NULL, 'Lisbon & Beyond', 'Portugal\'s charming capital with trams, pastéis, and fado music', 13400.00, 'ZAR', 5, 8, 'active', NOW(), 'https://images.unsplash.com/photo-1548707309-dcebeab9ea9b?w=800', 'Free cancellation up to 14 days before departure'),
(13, 3, NULL, 'Bangkok Temple Trail', 'Explore Bangkok\'s golden temples, floating markets, and cuisine', 10900.00, 'ZAR', 6, 8, 'active', NOW(), 'https://images.unsplash.com/photo-1563492065599-3520f775eeed?w=800', 'Free cancellation up to 14 days before departure'),
(14, 4, NULL, 'Joburg & Soweto Cultural Tour', 'South African history, art, and culture in the city of gold', 5500.00, 'ZAR', 3, 12, 'active', NOW(), 'https://images.unsplash.com/photo-1577717907211-b0ee4cd2bb17?w=800', 'Non-refundable but date-change permitted'),
(15, 10, NULL, 'Sydney Harbour Special', 'Discover Australia\'s most iconic city with Opera House and Bondi', 32000.00, 'ZAR', 7, 4, 'active', NOW(), 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800', 'Free cancellation up to 30 days before departure');

-- ============================================================
-- 12. GROUP TRIPS
-- ============================================================

INSERT IGNORE INTO grouptrips (group_trip_id, package_id, departure_date, return_date, min_participants, max_participants, current_participants, meeting_point, status, created_at) VALUES
(1, 4, '2026-07-15', '2026-07-22', 4, 8, 0, 'OR Tambo International Airport, Terminal A, 06:00', 'open', NOW()),
(2, 5, '2026-08-01', '2026-08-08', 2, 6, 0, 'OR Tambo International Airport, Terminal B, 07:30', 'open', NOW()),
(3, 10, '2026-06-20', '2026-06-24', 4, 10, 0, 'King Shaka International Airport, Departures, 08:00', 'open', NOW()),
(4, 11, '2026-09-05', '2026-09-11', 2, 6, 0, 'OR Tambo International Airport, Terminal A, 05:45', 'open', NOW()),
(5, 14, '2026-07-01', '2026-07-04', 4, 12, 0, 'OR Tambo International Airport, Arrivals Hall, 09:00','open', NOW());

-- ============================================================
-- 13. PACKAGE DESTINATIONS
-- ============================================================

INSERT IGNORE INTO packagedestinations (package_id, destination_id, day_number, visit_order) VALUES
(1, 1, 1, 1),
(2, 3, 1, 1),
(3, 2, 1, 1),
(4, 5, 1, 1),
(5, 7, 1, 1),
(6, 8, 1, 1),
(7, 4, 1, 1),
(8, 6, 1, 1),
(9, 9, 1, 1),
(10, 13, 1, 1),
(11, 12, 1, 1),
(12, 14, 1, 1),
(13, 10, 1, 1),
(14, 15, 1, 1),
(15, 11, 1, 1);

-- ============================================================
-- 14. PACKAGE FLIGHTS
-- ============================================================

INSERT IGNORE INTO packageflights (package_id, flight_id, is_outbound) VALUES
(1, 1, 1), (1, 2, 0),
(2, 3, 1), (2, 4, 0),
(3, 5, 1), (3, 6, 0),
(4, 9, 1), (4, 10, 0),
(5, 9, 1), (5, 13, 1), (5, 10, 0),
(6, 14, 1),
(7, 7, 1), (7, 8, 0),
(8, 11, 1), (8, 12, 0),
(9, 15, 1),
(10, 18, 1), (10, 19, 0),
(11, 15, 1),
(12, 20, 1),
(13, 7, 1),
(15, 7, 1);

-- ============================================================
-- 15. PACKAGE ACCOMMODATIONS
-- ============================================================

INSERT IGNORE INTO packageaccommodations (package_id, accommodation_id, num_nights, check_in_day) VALUES
(1, 1, 3, 1),
(2, 3, 5, 1),
(3, 2, 4, 1),
(3, 16, 1, 5),
(4, 5, 6, 1),
(5, 7, 6, 1),
(5, 17, 1, 6),
(6, 8, 7, 1),
(6, 18, 1, 7),
(7, 4, 4, 1),
(7, 20, 1, 5),
(8, 6, 5, 1),
(9, 9, 4, 1),
(10, 13, 3, 1),
(11, 12, 5, 1),
(12, 14, 4, 1),
(13, 10, 5, 1),
(14, 15, 2, 1),
(15, 11, 6, 1);

-- ============================================================
-- 16. PACKAGE ATTRACTIONS
-- ============================================================

INSERT IGNORE INTO packageattractions (package_id, attraction_id, visit_day, is_included) VALUES
(1, 1, 2, 1), (1, 2, 3, 1),
(2, 5, 2, 1), (2, 6, 3, 1),
(3, 3, 2, 1), (3, 4, 3, 1),
(4, 9, 2, 1),
(5, 12, 2, 1),
(6, 13, 3, 1),
(7, 7, 2, 1), (7, 8, 3, 1),
(8, 10, 2, 1), (8, 11, 3, 1),
(9, 14, 2, 1),
(10, 18, 2, 1),
(11, 17, 2, 1),
(12, 19, 2, 1),
(13, 15, 2, 1),
(14, 20, 2, 1),
(15, 16, 2, 1);

-- ============================================================
-- 17. PACKAGE RESTAURANTS
-- ============================================================

INSERT IGNORE INTO packagerestaurants (package_id, restaurant_id, meal_type, visit_day) VALUES
(1, 1, 'dinner', 2),
(2, 3, 'lunch', 2),
(3, 2, 'dinner', 3),
(4, 5, 'dinner', 2),
(5, 7, 'dinner', 3),
(6, 8, 'dinner', 4),
(7, 4, 'dinner', 2),
(8, 6, 'lunch', 3),
(9, 9, 'dinner', 2),
(10, 13, 'lunch', 2),
(11, 12, 'dinner', 3),
(12, 14, 'dinner', 2),
(13, 10, 'dinner', 3),
(14, 15, 'dinner', 2),
(15, 11, 'dinner', 3);

-- ============================================================
-- 18. BOOKINGS
-- ============================================================

INSERT IGNORE INTO bookings (booking_id, traveller_id, package_id, group_trip_id, travel_date, num_travellers, total_price, currency, status, payment_reference, special_requests, created_at) VALUES
(1, 1, 1, NULL, '2026-06-10', 2, 17800.00, 'ZAR', 'completed', 'PAY-001-2026', 'Vegetarian meals preferred', '2026-04-01 09:00:00'),
(2, 2, 3, NULL, '2026-07-20', 1, 7500.00, 'ZAR', 'confirmed', 'PAY-002-2026', NULL, '2026-05-01 11:30:00'),
(3, 5, 4, 1, '2026-07-15', 1, 18500.00, 'ZAR', 'confirmed', 'PAY-003-2026', 'Window seat on flight please', '2026-05-05 14:00:00'),
(4, 6, 7, NULL, '2026-07-01', 2, 33600.00, 'ZAR', 'confirmed', 'PAY-004-2026', 'Honeymoon suite if available', '2026-05-10 08:00:00'),
(5, 7, 2, NULL, '2026-08-12', 1, 12500.00, 'ZAR', 'pending', 'PAY-005-2026', NULL, '2026-05-15 16:45:00'),
(6, 8, 5, 2, '2026-08-01', 1, 14200.00, 'ZAR', 'confirmed', 'PAY-006-2026', 'I have a shellfish allergy', '2026-05-12 10:00:00'),
(7, 9, 6, NULL, '2026-09-10', 2, 44000.00, 'ZAR', 'confirmed', 'PAY-007-2026', 'Anniversary trip — surprise setup', '2026-05-13 09:30:00'),
(8, 1, 9, NULL, '2026-10-05', 1, 15600.00, 'ZAR', 'pending', 'PAY-008-2026', NULL, '2026-05-18 12:00:00'),
(9, 2, 10, 3, '2026-06-20', 1, 4800.00, 'ZAR', 'confirmed', 'PAY-009-2026', 'Early check-in requested', '2026-05-19 07:00:00'),
(10, 5, 8, NULL, '2026-11-20', 2, 57000.00, 'ZAR', 'pending', 'PAY-010-2026', 'Upper floors preferred', '2026-05-20 15:00:00');

-- ============================================================
-- 19. AGENCY REVIEWS
-- ============================================================

INSERT IGNORE INTO agencyreviews (review_id, traveller_id, agency_id, booking_id, rating, comment, created_at) VALUES
(1, 1, 3, 1, 5, 'Amazing trip, everything was perfectly organized! The Paris hotel was stunning and flights were seamless. Will definitely book with Global Travel again.', '2026-06-20 10:00:00');

-- ============================================================
-- 20. PACKAGE REVIEWS
-- ============================================================

INSERT IGNORE INTO packagereviews (review_id, traveller_id, package_id, booking_id, rating, comment, created_at) VALUES
(1, 1, 1, 1, 5, 'The Paris Escape package was everything I dreamed of. The Eiffel Tower visit at night was magical, and Hotel Le Marais had incredible character. Highly recommend!', '2026-06-21 11:00:00');

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
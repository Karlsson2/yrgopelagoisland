# Jurassic Hotel on Jurassic Island

This is a lovely island with a great hotel full of travellers wishing to have an amazing encounter with the dinosaurs!

# Instructions

In order to run the project, simply download and fill out your ENV with the following data:

```
API_KEY=your-api-key-here
ISLAND_NAME="The name of your island"
HOTEL_NAME="The name of your hotel"
USER_NAME="Your username"
STARS=4
```

The username and api-key can be retrieved from www.yrgopelag.se

# Instructions

Database is Sqlite and can be regenerated with the following code:

Table creation:

```
CREATE TABLE IF NOT EXISTS rooms (id integer primary key AUTOINCREMENT, category varchar, price_per_night float, image1 varchar, image2 varchar, sleeps integer, view varchar, aircon BOOLEAN, description varchar);

CREATE TABLE IF NOT EXISTS booking (id integer primary key AUTOINCREMENT, arrival_date date, departure_date date, transfercode varchar, total_cost float, room_id integer,
FOREIGN KEY (room_id) REFERENCES rooms(id));

CREATE TABLE IF NOT EXISTS features (id integer primary key AUTOINCREMENT, name varchar, price float, image varchar, description varchar);

CREATE TABLE IF NOT EXISTS booking_features (id integer primary key AUTOINCREMENT, booking_id integer, feature_id integer, FOREIGN KEY (booking_id) REFERENCES booking(id), FOREIGN KEY (feature_id) REFERENCES feature(id));

CREATE TABLE IF NOT EXISTS discounts (
id INTEGER PRIMARY KEY AUTOINCREMENT,
room_id INTEGER,
discount_percentage FLOAT,
description VARCHAR,
days_required INTEGER,
FOREIGN KEY (room_id) REFERENCES rooms(id)
);
CREATE TABLE IF NOT EXISTS reviews (
id INTEGER PRIMARY KEY AUTOINCREMENT,
room_id INTEGER,
description VARCHAR,
name VARCHAR,
time date,
rating integer,
FOREIGN KEY (room_id) REFERENCES rooms(id)
);
```

CREATE TABLE IF NOT EXISTS signups (id INTEGER PRIMARY KEY AUTOINCREMENT, email varchar);```

Data insertion:

```
-- Insert fake data into the 'rooms' table
INSERT INTO rooms (id, category, price_per_night, image1, image2,sleeps,view,aircon,description)
VALUES
(1, 'Standard', 04.00, 'images/single.jpg', 'images/single-bathroom.jpg', 1, 'Jungle', false, 'Discover serenity in our Jurassic Standard Rooms, where simplicity meets comfort amidst the vibrant jungle. These cozy retreats offer a tranquil escape, immersing you in the rhythmic symphony of nature. With a focus on modern conveniences, the room balances the allure of the islands untamed beauty with the comfort of contemporary living. Admire the lush jungle view from your window, providing a constant connection to the captivating landscape just outside.'),
(2, 'Superior', 07.00, 'images/double.jpg', 'images/superior-bathroom.jpg', 2, 'Beach', true, 'Elevate your experience in the heart of the Jurassic with our Superior Rooms, boasting spacious luxury and an enchanting ocean view. From your private balcony, absorb breathtaking vistas of the azure waters and pristine coastline, harmonizing the ancient charm of the island with modern elegance. Superior Rooms offer upgraded amenities for an immersive and comfortable escape, ensuring a perfect blend of opulence and the mesmerizing natural beauty of the oceanic panorama.'),
(3, 'Suite', 15.00, 'images/suite.jpg', 'images/suite-bathroom.jpg', 3, 'Beach', true, 'Indulge in opulence with our Suites, where luxury seamlessly intertwines with the untamed wilderness, and an expansive ocean view unfolds before you. Step onto your private terrace, complete with a soothing hot tub, and unwind in style amidst the ancient allure of the Jurassic era. Panoramic views of the ocean create an immersive experience, complemented by personalized services and exquisite details. Every moment in our Suites is a celebration of extravagance, leaving you with memories of the islands prehistoric wonders and the rhythmic ebb and flow of the ocean waves.');

-- Insert fake data into the 'features' table
INSERT INTO features (id, name, price, image, description)
VALUES
(1, 'Swim with dinosaurs', 01.00, 'images/swim.jpg', 'Immerse yourself in the ultimate adventure with our "Swimming with Real Dinosaurs" experience. Plunge into the depths and share the waters with living, breathing dinosaurs. Witness their graceful movements, feel the prehistoric presence, and create indelible memories as you swim alongside these magnificent creatures in a one-of-a-kind encounter that brings the past to life.'),
(2, 'Visit the incubation room', 02.00, 'images/incubation.jpg', 'Step into the heart of dinosaur creation with a visit to our "Incubation Room." Witness the marvels of life as you observe real dinosaur eggs in various stages of development. Learn about the delicate process of hatching and the science behind resurrecting these ancient beings. A captivating and educational experience awaits, offering a glimpse into the extraordinary world of dinosaur birth.'),
(3, 'Fly amongst dinosaurs', 03.00, 'images/flight.jpg', 'Embark on a breathtaking adventure with our "Flying Amongst Dinosaurs" activity. Soar through the skies aboard a specially designed aircraft, surrounded by majestic, lifelike dinosaurs. Experience the awe of flying alongside these colossal creatures, offering a unique perspective on their grandeur. This exhilarating journey combines thrills and wonder, promising an unforgettable aerial encounter with the giants of the past.'),
(4, 'Dinosaur Safari', 03.00, 'images/safari.jpg', 'Embark on a thrilling "Dinosaur Safari" and traverse the ancient landscapes where dinosaurs once roamed. Board our safari vehicles for a guided tour through realistic habitats, encountering life-sized, animatronic dinosaurs in their natural settings. Get up close and personal with these incredible creatures, as expert guides share fascinating insights into their behavior and history. A safari adventure that promises excitement, education, and the chance to witness the wonders of the prehistoric world.');

-- Discounts for Room 1 (Single Room)
INSERT INTO discounts (room_id, discount_percentage, description, days_required) VALUES (1, 0.05, '5% Off for 3 Days Stay', 3);
INSERT INTO discounts (room_id, discount_percentage, description, days_required) VALUES (1, 0.1, '10% Off for 7 Days Stay', 7);
INSERT INTO discounts (room_id, discount_percentage, description, days_required) VALUES (1, 0.15, '15% Off for 14 Days Stay', 14);

-- Discounts for Room 2 (Superior Room)
INSERT INTO discounts (room_id, discount_percentage, description, days_required) VALUES (2, 0.07, '7% Off for 5 Days Stay', 5);
INSERT INTO discounts (room_id, discount_percentage, description, days_required) VALUES (2, 0.12, '12% Off for 10 Days Stay', 10);
INSERT INTO discounts (room_id, discount_percentage, description, days_required) VALUES (2, 0.2, '20% Off for 15 Days Stay', 15);

-- Discounts for Room 3 (Suite)
INSERT INTO discounts (room_id, discount_percentage, description, days_required) VALUES (3, 0.1, '10% Off for 3 Days Stay', 3);
INSERT INTO discounts (room_id, discount_percentage, description, days_required) VALUES (3, 0.18, '18% Off for 8 Days Stay', 8);
INSERT INTO discounts (room_id, discount_percentage, description, days_required) VALUES (3, 0.25, '25% Off for 12 Days Stay', 12);
```

# Code review

1. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
2. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
3. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
4. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
5. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
6. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
7. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
8. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
9. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
10. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.

```

```

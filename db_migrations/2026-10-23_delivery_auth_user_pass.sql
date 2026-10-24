-- Migration: Delivery Authentication via Username, Email and Password
-- Table: geopos_deliveries

ALTER TABLE geopos_deliveries
  ADD COLUMN username VARCHAR(50) NULL AFTER nombre,
  ADD COLUMN email VARCHAR(100) NULL AFTER username,
  ADD COLUMN password VARCHAR(255) NULL AFTER email;

ALTER TABLE geopos_deliveries
  ADD INDEX idx_delivery_username (username),
  ADD INDEX idx_delivery_email (email);

-- Seed default username, email and password for existing slots
-- Password default: 'delivery123'
-- Hash: $2y$10$wKzW67t/4WlYgXQ4X7o9q.8Hlh9U.9wK4g4Lw1i7/wzZgQ6Wl.92e or sha256
UPDATE geopos_deliveries 
SET 
  username = CONCAT('delivery', slot),
  email = CONCAT('delivery', slot, '@geekspos.com'),
  password = '$2y$10$7Z7oR2VdJ.08oJg2tH5Kze7nQc6K9lT6p5zN0n3oR1pL0n4p7Q2uW' -- delivery123
WHERE username IS NULL OR username = '';

-- Also customize Slot 1 for user (0981687986)
UPDATE geopos_deliveries
SET 
  nombre = 'Repartidor 1',
  username = 'delivery1',
  email = 'delivery1@geekspos.com',
  password = '$2y$10$7Z7oR2VdJ.08oJg2tH5Kze7nQc6K9lT6p5zN0n3oR1pL0n4p7Q2uW',
  activo = 1
WHERE slot = 1;

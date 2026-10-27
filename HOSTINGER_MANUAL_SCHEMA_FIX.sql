-- Tourliz CMS production schema repair
-- Generated: 2026-10-27
-- Import this file into the Tourliz production database using phpMyAdmin.
-- Take a database backup before running any schema update.

-- Fix the current normalized-itinerary import error.
ALTER TABLE `itineraries`
    ADD COLUMN IF NOT EXISTS `currency` VARCHAR(10) NOT NULL DEFAULT 'MYR' AFTER `status`;

ALTER TABLE `itinerary_b2c_details`
    ADD COLUMN IF NOT EXISTS `currency` VARCHAR(10) NOT NULL DEFAULT 'MYR' AFTER `selling_price`;

ALTER TABLE `itinerary_b2b_details`
    ADD COLUMN IF NOT EXISTS `currency` VARCHAR(10) NOT NULL DEFAULT 'MYR' AFTER `net_rate`;

-- Support optional, flexible, and exact departure dates for B2B leads.
ALTER TABLE `custom_itineraries`
    ADD COLUMN IF NOT EXISTS `travel_date_status` VARCHAR(20) NOT NULL DEFAULT 'tbd' AFTER `start_date`,
    ADD COLUMN IF NOT EXISTS `preferred_travel_month` VARCHAR(7) NULL AFTER `travel_date_status`,
    ADD COLUMN IF NOT EXISTS `travel_date_notes` VARCHAR(255) NULL AFTER `preferred_travel_month`;

-- Support optional, flexible, and exact departure dates for B2C/walk-in leads.
ALTER TABLE `b2c_itineraries`
    ADD COLUMN IF NOT EXISTS `travel_date_status` VARCHAR(20) NOT NULL DEFAULT 'tbd' AFTER `start_date`,
    ADD COLUMN IF NOT EXISTS `preferred_travel_month` VARCHAR(7) NULL AFTER `travel_date_status`,
    ADD COLUMN IF NOT EXISTS `travel_date_notes` VARCHAR(255) NULL AFTER `preferred_travel_month`;

-- Preserve existing exact dates when introducing the new date-status field.
UPDATE `custom_itineraries`
SET `travel_date_status` = 'exact'
WHERE `start_date` IS NOT NULL
  AND (`travel_date_status` IS NULL OR `travel_date_status` = '' OR `travel_date_status` = 'tbd');

UPDATE `b2c_itineraries`
SET `travel_date_status` = 'exact'
WHERE `start_date` IS NOT NULL
  AND (`travel_date_status` IS NULL OR `travel_date_status` = '' OR `travel_date_status` = 'tbd');

-- Verification output: each result should show the new columns.
SHOW COLUMNS FROM `itineraries` LIKE 'currency';
SHOW COLUMNS FROM `itinerary_b2c_details` LIKE 'currency';
SHOW COLUMNS FROM `itinerary_b2b_details` LIKE 'currency';
SHOW COLUMNS FROM `custom_itineraries` LIKE 'travel_date_status';
SHOW COLUMNS FROM `b2c_itineraries` LIKE 'travel_date_status';

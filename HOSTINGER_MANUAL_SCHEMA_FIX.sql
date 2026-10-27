-- Tourliz CMS production schema repair
-- Generated: 2026-10-27
-- Import this file into the Tourliz production database using phpMyAdmin.
-- Take a database backup before running any schema update.

-- Create normalized itinerary child tables when an older production database
-- has the master itineraries table but never received the child-table migration.
CREATE TABLE IF NOT EXISTS `itinerary_b2c_details` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `itinerary_id` BIGINT UNSIGNED NOT NULL,
    `is_template` TINYINT(1) NOT NULL DEFAULT 1,
    `quotation_reference` VARCHAR(255) NULL,
    `lead_id` BIGINT UNSIGNED NULL,
    `customer_name` VARCHAR(255) NULL,
    `customer_whatsapp` VARCHAR(255) NULL,
    `customer_email` VARCHAR(255) NULL,
    `travel_date_start` DATE NULL,
    `travel_date_end` DATE NULL,
    `adults` INT NOT NULL DEFAULT 2,
    `children` INT NOT NULL DEFAULT 0,
    `rooms` INT NOT NULL DEFAULT 1,
    `lead_source` VARCHAR(255) NULL,
    `assigned_staff_id` BIGINT UNSIGNED NULL,
    `short_description` TEXT NULL,
    `selling_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(10) NOT NULL DEFAULT 'MYR',
    `child_price` DECIMAL(12,2) NULL,
    `image` VARCHAR(255) NULL,
    `gallery` JSON NULL,
    `seo_title` VARCHAR(255) NULL,
    `seo_description` TEXT NULL,
    `seo_keywords` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `itinerary_b2c_details_itinerary_id_unique` (`itinerary_id`),
    UNIQUE KEY `itinerary_b2c_details_quotation_reference_unique` (`quotation_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `itinerary_b2b_details` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `itinerary_id` BIGINT UNSIGNED NOT NULL,
    `agency_id` BIGINT UNSIGNED NULL,
    `net_rate` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(10) NOT NULL DEFAULT 'MYR',
    `agent_rate` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `markup_percentage` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `markup_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `commission` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `min_pax` INT NOT NULL DEFAULT 1,
    `max_pax` INT NOT NULL DEFAULT 50,
    `hotel_category` VARCHAR(255) NULL,
    `room_category` VARCHAR(255) NULL,
    `vehicle_category` VARCHAR(255) NULL,
    `guide_required` TINYINT(1) NOT NULL DEFAULT 0,
    `supplier_id` BIGINT UNSIGNED NULL,
    `supplier_ids` JSON NULL,
    `validity_start` DATE NULL,
    `validity_end` DATE NULL,
    `operational_notes` TEXT NULL,
    `agent_notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `itinerary_b2b_details_itinerary_id_unique` (`itinerary_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `itinerary_days` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `itinerary_id` BIGINT UNSIGNED NOT NULL,
    `day_number` INT NOT NULL,
    `title` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `highlights` JSON NULL,
    `inclusions` JSON NULL,
    `exclusions` JSON NULL,
    `overnight_location` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `itinerary_days_itinerary_id_day_number_index` (`itinerary_id`, `day_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `itinerary_day_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `itinerary_day_id` BIGINT UNSIGNED NOT NULL,
    `item_type` VARCHAR(255) NULL,
    `item_id` BIGINT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `supplier_id` BIGINT UNSIGNED NULL,
    `room_type` VARCHAR(255) NULL,
    `start_time` TIME NULL,
    `end_time` TIME NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `itinerary_day_items_day_sort_index` (`itinerary_day_id`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fix the current normalized-itinerary import error.
ALTER TABLE `itineraries`
    ADD COLUMN IF NOT EXISTS `currency` VARCHAR(10) NOT NULL DEFAULT 'MYR' AFTER `status`;

-- Older Tourliz databases used itineraries.package_id as a mandatory field.
-- Unified B2B/B2C itineraries do not belong to a package, so make it nullable
-- only when that legacy column is present.
SET @package_id_fix = IF(
    EXISTS(
        SELECT 1
        FROM `information_schema`.`COLUMNS`
        WHERE `TABLE_SCHEMA` = DATABASE()
          AND `TABLE_NAME` = 'itineraries'
          AND `COLUMN_NAME` = 'package_id'
          AND `IS_NULLABLE` = 'NO'
    ),
    'ALTER TABLE `itineraries` MODIFY `package_id` BIGINT UNSIGNED NULL DEFAULT NULL',
    'SELECT ''itineraries.package_id is already nullable or does not exist'' AS message'
);
PREPARE package_id_statement FROM @package_id_fix;
EXECUTE package_id_statement;
DEALLOCATE PREPARE package_id_statement;

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
SHOW COLUMNS FROM `itineraries` LIKE 'package_id';
SHOW COLUMNS FROM `itinerary_b2c_details` LIKE 'currency';
SHOW COLUMNS FROM `itinerary_b2b_details` LIKE 'currency';
SHOW TABLES LIKE 'itinerary_days';
SHOW TABLES LIKE 'itinerary_day_items';
SHOW COLUMNS FROM `custom_itineraries` LIKE 'travel_date_status';
SHOW COLUMNS FROM `b2c_itineraries` LIKE 'travel_date_status';

-- Diagnostic: this result should contain only fields that the application
-- supplies on insert. Any unexpected legacy field listed here must be made
-- nullable before unified itinerary creation can succeed.
SELECT `COLUMN_NAME`, `COLUMN_TYPE`
FROM `information_schema`.`COLUMNS`
WHERE `TABLE_SCHEMA` = DATABASE()
  AND `TABLE_NAME` = 'itineraries'
  AND `IS_NULLABLE` = 'NO'
  AND `COLUMN_DEFAULT` IS NULL
  AND `EXTRA` NOT LIKE '%auto_increment%';

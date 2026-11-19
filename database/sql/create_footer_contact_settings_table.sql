-- Run in phpMyAdmin on production database if migration cannot be run
CREATE TABLE IF NOT EXISTS `footer_contact_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `contact_phone` VARCHAR(255) NULL,
  `contact_email` VARCHAR(255) NULL,
  `contact_address` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_02_160000_create_footer_contact_settings_table', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations`
  WHERE `migration` = '2026_07_02_160000_create_footer_contact_settings_table'
);

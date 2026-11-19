-- Run in phpMyAdmin if artisan migrate cannot be used
-- Adds full footer dynamic columns to footer_contact_settings

ALTER TABLE `footer_contact_settings`
  ADD COLUMN IF NOT EXISTS `foot_kids_image` VARCHAR(255) NULL AFTER `contact_address`,
  ADD COLUMN IF NOT EXISTS `foot_kids_shadow_image` VARCHAR(255) NULL AFTER `foot_kids_image`,
  ADD COLUMN IF NOT EXISTS `form_title` VARCHAR(255) NULL AFTER `foot_kids_shadow_image`,
  ADD COLUMN IF NOT EXISTS `form_subtitle` VARCHAR(255) NULL AFTER `form_title`,
  ADD COLUMN IF NOT EXISTS `enroll_title_small` VARCHAR(255) NULL AFTER `form_subtitle`,
  ADD COLUMN IF NOT EXISTS `enroll_title_highlight` VARCHAR(255) NULL AFTER `enroll_title_small`,
  ADD COLUMN IF NOT EXISTS `enroll_title_suffix` VARCHAR(255) NULL AFTER `enroll_title_highlight`,
  ADD COLUMN IF NOT EXISTS `enroll_ribbon_image` VARCHAR(255) NULL AFTER `enroll_title_suffix`,
  ADD COLUMN IF NOT EXISTS `enroll_product_image` VARCHAR(255) NULL AFTER `enroll_ribbon_image`,
  ADD COLUMN IF NOT EXISTS `enroll_button_text` VARCHAR(255) NULL AFTER `enroll_product_image`,
  ADD COLUMN IF NOT EXISTS `enroll_button_url` VARCHAR(255) NULL AFTER `enroll_button_text`,
  ADD COLUMN IF NOT EXISTS `connect_tag_image` VARCHAR(255) NULL AFTER `enroll_button_url`,
  ADD COLUMN IF NOT EXISTS `facebook_url` VARCHAR(255) NULL AFTER `connect_tag_image`,
  ADD COLUMN IF NOT EXISTS `twitter_url` VARCHAR(255) NULL AFTER `facebook_url`,
  ADD COLUMN IF NOT EXISTS `instagram_url` VARCHAR(255) NULL AFTER `twitter_url`,
  ADD COLUMN IF NOT EXISTS `youtube_url` VARCHAR(255) NULL AFTER `instagram_url`,
  ADD COLUMN IF NOT EXISTS `amazon_url` VARCHAR(255) NULL AFTER `youtube_url`,
  ADD COLUMN IF NOT EXISTS `amazon_logo_image` VARCHAR(255) NULL AFTER `amazon_url`,
  ADD COLUMN IF NOT EXISTS `copyright_text` VARCHAR(255) NULL AFTER `amazon_logo_image`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_14_161500_add_full_fields_to_footer_contact_settings_table', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations`
  WHERE `migration` = '2026_07_14_161500_add_full_fields_to_footer_contact_settings_table'
);

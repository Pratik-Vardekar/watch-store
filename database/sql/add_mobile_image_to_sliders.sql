-- Add mobile_image and sort_order to sliders table if they do not exist
-- Run this in phpMyAdmin or MySQL client for remote / production server

ALTER TABLE `sliders` 
ADD COLUMN IF NOT EXISTS `mobile_image` VARCHAR(255) NULL AFTER `image`,
ADD COLUMN IF NOT EXISTS `sort_order` INT NOT NULL DEFAULT 0 AFTER `status`,
ADD COLUMN IF NOT EXISTS `subtitle` VARCHAR(255) NULL AFTER `title`,
ADD COLUMN IF NOT EXISTS `link` TEXT NULL AFTER `description`;


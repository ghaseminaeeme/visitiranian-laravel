-- ONLY for databases imported from OLD schema.sql (before 2026-07-01).
-- If you get "Unknown column display_template_id", the patch is already applied — skip it.
-- Fresh installs: run ./scripts/local-mysql.sh setup instead.

-- Align sliders/advertisements (and related tables) with Laravel migrations/models.

ALTER TABLE `sliders`
  DROP FOREIGN KEY `sliders_display_template_id_foreign`,
  CHANGE COLUMN `display_template_id` `template_id` BIGINT UNSIGNED NOT NULL,
  ADD COLUMN `deleted_at` TIMESTAMP NULL AFTER `updated_at`,
  ADD CONSTRAINT `sliders_template_id_foreign`
    FOREIGN KEY (`template_id`) REFERENCES `display_templates` (`id`) ON DELETE RESTRICT;

ALTER TABLE `advertisements`
  DROP FOREIGN KEY `advertisements_display_template_id_foreign`,
  CHANGE COLUMN `display_template_id` `template_id` BIGINT UNSIGNED NOT NULL,
  CHANGE COLUMN `impression_count` `impressions_count` INT UNSIGNED NOT NULL DEFAULT 0,
  CHANGE COLUMN `click_count` `clicks_count` INT UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN `deleted_at` TIMESTAMP NULL AFTER `updated_at`,
  ADD CONSTRAINT `advertisements_template_id_foreign`
    FOREIGN KEY (`template_id`) REFERENCES `display_templates` (`id`) ON DELETE RESTRICT;

ALTER TABLE `doctor_photos`
  ADD COLUMN `deleted_at` TIMESTAMP NULL AFTER `updated_at`;

ALTER TABLE `pages`
  ADD COLUMN `deleted_at` TIMESTAMP NULL AFTER `updated_at`;

ALTER TABLE `reviews`
  ADD COLUMN `deleted_at` TIMESTAMP NULL AFTER `updated_at`;

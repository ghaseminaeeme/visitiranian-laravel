-- =============================================================================
-- VisitIranian Doctors Platform — MySQL Schema
-- Charset: utf8mb4_unicode_ci | Engine: InnoDB | MySQL 8.0+
-- Run: mysql -u root -p < database/sql/schema.sql
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `h399366_visitiranianDb`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `h399366_visitiranianDb`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------------
-- Drop existing tables (reverse dependency order)
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `slug_redirects`;
DROP TABLE IF EXISTS `pages`;
DROP TABLE IF EXISTS `short_links`;
DROP TABLE IF EXISTS `support_tickets`;
DROP TABLE IF EXISTS `error_logs`;
DROP TABLE IF EXISTS `site_social_links`;
DROP TABLE IF EXISTS `sliders`;
DROP TABLE IF EXISTS `advertisements`;
DROP TABLE IF EXISTS `ad_placements`;
DROP TABLE IF EXISTS `display_templates`;
DROP TABLE IF EXISTS `sms_logs`;
DROP TABLE IF EXISTS `sms_templates`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `appointment_waitlist`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `doctor_schedule_exceptions`;
DROP TABLE IF EXISTS `doctor_schedules`;
DROP TABLE IF EXISTS `doctor_clinic`;
DROP TABLE IF EXISTS `doctor_photos`;
DROP TABLE IF EXISTS `doctor_social_links`;
DROP TABLE IF EXISTS `doctor_contact_phones`;
DROP TABLE IF EXISTS `doctor_specialty`;
DROP TABLE IF EXISTS `doctors`;
DROP TABLE IF EXISTS `clinics`;
DROP TABLE IF EXISTS `specialties`;
DROP TABLE IF EXISTS `cities`;
DROP TABLE IF EXISTS `provinces`;
DROP TABLE IF EXISTS `media`;
DROP TABLE IF EXISTS `model_has_roles`;
DROP TABLE IF EXISTS `model_has_permissions`;
DROP TABLE IF EXISTS `role_has_permissions`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------------
-- Laravel auth: users
-- ---------------------------------------------------------------------------
CREATE TABLE `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255)    NOT NULL,
  `email`             VARCHAR(255)    NOT NULL,
  `phone`             VARCHAR(20)     NULL,
  `email_verified_at` TIMESTAMP       NULL,
  `password`          VARCHAR(255)    NOT NULL,
  `is_active`         TINYINT(1)      NOT NULL DEFAULT 1,
  `remember_token`    VARCHAR(100)    NULL,
  `created_at`        TIMESTAMP       NULL,
  `updated_at`        TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_phone_index` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id`            VARCHAR(255) NOT NULL,
  `user_id`       BIGINT UNSIGNED NULL,
  `ip_address`    VARCHAR(45)  NULL,
  `user_agent`    TEXT         NULL,
  `payload`       LONGTEXT     NOT NULL,
  `last_activity` INT          NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key`        VARCHAR(255) NOT NULL,
  `value`      MEDIUMTEXT   NOT NULL,
  `expiration` BIGINT       NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key`        VARCHAR(255) NOT NULL,
  `owner`      VARCHAR(255) NOT NULL,
  `expiration` BIGINT       NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue`        VARCHAR(255)    NOT NULL,
  `payload`      LONGTEXT        NOT NULL,
  `attempts`     TINYINT UNSIGNED NOT NULL,
  `reserved_at`  INT UNSIGNED    NULL,
  `available_at` INT UNSIGNED    NOT NULL,
  `created_at`   INT UNSIGNED    NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id`             VARCHAR(255) NOT NULL,
  `name`           VARCHAR(255) NOT NULL,
  `total_jobs`     INT          NOT NULL,
  `pending_jobs`   INT          NOT NULL,
  `failed_jobs`    INT          NOT NULL,
  `failed_job_ids` LONGTEXT     NOT NULL,
  `options`        MEDIUMTEXT   NULL,
  `cancelled_at`   INT          NULL,
  `created_at`     INT          NOT NULL,
  `finished_at`    INT          NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`       VARCHAR(255)    NOT NULL,
  `connection` VARCHAR(255)    NOT NULL,
  `queue`      VARCHAR(255)    NOT NULL,
  `payload`    LONGTEXT        NOT NULL,
  `exception`  LONGTEXT        NOT NULL,
  `failed_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`, `queue`, `failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Spatie Laravel Permission
-- ---------------------------------------------------------------------------
CREATE TABLE `permissions` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)    NOT NULL,
  `guard_name` VARCHAR(255)    NOT NULL,
  `created_at` TIMESTAMP       NULL,
  `updated_at` TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)    NOT NULL,
  `guard_name` VARCHAR(255)    NOT NULL,
  `created_at` TIMESTAMP       NULL,
  `updated_at` TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `model_type`    VARCHAR(255)    NOT NULL,
  `model_id`      BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`, `model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign`
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `model_has_roles` (
  `role_id`    BIGINT UNSIGNED NOT NULL,
  `model_type` VARCHAR(255)    NOT NULL,
  `model_id`   BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `model_id`, `model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`, `model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_has_permissions` (
  `permission_id` BIGINT UNSIGNED NOT NULL,
  `role_id`       BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign`
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Spatie Laravel Media Library
-- ---------------------------------------------------------------------------
CREATE TABLE `media` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_type`            VARCHAR(255)    NOT NULL,
  `model_id`              BIGINT UNSIGNED NOT NULL,
  `uuid`                  CHAR(36)        NULL,
  `collection_name`       VARCHAR(255)    NOT NULL,
  `name`                  VARCHAR(255)    NOT NULL,
  `file_name`             VARCHAR(255)    NOT NULL,
  `mime_type`             VARCHAR(255)    NULL,
  `disk`                  VARCHAR(255)    NOT NULL,
  `conversions_disk`      VARCHAR(255)    NULL,
  `size`                  BIGINT UNSIGNED NOT NULL,
  `manipulations`         JSON            NOT NULL,
  `custom_properties`     JSON            NOT NULL,
  `generated_conversions` JSON            NOT NULL,
  `responsive_images`     JSON            NOT NULL,
  `order_column`          INT UNSIGNED    NULL,
  `created_at`            TIMESTAMP       NULL,
  `updated_at`            TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`, `model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Geography
-- ---------------------------------------------------------------------------
CREATE TABLE `provinces` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)    NOT NULL,
  `slug`       VARCHAR(100)    NOT NULL,
  `sort_order` INT             NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP       NULL,
  `updated_at` TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `provinces_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cities` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `province_id`      BIGINT UNSIGNED NOT NULL,
  `name`             VARCHAR(100)    NOT NULL,
  `slug`             VARCHAR(100)    NOT NULL,
  `meta_title`       VARCHAR(255)    NULL,
  `meta_description` VARCHAR(500)    NULL,
  `sort_order`       INT             NOT NULL DEFAULT 0,
  `created_at`       TIMESTAMP       NULL,
  `updated_at`       TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cities_slug_unique` (`slug`),
  KEY `cities_province_id_index` (`province_id`),
  CONSTRAINT `cities_province_id_foreign`
    FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Taxonomy
-- ---------------------------------------------------------------------------
CREATE TABLE `specialties` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(150)    NOT NULL,
  `slug`             VARCHAR(150)    NOT NULL,
  `description`      TEXT            NULL,
  `meta_title`       VARCHAR(255)    NULL,
  `meta_description` VARCHAR(500)    NULL,
  `sort_order`       INT             NOT NULL DEFAULT 0,
  `created_at`       TIMESTAMP       NULL,
  `updated_at`       TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `specialties_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Doctors
-- ---------------------------------------------------------------------------
CREATE TABLE `doctors` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`               BIGINT UNSIGNED NULL,
  `city_id`               BIGINT UNSIGNED NOT NULL,
  `primary_specialty_id`  BIGINT UNSIGNED NULL,
  `name`                  VARCHAR(255)    NOT NULL,
  `slug`                  VARCHAR(255)    NOT NULL,
  `bio`                   TEXT            NULL,
  `address`               TEXT            NULL,
  `photo_path`            VARCHAR(500)    NULL,
  `sms_mobile`            VARCHAR(20)     NULL COMMENT 'Private — SMS/admin only, never public',
  `name_normalized`       VARCHAR(255)    NULL,
  `search_text`           TEXT            NULL,
  `meta_title`            VARCHAR(255)    NULL,
  `meta_description`      VARCHAR(500)    NULL,
  `is_published`          TINYINT(1)      NOT NULL DEFAULT 0,
  `is_active`             TINYINT(1)      NOT NULL DEFAULT 1,
  `is_vip`                TINYINT(1)      NOT NULL DEFAULT 0,
  `expires_at`            DATETIME        NULL,
  `qr_code_path`          VARCHAR(500)    NULL,
  `published_at`          DATETIME        NULL,
  `created_at`            TIMESTAMP       NULL,
  `updated_at`            TIMESTAMP       NULL,
  `deleted_at`            TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `doctors_slug_unique` (`slug`),
  KEY `doctors_city_published_active_vip_index` (`city_id`, `is_published`, `is_active`, `is_vip`),
  KEY `doctors_published_at_index` (`is_published`, `published_at`),
  KEY `doctors_expires_at_index` (`expires_at`),
  KEY `doctors_sms_mobile_index` (`sms_mobile`),
  KEY `doctors_user_id_index` (`user_id`),
  KEY `doctors_primary_specialty_id_index` (`primary_specialty_id`),
  FULLTEXT KEY `doctors_search_fulltext` (`name`, `search_text`),
  CONSTRAINT `doctors_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `doctors_city_id_foreign`
    FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `doctors_primary_specialty_id_foreign`
    FOREIGN KEY (`primary_specialty_id`) REFERENCES `specialties` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `doctor_specialty` (
  `doctor_id`    BIGINT UNSIGNED NOT NULL,
  `specialty_id` BIGINT UNSIGNED NOT NULL,
  `is_primary`   TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`   TIMESTAMP       NULL,
  `updated_at`   TIMESTAMP       NULL,
  PRIMARY KEY (`doctor_id`, `specialty_id`),
  KEY `doctor_specialty_specialty_doctor_index` (`specialty_id`, `doctor_id`),
  CONSTRAINT `doctor_specialty_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `doctor_specialty_specialty_id_foreign`
    FOREIGN KEY (`specialty_id`) REFERENCES `specialties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `doctor_contact_phones` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `doctor_id`  BIGINT UNSIGNED NOT NULL,
  `phone`      VARCHAR(20)     NOT NULL,
  `label`      VARCHAR(50)     NULL COMMENT 'e.g. مطب, منشی',
  `sort_order` INT             NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP       NULL,
  `updated_at` TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `doctor_contact_phones_doctor_id_index` (`doctor_id`),
  CONSTRAINT `doctor_contact_phones_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `doctor_social_links` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `doctor_id`  BIGINT UNSIGNED NOT NULL,
  `platform`   ENUM(
    'telegram', 'whatsapp', 'instagram', 'linkedin',
    'bale', 'eita', 'aparat', 'rubika', 'website'
  ) NOT NULL,
  `url`        VARCHAR(500) NOT NULL,
  `sort_order` INT          NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    NULL,
  `updated_at` TIMESTAMP    NULL,
  PRIMARY KEY (`id`),
  KEY `doctor_social_links_doctor_id_index` (`doctor_id`),
  CONSTRAINT `doctor_social_links_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `doctor_photos` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `doctor_id`        BIGINT UNSIGNED NOT NULL,
  `file_path`        VARCHAR(500)    NOT NULL,
  `thumb_path`       VARCHAR(500)    NULL,
  `profile_path`     VARCHAR(500)    NULL,
  `width`            INT UNSIGNED    NULL,
  `height`           INT UNSIGNED    NULL,
  `status`           ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` VARCHAR(500)    NULL,
  `approved_at`      DATETIME        NULL,
  `approved_by`      BIGINT UNSIGNED NULL,
  `created_at`       TIMESTAMP       NULL,
  `updated_at`       TIMESTAMP       NULL,
  `deleted_at`       TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `doctor_photos_doctor_id_status_index` (`doctor_id`, `status`),
  CONSTRAINT `doctor_photos_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `doctor_photos_approved_by_foreign`
    FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Clinics
-- ---------------------------------------------------------------------------
CREATE TABLE `clinics` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `city_id`          BIGINT UNSIGNED NOT NULL,
  `name`             VARCHAR(255)    NOT NULL,
  `slug`             VARCHAR(255)    NOT NULL,
  `address`          TEXT            NULL,
  `phone`            VARCHAR(20)     NULL,
  `lat`              DECIMAL(10, 7)  NULL,
  `lng`              DECIMAL(10, 7)  NULL,
  `meta_title`       VARCHAR(255)    NULL,
  `meta_description` VARCHAR(500)    NULL,
  `created_at`       TIMESTAMP       NULL,
  `updated_at`       TIMESTAMP       NULL,
  `deleted_at`       TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clinics_slug_unique` (`slug`),
  KEY `clinics_city_id_index` (`city_id`),
  CONSTRAINT `clinics_city_id_foreign`
    FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `doctor_clinic` (
  `doctor_id`  BIGINT UNSIGNED NOT NULL,
  `clinic_id`  BIGINT UNSIGNED NOT NULL,
  `role`       VARCHAR(100)    NULL COMMENT 'e.g. attending, owner',
  `created_at` TIMESTAMP       NULL,
  `updated_at` TIMESTAMP       NULL,
  PRIMARY KEY (`doctor_id`, `clinic_id`),
  KEY `doctor_clinic_clinic_id_index` (`clinic_id`),
  CONSTRAINT `doctor_clinic_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `doctor_clinic_clinic_id_foreign`
    FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Scheduling & Appointments
-- ---------------------------------------------------------------------------
CREATE TABLE `doctor_schedules` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `doctor_id`             BIGINT UNSIGNED NOT NULL,
  `clinic_id`             BIGINT UNSIGNED NULL,
  `day_of_week`           TINYINT UNSIGNED NOT NULL COMMENT '0=Sunday … 6=Saturday',
  `start_time`            TIME            NOT NULL,
  `end_time`              TIME            NOT NULL,
  `slot_duration_minutes` SMALLINT UNSIGNED NOT NULL DEFAULT 30,
  `is_active`             TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`            TIMESTAMP       NULL,
  `updated_at`            TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `doctor_schedules_doctor_day_index` (`doctor_id`, `day_of_week`, `is_active`),
  CONSTRAINT `doctor_schedules_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `doctor_schedules_clinic_id_foreign`
    FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `doctor_schedule_exceptions` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `doctor_id`      BIGINT UNSIGNED NOT NULL,
  `exception_date` DATE            NOT NULL,
  `is_closed`      TINYINT(1)      NOT NULL DEFAULT 1,
  `start_time`     TIME            NULL,
  `end_time`       TIME            NULL,
  `reason`         VARCHAR(255)    NULL,
  `created_at`     TIMESTAMP       NULL,
  `updated_at`     TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `doctor_schedule_exceptions_doctor_date_unique` (`doctor_id`, `exception_date`),
  CONSTRAINT `doctor_schedule_exceptions_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `appointments` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `doctor_id`             BIGINT UNSIGNED NOT NULL,
  `clinic_id`             BIGINT UNSIGNED NULL,
  `starts_at`             DATETIME        NOT NULL,
  `ends_at`               DATETIME        NOT NULL,
  `patient_name`          VARCHAR(255)    NOT NULL,
  `patient_phone`         VARCHAR(20)     NOT NULL,
  `patient_national_code` CHAR(10)        NOT NULL,
  `tracking_code`         CHAR(8)         NOT NULL,
  `status`                ENUM('confirmed', 'cancelled', 'completed', 'no_show') NOT NULL DEFAULT 'confirmed',
  `booked_at`             DATETIME        NOT NULL,
  `cancelled_at`          DATETIME        NULL,
  `cancellation_reason`   VARCHAR(500)    NULL,
  `reminded_24h_at`       DATETIME        NULL,
  `reminded_2h_at`        DATETIME        NULL,
  `confirmed_slot_key`    VARCHAR(64) AS (
    CASE WHEN `status` = 'confirmed'
      THEN CONCAT(`doctor_id`, '-', `starts_at`)
      ELSE NULL
    END
  ) STORED,
  `created_at`            TIMESTAMP       NULL,
  `updated_at`            TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appointments_tracking_code_unique` (`tracking_code`),
  UNIQUE KEY `appointments_confirmed_slot_key_unique` (`confirmed_slot_key`),
  KEY `appointments_doctor_starts_index` (`doctor_id`, `starts_at`),
  KEY `appointments_patient_lookup_index` (`patient_phone`, `patient_national_code`, `status`),
  KEY `appointments_status_starts_index` (`status`, `starts_at`),
  CONSTRAINT `appointments_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `appointments_clinic_id_foreign`
    FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `appointment_waitlist` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `doctor_id`             BIGINT UNSIGNED NOT NULL,
  `patient_name`          VARCHAR(255)    NOT NULL,
  `patient_phone`         VARCHAR(20)     NOT NULL,
  `patient_national_code` CHAR(10)        NOT NULL,
  `preferred_date`        DATE            NOT NULL,
  `preferred_starts_at`   DATETIME        NULL,
  `status`                ENUM('waiting', 'notified', 'booked', 'expired', 'cancelled') NOT NULL DEFAULT 'waiting',
  `notified_at`           DATETIME        NULL,
  `expires_at`            DATETIME        NULL COMMENT 'Reservation window after SMS notification',
  `created_at`            TIMESTAMP       NULL,
  `updated_at`            TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `appointment_waitlist_doctor_date_status_index` (`doctor_id`, `preferred_date`, `status`),
  KEY `appointment_waitlist_patient_index` (`patient_phone`, `patient_national_code`),
  CONSTRAINT `appointment_waitlist_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Settings & SMS
-- ---------------------------------------------------------------------------
CREATE TABLE `settings` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`         VARCHAR(100)    NOT NULL,
  `value`       JSON            NOT NULL,
  `group`       VARCHAR(50)     NOT NULL DEFAULT 'general',
  `description` VARCHAR(500)    NULL,
  `created_at`  TIMESTAMP       NULL,
  `updated_at`  TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sms_templates` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_key`    VARCHAR(100)    NOT NULL,
  `name`         VARCHAR(150)    NOT NULL,
  `template_body` TEXT           NOT NULL,
  `is_enabled`   TINYINT(1)      NOT NULL DEFAULT 1,
  `placeholders` JSON            NULL,
  `description`  VARCHAR(500)    NULL,
  `created_at`   TIMESTAMP       NULL,
  `updated_at`   TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sms_templates_event_key_unique` (`event_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sms_logs` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone`               VARCHAR(20)     NOT NULL,
  `event_key`           VARCHAR(100)    NOT NULL,
  `sms_template_id`     BIGINT UNSIGNED NULL,
  `body`                TEXT            NOT NULL,
  `status`              ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
  `provider_message_id` VARCHAR(100)    NULL,
  `provider_response`   JSON            NULL,
  `appointment_id`      BIGINT UNSIGNED NULL,
  `doctor_id`           BIGINT UNSIGNED NULL,
  `sent_at`             DATETIME        NULL,
  `failed_at`           DATETIME        NULL,
  `error_message`       TEXT            NULL,
  `created_at`          TIMESTAMP       NULL,
  `updated_at`          TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `sms_logs_phone_index` (`phone`),
  KEY `sms_logs_event_key_index` (`event_key`),
  KEY `sms_logs_appointment_id_index` (`appointment_id`),
  CONSTRAINT `sms_logs_sms_template_id_foreign`
    FOREIGN KEY (`sms_template_id`) REFERENCES `sms_templates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sms_logs_appointment_id_foreign`
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sms_logs_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Visual templates, ads, sliders
-- ---------------------------------------------------------------------------
CREATE TABLE `display_templates` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`            VARCHAR(50)     NOT NULL,
  `name`           VARCHAR(150)    NOT NULL,
  `purpose`        VARCHAR(255)    NULL,
  `aspect_ratio`   VARCHAR(20)     NOT NULL COMMENT 'e.g. 1:1, 16:9',
  `image_width`    INT UNSIGNED    NOT NULL,
  `image_height`   INT UNSIGNED    NOT NULL,
  `text_fields`    JSON            NULL COMMENT 'Field definitions with max lengths',
  `layout_config`  JSON            NULL COMMENT 'Overlay positions, colors, safe zones',
  `is_active`      TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`     TIMESTAMP       NULL,
  `updated_at`     TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `display_templates_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ad_placements` (
  `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key`                   VARCHAR(50)     NOT NULL,
  `name`                  VARCHAR(150)    NOT NULL,
  `description`           VARCHAR(500)    NULL,
  `allowed_template_keys` JSON            NULL,
  `is_active`             TINYINT(1)      NOT NULL DEFAULT 1,
  `sort_order`            INT             NOT NULL DEFAULT 0,
  `created_at`            TIMESTAMP       NULL,
  `updated_at`            TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ad_placements_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `advertisements` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `placement_id`        BIGINT UNSIGNED NOT NULL,
  `template_id`         BIGINT UNSIGNED NOT NULL,
  `title`               VARCHAR(100)    NULL,
  `subtitle`            VARCHAR(200)    NULL,
  `cta_text`            VARCHAR(50)     NULL,
  `cta_url`             VARCHAR(500)    NULL,
  `image_path`          VARCHAR(500)    NULL,
  `sort_order`          INT             NOT NULL DEFAULT 0,
  `is_active`           TINYINT(1)      NOT NULL DEFAULT 1,
  `starts_at`           DATETIME        NULL,
  `ends_at`             DATETIME        NULL,
  `impressions_count`   INT UNSIGNED    NOT NULL DEFAULT 0,
  `clicks_count`        INT UNSIGNED    NOT NULL DEFAULT 0,
  `created_at`          TIMESTAMP       NULL,
  `updated_at`          TIMESTAMP       NULL,
  `deleted_at`          TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `advertisements_placement_active_index` (`placement_id`, `is_active`, `sort_order`),
  CONSTRAINT `advertisements_placement_id_foreign`
    FOREIGN KEY (`placement_id`) REFERENCES `ad_placements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `advertisements_template_id_foreign`
    FOREIGN KEY (`template_id`) REFERENCES `display_templates` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sliders` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_id`         BIGINT UNSIGNED NOT NULL,
  `title`               VARCHAR(100)    NULL,
  `subtitle`            VARCHAR(200)    NULL,
  `cta_text`            VARCHAR(50)     NULL,
  `cta_url`             VARCHAR(500)    NULL,
  `image_path`          VARCHAR(500)    NULL,
  `sort_order`          INT             NOT NULL DEFAULT 0,
  `is_active`           TINYINT(1)      NOT NULL DEFAULT 1,
  `starts_at`           DATETIME        NULL,
  `ends_at`             DATETIME        NULL,
  `created_at`          TIMESTAMP       NULL,
  `updated_at`          TIMESTAMP       NULL,
  `deleted_at`          TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `sliders_active_sort_index` (`is_active`, `sort_order`),
  CONSTRAINT `sliders_template_id_foreign`
    FOREIGN KEY (`template_id`) REFERENCES `display_templates` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- CMS, redirects, links
-- ---------------------------------------------------------------------------
CREATE TABLE `short_links` (
  `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`           VARCHAR(20)     NOT NULL,
  `target_url`     VARCHAR(1000)   NOT NULL,
  `appointment_id` BIGINT UNSIGNED NULL,
  `click_count`    INT UNSIGNED    NOT NULL DEFAULT 0,
  `expires_at`     DATETIME        NULL,
  `created_at`     TIMESTAMP       NULL,
  `updated_at`     TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short_links_code_unique` (`code`),
  KEY `short_links_appointment_id_index` (`appointment_id`),
  CONSTRAINT `short_links_appointment_id_foreign`
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pages` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(255)    NOT NULL,
  `slug`             VARCHAR(255)    NOT NULL,
  `body`             LONGTEXT        NULL,
  `meta_title`       VARCHAR(255)    NULL,
  `meta_description` VARCHAR(500)    NULL,
  `is_published`     TINYINT(1)      NOT NULL DEFAULT 0,
  `published_at`     DATETIME        NULL,
  `created_at`       TIMESTAMP       NULL,
  `updated_at`       TIMESTAMP       NULL,
  `deleted_at`       TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `slug_redirects` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `old_slug`   VARCHAR(255)    NOT NULL,
  `new_slug`   VARCHAR(255)    NOT NULL,
  `model_type` VARCHAR(100)    NOT NULL,
  `model_id`   BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP       NULL,
  `updated_at` TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_redirects_old_slug_unique` (`old_slug`),
  KEY `slug_redirects_model_index` (`model_type`, `model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Logging & support
-- ---------------------------------------------------------------------------
CREATE TABLE `error_logs` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `level`            ENUM('warning', 'error', 'critical') NOT NULL DEFAULT 'error',
  `message`          TEXT            NOT NULL,
  `exception_class`  VARCHAR(255)    NULL,
  `file`             VARCHAR(500)    NULL,
  `line`             INT UNSIGNED    NULL,
  `stack_trace`      MEDIUMTEXT      NULL,
  `url`              VARCHAR(500)    NULL,
  `http_method`      VARCHAR(10)     NULL,
  `user_id`          BIGINT UNSIGNED NULL,
  `ip`               VARCHAR(45)     NULL,
  `user_agent`       VARCHAR(500)    NULL,
  `context`          JSON            NULL,
  `request_input`    JSON            NULL,
  `occurred_at`      DATETIME        NOT NULL,
  `status`           ENUM('new', 'investigating', 'resolved') NOT NULL DEFAULT 'new',
  `resolved_at`      DATETIME        NULL,
  `resolved_note`    TEXT            NULL,
  `created_at`       TIMESTAMP       NULL,
  `updated_at`       TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `error_logs_occurred_at_index` (`occurred_at`),
  KEY `error_logs_status_level_index` (`status`, `level`),
  KEY `error_logs_url_index` (`url`(191)),
  CONSTRAINT `error_logs_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `support_tickets` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_number` VARCHAR(20)     NOT NULL,
  `user_id`       BIGINT UNSIGNED NOT NULL,
  `subject`       VARCHAR(255)    NOT NULL,
  `body`          TEXT            NOT NULL,
  `category`      ENUM('bug', 'question', 'feature', 'urgent') NOT NULL DEFAULT 'question',
  `error_log_id`  BIGINT UNSIGNED NULL,
  `status`        ENUM('sent', 'in_progress', 'resolved') NOT NULL DEFAULT 'sent',
  `page_url`      VARCHAR(500)    NULL,
  `notified_via`  JSON            NULL,
  `created_at`    TIMESTAMP       NULL,
  `updated_at`    TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_number_unique` (`ticket_number`),
  KEY `support_tickets_status_created_index` (`status`, `created_at`),
  CONSTRAINT `support_tickets_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_tickets_error_log_id_foreign`
    FOREIGN KEY (`error_log_id`) REFERENCES `error_logs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `site_social_links` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `platform`   ENUM(
    'telegram', 'whatsapp', 'instagram', 'linkedin',
    'bale', 'eita', 'aparat', 'rubika', 'twitter', 'website'
  ) NOT NULL,
  `url`        VARCHAR(500) NOT NULL,
  `label`      VARCHAR(100) NULL,
  `sort_order` INT          NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP    NULL,
  `updated_at` TIMESTAMP    NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Reviews (placeholder — UI in later phase)
-- ---------------------------------------------------------------------------
CREATE TABLE `reviews` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `doctor_id`   BIGINT UNSIGNED NOT NULL,
  `rating`      TINYINT UNSIGNED NOT NULL COMMENT '1-5',
  `body`        TEXT            NULL,
  `author_name` VARCHAR(150)    NULL,
  `is_approved` TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP       NULL,
  `updated_at`  TIMESTAMP       NULL,
  `deleted_at`  TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_doctor_approved_index` (`doctor_id`, `is_approved`),
  CONSTRAINT `reviews_doctor_id_foreign`
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_rating_check` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- SEED DATA
-- =============================================================================

-- Spatie default roles
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin',  'web', NOW(), NOW()),
(2, 'doctor', 'web', NOW(), NOW());

-- 31 Iranian provinces
INSERT INTO `provinces` (`id`, `name`, `slug`, `sort_order`, `created_at`, `updated_at`) VALUES
(1,  'آذربایجان شرقی',       'east-azerbaijan',        1,  NOW(), NOW()),
(2,  'آذربایجان غربی',       'west-azerbaijan',        2,  NOW(), NOW()),
(3,  'اردبیل',               'ardabil',                3,  NOW(), NOW()),
(4,  'اصفهان',               'isfahan',                4,  NOW(), NOW()),
(5,  'البرز',                'alborz',                 5,  NOW(), NOW()),
(6,  'ایلام',                'ilam',                   6,  NOW(), NOW()),
(7,  'بوشهر',                'bushehr',                7,  NOW(), NOW()),
(8,  'تهران',                'tehran-province',        8,  NOW(), NOW()),
(9,  'چهارمحال و بختیاری',   'chaharmahal-bakhtiari',  9,  NOW(), NOW()),
(10, 'خراسان جنوبی',         'south-khorasan',         10, NOW(), NOW()),
(11, 'خراسان رضوی',          'razavi-khorasan',        11, NOW(), NOW()),
(12, 'خراسان شمالی',         'north-khorasan',         12, NOW(), NOW()),
(13, 'خوزستان',              'khuzestan',              13, NOW(), NOW()),
(14, 'زنجان',                'zanjan',                 14, NOW(), NOW()),
(15, 'سمنان',                'semnan',                 15, NOW(), NOW()),
(16, 'سیستان و بلوچستان',    'sistan-baluchestan',     16, NOW(), NOW()),
(17, 'فارس',                 'fars',                   17, NOW(), NOW()),
(18, 'قزوین',                'qazvin',                 18, NOW(), NOW()),
(19, 'قم',                   'qom-province',           19, NOW(), NOW()),
(20, 'کردستان',              'kurdistan',              20, NOW(), NOW()),
(21, 'کرمان',                'kerman',                 21, NOW(), NOW()),
(22, 'کرمانشاه',             'kermanshah',             22, NOW(), NOW()),
(23, 'کهگیلویه و بویراحمد',  'kohgiluyeh-boyerahmad',  23, NOW(), NOW()),
(24, 'گلستان',               'golestan',               24, NOW(), NOW()),
(25, 'گیلان',                'gilan',                  25, NOW(), NOW()),
(26, 'لرستان',               'lorestan',               26, NOW(), NOW()),
(27, 'مازندران',             'mazandaran',             27, NOW(), NOW()),
(28, 'مرکزی',                'markazi',                28, NOW(), NOW()),
(29, 'هرمزگان',              'hormozgan',              29, NOW(), NOW()),
(30, 'همدان',                'hamadan',                30, NOW(), NOW()),
(31, 'یزد',                  'yazd-province',          31, NOW(), NOW());

-- ~20 major cities
INSERT INTO `cities` (`id`, `province_id`, `name`, `slug`, `sort_order`, `created_at`, `updated_at`) VALUES
(1,  8,  'تهران',      'tehran',       1,  NOW(), NOW()),
(2,  11, 'مشهد',       'mashhad',      2,  NOW(), NOW()),
(3,  4,  'اصفهان',     'isfahan-city', 3,  NOW(), NOW()),
(4,  17, 'شیراز',      'shiraz',       4,  NOW(), NOW()),
(5,  1,  'تبریز',      'tabriz',       5,  NOW(), NOW()),
(6,  5,  'کرج',        'karaj',        6,  NOW(), NOW()),
(7,  13, 'اهواز',      'ahvaz',        7,  NOW(), NOW()),
(8,  19, 'قم',         'qom',          8,  NOW(), NOW()),
(9,  22, 'کرمانشاه',   'kermanshah-city', 9, NOW(), NOW()),
(10, 2,  'ارومیه',     'urmia',        10, NOW(), NOW()),
(11, 25, 'رشت',        'rasht',        11, NOW(), NOW()),
(12, 16, 'زاهدان',     'zahedan',      12, NOW(), NOW()),
(13, 30, 'همدان',      'hamadan-city', 13, NOW(), NOW()),
(14, 21, 'کرمان',      'kerman-city',  14, NOW(), NOW()),
(15, 31, 'یزد',        'yazd',         15, NOW(), NOW()),
(16, 28, 'اراک',       'arak',         16, NOW(), NOW()),
(17, 29, 'بندرعباس',   'bandar-abbas', 17, NOW(), NOW()),
(18, 20, 'سنندج',      'sanandaj',     18, NOW(), NOW()),
(19, 18, 'قزوین',      'qazvin-city',  19, NOW(), NOW()),
(20, 24, 'گرگان',      'gorgan',       20, NOW(), NOW());

-- ~30 medical specialties
INSERT INTO `specialties` (`id`, `name`, `slug`, `sort_order`, `created_at`, `updated_at`) VALUES
(1,  'قلب و عروق',           'cardiology',              1,  NOW(), NOW()),
(2,  'پوست و مو',            'dermatology',             2,  NOW(), NOW()),
(3,  'اطفال',                'pediatrics',              3,  NOW(), NOW()),
(4,  'داخلی',                'internal-medicine',       4,  NOW(), NOW()),
(5,  'جراحی عمومی',          'general-surgery',         5,  NOW(), NOW()),
(6,  'ارتوپدی',              'orthopedics',             6,  NOW(), NOW()),
(7,  'مغز و اعصاب',          'neurology',               7,  NOW(), NOW()),
(8,  'چشم پزشکی',            'ophthalmology',           8,  NOW(), NOW()),
(9,  'گوش و حلق و بینی',     'ent',                     9,  NOW(), NOW()),
(10, 'زنان و زایمان',        'obstetrics-gynecology',   10, NOW(), NOW()),
(11, 'روانپزشکی',            'psychiatry',              11, NOW(), NOW()),
(12, 'روانشناسی',            'psychology',              12, NOW(), NOW()),
(13, 'دندانپزشکی',           'dentistry',               13, NOW(), NOW()),
(14, 'اورولوژی',             'urology',                 14, NOW(), NOW()),
(15, 'گوارش و کبد',          'gastroenterology',        15, NOW(), NOW()),
(16, 'ریه و دستگاه تنفسی',   'pulmonology',             16, NOW(), NOW()),
(17, 'غدد و متابولیسم',      'endocrinology',           17, NOW(), NOW()),
(18, 'خون و سرطان',          'hematology-oncology',     18, NOW(), NOW()),
(19, 'کلیه (نفرولوژی)',      'nephrology',              19, NOW(), NOW()),
(20, 'طب فیزیکی و توانبخشی', 'physical-medicine',       20, NOW(), NOW()),
(21, 'بیهوشی',               'anesthesiology',          21, NOW(), NOW()),
(22, 'رادیولوژی',            'radiology',               22, NOW(), NOW()),
(23, 'پاتولوژی',             'pathology',               23, NOW(), NOW()),
(24, 'طب اورژانس',           'emergency-medicine',      24, NOW(), NOW()),
(25, 'پزشک عمومی',           'general-practitioner',    25, NOW(), NOW()),
(26, 'تغذیه',                'nutrition',               26, NOW(), NOW()),
(27, 'فیزیوتراپی',           'physiotherapy',           27, NOW(), NOW()),
(28, 'عفونی',                'infectious-diseases',     28, NOW(), NOW()),
(29, 'روماتولوژی',           'rheumatology',            29, NOW(), NOW()),
(30, 'جراحی مغز و اعصاب',    'neurosurgery',            30, NOW(), NOW());

-- Display templates
INSERT INTO `display_templates` (`id`, `key`, `name`, `purpose`, `aspect_ratio`, `image_width`, `image_height`, `text_fields`, `layout_config`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'doctor_card',     'کارت پزشک',           'Doctor listing cards',           '1:1',   400,  400,  NULL, '{"safe_zone":"center","overlay":null}', 1, NOW(), NOW()),
(2, 'doctor_profile',  'عکس پروفایل پزشک',    'Doctor profile header',          '1:1',   800,  800,  NULL, '{"safe_zone":"center","overlay":null}', 1, NOW(), NOW()),
(3, 'hero_slide',      'اسلاید صفحه اصلی',    'Homepage hero slider',           '16:9',  1920, 1080,
 '{"title":{"max":40},"subtitle":{"max":80},"cta_text":{"max":20}}',
 '{"text_position":"bottom-right","overlay":"gradient-dark"}', 1, NOW(), NOW()),
(4, 'banner_wide',     'بنر عریض',            'Listing top banner',             '3:1',   1200, 400,
 '{"title":{"max":30},"cta_text":{"max":15}}',
 '{"text_position":"center-left","overlay":"semi-dark"}', 1, NOW(), NOW()),
(5, 'banner_sidebar',  'بنر سایدبار',         'Doctor profile sidebar',         '1:1',   400,  400,
 '{"title":{"max":25}}',
 '{"text_position":"bottom","overlay":"gradient"}', 1, NOW(), NOW()),
(6, 'banner_footer',   'بنر فوتر',            'Footer banner',                  '4:1',   1200, 300,
 '{"title":{"max":30}}',
 '{"text_position":"center","overlay":null}', 1, NOW(), NOW());

-- Ad placements
INSERT INTO `ad_placements` (`id`, `key`, `name`, `description`, `allowed_template_keys`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'home_slider',            'اسلایدر صفحه اصلی',        'Hero carousel on homepage',              '["hero_slide"]',                          1, NOW(), NOW()),
(2, 'listing_top',            'بالای لیست پزشکان',        'Wide banner above doctor listings',      '["banner_wide"]',                         2, NOW(), NOW()),
(3, 'doctor_profile_sidebar', 'سایدبار پروفایل پزشک',     'Sidebar ad on doctor profile',           '["banner_sidebar"]',                      3, NOW(), NOW()),
(4, 'footer_banner',          'بنر فوتر',                 'Footer-wide promotional banner',         '["banner_footer","banner_wide"]',         4, NOW(), NOW()),
(5, 'home_between_sections',  'بین بخش‌های صفحه اصلی',    'Mid-page banner on homepage',            '["banner_wide","banner_sidebar"]',        5, NOW(), NOW());

-- Default settings
INSERT INTO `settings` (`key`, `value`, `group`, `description`, `created_at`, `updated_at`) VALUES
('site_name_fa',           '{"value":"ویزیت ایرانیان"}',                          'general',  'Persian site name',                    NOW(), NOW()),
('site_name_en',           '{"value":"VisitIranian"}',                              'general',  'English site name',                    NOW(), NOW()),
('hide_doctors_on_expiry', '{"value":true}',                                        'doctors',  'Hide expired doctor profiles globally', NOW(), NOW()),
('support_notify_channel', '{"value":"telegram"}',                                  'developer','Developer support notification channel', NOW(), NOW()),
('booking_slot_hold_minutes', '{"value":0}',                                        'booking',  'Minutes to hold slot during checkout', NOW(), NOW()),
('waitlist_hold_hours',    '{"value":2}',                                           'booking',  'Hours waitlist patient has to book after SMS', NOW(), NOW()),
('reminder_24h_enabled',   '{"value":true}',                                        'sms',      'Send 24-hour appointment reminders',   NOW(), NOW()),
('reminder_2h_enabled',    '{"value":true}',                                        'sms',      'Send 2-hour appointment reminders',    NOW(), NOW()),
('kavenegar_sender',       '{"value":""}',                                          'sms',      'Kavenegar sender line number',         NOW(), NOW()),
('default_meta_title',     '{"value":"ویزیت ایرانیان — معرفی پزشکان ایران"}',       'seo',      'Default meta title',                   NOW(), NOW()),
('default_meta_description','{"value":"جستجو و رزرو نوبت آنلاین با بهترین پزشکان ایران"}', 'seo', 'Default meta description', NOW(), NOW());

-- Default SMS templates (Persian)
INSERT INTO `sms_templates` (`event_key`, `name`, `template_body`, `is_enabled`, `placeholders`, `description`, `created_at`, `updated_at`) VALUES
('booking_patient', 'تأیید رزرو — بیمار',
 'سلام {patient_name}، نوبت شما با دکتر {doctor_name} در {appointment_date} ساعت {appointment_time} ثبت شد. کد پیگیری: {tracking_code} — {tracking_url} — یا از {peygiri_url} با موبایل و کد ملی پیگیری کنید.',
 1, '["patient_name","doctor_name","appointment_date","appointment_time","tracking_code","tracking_url","peygiri_url","short_url"]',
 'Sent to patient after successful booking', NOW(), NOW()),
('booking_doctor', 'اطلاع رزرو — پزشک',
 'نوبت جدید: {patient_name} — {appointment_date} ساعت {appointment_time}. کد: {tracking_code}',
 1, '["patient_name","appointment_date","appointment_time","tracking_code"]',
 'Sent to doctor sms_mobile after booking', NOW(), NOW()),
('cancel_patient', 'کنسل — بیمار',
 'نوبت شما با دکتر {doctor_name} در {appointment_date} ساعت {appointment_time} لغو شد. کد: {tracking_code}',
 1, '["doctor_name","appointment_date","appointment_time","tracking_code"]',
 'Sent to patient on cancellation', NOW(), NOW()),
('cancel_doctor', 'کنسل — پزشک',
 'لغو نوبت: {patient_name} — {appointment_date} ساعت {appointment_time}',
 1, '["patient_name","appointment_date","appointment_time"]',
 'Sent to doctor on cancellation', NOW(), NOW()),
('reminder_24h_patient', 'یادآور ۲۴ ساعته',
 'یادآوری: فردا ساعت {appointment_time} نوبت شما با دکتر {doctor_name}. {tracking_url}',
 1, '["doctor_name","appointment_time","tracking_url"]',
 '24-hour reminder to patient', NOW(), NOW()),
('reminder_2h_patient', 'یادآور ۲ ساعته',
 'یادآوری: ۲ ساعت دیگر نوبت شما با دکتر {doctor_name} ساعت {appointment_time}. {tracking_url}',
 1, '["doctor_name","appointment_time","tracking_url"]',
 '2-hour reminder to patient', NOW(), NOW()),
('waitlist_slot_available', 'اسلات آزاد — لیست انتظار',
 'سلام {patient_name}، نوبت خالی با دکتر {doctor_name} در {appointment_date} ساعت {appointment_time} برای شماست. تا {expires_at} رزرو کنید: {short_url}',
 1, '["patient_name","doctor_name","appointment_date","appointment_time","expires_at","short_url"]',
 'Sent when a waitlisted slot opens', NOW(), NOW());

-- =============================================================================
-- End of schema.sql
-- =============================================================================

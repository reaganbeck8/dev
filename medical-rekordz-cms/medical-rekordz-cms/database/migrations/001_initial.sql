-- ============================================================
-- Medical Rekordz CMS — Database Schema
-- Prefix: mrk_
-- Charset: utf8mb4 / utf8mb4_unicode_ci
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ─── Users ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_users` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(100)    NOT NULL,
    `email`         VARCHAR(180)    NOT NULL,
    `password_hash` VARCHAR(255)    NOT NULL,
    `role`          ENUM('admin','superadmin') NOT NULL DEFAULT 'admin',
    `is_active`     TINYINT(1)      NOT NULL DEFAULT 1,
    `last_login`    DATETIME        NULL DEFAULT NULL,
    `created_at`    DATETIME        NOT NULL,
    `updated_at`    DATETIME        NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Artists ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_artists` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(150)    NOT NULL,
    `slug`              VARCHAR(210)    NOT NULL,
    `tagline`           VARCHAR(255)    NULL DEFAULT NULL,
    `bio`               TEXT            NULL DEFAULT NULL,
    `profile_image_id`  INT UNSIGNED    NULL DEFAULT NULL,
    `status`            ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `sort_order`        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`        DATETIME        NOT NULL,
    `updated_at`        DATETIME        NOT NULL,
    `deleted_at`        DATETIME        NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_artists_slug` (`slug`),
    KEY `idx_artists_status` (`status`),
    KEY `idx_artists_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Artist Socials ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_artist_socials` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `artist_id`  INT UNSIGNED NOT NULL,
    `platform`   VARCHAR(50)  NOT NULL,
    `url`        VARCHAR(500) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_artist_socials_artist` (`artist_id`),
    CONSTRAINT `fk_socials_artist` FOREIGN KEY (`artist_id`)
        REFERENCES `mrk_artists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Releases ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_releases` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `artist_id`     INT UNSIGNED    NOT NULL,
    `title`         VARCHAR(200)    NOT NULL,
    `slug`          VARCHAR(210)    NOT NULL,
    `type`          ENUM('album','ep','single','mixtape') NOT NULL DEFAULT 'single',
    `cover_id`      INT UNSIGNED    NULL DEFAULT NULL,
    `release_date`  DATE            NULL DEFAULT NULL,
    `description`   TEXT            NULL DEFAULT NULL,
    `status`        ENUM('published','draft') NOT NULL DEFAULT 'draft',
    `created_at`    DATETIME        NOT NULL,
    `updated_at`    DATETIME        NOT NULL,
    `deleted_at`    DATETIME        NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_releases_slug` (`slug`),
    KEY `idx_releases_artist` (`artist_id`),
    KEY `idx_releases_status` (`status`),
    KEY `idx_releases_deleted` (`deleted_at`),
    CONSTRAINT `fk_releases_artist` FOREIGN KEY (`artist_id`)
        REFERENCES `mrk_artists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Tracks ───────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_tracks` (
    `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `release_id`    INT UNSIGNED     NOT NULL,
    `title`         VARCHAR(200)     NOT NULL,
    `track_number`  TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `duration`      SMALLINT UNSIGNED NULL DEFAULT NULL COMMENT 'Duration in seconds',
    `media_id`      INT UNSIGNED     NULL DEFAULT NULL,
    `created_at`    DATETIME         NOT NULL,
    `updated_at`    DATETIME         NOT NULL,
    `deleted_at`    DATETIME         NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_tracks_release` (`release_id`),
    KEY `idx_tracks_deleted` (`deleted_at`),
    CONSTRAINT `fk_tracks_release` FOREIGN KEY (`release_id`)
        REFERENCES `mrk_releases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Media ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_media` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `original_name` VARCHAR(255) NOT NULL,
    `stored_name`   VARCHAR(255) NOT NULL,
    `file_path`     VARCHAR(500) NOT NULL,
    `mime_type`     VARCHAR(100) NOT NULL,
    `file_type`     ENUM('image','audio','video','document') NOT NULL,
    `file_size`     INT UNSIGNED NOT NULL COMMENT 'Size in bytes',
    `uploaded_by`   INT UNSIGNED NULL DEFAULT NULL,
    `created_at`    DATETIME     NOT NULL,
    `deleted_at`    DATETIME     NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_media_type` (`file_type`),
    KEY `idx_media_deleted` (`deleted_at`),
    CONSTRAINT `fk_media_user` FOREIGN KEY (`uploaded_by`)
        REFERENCES `mrk_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Videos ───────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_videos` (
    `id`            INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `title`         VARCHAR(200)      NOT NULL,
    `description`   TEXT              NULL DEFAULT NULL,
    `video_type`    ENUM('youtube','local') NOT NULL DEFAULT 'youtube',
    `youtube_url`   VARCHAR(500)      NULL DEFAULT NULL,
    `media_id`      INT UNSIGNED      NULL DEFAULT NULL,
    `thumbnail_id`  INT UNSIGNED      NULL DEFAULT NULL,
    `sort_order`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `status`        ENUM('published','draft') NOT NULL DEFAULT 'published',
    `created_at`    DATETIME          NOT NULL,
    `updated_at`    DATETIME          NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_videos_status` (`status`),
    CONSTRAINT `fk_videos_media` FOREIGN KEY (`media_id`)
        REFERENCES `mrk_media` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_videos_thumbnail` FOREIGN KEY (`thumbnail_id`)
        REFERENCES `mrk_media` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Pages ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_pages` (
    `id`            INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `title`         VARCHAR(200)      NOT NULL,
    `slug`          VARCHAR(210)      NOT NULL,
    `content`       LONGTEXT          NULL DEFAULT NULL,
    `meta_title`    VARCHAR(200)      NULL DEFAULT NULL,
    `meta_desc`     VARCHAR(320)      NULL DEFAULT NULL,
    `status`        ENUM('published','draft') NOT NULL DEFAULT 'draft',
    `sort_order`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`    DATETIME          NOT NULL,
    `updated_at`    DATETIME          NOT NULL,
    `deleted_at`    DATETIME          NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pages_slug` (`slug`),
    KEY `idx_pages_status` (`status`),
    KEY `idx_pages_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Settings ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_settings` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key`   VARCHAR(100) NOT NULL,
    `setting_value` TEXT         NULL DEFAULT NULL,
    `setting_type`  ENUM('string','bool','int','json') NOT NULL DEFAULT 'string',
    `setting_group` VARCHAR(50)  NOT NULL DEFAULT 'general',
    `updated_at`    DATETIME     NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_settings_key` (`setting_key`),
    KEY `idx_settings_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Navigation Items ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_nav_items` (
    `id`          INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `label`       VARCHAR(100)      NOT NULL,
    `url`         VARCHAR(500)      NULL DEFAULT NULL,
    `page_id`     INT UNSIGNED      NULL DEFAULT NULL,
    `parent_id`   INT UNSIGNED      NULL DEFAULT NULL,
    `sort_order`  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `is_active`   TINYINT(1)        NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_nav_parent` (`parent_id`),
    CONSTRAINT `fk_nav_page` FOREIGN KEY (`page_id`)
        REFERENCES `mrk_pages` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_nav_parent` FOREIGN KEY (`parent_id`)
        REFERENCES `mrk_nav_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Contact Submissions ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_contact_submissions` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(150) NOT NULL,
    `email`      VARCHAR(180) NOT NULL,
    `phone`      VARCHAR(30)  NULL DEFAULT NULL,
    `message`    TEXT         NOT NULL,
    `ip_address` VARCHAR(45)  NULL DEFAULT NULL,
    `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_contact_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Activity Log ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_activity_log` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED NULL DEFAULT NULL,
    `action`      VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50)  NULL DEFAULT NULL,
    `entity_id`   INT UNSIGNED NULL DEFAULT NULL,
    `detail`      TEXT         NULL DEFAULT NULL,
    `ip_address`  VARCHAR(45)  NULL DEFAULT NULL,
    `created_at`  DATETIME     NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_log_user` (`user_id`),
    KEY `idx_log_action` (`action`),
    KEY `idx_log_created` (`created_at`),
    CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`)
        REFERENCES `mrk_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Login Attempts ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `mrk_login_attempts` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `identifier`   VARCHAR(255) NOT NULL COMMENT 'scope:email md5 hash',
    `ip_address`   VARCHAR(45)  NOT NULL,
    `attempted_at` DATETIME     NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_attempts_identifier` (`identifier`),
    KEY `idx_attempts_ip` (`ip_address`),
    KEY `idx_attempts_time` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Deferred FK constraints (avoid circular dependency on install) ───────────
-- mrk_artists.profile_image_id → mrk_media.id
ALTER TABLE `mrk_artists`
    ADD CONSTRAINT `fk_artists_profile_image`
    FOREIGN KEY (`profile_image_id`) REFERENCES `mrk_media` (`id`) ON DELETE SET NULL;

-- mrk_releases.cover_id → mrk_media.id
ALTER TABLE `mrk_releases`
    ADD CONSTRAINT `fk_releases_cover`
    FOREIGN KEY (`cover_id`) REFERENCES `mrk_media` (`id`) ON DELETE SET NULL;

-- mrk_tracks.media_id → mrk_media.id
ALTER TABLE `mrk_tracks`
    ADD CONSTRAINT `fk_tracks_media`
    FOREIGN KEY (`media_id`) REFERENCES `mrk_media` (`id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS = 1;

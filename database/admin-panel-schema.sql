CREATE DATABASE IF NOT EXISTS `fikircreative`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `fikircreative`;

CREATE TABLE IF NOT EXISTS `yoneticiler` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kullanici_adi` varchar(80) NOT NULL UNIQUE,
  `sifre` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `yoneticiler` (`kullanici_adi`, `sifre`)
SELECT 'admin', SHA1('fikircreative2025')
WHERE NOT EXISTS (SELECT 1 FROM `yoneticiler` WHERE `kullanici_adi` = 'admin');

CREATE TABLE IF NOT EXISTS `ayarlar` (
  `ayar_key` varchar(120) NOT NULL,
  `ayar_val` longtext NULL,
  PRIMARY KEY (`ayar_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ayarlar` (`ayar_key`, `ayar_val`) VALUES
('site_baslik', 'Fikir Creative'),
('site_slogan', 'Yerel işletmeler için dijital büyüme sistemi'),
('seo_desc', 'Fikir Creative, yerel işletmeler için web sitesi, reklam, video ve WhatsApp dönüşüm altyapısı kurar.'),
('seo_keys', 'dijital ajans, web tasarım, performans pazarlaması, whatsapp otomasyonu'),
('email', 'hello@fikircreative.com'),
('telefon', '+90 (532) 000 00 00'),
('adres', 'İstanbul, Türkiye'),
('site_renk', '#F15A24'),
('footer_text', '© Fikir Creative. Tüm Hakları Saklıdır.')
ON DUPLICATE KEY UPDATE ayar_val = VALUES(ayar_val);

INSERT INTO `ayarlar` (`ayar_key`, `ayar_val`) VALUES
('seo_title', 'Fikir Creative - Dijital Buyume Ajansi'),
('site_url', 'https://www.fikircreative.com'),
('availability_text', 'Yeni projeler icin uygun'),
('header_cta_text', 'Iletisim'),
('header_cta_url', '/#contact'),
('ga4_id', ''),
('gtm_id', ''),
('meta_pixel_id', ''),
('google_site_verification', ''),
('geo_region', 'TR-34'),
('geo_placename', 'Istanbul'),
('geo_position', ''),
('icbm', ''),
('footer_description', ''),
('politika_gizlilik', '<p>Gizlilik politikasi metni admin panelinden duzenlenebilir.</p>'),
('politika_kullanim', '<p>Kullanim sartlari metni admin panelinden duzenlenebilir.</p>'),
('politika_cerezler', '<p>Cerez politikasi metni admin panelinden duzenlenebilir.</p>'),
('politika_kvkk', '<p>KVKK aydinlatma metni admin panelinden duzenlenebilir.</p>')
ON DUPLICATE KEY UPDATE ayar_val = ayar_val;

CREATE TABLE IF NOT EXISTS `portfolyo` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) NOT NULL,
  `kategori` varchar(80) NOT NULL DEFAULT 'web',
  `medya_turu` enum('resim','video') NOT NULL DEFAULT 'resim',
  `medya_url` varchar(1024) NULL,
  `gorsel_url` varchar(1024) NULL,
  `sira` int NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sira` (`sira`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `hizmetler` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) NOT NULL,
  `aciklama` text NULL,
  `ikon_svg` longtext NULL,
  `sira` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_sira` (`sira`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `kategori` varchar(80) NOT NULL DEFAULT 'sosyal',
  `icerik` longtext NULL,
  `yazar` varchar(120) NOT NULL DEFAULT 'Fikir Creative',
  `meta_desc` text NULL,
  `resim` varchar(1024) NULL,
  `tarih` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mesajlar` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ad_soyad` varchar(160) NOT NULL,
  `eposta` varchar(255) NOT NULL,
  `konu` varchar(255) NOT NULL,
  `mesaj` text NOT NULL,
  `okundu` tinyint(1) NOT NULL DEFAULT 0,
  `tarih` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_okundu_tarih` (`okundu`, `tarih`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ekip` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ad_soyad` varchar(160) NOT NULL,
  `gorev` varchar(160) NULL,
  `bio` text NULL,
  `instagram` varchar(255) NULL,
  `linkedin` varchar(255) NULL,
  `foto` varchar(1024) NULL,
  `sira` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `page_type` enum('service','location','blog','portfolio','standard') DEFAULT 'standard',
  `h1` varchar(255) NULL,
  `excerpt` text NULL,
  `content` longtext NULL,
  `seo_title` varchar(255) NULL,
  `meta_description` text NULL,
  `canonical_url` varchar(255) NULL,
  `meta_robots` varchar(50) DEFAULT 'index, follow',
  `og_title` varchar(255) NULL,
  `og_description` text NULL,
  `og_image` varchar(255) NULL,
  `focus_keyword` varchar(255) NULL,
  `related_keywords` text NULL,
  `cover_image` varchar(255) NULL,
  `mobile_cover_image` varchar(255) NULL,
  `cta_title` varchar(255) NULL,
  `cta_text` text NULL,
  `primary_button_text` varchar(100) NULL,
  `primary_button_url` varchar(255) NULL,
  `secondary_button_text` varchar(100) NULL,
  `secondary_button_url` varchar(255) NULL,
  `status` enum('draft','published','passive') DEFAULT 'draft',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `published_at` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `page_blocks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `block_type` varchar(50) NOT NULL,
  `block_data` longtext NULL,
  `sort_order` int DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `page_faqs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `page_relations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `related_id` int NOT NULL,
  `relation_type` enum('service','location','blog','portfolio') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `link_hub_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_title` varchar(255) DEFAULT NULL,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_description` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `profile_image_url` varchar(255) DEFAULT NULL,
  `background_image_url` varchar(255) DEFAULT NULL,
  `background_video_url` varchar(255) DEFAULT NULL,
  `mobile_poster_url` varchar(255) DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `page_background_type` varchar(50) DEFAULT 'color',
  `page_background_color` varchar(20) DEFAULT '#050505',
  `page_background_gradient_from` varchar(20) DEFAULT NULL,
  `page_background_gradient_to` varchar(20) DEFAULT NULL,
  `page_background_image_url` varchar(255) DEFAULT NULL,
  `card_background_type` varchar(50) DEFAULT 'glass',
  `card_background_color` varchar(20) DEFAULT NULL,
  `card_background_gradient_from` varchar(20) DEFAULT NULL,
  `card_background_gradient_to` varchar(20) DEFAULT NULL,
  `card_background_image_url` varchar(255) DEFAULT NULL,
  `card_background_overlay_opacity` decimal(3,2) DEFAULT 0.70,
  `card_background_position` varchar(50) DEFAULT 'center',
  `card_background_size` varchar(50) DEFAULT 'cover',
  `logo_display_mode` varchar(50) DEFAULT 'image',
  `logo_size_desktop` int DEFAULT 80,
  `logo_size_mobile` int DEFAULT 64,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `link_hub_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `url` text NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `color_mode` varchar(50) DEFAULT 'dark',
  `gradient_from` varchar(20) DEFAULT NULL,
  `gradient_to` varchar(20) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int NOT NULL DEFAULT 0,
  `open_in_new_tab` tinyint(1) NOT NULL DEFAULT 1,
  `utm_source` varchar(100) DEFAULT NULL,
  `utm_medium` varchar(100) DEFAULT NULL,
  `utm_campaign` varchar(100) DEFAULT NULL,
  `click_count` int NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `link_hub_social_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `url` text NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `link_hub_seo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image_url` varchar(255) DEFAULT NULL,
  `robots_index` tinyint(1) NOT NULL DEFAULT 1,
  `robots_follow` tinyint(1) NOT NULL DEFAULT 1,
  `ga4_id` varchar(50) DEFAULT NULL,
  `gtm_id` varchar(50) DEFAULT NULL,
  `meta_pixel_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `link_hub_clicks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `link_id` int NOT NULL,
  `clicked_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `referrer` text DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_hash` varchar(255) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `utm_source` varchar(100) DEFAULT NULL,
  `utm_medium` varchar(100) DEFAULT NULL,
  `utm_campaign` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `link_id_idx` (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `link_hub_settings` (`id`, `page_title`, `hero_title`, `hero_description`)
VALUES (1, 'FikirCreative Link Hub', 'FikirCreative', 'Markanı dijitalde fikre, tasarıma ve satışa dönüştürüyoruz.')
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO `link_hub_seo` (`id`, `title`, `description`)
VALUES (1, 'FikirCreative | Bağlantılar', 'FikirCreative resmi bağlantıları.')
ON DUPLICATE KEY UPDATE id = id;

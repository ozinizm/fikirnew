CREATE DATABASE IF NOT EXISTS `fikircreative`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `fikircreative`;

-- 1. Administrators Table
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

-- 2. Settings Table
CREATE TABLE IF NOT EXISTS `ayarlar` (
  `ayar_key` varchar(120) NOT NULL,
  `ayar_val` longtext NULL,
  PRIMARY KEY (`ayar_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Portfolio Table
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

-- 4. Services Table
CREATE TABLE IF NOT EXISTS `hizmetler` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) NOT NULL,
  `aciklama` text NULL,
  `ikon_svg` longtext NULL,
  `sira` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_sira` (`sira`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Blog Table
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

-- 6. Messages Table
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

-- 7. Team Table
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

-- 8. Statistics Table
CREATE TABLE IF NOT EXISTS `istatistikler` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `deger` VARCHAR(50) NOT NULL,
  `etiket` VARCHAR(255) NOT NULL,
  `sira` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Testimonials Table
CREATE TABLE IF NOT EXISTS `referanslar` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `isim` VARCHAR(160) NOT NULL,
  `unvan` VARCHAR(160) NOT NULL,
  `mesaj` TEXT NOT NULL,
  `foto` VARCHAR(1024) NULL,
  `sira` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Brands (Client Logos) Table
CREATE TABLE IF NOT EXISTS `markalar` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `isim` VARCHAR(255) NOT NULL,
  `logo` VARCHAR(1024) NULL,
  `logo_svg` TEXT NULL,
  `sira` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Pages Builder Tables
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

-- 12. Link Hub Tables
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


-- ====================================================
-- SEED DATA FOR PRODUCTION ENVIRONMENT
-- ====================================================

-- 1. Ayarlar (Settings) Seed
INSERT INTO `ayarlar` (`ayar_key`, `ayar_val`) VALUES
('site_baslik', 'Fikir Creative'),
('site_slogan', 'Web sitesi, reklam, video içerik ve WhatsApp dönüşüm altyapısıyla yerel işletmeler için ölçülebilir müşteri kazanımı kuruyoruz.'),
('seo_title', 'Fikir Creative — Dijital Büyüme Ajansı'),
('seo_desc', 'Fikir Creative; klinikler, güzellik merkezleri, restoranlar ve hizmet işletmeleri için web sitesi, reklam yönetimi, video içerik ve WhatsApp dönüşüm sistemi kuran dijital büyüme ajansıdır.'),
('seo_keys', 'dijital ajans, yerel işletme pazarlaması, web tasarım, performans pazarlaması, reklam ajansı, whatsapp otomasyonu, dijital büyüme'),
('email', 'hello@fikircreative.com'),
('telefon', '+90 (532) 000 00 00'),
('adres', 'İstanbul, Türkiye'),
('site_renk', '#ff4d00'),
('footer_text', '© 2026 Fikir Creative. Tüm Hakları Saklıdır.'),
('site_url', 'https://www.fikircreative.com'),
('availability_text', 'Yeni projeler için uygun'),
('header_cta_text', 'İletişim'),
('header_cta_url', '/#contact'),
('ga4_id', ''),
('gtm_id', ''),
('meta_pixel_id', ''),
('google_site_verification', ''),
('geo_region', 'TR-34'),
('geo_placename', 'Istanbul'),
('geo_position', '41.0082;28.9784'),
('icbm', '41.0082, 28.9784'),
('footer_description', 'Dijital Büyüme Ajansı'),
('politika_gizlilik', '<p>Gizlilik politikası metni admin panelinden düzenlenebilir.</p>'),
('politika_kullanim', '<p>Kullanım şartları metni admin panelinden düzenlenebilir.</p>'),
('politika_cerezler', '<p>Çerez politikası metni admin panelinden düzenlenebilir.</p>'),
('politika_kvkk', '<p>KVKK aydınlatma metni admin panelinden düzenlenebilir.</p>'),
('hero_showcase_image', 'https://framerusercontent.com/images/dT5S1njJpyHvznBNeTmMAwfBcqQ.png'),
('hero_title_line1_left', 'Sadece'),
('hero_title_line1_right', 'görünürlük değil,'),
('hero_title_line2_left', 'müşteri'),
('hero_title_line2_right', 'kazandıran sistem'),
('hero_title_line3_left', 'kuran'),
('hero_inline_image_1', '/hero-1.png'),
('hero_inline_image_2', '/hero-2.png'),
('hero_inline_image_3', '/hero-3.png'),
('experience_1_role', 'Founder at Fikir Creative'),
('experience_1_period', '2024–Now'),
('experience_2_role', 'Brand Designer at Google'),
('experience_2_period', '2023–2024'),
('experience_3_role', 'Web Designer at Shopify'),
('experience_3_period', '2018–2023'),
('experience_4_role', 'Junior Designer at Meta'),
('experience_4_period', '2015–2018')
ON DUPLICATE KEY UPDATE ayar_val = VALUES(ayar_val);

-- 2. Portfolyo (Portfolio) Seed
INSERT INTO `portfolyo` (`id`, `baslik`, `kategori`, `medya_turu`, `medya_url`, `gorsel_url`, `sira`) VALUES
(1, 'Archin', 'Web Tasarım', 'resim', 'https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png', 'https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png', 1),
(2, 'VNTNR', 'Marka Kimliği', 'resim', 'https://framerusercontent.com/images/QhPkJGJBXS8kPS7IhPj7ZBGZpII.png', 'https://framerusercontent.com/images/QhPkJGJBXS8kPS7IhPj7ZBGZpII.png', 2),
(3, 'Aeorim', 'Revamp', 'resim', 'https://framerusercontent.com/images/yOPV9nZRSJXmNPqyeWfZSThWAc.png', 'https://framerusercontent.com/images/yOPV9nZRSJXmNPqyeWfZSThWAc.png', 3)
ON DUPLICATE KEY UPDATE id = id;

-- 3. Hizmetler (Services) Seed
INSERT INTO `hizmetler` (`id`, `baslik`, `aciklama`, `ikon_svg`, `sira`) VALUES
(1, 'Web Tasarım & Yazılım', 'Dönüşüm odaklı web siteleri, kampanya sayfaları ve yerel işletmeler için müşteri kazanım altyapısı kuruyoruz.\r\n• UI/UX Tasarım\r\n• Mobil Uyumlu Arayüz\r\n• Web Geliştirme', 'https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png', 1),
(2, 'Performans Pazarlaması', 'Veri odaklı Meta ve Google Ads kampanyaları ile bütçenizi optimize ederek ölçülebilir müşteri dönüşümü sağlıyoruz.\r\n• Meta Ads\r\n• Google Ads\r\n• Retargeting', 'https://framerusercontent.com/images/L3jNOIvjVNNJ9KYGN7ZewlhM4.png', 2),
(3, 'WhatsApp Dönüşüm Sistemi', 'Gelen web ve sosyal medya trafiğini n8n entegrasyonları ile otomatik lead toplama ve karşılama sistemine dönüştürüyoruz.\r\n• n8n İş Akışları\r\n• Otomatik Karşılama\r\n• CRM Entegrasyonu', 'https://framerusercontent.com/images/yOPV9nZRSJXmNPqyeWfZSThWAc.png', 3)
ON DUPLICATE KEY UPDATE id = id;

-- 4. Ekip (Team) Seed
INSERT INTO `ekip` (`id`, `ad_soyad`, `gorev`, `bio`, `instagram`, `linkedin`, `foto`, `sira`) VALUES
(1, 'Fikir Creative', 'Dijital Büyüme Ajansı', 'Yerel işletmeler için web sitesi, reklam yönetimi, video içerik ve WhatsApp dönüşüm sistemi kuruyoruz. Amaç; daha profesyonel görünüm, doğru hedef kitle & ölçülebilir müşteri dönüşümüdür.', 'https://instagram.com', 'https://linkedin.com', 'https://framerusercontent.com/images/cdiudTEW8MSbl2008vSYXSq9ndI.png', 1)
ON DUPLICATE KEY UPDATE id = id;

-- 5. İstatistikler (Stats) Seed
INSERT INTO `istatistikler` (`id`, `deger`, `etiket`, `sira`) VALUES
(1, '26+', 'Kurulan Dijital Sistem', 1),
(2, '98%', 'Müşteri Kazanım Odağı', 2),
(3, '10M', 'Yerel İşletme Deneyimi', 3)
ON DUPLICATE KEY UPDATE id = id;

-- 6. Referanslar (Testimonials) Seed
INSERT INTO `referanslar` (`id`, `isim`, `unvan`, `mesaj`, `foto`, `sira`) VALUES
(1, 'Ethan Moore', 'Kurucu Ortak, NovaTech', 'Fikir Creative, fikirlerimizi keskin ve temiz bir markaya dönüştürdü. Hızlı, pratik ve doğrudan hedefe ulaştık.', '/why-slide-1.png', 1),
(2, 'Olivia Tran', 'Kreatif Direktör, Bloom Agency', 'Açık, düşünceli ve hızlılar. Web sitemizi kurup reklamlarımızı optimize etme sürecini tamamen zahmetsiz hale getirdiler.', '/why-slide-2.png', 2),
(3, 'Lucas Bennett', 'Ürün Yöneticisi, Hexa Studio', 'Akıllıca bir web mimarisi, sorunsuz teslimat. Fikir Creative ekibiyle çalışmak yerel büyümemiz için mükemmel bir adımdı.', '/why-slide-3.png', 3)
ON DUPLICATE KEY UPDATE id = id;

-- 7. Markalar (Client Marquee Logos) Seed
INSERT INTO `markalar` (`id`, `isim`, `logo`, `logo_svg`, `sira`) VALUES
(1, 'NovaTech', NULL, '<svg width=\"110\" height=\"24\" viewBox=\"0 0 110 24\" fill=\"currentColor\"><circle cx=\"12\" cy=\"12\" r=\"8\" stroke=\"currentColor\" stroke-width=\"2.5\" fill=\"none\" /><circle cx=\"12\" cy=\"12\" r=\"3\" /><text x=\"28\" y=\"17\" class=\"font-inter font-bold tracking-tight text-[15px]\">NovaTech</text></svg>', 1),
(2, 'Bloom', NULL, '<svg width=\"120\" height=\"24\" viewBox=\"0 0 120 24\" fill=\"currentColor\"><path d=\"M12 4 L19 12 L12 20 L5 12 Z\" stroke=\"currentColor\" stroke-width=\"2.5\" fill=\"none\" /><circle cx=\"12\" cy=\"12\" r=\"2.5\" /><text x=\"30\" y=\"17\" class=\"font-inter font-extrabold tracking-tight text-[15px]\">Bloom</text></svg>', 2),
(3, 'HexaStudio', NULL, '<svg width=\"115\" height=\"24\" viewBox=\"0 0 115 24\" fill=\"currentColor\"><path d=\"M12 3 L19 7 L19 15 L12 19 L5 15 L5 7 Z\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" /><text x=\"28\" y=\"17\" class=\"font-inter font-semibold tracking-tight text-[15px]\">HexaStudio</text></svg>', 3),
(4, 'Apex', NULL, '<svg width=\"100\" height=\"24\" viewBox=\"0 0 100 24\" fill=\"currentColor\"><polygon points=\"12 4 21 19 3 19\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.5\" /><text x=\"28\" y=\"17\" class=\"font-inter font-bold tracking-tight text-[15px]\">Apex</text></svg>', 4),
(5, 'Codex', NULL, '<svg width=\"110\" height=\"24\" viewBox=\"0 0 110 24\" fill=\"currentColor\"><path d=\"M8 8 L12 4 L16 8 M16 16 L12 20 L8 16\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.5\" stroke-linecap=\"round\" /><text x=\"26\" y=\"17\" class=\"font-inter font-bold tracking-wider text-[15px]\">Codex</text></svg>', 5),
(6, 'Aeorim', NULL, '<svg width=\"105\" height=\"24\" viewBox=\"0 0 105 24\" fill=\"currentColor\"><circle cx=\"12\" cy=\"12\" r=\"9\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-dasharray=\"16 4\" /><text x=\"28\" y=\"17\" class=\"font-inter font-extrabold tracking-tight text-[15px]\">Aeorim</text></svg>', 6)
ON DUPLICATE KEY UPDATE id = id;

-- 8. Link Hub Seeds
INSERT INTO `link_hub_settings` (`id`, `page_title`, `hero_title`, `hero_description`, `page_background_type`, `page_background_color`, `card_background_type`)
VALUES (1, 'FikirCreative Link Hub', 'FikirCreative', 'Markanı dijitalde fikre, tasarıma ve satışa dönüştürüyoruz.', 'color', '#050505', 'glass')
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO `link_hub_seo` (`id`, `title`, `description`)
VALUES (1, 'FikirCreative | Bağlantılar', 'FikirCreative resmi bağlantıları.')
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO `link_hub_links` (`id`, `title`, `url`, `color_mode`, `is_featured`, `icon`, `sort_order`) VALUES
(1, 'Teklif Al', 'https://fikircreative.com/teklif-al', 'gradient', 1, 'fa-solid fa-bolt', 1),
(2, 'WhatsApp\'tan Yaz', 'https://wa.me/905320000000', 'brand', 1, 'fa-brands fa-whatsapp', 2),
(3, 'Web Tasarım & Yazılım', 'https://fikircreative.com/hizmetler/web-tasarim', 'dark', 0, 'fa-solid fa-code', 3),
(4, 'Marka Kimliği & Logo Tasarım', 'https://fikircreative.com/hizmetler/marka-kimligi', 'dark', 0, 'fa-solid fa-pen-nib', 4),
(5, 'Sosyal Medya Yönetimi', 'https://fikircreative.com/hizmetler/sosyal-medya', 'dark', 0, 'fa-solid fa-hashtag', 5),
(6, 'Google Ads & Meta Ads', 'https://fikircreative.com/hizmetler/dijital-pazarlama', 'dark', 0, 'fa-solid fa-bullhorn', 6),
(7, 'SEO & Local SEO', 'https://fikircreative.com/hizmetler/seo', 'dark', 0, 'fa-solid fa-magnifying-glass-chart', 7),
(8, 'UI/UX Tasarım', 'https://fikircreative.com/hizmetler/ui-ux', 'dark', 0, 'fa-solid fa-mobile-screen-button', 8),
(9, 'Portföyü Gör', 'https://fikircreative.com/portfolyo', 'dark', 0, 'fa-solid fa-images', 9),
(10, 'Ana Siteye Git', 'https://fikircreative.com', 'light', 0, 'fa-solid fa-globe', 10)
ON DUPLICATE KEY UPDATE id = id;

INSERT INTO `link_hub_social_links` (`id`, `platform`, `url`, `icon`, `sort_order`) VALUES
(1, 'Instagram', 'https://instagram.com/fikircreative', 'fa-brands fa-instagram', 1),
(2, 'LinkedIn', 'https://linkedin.com/company/fikircreative', 'fa-brands fa-linkedin-in', 2),
(3, 'Behance', 'https://behance.net/fikircreative', 'fa-brands fa-behance', 3)
ON DUPLICATE KEY UPDATE id = id;

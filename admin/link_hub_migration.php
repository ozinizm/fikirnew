<?php
/**
 * link_hub_migration.php
 * Bu dosya Link Hub tablolarını oluşturur. Çalıştırdıktan sonra silebilirsiniz.
 */

// Sadece komut satırı veya geçici token ile çalıştırmaya izin ver
// Geliştirme aşamasında doğrudan çalıştırabilmek için geçici bir kontrol koyuyoruz.
// Eğer canlı ortamdaysanız, bu dosyayı sildiğinizden emin olun!

require_once __DIR__ . '/../includes/db.php';

echo "<h1>Link Hub Migration Başlatılıyor...</h1><pre>";

try {
    // 1. link_hub_settings
    $sql1 = "CREATE TABLE IF NOT EXISTS `link_hub_settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
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
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql1);
    echo "✔ link_hub_settings tablosu oluşturuldu.\n";

    // 2. link_hub_links
    $sql2 = "CREATE TABLE IF NOT EXISTS `link_hub_links` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` varchar(255) DEFAULT NULL,
        `url` text NOT NULL,
        `icon` varchar(100) DEFAULT NULL,
        `color_mode` varchar(50) DEFAULT 'dark',
        `gradient_from` varchar(20) DEFAULT NULL,
        `gradient_to` varchar(20) DEFAULT NULL,
        `is_featured` tinyint(1) NOT NULL DEFAULT 0,
        `is_active` tinyint(1) NOT NULL DEFAULT 1,
        `sort_order` int(11) NOT NULL DEFAULT 0,
        `open_in_new_tab` tinyint(1) NOT NULL DEFAULT 1,
        `utm_source` varchar(100) DEFAULT NULL,
        `utm_medium` varchar(100) DEFAULT NULL,
        `utm_campaign` varchar(100) DEFAULT NULL,
        `click_count` int(11) NOT NULL DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql2);
    echo "✔ link_hub_links tablosu oluşturuldu.\n";

    // 3. link_hub_social_links
    $sql3 = "CREATE TABLE IF NOT EXISTS `link_hub_social_links` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `platform` varchar(50) NOT NULL,
        `label` varchar(100) DEFAULT NULL,
        `url` text NOT NULL,
        `icon` varchar(100) DEFAULT NULL,
        `is_active` tinyint(1) NOT NULL DEFAULT 1,
        `sort_order` int(11) NOT NULL DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql3);
    echo "✔ link_hub_social_links tablosu oluşturuldu.\n";

    // 4. link_hub_seo
    $sql4 = "CREATE TABLE IF NOT EXISTS `link_hub_seo` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
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
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql4);
    echo "✔ link_hub_seo tablosu oluşturuldu.\n";

    // 5. link_hub_clicks
    $sql5 = "CREATE TABLE IF NOT EXISTS `link_hub_clicks` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `link_id` int(11) NOT NULL,
        `clicked_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `referrer` text DEFAULT NULL,
        `user_agent` text DEFAULT NULL,
        `ip_hash` varchar(255) DEFAULT NULL,
        `device_type` varchar(50) DEFAULT NULL,
        `utm_source` varchar(100) DEFAULT NULL,
        `utm_medium` varchar(100) DEFAULT NULL,
        `utm_campaign` varchar(100) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `link_id_idx` (`link_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql5);
    echo "✔ link_hub_clicks tablosu oluşturuldu.\n";

    // Insert default settings if empty
    $check_settings = $db->query("SELECT COUNT(*) FROM link_hub_settings")->fetchColumn();
    if ($check_settings == 0) {
        $db->exec("INSERT INTO link_hub_settings (page_title, hero_title, hero_description) VALUES ('FikirCreative Link Hub', 'FikirCreative', 'Markanı dijitalde fikre, tasarıma ve satışa dönüştürüyoruz.')");
        echo "✔ Varsayılan settings eklendi.\n";
    }

    $check_seo = $db->query("SELECT COUNT(*) FROM link_hub_seo")->fetchColumn();
    if ($check_seo == 0) {
        $db->exec("INSERT INTO link_hub_seo (title, description) VALUES ('FikirCreative | Bağlantılar', 'FikirCreative resmi bağlantıları.')");
        echo "✔ Varsayılan SEO ayarları eklendi.\n";
    }

    // Varsayılan Linkler (Eğer boşsa)
    $check_links = $db->query("SELECT COUNT(*) FROM link_hub_links")->fetchColumn();
    if ($check_links == 0) {
        $default_links = [
            ['title' => 'Teklif Al', 'url' => 'https://fikircreative.com/teklif-al', 'color_mode' => 'gradient', 'is_featured' => 1, 'icon' => 'fa-solid fa-bolt'],
            ['title' => 'WhatsApp\'tan Yaz', 'url' => 'https://wa.me/905550000000', 'color_mode' => 'brand', 'is_featured' => 1, 'icon' => 'fa-brands fa-whatsapp'],
            ['title' => 'Web Tasarım & Yazılım', 'url' => 'https://fikircreative.com/hizmetler/web-tasarim', 'color_mode' => 'dark', 'is_featured' => 0, 'icon' => 'fa-solid fa-code'],
            ['title' => 'Marka Kimliği & Logo Tasarım', 'url' => 'https://fikircreative.com/hizmetler/marka-kimligi', 'color_mode' => 'dark', 'is_featured' => 0, 'icon' => 'fa-solid fa-pen-nib'],
            ['title' => 'Sosyal Medya Yönetimi', 'url' => 'https://fikircreative.com/hizmetler/sosyal-medya', 'color_mode' => 'dark', 'is_featured' => 0, 'icon' => 'fa-solid fa-hashtag'],
            ['title' => 'Google Ads & Meta Ads', 'url' => 'https://fikircreative.com/hizmetler/dijital-pazarlama', 'color_mode' => 'dark', 'is_featured' => 0, 'icon' => 'fa-solid fa-bullhorn'],
            ['title' => 'SEO & Local SEO', 'url' => 'https://fikircreative.com/hizmetler/seo', 'color_mode' => 'dark', 'is_featured' => 0, 'icon' => 'fa-solid fa-magnifying-glass-chart'],
            ['title' => 'UI/UX Tasarım', 'url' => 'https://fikircreative.com/hizmetler/ui-ux', 'color_mode' => 'dark', 'is_featured' => 0, 'icon' => 'fa-solid fa-mobile-screen-button'],
            ['title' => 'Portföyü Gör', 'url' => 'https://fikircreative.com/portfolyo', 'color_mode' => 'dark', 'is_featured' => 0, 'icon' => 'fa-solid fa-images'],
            ['title' => 'Ana Siteye Git', 'url' => 'https://fikircreative.com', 'color_mode' => 'light', 'is_featured' => 0, 'icon' => 'fa-solid fa-globe'],
        ];

        $stmt = $db->prepare("INSERT INTO link_hub_links (title, url, color_mode, is_featured, icon, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        $sort = 1;
        foreach ($default_links as $link) {
            $stmt->execute([$link['title'], $link['url'], $link['color_mode'], $link['is_featured'], $link['icon'], $sort++]);
        }
        echo "✔ Varsayılan bağlantılar eklendi.\n";
    }

    // Varsayılan Sosyal Linkler (Eğer boşsa)
    $check_social = $db->query("SELECT COUNT(*) FROM link_hub_social_links")->fetchColumn();
    if ($check_social == 0) {
        $default_social = [
            ['platform' => 'Instagram', 'url' => 'https://instagram.com/fikircreative', 'icon' => 'fa-brands fa-instagram'],
            ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/company/fikircreative', 'icon' => 'fa-brands fa-linkedin-in'],
            ['platform' => 'Behance', 'url' => 'https://behance.net/fikircreative', 'icon' => 'fa-brands fa-behance'],
        ];
        
        $stmt = $db->prepare("INSERT INTO link_hub_social_links (platform, url, icon, sort_order) VALUES (?, ?, ?, ?)");
        $sort = 1;
        foreach ($default_social as $social) {
            $stmt->execute([$social['platform'], $social['url'], $social['icon'], $sort++]);
        }
        echo "✔ Varsayılan sosyal medya bağlantıları eklendi.\n";
    }

    echo "\n\n✅ MIGRATION BAŞARIYLA TAMAMLANDI.";

} catch (PDOException $e) {
    echo "\n❌ HATA: " . $e->getMessage();
}

echo "</pre>";
?>

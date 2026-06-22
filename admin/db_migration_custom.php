<?php
/**
 * db_migration_custom.php - Custom Migration for Stats, Testimonials, Hero, Profile, Portfolio & Services Configurations
 */
require_once __DIR__ . '/../includes/db.php';

$is_silent = isset($silent_migration) && $silent_migration;

try {
    // 1. Statistics Table
    $db->exec("CREATE TABLE IF NOT EXISTS `istatistikler` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `deger` VARCHAR(50) NOT NULL,
        `etiket` VARCHAR(255) NOT NULL,
        `sira` INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Insert Default Stats if empty
    $count = $db->query("SELECT COUNT(*) FROM `istatistikler`")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO `istatistikler` (`deger`, `etiket`, `sira`) VALUES
        ('26+', 'Kurulan Dijital Sistem', 1),
        ('98%', 'Müşteri Kazanım Odağı', 2),
        ('10M', 'Yerel İşletme Deneyimi', 3);");
        if (!$is_silent) {
            echo "Default statistics inserted.<br>";
        }
    }

    // 2. Testimonials Table
    $db->exec("CREATE TABLE IF NOT EXISTS `referanslar` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `isim` VARCHAR(160) NOT NULL,
        `unvan` VARCHAR(160) NOT NULL,
        `mesaj` TEXT NOT NULL,
        `foto` VARCHAR(1024) NULL,
        `sira` INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Insert Default Testimonials if empty
    $count = $db->query("SELECT COUNT(*) FROM `referanslar`")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO `referanslar` (`isim`, `unvan`, `mesaj`, `foto`, `sira`) VALUES
        ('Ethan Moore', 'Kurucu Ortak, NovaTech', 'Fikir Creative, fikirlerimizi keskin ve temiz bir markaya dönüştürdü. Hızlı, pratik ve doğrudan hedefe ulaştık.', '/why-slide-1.png', 1),
        ('Olivia Tran', 'Kreatif Direktör, Bloom Agency', 'Açık, düşünceli ve hızlılar. Web sitemizi kurup reklamlarımızı optimize etme sürecini tamamen zahmetsiz hale getirdiler.', '/why-slide-2.png', 2),
        ('Lucas Bennett', 'Ürün Yöneticisi, Hexa Studio', 'Akıllıca bir web mimarisi, sorunsuz teslimat. Fikir Creative ekibiyle çalışmak yerel büyümemiz için mükemmel bir adımdı.', '/why-slide-3.png', 3);");
        if (!$is_silent) {
            echo "Default testimonials inserted.<br>";
        }
    }

    // 3. Ekip (Team) Table
    $db->exec("CREATE TABLE IF NOT EXISTS `ekip` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `ad_soyad` VARCHAR(160) NOT NULL,
        `gorev` VARCHAR(160) NULL,
        `bio` TEXT NULL,
        `instagram` VARCHAR(255) NULL,
        `linkedin` VARCHAR(255) NULL,
        `foto` VARCHAR(1024) NULL,
        `sira` INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Insert Default Team Member (Founder) if empty
    $count = $db->query("SELECT COUNT(*) FROM `ekip`")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO `ekip` (`ad_soyad`, `gorev`, `bio`, `instagram`, `linkedin`, `foto`, `sira`) VALUES
        ('Fikir Creative', 'Dijital Büyüme Ajansı', 'Yerel işletmeler için web sitesi, reklam yönetimi, video içerik ve WhatsApp dönüşüm sistemi kuruyoruz. Amaç; daha profesyonel görünüm, doğru hedef kitle & ölçülebilir müşteri dönüşümüdür.', 'https://instagram.com', 'https://linkedin.com', 'https://framerusercontent.com/images/cdiudTEW8MSbl2008vSYXSq9ndI.png', 1);");
        if (!$is_silent) {
            echo "Default team member (founder) inserted.<br>";
        }
    }

    // 4. Portfolyo Table Pre-population
    $count = $db->query("SELECT COUNT(*) FROM `portfolyo`")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO `portfolyo` (`baslik`, `kategori`, `medya_turu`, `medya_url`, `gorsel_url`, `sira`) VALUES
        ('Archin', 'Web Tasarım', 'resim', 'https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png', 'https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png', 1),
        ('VNTNR', 'Marka Kimliği', 'resim', 'https://framerusercontent.com/images/QhPkJGJBXS8kPS7IhPj7ZBGZpII.png', 'https://framerusercontent.com/images/QhPkJGJBXS8kPS7IhPj7ZBGZpII.png', 2),
        ('Aeorim', 'Revamp', 'resim', 'https://framerusercontent.com/images/yOPV9nZRSJXmNPqyeWfZSThWAc.png', 'https://framerusercontent.com/images/yOPV9nZRSJXmNPqyeWfZSThWAc.png', 3);");
        if (!$is_silent) {
            echo "Default portfolio items inserted.<br>";
        }
    }

    // 5. Hizmetler Table Pre-population
    $count = $db->query("SELECT COUNT(*) FROM `hizmetler`")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO `hizmetler` (`baslik`, `aciklama`, `ikon_svg`, `sira`) VALUES
        ('Web Tasarım & Yazılım', 'Dönüşüm odaklı web siteleri, kampanya sayfaları ve yerel işletmeler için müşteri kazanım altyapısı kuruyoruz.\r\n• UI/UX Tasarım\r\n• Mobil Uyumlu Arayüz\r\n• Web Geliştirme', 'https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png', 1),
        ('Performans Pazarlaması', 'Veri odaklı Meta ve Google Ads kampanyaları ile bütçenizi optimize ederek ölçülebilir müşteri dönüşümü sağlıyoruz.\r\n• Meta Ads\r\n• Google Ads\r\n• Retargeting', 'https://framerusercontent.com/images/L3jNOIvjVNNJ9KYGN7ZewlhM4.png', 2),
        ('WhatsApp Dönüşüm Sistemi', 'Gelen web ve sosyal medya trafiğini n8n entegrasyonları ile otomatik lead toplama ve karşılama sistemine dönüştürüyoruz.\r\n• n8n İş Akışları\r\n• Otomatik Karşılama\r\n• CRM Entegrasyonu', 'https://framerusercontent.com/images/yOPV9nZRSJXmNPqyeWfZSThWAc.png', 3);");
        if (!$is_silent) {
            echo "Default services inserted.<br>";
        }
    }

    // 5.5. Markalar (Partners/Client Logos) Table
    $db->exec("CREATE TABLE IF NOT EXISTS `markalar` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `isim` VARCHAR(255) NOT NULL,
        `logo` VARCHAR(1024) NULL,
        `logo_svg` TEXT NULL,
        `sira` INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $count = $db->query("SELECT COUNT(*) FROM `markalar`")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO `markalar` (`isim`, `logo`, `logo_svg`, `sira`) VALUES
        ('NovaTech', NULL, '<svg width=\"110\" height=\"24\" viewBox=\"0 0 110 24\" fill=\"currentColor\"><circle cx=\"12\" cy=\"12\" r=\"8\" stroke=\"currentColor\" stroke-width=\"2.5\" fill=\"none\" /><circle cx=\"12\" cy=\"12\" r=\"3\" /><text x=\"28\" y=\"17\" class=\"font-inter font-bold tracking-tight text-[15px]\">NovaTech</text></svg>', 1),
        ('Bloom', NULL, '<svg width=\"120\" height=\"24\" viewBox=\"0 0 120 24\" fill=\"currentColor\"><path d=\"M12 4 L19 12 L12 20 L5 12 Z\" stroke=\"currentColor\" stroke-width=\"2.5\" fill=\"none\" /><circle cx=\"12\" cy=\"12\" r=\"2.5\" /><text x=\"30\" y=\"17\" class=\"font-inter font-extrabold tracking-tight text-[15px]\">Bloom</text></svg>', 2),
        ('HexaStudio', NULL, '<svg width=\"115\" height=\"24\" viewBox=\"0 0 115 24\" fill=\"currentColor\"><path d=\"M12 3 L19 7 L19 15 L12 19 L5 15 L5 7 Z\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" /><text x=\"28\" y=\"17\" class=\"font-inter font-semibold tracking-tight text-[15px]\">HexaStudio</text></svg>', 3),
        ('Apex', NULL, '<svg width=\"100\" height=\"24\" viewBox=\"0 0 100 24\" fill=\"currentColor\"><polygon points=\"12 4 21 19 3 19\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.5\" /><text x=\"28\" y=\"17\" class=\"font-inter font-bold tracking-tight text-[15px]\">Apex</text></svg>', 4),
        ('Codex', NULL, '<svg width=\"110\" height=\"24\" viewBox=\"0 0 110 24\" fill=\"currentColor\"><path d=\"M8 8 L12 4 L16 8 M16 16 L12 20 L8 16\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2.5\" stroke-linecap=\"round\" /><text x=\"26\" y=\"17\" class=\"font-inter font-bold tracking-wider text-[15px]\">Codex</text></svg>', 5),
        ('Aeorim', NULL, '<svg width=\"105\" height=\"24\" viewBox=\"0 0 105 24\" fill=\"currentColor\"><circle cx=\"12\" cy=\"12\" r=\"9\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-dasharray=\"16 4\" /><text x=\"28\" y=\"17\" class=\"font-inter font-extrabold tracking-tight text-[15px]\">Aeorim</text></svg>', 6);");
        if (!$is_silent) {
            echo "Default brand/partner logos inserted.<br>";
        }
    }

    // 6. Hero & Profile Custom Settings
    $custom_settings = [
        'hero_showcase_image' => 'https://framerusercontent.com/images/dT5S1njJpyHvznBNeTmMAwfBcqQ.png',
        'hero_title_line1_left' => 'Sadece',
        'hero_title_line1_right' => 'görünürlük değil,',
        'hero_title_line2_left' => 'müşteri',
        'hero_title_line2_right' => 'kazandıran sistem',
        'hero_title_line3_left' => 'kuran',
        'hero_inline_image_1' => '/hero-1.png',
        'hero_inline_image_2' => '/hero-2.png',
        'hero_inline_image_3' => '/hero-3.png',
        'site_slogan' => 'Web sitesi, reklam, video içerik ve WhatsApp dönüşüm altyapısıyla yerel işletmeler için ölçülebilir müşteri kazanımı kuruyoruz.',
        'experience_1_role' => 'Founder at Fikir Creative',
        'experience_1_period' => '2024–Now',
        'experience_2_role' => 'Brand Designer at Google',
        'experience_2_period' => '2023–2024',
        'experience_3_role' => 'Web Designer at Shopify',
        'experience_3_period' => '2018–2023',
        'experience_4_role' => 'Junior Designer at Meta',
        'experience_4_period' => '2015–2018'
    ];

    foreach ($custom_settings as $key => $val) {
        $stmt = $db->prepare("INSERT INTO `ayarlar` (`ayar_key`, `ayar_val`) VALUES (?, ?) ON DUPLICATE KEY UPDATE ayar_key = ayar_key");
        $stmt->execute([$key, $val]);
    }

    if (!$is_silent) {
        echo "Custom migration completed successfully.<br>";
    }
} catch (PDOException $e) {
    die("Custom migration failed: " . $e->getMessage());
}

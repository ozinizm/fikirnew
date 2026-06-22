<?php
/**
 * link_hub_update_v2.php
 * Veritabanı "Link Hub Premium Card" versiyonu için kolon ekleme işlemi.
 */

require_once __DIR__ . '/../includes/db.php';

echo "<h1>Link Hub Premium (v2) Veritabanı Güncellemesi Başlatılıyor...</h1><pre>";

try {
    $columnsToAdd = [
        // Dış Sayfa Arka Planı (Page Background)
        'page_background_type' => "VARCHAR(20) DEFAULT 'color'",
        'page_background_color' => "VARCHAR(30) DEFAULT '#050505'",
        'page_background_gradient_from' => "VARCHAR(30) DEFAULT NULL",
        'page_background_gradient_to' => "VARCHAR(30) DEFAULT NULL",
        'page_background_image_url' => "VARCHAR(255) DEFAULT NULL",
        
        // İç Kart Arka Planı (Card Background)
        'card_background_type' => "VARCHAR(20) DEFAULT 'glass'",
        'card_background_color' => "VARCHAR(30) DEFAULT NULL",
        'card_background_gradient_from' => "VARCHAR(30) DEFAULT NULL",
        'card_background_gradient_to' => "VARCHAR(30) DEFAULT NULL",
        'card_background_image_url' => "VARCHAR(255) DEFAULT NULL",
        'card_background_overlay_opacity' => "FLOAT DEFAULT 0.8",
        'card_background_position' => "VARCHAR(50) DEFAULT 'center'",
        'card_background_size' => "VARCHAR(50) DEFAULT 'cover'",
        
        // Logo
        'logo_display_mode' => "VARCHAR(20) DEFAULT 'image'",
        'logo_size_desktop' => "INT DEFAULT 80",
        'logo_size_mobile' => "INT DEFAULT 64"
    ];

    foreach ($columnsToAdd as $column => $definition) {
        try {
            $stmt = $db->query("SHOW COLUMNS FROM `link_hub_settings` LIKE '$column'");
            if ($stmt->rowCount() == 0) {
                $db->exec("ALTER TABLE `link_hub_settings` ADD COLUMN `$column` $definition");
                echo "✔ `$column` kolonu başarıyla eklendi.\n";
            } else {
                echo "ℹ `$column` kolonu zaten mevcut.\n";
            }
        } catch (PDOException $e) {
            echo "❌ HATA (`$column`): " . $e->getMessage() . "\n";
        }
    }

    echo "\n\n✅ V2 MIGRATION BAŞARIYLA TAMAMLANDI.";

} catch (Exception $e) {
    echo "\n❌ GENEL HATA: " . $e->getMessage();
}

echo "</pre>";
?>

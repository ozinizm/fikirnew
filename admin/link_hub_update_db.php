<?php
/**
 * link_hub_update_db.php
 * Bu dosya eksik olan kolonları veritabanına eklemek için kullanılır.
 */

require_once __DIR__ . '/../includes/db.php';

echo "<h1>Veritabanı Güncellemesi Başlatılıyor...</h1><pre>";

try {
    // Check and add columns to link_hub_links
    $columnsToAdd = [
        'color_mode' => "VARCHAR(50) DEFAULT 'dark' AFTER `icon`",
        'gradient_from' => "VARCHAR(20) DEFAULT NULL AFTER `color_mode`",
        'gradient_to' => "VARCHAR(20) DEFAULT NULL AFTER `gradient_from`",
        'open_in_new_tab' => "TINYINT(1) NOT NULL DEFAULT 1 AFTER `sort_order`"
    ];

    foreach ($columnsToAdd as $column => $definition) {
        try {
            // Check if column exists
            $stmt = $db->query("SHOW COLUMNS FROM `link_hub_links` LIKE '$column'");
            if ($stmt->rowCount() == 0) {
                $db->exec("ALTER TABLE `link_hub_links` ADD COLUMN `$column` $definition");
                echo "✔ `$column` kolonu eklendi.\n";
            } else {
                echo "ℹ `$column` kolonu zaten mevcut.\n";
            }
        } catch (PDOException $e) {
            echo "❌ HATA (`$column`): " . $e->getMessage() . "\n";
        }
    }

    echo "\n\n✅ VERİTABANI GÜNCELLEMESİ BAŞARIYLA TAMAMLANDI.";

} catch (Exception $e) {
    echo "\n❌ GENEL HATA: " . $e->getMessage();
}

echo "</pre>";
?>

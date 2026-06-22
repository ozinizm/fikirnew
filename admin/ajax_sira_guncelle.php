<?php
// DOSYA YOLU: admin/ajax_sira_guncelle.php
require_once '../includes/db.php'; 

if (isset($_POST['item'])) {
    $siralamalar = $_POST['item']; 
    try {
        foreach ($siralamalar as $sira => $id) {
            $yeni_sira = $sira + 1; // 1, 2, 3 diye gitsin
            $stmt = $db->prepare("UPDATE portfolyo SET sira = ? WHERE id = ?");
            $stmt->execute([$yeni_sira, $id]);
        }
        echo "Basarili"; 
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>
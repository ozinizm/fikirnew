<?php
/**
 * import_initial_pages.php - Mevcut statik sayfaları dinamik sisteme aktarır.
 */
require_once __DIR__ . '/../includes/db.php';

$initial_pages = [
    [
        'title' => 'Sosyal Medya Yönetimi',
        'slug' => 'sosyal-medya-yonetimi',
        'type' => 'service',
        'h1' => 'Markanızı Sosyal Medyada Düzenli, Güçlü ve Güvenilir Hale Getiriyoruz',
        'excerpt' => 'Markanıza özel içerik planı, tasarım, reels üretimi, paylaşım yönetimi ve raporlama ile profesyonel sosyal medya yönetimi hizmeti alın.',
        'content' => '<p>Fikir Creative olarak markanıza özel içerik stratejisi, tasarım dili, video üretimi ve yayın planı oluşturarak sosyal medya hesaplarınızı kurumsal bir iletişim kanalına dönüştürürüz.</p>',
        'seo_title' => 'Sosyal Medya Yönetimi | Fikir Creative',
        'meta_description' => 'Markanıza özel içerik planı, tasarım, reels üretimi, paylaşım yönetimi ve raporlama ile profesyonel sosyal medya yönetimi hizmeti alın.',
    ],
    [
        'title' => 'İstanbul Reklam Ajansı',
        'slug' => 'istanbul-reklam-ajansi',
        'type' => 'location',
        'h1' => 'İstanbul’un Kalbinde, Dijital Dünyanın Zirvesinde Markanızı Büyütüyoruz',
        'excerpt' => 'Fikir Creative olarak, İstanbul ve Anadolu Yakası’ndaki markalara stratejik reklam, profesyonel tasarım ve performans odaklı dijital çözümler sunuyoruz.',
        'content' => '<p>İstanbul gibi rekabetin en yüksek olduğu bir pazarda, markanızın sadece "var olması" yetmez; fark edilmesi ve tercih edilmesi gerekir.</p>',
        'seo_title' => 'İstanbul Reklam Ajansı | Fikir Creative',
        'meta_description' => 'İstanbul reklam ajansı arayışınızda Fikir Creative; sosyal medya yönetimi, SEO, Google Ads ve kurumsal kimlik çözümleriyle yanınızda.',
    ],
    // Diğer sayfalar admin panelden manuel de eklenebilir, bu iki örnek sistemin çalıştığını doğrular.
];

try {
    foreach ($initial_pages as $p) {
        $stmt = $db->prepare("INSERT IGNORE INTO pages (title, slug, page_type, h1, excerpt, content, seo_title, meta_description, status) VALUES (?,?,?,?,?,?,?,?, 'published')");
        $stmt->execute([
            $p['title'], $p['slug'], $p['type'], $p['h1'], $p['excerpt'], $p['content'], $p['seo_title'], $p['meta_description']
        ]);
        echo "Sayfa eklendi: " . $p['title'] . "\n";
    }
    echo "İçe aktarma tamamlandı.";
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}

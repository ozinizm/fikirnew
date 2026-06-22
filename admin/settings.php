<?php
/**
 * settings.php - Kaptan Köşkü | Genel Site ve SEO Ayarları
 */

// 1. ÖNCE BAĞLANTI VE KAYDETME MANTIĞI
require_once '../includes/db.php';

$message = '';
$brandDir = dirname(__DIR__) . '/brand';
if (!is_dir($brandDir)) {
    mkdir($brandDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A. Metin Ayarlarını Kaydet (Toplu)
    if (isset($_POST['settings'])) {
        foreach ($_POST['settings'] as $key => $val) {
            
            // EĞER ÇOKLU İLETİŞİM BİLGİSİ (DİZİ) GELDİYSE BUNU VİRGÜLLE BİRLEŞTİR
            if (is_array($val)) {
                $val = array_map('trim', $val); // Etraftaki boşlukları temizle
                $val = array_filter($val);      // İçi boş gönderilen kutucukları sil
                $val = implode(', ', $val);     // Ön yüzdeki kodumuz için virgülle birleştir
            }

            // Renk değeri için # prefix garantisi
            if ($key === 'site_renk') {
                $val = '#' . ltrim(trim($val), '#');
                // Geçersiz HEX ise varsayılana dön
                if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $val)) {
                    $val = '#F15A24';
                }
            }

            $stmt = $db->prepare("INSERT INTO ayarlar (ayar_key, ayar_val) VALUES (?, ?) ON DUPLICATE KEY UPDATE ayar_val = ?");
            $stmt->execute([$key, $val, $val]);
        }
    }

    // B. Görsel Yüklemelerini İşle (Görsel Kimlik Yenilendi)
    $files = [
        'logo_on_light', 'logo_on_dark', 'footer_logo', 
        'favicon_master', 'admin_logo', 'admin_sidebar_icon', 'og_image_default',
        'hero_showcase_image', 'hero_inline_image_1', 'hero_inline_image_2', 'hero_inline_image_3'
    ];
    
    $asset_version_incremented = false;
    $errors = [];
    
    foreach ($files as $file_key) {
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['name'] !== '') {
            if ($_FILES[$file_key]['error'] !== 0) {
                $err_code = $_FILES[$file_key]['error'];
                $err_msg = "Yükleme hatası (Kod: $err_code).";
                if ($err_code === 1 || $err_code === 2) {
                    $err_msg = "Dosya boyutu sunucu limitini aşıyor. Lütfen daha küçük bir dosya yükleyin.";
                }
                $errors[] = "$file_key: $err_msg";
                continue;
            }

            $ext = strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
            
            // Security check
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            $mime = $finfo ? @finfo_file($finfo, $_FILES[$file_key]['tmp_name']) : '';
            if($finfo) @finfo_close($finfo);
            if(!$mime) {
                $mime = mime_content_type($_FILES[$file_key]['tmp_name']);
            }
            if(!$mime) {
                $mime = $_FILES[$file_key]['type'] ?? '';
            }
            
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'ico'];
            $allowed_mimes = [
                'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/x-png', 
                'image/png', 'image/webp', 'image/svg+xml', 'image/x-icon', 
                'image/vnd.microsoft.icon', 'application/octet-stream'
            ];
            
            if (in_array($ext, $allowed_exts)) {
                $filename = str_replace('_', '-', $file_key) . '.' . $ext;
                $upload_path = ROOT_DIR . '/brand/' . $filename;

                if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $upload_path)) {
                    $val = 'brand/' . $filename;
                    $stmt = $db->prepare("INSERT INTO ayarlar (ayar_key, ayar_val) VALUES (?, ?) ON DUPLICATE KEY UPDATE ayar_val = ?");
                    $stmt->execute([$file_key, $val, $val]);
                    $asset_version_incremented = true;
                    
                    // Favicon generation logic
                    if ($file_key === 'favicon_master') {
                        $src_path = $upload_path;
                        $ext_lower = strtolower($ext);
                        $src_img = null;
                        if ($ext_lower === 'jpg' || $ext_lower === 'jpeg') {
                            $src_img = @imagecreatefromjpeg($src_path);
                        } elseif ($ext_lower === 'png') {
                            $src_img = @imagecreatefrompng($src_path);
                        } elseif ($ext_lower === 'webp') {
                            $src_img = @imagecreatefromwebp($src_path);
                        }
                        
                        if ($src_img) {
                            $src_w = imagesx($src_img);
                            $src_h = imagesy($src_img);
                            $sizes = [
                                ROOT_DIR . '/brand/favicon-16x16.png' => 16,
                                ROOT_DIR . '/brand/favicon-32x32.png' => 32,
                                ROOT_DIR . '/brand/apple-touch-icon.png' => 180,
                                ROOT_DIR . '/brand/android-chrome-192x192.png' => 192,
                                ROOT_DIR . '/brand/android-chrome-512x512.png' => 512,
                                ROOT_DIR . '/brand/favicon.ico' => 32
                            ];
                            foreach ($sizes as $out_path => $size) {
                                $dst_img = imagecreatetruecolor($size, $size);
                                imagealphablending($dst_img, false);
                                imagesavealpha($dst_img, true);
                                $transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
                                imagefilledrectangle($dst_img, 0, 0, $size, $size, $transparent);
                                imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $size, $size, $src_w, $src_h);
                                imagepng($dst_img, $out_path);
                                imagedestroy($dst_img);
                            }
                            imagedestroy($src_img);
                        }
                    }
                } else {
                    $errors[] = "$file_key: Dosya sunucuya kaydedilemedi. Klasör izinlerini kontrol edin.";
                }
            } else {
                $errors[] = "$file_key: Desteklenmeyen dosya tipi (Uzantı: $ext, Mime: $mime).";
            }
        }
    }
    
    // Cache busting için version güncelle
    if ($asset_version_incremented) {
        $db->query("INSERT INTO ayarlar (ayar_key, ayar_val) VALUES ('assetVersion', '2') ON DUPLICATE KEY UPDATE ayar_val = ayar_val + 1");
    }

    $error_message = '';
    if (!empty($errors)) {
        $error_message = "Bazı görseller yüklenemedi:<br>• " . implode("<br>• ", $errors);
    }
    $message = 'Tüm ayarlar ve iletişim bilgileri başarıyla güncellendi!';

}

// 2. TÜM AYARLARI ÇEK VE ASSOC ARRAY YAP
$settings_raw = $db->query("SELECT * FROM ayarlar")->fetchAll(PDO::FETCH_ASSOC);
$settings = [];
if ($settings_raw) {
    foreach ($settings_raw as $s) {
        // Sütun isimlerinin varlığını kontrol ederek hata almayı engelliyoruz
        $key = isset($s['ayar_key']) ? $s['ayar_key'] : (isset($s[0]) ? $s[0] : null);
        $val = isset($s['ayar_val']) ? $s['ayar_val'] : (isset($s[1]) ? $s[1] : null);

        if ($key !== null) {
            $settings[$key] = $val;
        }
    }
}

// Varsayılan Değerler
$defaults = [
    'site_baslik' => 'Fikir Creative',
    'site_slogan' => 'Dijital Etki Tasarlıyoruz',
    'email' => 'merhaba@fikircreative.com',
    'telefon' => '+90 212 000 00 00',
    'adres' => 'İstanbul, Türkiye',
    'instagram' => '',
    'linkedin' => '',
    'twitter' => '',
    'facebook' => '',
    'youtube' => '',
    'seo_desc' => 'Fikir Creative dijital ajans.',
    'seo_keys' => 'dijital ajans, web tasarım, sosyal medya',
    'analytics' => '',
    'footer_text' => '© ' . date('Y') . ' Fikir Creative. Tüm Hakları Saklıdır.',
    'site_renk'  => '#F15A24',
];

foreach ($defaults as $k => $v) {
    if (!isset($settings[$k]))
        $settings[$k] = $v;
}

require_once 'header.php';
?>

<div class="max-w-5xl mx-auto space-y-8 pb-20">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight uppercase">Site Ayarları</h2>
            <p class="text-gray-500 text-sm mt-1">Görünüm, çoklu iletişim ve SEO ayarlarını buradan yönetin. Sunucu Dosya Yükleme Limiti: <strong class="text-[#F15A24]"><?php echo ini_get('upload_max_filesize'); ?></strong> (Hızlı sayfa yüklemesi için görsellerin 2 MB altında ve WebP formatında olması önerilir).</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-5 rounded-3xl text-sm font-bold">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-5 rounded-3xl text-sm font-bold mt-4">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>


    <form method="POST" enctype="multipart/form-data" class="space-y-8">
        
        <div class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> Görsel Kimlik
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Header Logosu - Açık Zemin</label>
                    <p class="text-[9px] text-gray-600 mb-2">Kullanım: Beyaz/açık zeminli menü ve arka planlar<br>Önerilen: SVG veya 480x160 PNG<br>Sitede Kullanım: Yükseklik 42px, max-genişlik 180px</p>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['logo_on_light'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['logo_on_light']); ?>" class="h-8 mx-auto mb-4 object-contain">
                        <?php endif; ?>
                        <input type="file" name="logo_on_light" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>
                
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Header Logosu - Koyu Zemin</label>
                    <p class="text-[9px] text-gray-600 mb-2">Kullanım: Siyah/koyu arka planlar<br>Önerilen: SVG veya 480x160 PNG<br>Sitede Kullanım: Yükseklik 42px, max-genişlik 180px</p>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['logo_on_dark'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['logo_on_dark']); ?>" class="h-8 mx-auto mb-4 object-contain bg-black p-2 rounded">
                        <?php endif; ?>
                        <input type="file" name="logo_on_dark" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Footer Logosu</label>
                    <p class="text-[9px] text-gray-600 mb-2">Kullanım: Site footer alanı (koyu zemin)<br>Önerilen: SVG veya 480x160 PNG<br>Sitede Kullanım: Yükseklik 44-56px</p>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['footer_logo'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['footer_logo']); ?>" class="h-8 mx-auto mb-4 object-contain bg-black p-2 rounded">
                        <?php endif; ?>
                        <input type="file" name="footer_logo" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>
                
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Google / Tarayıcı Favicon</label>
                    <p class="text-[9px] text-gray-600 mb-2">Kullanım: Google arama sonucu, tarayıcı sekmesi.<br><strong>Sadece F marka sembolü kullanılmalıdır! (Yazılı logo yüklemeyin)</strong><br>Önerilen Master: 512x512 PNG, kare oran.</p>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['favicon_master'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['favicon_master']); ?>" class="w-8 h-8 mx-auto mb-4 object-contain">
                        <?php endif; ?>
                        <input type="file" name="favicon_master" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Varsayılan OG Image</label>
                    <p class="text-[9px] text-gray-600 mb-2">Kullanım: WhatsApp, LinkedIn, Facebook link önizlemesi (Varsayılan)<br>Önerilen: 1200x630 JPG veya PNG</p>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['og_image_default'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['og_image_default']); ?>" class="h-12 mx-auto mb-4 object-contain">
                        <?php endif; ?>
                        <input type="file" name="og_image_default" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Admin Panel Logosu</label>
                    <p class="text-[9px] text-gray-600 mb-2">Kullanım: Admin panel üst alanı / giriş ekranı<br>Önerilen: SVG veya 360x120 PNG<br>Sitede Kullanım: Yükseklik 42-52px</p>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['admin_logo'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['admin_logo']); ?>" class="h-8 mx-auto mb-4 object-contain">
                        <?php endif; ?>
                        <input type="file" name="admin_logo" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Admin Sidebar İkonu</label>
                    <p class="text-[9px] text-gray-600 mb-2">Kullanım: Admin sol menü küçük ikon<br>Önerilen: Sadece F sembolü, 128x128 PNG/SVG</p>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['admin_sidebar_icon'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['admin_sidebar_icon']); ?>" class="w-8 h-8 mx-auto mb-4 object-contain">
                        <?php endif; ?>
                        <input type="file" name="admin_sidebar_icon" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>
            </div>

        </div>

        <div class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> Genel Bilgiler & İletişim
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Site Başlığı</label>
                    <input type="text" name="settings[site_baslik]" value="<?php echo htmlspecialchars($settings['site_baslik']); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Slogan</label>
                    <input type="text" name="settings[site_slogan]" value="<?php echo htmlspecialchars($settings['site_slogan']); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Canlı Site URL</label>
                    <input type="url" name="settings[site_url]" value="<?php echo htmlspecialchars($settings['site_url'] ?? ''); ?>" placeholder="https://www.fikircreative.com" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Header Uygunluk Yazısı</label>
                    <input type="text" name="settings[availability_text]" value="<?php echo htmlspecialchars($settings['availability_text'] ?? ''); ?>" placeholder="Yeni projeler için uygun" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Header CTA Metni</label>
                    <input type="text" name="settings[header_cta_text]" value="<?php echo htmlspecialchars($settings['header_cta_text'] ?? ''); ?>" placeholder="İletişim" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Header CTA Linki</label>
                    <input type="text" name="settings[header_cta_url]" value="<?php echo htmlspecialchars($settings['header_cta_url'] ?? ''); ?>" placeholder="/#contact" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
                
                <div class="space-y-2" id="email-container">
                    <div class="flex items-center justify-between pl-2 mb-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">E-Posta Adresleri</label>
                        <button type="button" onclick="addInput('email-container', 'email', 'ornek@sirket.com')" class="text-[#F15A24] text-[10px] font-bold tracking-widest hover:text-white transition-all">+ YENİ EKLE</button>
                    </div>
                    <?php 
                    $emailler = isset($settings['email']) && !empty($settings['email']) ? explode(',', $settings['email']) : [''];
                    foreach($emailler as $index => $em): 
                    ?>
                    <div class="flex gap-2">
                        <input type="email" name="settings[email][]" value="<?php echo htmlspecialchars(trim($em)); ?>" placeholder="ornek@sirket.com" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                        <?php if($index > 0): ?>
                        <button type="button" onclick="this.parentElement.remove()" class="bg-red-500/10 text-red-500 px-5 rounded-2xl hover:bg-red-500 hover:text-white transition-all">X</button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="space-y-2" id="telefon-container">
                    <div class="flex items-center justify-between pl-2 mb-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Telefon Numaraları</label>
                        <button type="button" onclick="addInput('telefon-container', 'telefon', '+90 5XX XXX XX XX')" class="text-[#F15A24] text-[10px] font-bold tracking-widest hover:text-white transition-all">+ YENİ EKLE</button>
                    </div>
                    <?php 
                    $telefonlar = isset($settings['telefon']) && !empty($settings['telefon']) ? explode(',', $settings['telefon']) : [''];
                    foreach($telefonlar as $index => $tel): 
                    ?>
                    <div class="flex gap-2">
                        <input type="text" name="settings[telefon][]" value="<?php echo htmlspecialchars(trim($tel)); ?>" placeholder="+90 5XX XXX XX XX" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                        <?php if($index > 0): ?>
                        <button type="button" onclick="this.parentElement.remove()" class="bg-red-500/10 text-red-500 px-5 rounded-2xl hover:bg-red-500 hover:text-white transition-all">X</button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Adres Bilgisi</label>
                    <textarea name="settings[adres]" rows="2" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all"><?php echo htmlspecialchars($settings['adres']); ?></textarea>
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Footer Açıklaması</label>
                    <textarea name="settings[footer_description]" rows="2" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all"><?php echo htmlspecialchars($settings['footer_description'] ?? ''); ?></textarea>
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Footer Copyright Yazısı</label>
                    <input type="text" name="settings[footer_text]" value="<?php echo htmlspecialchars($settings['footer_text'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
            </div>
        </div>

        <!-- ─── KAHRAMAN (HERO) AYARLARI KARTI ─────────────────────────── -->
        <div class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> Kahraman (Hero) Alanı
            </h3>
            
            <div class="space-y-6">
                <!-- Showcase Görseli -->
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Showcase Görseli (Mozaik Banner)</label>
                    <p class="text-[9px] text-gray-600 mb-2">Giriş ekranında en altta yer alan geniş mozaik görsel.<br>Önerilen: 1800x1012 PNG veya JPG (16:9 oran)</p>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['hero_showcase_image'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['hero_showcase_image']); ?>" class="h-32 mx-auto mb-4 object-contain rounded-lg">
                        <?php endif; ?>
                        <input type="file" name="hero_showcase_image" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>
 
                <!-- Showcase Linki -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Showcase Tıklama Linki (İncele)</label>
                    <input type="text" name="settings[hero_showcase_url]" value="<?php echo htmlspecialchars($settings['hero_showcase_url'] ?? '/portfolio'); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
 
                <!-- Satır 1 Başlık Parçaları -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Satır 1 Sol Kelime</label>
                        <input type="text" name="settings[hero_title_line1_left]" value="<?php echo htmlspecialchars($settings['hero_title_line1_left'] ?? 'Sadece'); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Satır 1 Sağ Kelimeler</label>
                        <input type="text" name="settings[hero_title_line1_right]" value="<?php echo htmlspecialchars($settings['hero_title_line1_right'] ?? 'görünürlük değil,'); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                    </div>
                </div>
 
                <!-- Satır 1 Inline İkon -->
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Satır 1 Inline İkon (Sadece-görünürlük arası)</label>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['hero_inline_image_1'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['hero_inline_image_1']); ?>" class="h-10 mx-auto mb-4 object-contain bg-[#eee] p-1 rounded-full">
                        <?php endif; ?>
                        <input type="file" name="hero_inline_image_1" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>
 
                <!-- Satır 2 Başlık Parçaları -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Satır 2 Sol Kelimeler</label>
                        <input type="text" name="settings[hero_title_line2_left]" value="<?php echo htmlspecialchars($settings['hero_title_line2_left'] ?? 'müşteri'); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Satır 2 Sağ Kelimeler</label>
                        <input type="text" name="settings[hero_title_line2_right]" value="<?php echo htmlspecialchars($settings['hero_title_line2_right'] ?? 'kazandıran sistem'); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                    </div>
                </div>
 
                <!-- Satır 2 Inline İkon -->
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Satır 2 Inline İkon (müşteri-kazandıran arası)</label>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['hero_inline_image_2'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['hero_inline_image_2']); ?>" class="h-10 mx-auto mb-4 object-contain bg-[#eee] p-1 rounded-full">
                        <?php endif; ?>
                        <input type="file" name="hero_inline_image_2" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>
 
                <!-- Satır 3 Başlık Parçaları -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 col-span-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Satır 3 Sol Kelime</label>
                        <input type="text" name="settings[hero_title_line3_left]" value="<?php echo htmlspecialchars($settings['hero_title_line3_left'] ?? 'kuran'); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                    </div>
                </div>
 
                <!-- Satır 3 Inline İkon -->
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Satır 3 Inline İkon (kuran-Fikir arası)</label>
                    <div class="bg-[#080808] border border-[#222] p-6 rounded-2xl text-center">
                        <?php if (!empty($settings['hero_inline_image_3'])): ?>
                            <img src="<?php echo getAdminAssetPath($settings['hero_inline_image_3']); ?>" class="h-10 mx-auto mb-4 object-contain bg-[#eee] p-1 rounded-full">
                        <?php endif; ?>
                        <input type="file" name="hero_inline_image_3" accept="image/*" class="text-[10px] text-gray-600">
                    </div>
                </div>
            </div>

        </div>
 
        <!-- ─── KURUCU DENEYİMLERİ (TIMELINE) KARTI ─────────────────────────── -->
        <div class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> Kurucu Deneyim Kronolojisi (Timeline)
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Deneyim 1 -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">1. Pozisyon (Görevi / Firma)</label>
                    <input type="text" name="settings[experience_1_role]" value="<?php echo htmlspecialchars($settings['experience_1_role'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">1. Dönem (Yıl)</label>
                    <input type="text" name="settings[experience_1_period]" value="<?php echo htmlspecialchars($settings['experience_1_period'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
 
                <!-- Deneyim 2 -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">2. Pozisyon (Görevi / Firma)</label>
                    <input type="text" name="settings[experience_2_role]" value="<?php echo htmlspecialchars($settings['experience_2_role'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">2. Dönem (Yıl)</label>
                    <input type="text" name="settings[experience_2_period]" value="<?php echo htmlspecialchars($settings['experience_2_period'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
 
                <!-- Deneyim 3 -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">3. Pozisyon (Görevi / Firma)</label>
                    <input type="text" name="settings[experience_3_role]" value="<?php echo htmlspecialchars($settings['experience_3_role'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">3. Dönem (Yıl)</label>
                    <input type="text" name="settings[experience_3_period]" value="<?php echo htmlspecialchars($settings['experience_3_period'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
 
                <!-- Deneyim 4 -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">4. Pozisyon (Görevi / Firma)</label>
                    <input type="text" name="settings[experience_4_role]" value="<?php echo htmlspecialchars($settings['experience_4_role'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">4. Dönem (Yıl)</label>
                    <input type="text" name="settings[experience_4_period]" value="<?php echo htmlspecialchars($settings['experience_4_period'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
            </div>
        </div>

        <div class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> Sosyal Medya
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="relative">
                    <input type="text" name="settings[instagram]" value="<?php echo htmlspecialchars($settings['instagram'] ?? ''); ?>" placeholder="Instagram" class="w-full bg-[#080808] border border-[#222] text-white p-4 pl-12 rounded-xl outline-none focus:border-pink-500 transition-all">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-pink-500 font-bold text-xs">IG</span>
                </div>
                <div class="relative">
                    <input type="text" name="settings[linkedin]" value="<?php echo htmlspecialchars($settings['linkedin'] ?? ''); ?>" placeholder="LinkedIn" class="w-full bg-[#080808] border border-[#222] text-white p-4 pl-12 rounded-xl outline-none focus:border-blue-600 transition-all">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-600 font-bold text-xs">LI</span>
                </div>
                <div class="relative">
                    <input type="text" name="settings[twitter]" value="<?php echo htmlspecialchars($settings['twitter'] ?? ''); ?>" placeholder="X (Twitter)" class="w-full bg-[#080808] border border-[#222] text-white p-4 pl-12 rounded-xl outline-none focus:border-gray-400 transition-all">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">X</span>
                </div>
                <div class="relative">
                    <input type="text" name="settings[facebook]" value="<?php echo htmlspecialchars($settings['facebook'] ?? ''); ?>" placeholder="Facebook" class="w-full bg-[#080808] border border-[#222] text-white p-4 pl-12 rounded-xl outline-none focus:border-blue-800 transition-all">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-800 font-bold text-xs">FB</span>
                </div>
                <div class="relative">
                    <input type="text" name="settings[youtube]" value="<?php echo htmlspecialchars($settings['youtube'] ?? ''); ?>" placeholder="YouTube" class="w-full bg-[#080808] border border-[#222] text-white p-4 pl-12 rounded-xl outline-none focus:border-red-600 transition-all">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-red-600 font-bold text-xs">YT</span>
                </div>
            </div>
        </div>

        <div class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> SEO & Analitik
            </h3>
            
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">SEO Açıklaması (Meta Description)</label>
                    <textarea name="settings[seo_desc]" rows="2" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all"><?php echo htmlspecialchars($settings['seo_desc']); ?></textarea>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Anahtar Kelimeler (Keywords)</label>
                    <input type="text" name="settings[seo_keys]" value="<?php echo htmlspecialchars($settings['seo_keys']); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">SEO Başlığı</label>
                    <input type="text" name="settings[seo_title]" value="<?php echo htmlspecialchars($settings['seo_title'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-500 uppercase tracking-widest pl-2">Google Site Verification</label>
                    <input type="text" name="settings[google_site_verification]" value="<?php echo htmlspecialchars($settings['google_site_verification'] ?? ''); ?>" placeholder="Search Console doğrulama kodu" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-blue-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-500 uppercase tracking-widest pl-2">Google Analytics (GA4) Ölçüm Kimliği</label>
                    <input type="text" name="settings[ga4_id]" value="<?php echo htmlspecialchars($settings['ga4_id'] ?? ''); ?>" placeholder="G-XXXXXXXXXX" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-blue-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-500 uppercase tracking-widest pl-2">Google Tag Manager ID</label>
                    <input type="text" name="settings[gtm_id]" value="<?php echo htmlspecialchars($settings['gtm_id'] ?? ''); ?>" placeholder="GTM-XXXXXXX" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-blue-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-500 uppercase tracking-widest pl-2">Meta Pixel ID</label>
                    <input type="text" name="settings[meta_pixel_id]" value="<?php echo htmlspecialchars($settings['meta_pixel_id'] ?? ''); ?>" placeholder="123456789012345" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-blue-500 transition-all">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Geo Region</label>
                        <input type="text" name="settings[geo_region]" value="<?php echo htmlspecialchars($settings['geo_region'] ?? ''); ?>" placeholder="TR-34" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Geo Placename</label>
                        <input type="text" name="settings[geo_placename]" value="<?php echo htmlspecialchars($settings['geo_placename'] ?? ''); ?>" placeholder="İstanbul" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Geo Position</label>
                        <input type="text" name="settings[geo_position]" value="<?php echo htmlspecialchars($settings['geo_position'] ?? ''); ?>" placeholder="41.0082;28.9784" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">ICBM</label>
                        <input type="text" name="settings[icbm]" value="<?php echo htmlspecialchars($settings['icbm'] ?? ''); ?>" placeholder="41.0082, 28.9784" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-500 uppercase tracking-widest pl-2">Özel Script Kodları (Analytics / Pixel Eski Yapı)</label>
                    <textarea name="settings[analytics]" rows="4" placeholder="<script>...</script>" class="w-full bg-[#080808] border border-[#222] text-blue-400 font-mono text-xs p-5 rounded-2xl outline-none focus:border-blue-500 transition-all"><?php echo htmlspecialchars($settings['analytics']); ?></textarea>
                </div>
            </div>
        </div>

        <!-- ─── TEMA RENGİ KARTI ─────────────────────────── -->
        <div class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> Tema Rengi
            </h3>
            <p class="text-gray-500 text-xs mb-8 leading-relaxed">
                Sitenin ana kurumsal rengini buradan değiştirin. Butonlar, ikonlar, hover efektleri ve arka planlar otomatik güncellenir.
            </p>
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Görsel renk seçici -->
                <div class="relative group">
                    <input type="color" id="color-picker" value="<?php echo htmlspecialchars($settings['site_renk']); ?>"
                           class="w-20 h-20 rounded-2xl border-2 border-[#333] cursor-pointer bg-transparent p-1 transition-all group-hover:border-[#555]"
                           oninput="syncColorInputs(this.value)">
                    <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest mt-2 text-center">Seçici</p>
                </div>
                <!-- HEX metin girişi -->
                <div class="flex-1 space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">HEX Renk Kodu</label>
                    <div class="flex gap-3 items-center">
                        <span class="text-gray-600 font-mono font-bold text-lg">#</span>
                        <input type="text" id="color-hex" name="settings[site_renk]"
                               value="<?php echo ltrim(htmlspecialchars($settings['site_renk']), '#'); ?>"
                               maxlength="6" pattern="[0-9A-Fa-f]{6}"
                               placeholder="F15A24"
                               class="flex-1 bg-[#080808] border border-[#222] text-white font-mono p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all uppercase tracking-widest text-lg"
                               oninput="syncColorPicker(this.value)">
                        <!-- Önizleme kutusu -->
                        <div id="color-preview" class="w-16 h-16 rounded-2xl border border-[#333] flex-shrink-0 transition-all"
                             style="background-color: <?php echo htmlspecialchars($settings['site_renk']); ?>"></div>
                    </div>
                    <p class="text-[10px] text-gray-600 pl-2">Örnek: <span class="text-white font-mono">F15A24</span> (# işareti olmadan girin)</p>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-[#F15A24] hover:bg-white hover:text-[#F15A24] text-white font-black py-6 rounded-3xl transition-all duration-500 uppercase tracking-[0.4em] text-sm shadow-xl shadow-orange-600/20">
            SİSTEMİ GÜNCELLE VE KAYDET
        </button>
    </form>
</div>

<script>
function addInput(containerId, inputName, placeholder) {
    const container = document.getElementById(containerId);
    const div = document.createElement('div');
    div.className = 'flex gap-2 mt-2';
    div.innerHTML = `
        <input type="${inputName === 'email' ? 'email' : 'text'}" name="settings[${inputName}][]" placeholder="${placeholder}" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
        <button type="button" onclick="this.parentElement.remove()" class="bg-red-500/10 text-red-500 px-5 rounded-2xl hover:bg-red-500 hover:text-white transition-all">X</button>
    `;
    container.appendChild(div);
}

// ─── Tema Rengi Senkronizasyon Fonksiyonları ───────────────
function syncColorInputs(hexWithHash) {
    var hex = hexWithHash.replace('#', '');
    var hexField = document.getElementById('color-hex');
    var preview  = document.getElementById('color-preview');
    if (hexField)  hexField.value = hex.toUpperCase();
    if (preview)   preview.style.backgroundColor = hexWithHash;
}

function syncColorPicker(hexOnly) {
    var picker  = document.getElementById('color-picker');
    var preview = document.getElementById('color-preview');
    var cleaned = hexOnly.replace('#', '');
    if (/^[0-9A-Fa-f]{6}$/.test(cleaned)) {
        var full = '#' + cleaned;
        if (picker)  picker.value = full;
        if (preview) preview.style.backgroundColor = full;
    }
}
</script>

<?php require_once 'footer.php'; ?>

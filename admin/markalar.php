<?php
/**
 * admin/markalar.php - Referans İşletmeler ve Logo Yönetimi
 */
require_once '../includes/db.php';
$silent_migration = true;
require_once 'db_migration_custom.php';
require_once 'includes/auth.php'; // Ensure user is authenticated

$message = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// 1. SİLME İŞLEMİ
if ($action === 'delete' && $id) {
    // Fotoğrafı temizle
    $stmt = $db->prepare("SELECT logo FROM markalar WHERE id = ?");
    $stmt->execute([$id]);
    $eski = $stmt->fetch();
    if ($eski && !empty($eski['logo']) && file_exists(ROOT_DIR . '/' . $eski['logo'])) {
        unlink(ROOT_DIR . '/' . $eski['logo']);
    }

    $stmt = $db->prepare("DELETE FROM markalar WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: markalar.php?msg=deleted');
    exit;
}

// 2. EKLEME VEYA GÜNCELLEME İŞLEMİ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isim = $_POST['isim'];
    $logo_svg = $_POST['logo_svg'] ?? '';
    $sira = (int)$_POST['sira'];
    $logo = $_POST['eski_logo'] ?? '';

    if (isset($_FILES['logo_dosya']) && $_FILES['logo_dosya']['error'] === 0) {
        $ext = pathinfo($_FILES['logo_dosya']['name'], PATHINFO_EXTENSION);
        $filename = 'brand_logo_' . time() . '_' . uniqid() . '.' . $ext;
        $upload_path = ROOT_DIR . '/images/' . $filename;

        if (!is_dir(ROOT_DIR . '/images')) {
            mkdir(ROOT_DIR . '/images', 0777, true);
        }

        if (move_uploaded_file($_FILES['logo_dosya']['tmp_name'], $upload_path)) {
            if (!empty($logo) && file_exists(ROOT_DIR . '/' . $logo)) {
                unlink(ROOT_DIR . '/' . $logo);
            }
            $logo = 'images/' . $filename;
        }
    }

    if ($id) {
        $stmt = $db->prepare("UPDATE markalar SET isim=?, logo=?, logo_svg=?, sira=? WHERE id=?");
        $stmt->execute([$isim, $logo, $logo_svg, $sira, $id]);
        $message = 'Referans marka başarıyla güncellendi.';
    } else {
        $stmt = $db->prepare("INSERT INTO markalar (isim, logo, logo_svg, sira) VALUES (?, ?, ?, ?)");
        $stmt->execute([$isim, $logo, $logo_svg, $sira]);
        $message = 'Yeni referans marka başarıyla eklendi.';
    }
    $action = 'list';
}

require_once 'header.php';

$edit_item = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare("SELECT * FROM markalar WHERE id = ?");
    $stmt->execute([$id]);
    $edit_item = $stmt->fetch();
}

$markalar = $db->query("SELECT * FROM markalar ORDER BY sira ASC, id DESC")->fetchAll();
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Referans İşletmeler</h2>
            <p class="text-gray-500 text-sm mt-1">Ana sayfada kayan şerit halinde yer alan referans marka logolarını yönetin.</p>
        </div>
        <?php if ($action === 'list'): ?>
            <a href="markalar.php?action=add" class="bg-[#F15A24] hover:bg-[#d84a1a] text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                YENİ MARKA EKLE
            </a>
        <?php else: ?>
            <a href="markalar.php" class="bg-[#1a1a1a] border border-[#222] hover:bg-[#222] text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                İPTAL VE GERİ DÖN
            </a>
        <?php endif; ?>
    </div>

    <?php if ($message || isset($_GET['msg'])): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-2xl text-sm font-medium">
            <?php echo $message ?: ($_GET['msg'] === 'deleted' ? 'Referans marka başarıyla silindi.' : 'İşlem başarıyla tamamlandı.'); ?>
        </div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($markalar)): ?>
                <div class="col-span-full bg-[#111] border border-[#222] rounded-3xl p-10 text-center text-gray-500 italic">
                    Henüz bir referans marka eklenmemiş.
                </div>
            <?php endif; ?>
            
            <?php foreach ($markalar as $m): ?>
            <div class="bg-[#111] border border-[#222] rounded-3xl flex flex-col group relative overflow-hidden transition-all hover:border-[#333]">
                <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                    <a href="markalar.php?action=edit&id=<?php echo $m['id']; ?>" class="p-2 bg-[#1a1a1a] border border-[#333] hover:border-blue-500 text-white rounded-xl transition-all shadow-lg" title="Düzenle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <a href="markalar.php?action=delete&id=<?php echo $m['id']; ?>" onclick="return confirm('Bu markayı silmek istediğinize emin misiniz?')" class="p-2 bg-[#1a1a1a] border border-[#333] hover:border-red-500 text-white rounded-xl transition-all shadow-lg" title="Sil">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </a>
                </div>

                <div class="h-32 w-full bg-[#0d0d0d] border-b border-[#222] flex items-center justify-center p-6 relative overflow-hidden">
                    <?php if (!empty($m['logo'])): ?>
                        <img src="<?php echo getAdminAssetPath($m['logo']); ?>" alt="<?php echo htmlspecialchars($m['isim']); ?>" class="max-h-12 max-w-[80%] object-contain opacity-60 group-hover:opacity-100 transition-opacity">
                    <?php elseif (!empty($m['logo_svg'])): ?>
                        <div class="w-full text-neutral-500 group-hover:text-white transition-colors flex items-center justify-center [&>svg]:max-h-12 [&>svg]:w-auto">
                            <?php echo $m['logo_svg']; ?>
                        </div>
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-700 text-xs">Görsel / SVG Yok</div>
                    <?php endif; ?>
                </div>

                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-white font-bold text-base"><?php echo htmlspecialchars($m['isim']); ?></h3>
                        <p class="text-xs text-gray-500 mt-1">
                            <?php echo !empty($m['logo']) ? 'Görsel Logo Dosyası' : 'Vektörel SVG Kod'; ?>
                        </p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="inline-block text-[9px] uppercase tracking-widest text-[#F15A24] font-bold bg-[#F15A24]/10 px-2.5 py-1 rounded-full">Sıra: <?php echo $m['sira']; ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="max-w-4xl mx-auto">
            <form method="POST" enctype="multipart/form-data" class="bg-[#111] border border-[#222] p-10 rounded-3xl space-y-8 shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 pl-1">Marka / İşletme İsmi</label>
                            <input type="text" name="isim" value="<?php echo htmlspecialchars($edit_item['isim'] ?? ''); ?>" required
                                   class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl focus:border-[#F15A24] outline-none transition-all">
                        </div>
                        
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 pl-1">Sıralama (0-99)</label>
                            <input type="number" name="sira" value="<?php echo htmlspecialchars($edit_item['sira'] ?? '0'); ?>"
                                   class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl focus:border-[#F15A24] outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-6" x-data="{ logoType: '<?php echo (!empty($edit_item['logo_svg']) && empty($edit_item['logo'])) ? 'svg' : 'file'; ?>' }">
                        <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-1 pl-1">Logo Formatı Seçin</label>
                        <div class="flex gap-4 mb-4">
                            <label class="flex-1 flex items-center justify-center gap-3 p-4 rounded-xl border transition-all cursor-pointer text-sm" :class="logoType == 'file' ? 'bg-blue-500/10 border-blue-500 text-blue-500' : 'bg-[#080808] border-[#222] text-gray-500'">
                                <input type="radio" name="logo_type" value="file" x-model="logoType" class="hidden">
                                Görsel Yükle (PNG/WebP/SVG)
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-3 p-4 rounded-xl border transition-all cursor-pointer text-sm" :class="logoType == 'svg' ? 'bg-orange-500/10 border-orange-500 text-orange-500' : 'bg-[#080808] border-[#222] text-gray-500'">
                                <input type="radio" name="logo_type" value="svg" x-model="logoType" class="hidden">
                                Inline SVG Kod Gir
                            </label>
                        </div>

                        <!-- 1. GÖRSEL YÜKLEME -->
                        <div x-show="logoType == 'file'" class="space-y-4">
                            <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 pl-1">Logo Dosyası</label>
                            <div class="bg-[#080808] border-2 border-dashed border-[#222] rounded-2xl p-6 text-center relative hover:border-[#F15A24] transition-colors group h-44 flex flex-col items-center justify-center">
                                <input type="file" name="logo_dosya" accept=".webp, .jpg, .jpeg, .png, .svg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="flex flex-col items-center justify-center gap-2 w-full h-full">
                                    <?php if (isset($edit_item['logo']) && !empty($edit_item['logo'])): ?>
                                        <img src="<?php echo getAdminAssetPath($edit_item['logo']); ?>" class="max-h-16 object-contain rounded shadow-md mb-1">
                                        <span class="text-xs text-green-500 font-bold">Mevcut Görsel</span>
                                    <?php else: ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-600 group-hover:text-[#F15A24] transition-colors"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        <span class="text-sm font-bold text-gray-400 group-hover:text-white transition-colors">Dosya Seçin</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <input type="hidden" name="eski_logo" value="<?php echo htmlspecialchars($edit_item['logo'] ?? ''); ?>">
                        </div>

                        <!-- 2. VEKTÖREL SVG KODU -->
                        <div x-show="logoType == 'svg'" class="space-y-4">
                            <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold pl-1">Vektörel SVG Kodları</label>
                            <p class="text-[10px] text-gray-500 leading-normal mb-2">Web sitesinde renklerin otomatik uyum sağlaması için SVG'nin içindeki <code>fill</code> değerlerini <code>currentColor</code> yapmanız önerilir.</p>
                            <textarea name="logo_svg" rows="6" placeholder="<svg ...> ... </svg>"
                                      class="w-full bg-[#080808] border border-[#222] text-gray-300 font-mono text-xs p-4 rounded-xl focus:border-[#F15A24] outline-none transition-all"><?php echo htmlspecialchars($edit_item['logo_svg'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-[#222]">
                    <button type="submit" class="w-full bg-[#F15A24] hover:bg-[#d84a1a] text-white font-bold py-5 rounded-2xl transition-all shadow-lg shadow-orange-600/20">
                        <?php echo $id ? 'DEĞİŞİKLİKLERİ KAYDET' : 'MARKAYI EKLE VE YAYINLA'; ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>

<?php
/**
 * services.php - Service Management (Görsel Yükleme Destekli - Yeni Zebra Tasarıma Uygun)
 */

require_once '../includes/db.php';

$message = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($action === 'delete' && $id) {
    $stmt = $db->prepare("DELETE FROM hizmetler WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: services.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baslik = $_POST['baslik'];
    $aciklama = $_POST['aciklama'];
    $sira = (int)$_POST['sira'];
    $ikon_url = $_POST['eski_ikon']; 

    if (isset($_FILES['ikon_dosya']) && $_FILES['ikon_dosya']['error'] === 0) {
        $ext = pathinfo($_FILES['ikon_dosya']['name'], PATHINFO_EXTENSION);
        $filename = 'icon_' . time() . '_' . uniqid() . '.' . $ext;
        $upload_path = ROOT_DIR . '/images/' . $filename; 

        if (!is_dir(ROOT_DIR . '/images')) { mkdir(ROOT_DIR . '/images', 0777, true); }

        if (move_uploaded_file($_FILES['ikon_dosya']['tmp_name'], $upload_path)) {
            $ikon_url = 'images/' . $filename; 
        }
    }


    if ($id) {
        $stmt = $db->prepare("UPDATE hizmetler SET baslik=?, aciklama=?, ikon_svg=?, sira=? WHERE id=?");
        $stmt->execute([$baslik, $aciklama, $ikon_url, $sira, $id]);
        $message = 'Hizmet başarıyla güncellendi.';
    }
    else {
        $stmt = $db->prepare("INSERT INTO hizmetler (baslik, aciklama, ikon_svg, sira) VALUES (?, ?, ?, ?)");
        $stmt->execute([$baslik, $aciklama, $ikon_url, $sira]);
        $message = 'Yeni hizmet eklendi.';
    }
    $action = 'list';
}

require_once 'header.php';

$edit_item = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare("SELECT * FROM hizmetler WHERE id = ?");
    $stmt->execute([$id]);
    $edit_item = $stmt->fetch();
}

$services = $db->query("SELECT * FROM hizmetler ORDER BY sira ASC")->fetchAll();
?>

<div class="space-y-8">
    
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Performans Odaklı Hizmetlerimiz</h2>
            <p class="text-gray-500 text-sm mt-1">Hizmetleri ekleyin, kapak görsellerini güncelleyin ve sıralayın.</p>
        </div>
        <?php if ($action === 'list'): ?>
            <a href="services.php?action=add" class="bg-[#F15A24] hover:bg-[#d84a1a] text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                YENİ HİZMET EKLE
            </a>
        <?php else: ?>
            <a href="services.php" class="bg-[#1a1a1a] border border-[#222] hover:bg-[#222] text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                İPTAL VE GERİ DÖN
            </a>
        <?php endif; ?>
    </div>

    <?php if ($message || isset($_GET['msg'])): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-2xl text-sm font-medium">
            <?php echo $message ?: ($_GET['msg'] === 'deleted' ? 'Hizmet başarıyla silindi.' : ''); ?>
        </div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($services)): ?>
                <div class="col-span-full bg-[#111] border border-[#222] rounded-3xl p-10 text-center text-gray-500 italic">
                    Henüz bir hizmet eklenmemiş.
                </div>
            <?php endif; ?>
            
            <?php foreach ($services as $s): ?>
            <div class="bg-[#111] border border-[#222] rounded-3xl flex flex-col group relative overflow-hidden transition-all hover:border-[#333]">
                <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                    <a href="services.php?action=edit&id=<?php echo $s['id']; ?>" class="p-2 bg-[#1a1a1a] border border-[#333] hover:border-blue-500 text-white rounded-xl transition-all shadow-lg" title="Düzenle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <a href="services.php?action=delete&id=<?php echo $s['id']; ?>" onclick="return confirm('Bu hizmeti silmek istediğinize emin misiniz?')" class="p-2 bg-[#1a1a1a] border border-[#333] hover:border-red-500 text-white rounded-xl transition-all shadow-lg" title="Sil">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </a>
                </div>

                <div class="h-40 w-full bg-[#1a1a1a] border-b border-[#222] relative overflow-hidden">
                    <?php if (!empty($s['ikon_svg']) && strpos($s['ikon_svg'], '<svg') === false): ?>
                        <img src="<?php echo getAdminAssetPath($s['ikon_svg']); ?>" alt="Görsel" class="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition-opacity">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-700 text-xs">Görsel Yok</div>
                    <?php endif; ?>
                </div>

                <div class="p-8">
                    <h3 class="text-white font-bold text-xl mb-3"><?php echo htmlspecialchars($s['baslik']); ?></h3>
                    <p class="text-sm text-gray-500 leading-relaxed line-clamp-3"><?php echo htmlspecialchars($s['aciklama']); ?></p>
                    <span class="inline-block mt-5 text-[10px] uppercase tracking-widest text-[#F15A24] font-bold bg-[#F15A24]/10 px-3 py-1 rounded-full">Sıra: <?php echo $s['sira']; ?></span>
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
                            <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 pl-1">Hizmet Başlığı</label>
                            <input type="text" name="baslik" value="<?php echo htmlspecialchars($edit_item['baslik'] ?? ''); ?>" required
                                   class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl focus:border-[#F15A24] outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 pl-1">Açıklama</label>
                            <textarea name="aciklama" rows="6" required
                                      class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl focus:border-[#F15A24] outline-none transition-all"><?php echo htmlspecialchars($edit_item['aciklama'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 pl-1">Sıralama (0-99)</label>
                            <input type="number" name="sira" value="<?php echo htmlspecialchars($edit_item['sira'] ?? '0'); ?>"
                                   class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl focus:border-[#F15A24] outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 pl-1">Hizmet Kapak Görseli (Zebra/Grid Tasarım)</label>
                        <div class="bg-[#080808] border-2 border-dashed border-[#222] rounded-2xl p-8 text-center relative hover:border-[#F15A24] transition-colors group h-64 flex flex-col items-center justify-center">
                            
                            <input type="file" name="ikon_dosya" accept=".webp, .jpg, .jpeg, .png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="flex flex-col items-center justify-center gap-3 w-full h-full">
                                <?php if (isset($edit_item['ikon_svg']) && !empty($edit_item['ikon_svg']) && strpos($edit_item['ikon_svg'], '<svg') === false): ?>
                                    <img src="<?php echo getAdminAssetPath($edit_item['ikon_svg']); ?>" class="w-full h-32 object-cover rounded-lg shadow-md mb-2">
                                    <span class="text-xs text-green-500 font-bold">Mevcut Görsel Yüklü</span>
                                    <span class="text-[10px] text-gray-500">Değiştirmek için tıklayın veya sürükleyin</span>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-600 group-hover:text-[#F15A24] transition-colors"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <span class="text-sm font-bold text-gray-400 group-hover:text-white transition-colors">Görsel Yükle</span>
                                    <span class="text-[10px] text-gray-600 max-w-[200px]">Hızlı yükleme için yüksek kaliteli <strong class="text-[#F15A24]">WebP</strong> formatı önerilir (Max 2 MB, 16:10)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <input type="hidden" name="eski_ikon" value="<?php echo htmlspecialchars($edit_item['ikon_svg'] ?? ''); ?>">
                    </div>
                </div>


                <div class="pt-6 border-t border-[#222]">
                    <button type="submit" class="w-full bg-[#F15A24] hover:bg-[#d84a1a] text-white font-bold py-5 rounded-2xl transition-all shadow-lg shadow-orange-600/20">
                        <?php echo $id ? 'DEĞİŞİKLİKLERİ KAYDET' : 'YENİ HİZMETİ YAYINLA'; ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
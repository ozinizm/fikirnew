<?php
/**
 * DOSYA YOLU: admin/portfolio.php (Akıllı Video & Kapak Destekli)
 */
require_once '../includes/db.php'; 

$message = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($action === 'delete' && $id) {
    $stmt = $db->prepare("DELETE FROM portfolyo WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: portfolio.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baslik = $_POST['baslik'];
    $kategori = $_POST['kategori'];
    $medya_turu = $_POST['medya_turu'];
    $medya_url = $_POST['medya_url'] ?? '';
    $sira = (int)$_POST['sira'];
    $gorsel_url = $_POST['eski_gorsel_url'] ?? ''; // Yeni eklendi: Mevcut kapak görseli

    // Video URL dönüştürme işlemi (BunnyCDN)
    if ($medya_turu === 'video' && !empty($medya_url)) {
        if (preg_match('/[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}/i', $medya_url, $matches)) {
            $video_id = $matches[0];
            $medya_url = "https://vz-14e8822f-9b5.b-cdn.net/{$video_id}/play_720p.mp4";
        }
    }

    // NORMAL RESİM YÜKLEME (Medya türü resim ise)
    if (isset($_FILES['resim_dosya']) && $_FILES['resim_dosya']['error'] === 0) {
        $ext = pathinfo($_FILES['resim_dosya']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $upload_path = ROOT_DIR . '/images/' . $filename; 
        if (!is_dir(ROOT_DIR . '/images')) { mkdir(ROOT_DIR . '/images', 0777, true); }
        if (move_uploaded_file($_FILES['resim_dosya']['tmp_name'], $upload_path)) {
            $medya_url = 'images/' . $filename; 
        }
    }

    // YENİ: VİDEO İÇİN KAPAK GÖRSELİ YÜKLEME
    if (isset($_FILES['video_kapak']) && $_FILES['video_kapak']['error'] === 0) {
        $ext = pathinfo($_FILES['video_kapak']['name'], PATHINFO_EXTENSION);
        $filename = 'poster_' . time() . '_' . uniqid() . '.' . $ext;
        $upload_path = ROOT_DIR . '/images/' . $filename; 
        if (!is_dir(ROOT_DIR . '/images')) { mkdir(ROOT_DIR . '/images', 0777, true); }
        if (move_uploaded_file($_FILES['video_kapak']['tmp_name'], $upload_path)) {
            $gorsel_url = 'images/' . $filename; 
        }
    }


    if ($id) {
        // YENİ: gorsel_url güncelleniyor
        $stmt = $db->prepare("UPDATE portfolyo SET baslik=?, kategori=?, medya_turu=?, medya_url=?, sira=?, gorsel_url=? WHERE id=?");
        $stmt->execute([$baslik, $kategori, $medya_turu, $medya_url, $sira, $gorsel_url, $id]);
        $message = 'Proje başarıyla güncellendi.';
    } else {
        // YENİ: gorsel_url ekleniyor
        $stmt = $db->prepare("INSERT INTO portfolyo (baslik, kategori, medya_turu, medya_url, sira, gorsel_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$baslik, $kategori, $medya_turu, $medya_url, $sira, $gorsel_url]);
        $message = 'Yeni proje başarıyla eklendi.';
    }
    $action = 'list';
}

require_once 'header.php'; 

$edit_item = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare("SELECT * FROM portfolyo WHERE id = ?");
    $stmt->execute([$id]);
    $edit_item = $stmt->fetch();
}

$items = $db->query("SELECT * FROM portfolyo ORDER BY sira ASC, id DESC")->fetchAll();
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Portfolyo İşleri</h2>
            <p class="text-gray-500 text-sm mt-1">Projeleri ekleyin, düzenleyin veya bento grid sırasını belirleyin.</p>
        </div>
        <?php if ($action === 'list'): ?>
            <a href="portfolio.php?action=add" class="bg-[#F15A24] hover:bg-[#d84a1a] text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                YENİ PROJE EKLE
            </a>
        <?php else: ?>
            <a href="portfolio.php" class="bg-[#1a1a1a] border border-[#222] hover:bg-[#222] text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                İPTAL VE GERİ DÖN
            </a>
        <?php endif; ?>
    </div>

    <?php if ($message || isset($_GET['msg'])): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-2xl text-sm font-medium">
            <?php echo $message ?: ($_GET['msg'] === 'deleted' ? 'Proje başarıyla silindi.' : ''); ?>
        </div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <div class="bg-[#111] border border-[#222] rounded-3xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-gray-500 border-b border-[#222]">
                        <th class="px-6 py-5 font-bold uppercase tracking-widest text-[10px] w-12 text-center">Taşı</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-widest text-[10px]">Görsel/Video</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-widest text-[10px]">Proje Başlığı</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-widest text-[10px]">Kategori</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-widest text-[10px]">Sıra</th>
                        <th class="px-8 py-5 font-bold uppercase tracking-widest text-[10px] text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody id="sortable" class="divide-y divide-[#222]">
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="px-8 py-20 text-center text-gray-500 italic">Henüz bir proje eklenmemiş.</td></tr>
                    <?php endif; ?>
                    
                    <?php foreach ($items as $item): ?>
                    <tr id="item_<?php echo $item['id']; ?>" class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-4 text-center">
                            <div class="handle cursor-grab active:cursor-grabbing text-gray-500 hover:text-orange-500 flex justify-center items-center p-2 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="12" r="1"/><circle cx="9" cy="5" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="19" r="1"/></svg>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="w-20 h-14 bg-[#1a1a1a] rounded-lg overflow-hidden border border-[#222] relative">
                                <?php if ($item['medya_turu'] == 'video'): ?>
                                    <video src="<?php echo htmlspecialchars($item['medya_url']); ?>" poster="<?php echo getAdminAssetPath($item['gorsel_url'] ?? ''); ?>" class="w-full h-full object-cover" muted loop playsinline onmouseenter="this.play()" onmouseleave="this.pause()"></video>
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/20 pointer-events-none">
                                        <svg class="text-white drop-shadow-md" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    </div>
                                <?php else: ?>
                                    <img src="<?php echo getAdminAssetPath($item['medya_url']); ?>" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/100x100/111/444?text=Hata'">
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-8 py-4 font-semibold text-white"><?php echo htmlspecialchars($item['baslik']); ?></td>
                        <td class="px-8 py-4 text-gray-400 capitalize"><?php echo htmlspecialchars($item['kategori']); ?></td>
                        <td class="px-8 py-4 text-gray-400 sira-gosterici"><?php echo htmlspecialchars($item['sira']); ?></td>
                        <td class="px-8 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="portfolio.php?action=edit&id=<?php echo $item['id']; ?>" class="p-2.5 bg-[#1a1a1a] border border-[#333] hover:border-blue-500 text-gray-400 hover:text-blue-500 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <a href="portfolio.php?action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Bu projeyi silmek istediğinize emin misiniz?')" class="p-2.5 bg-[#1a1a1a] border border-[#333] hover:border-red-500 text-gray-400 hover:text-red-500 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <script>
        $(function() {
            $("#sortable").sortable({
                handle: '.handle',
                placeholder: "bg-[#222] border border-dashed border-[#555] h-20",
                update: function(event, ui) {
                    var data = $(this).sortable('serialize');
                    $.ajax({
                        data: data,
                        type: 'POST',
                        url: 'ajax_sira_guncelle.php',
                        success: function(sonuc) {
                            $('#sortable tr').each(function(index) {
                                $(this).find('.sira-gosterici').text(index + 1);
                            });
                        }
                    });
                }
            });
        });
        </script>

    <?php else: ?>
        <div class="max-w-4xl mx-auto">
            <form method="POST" enctype="multipart/form-data" class="bg-[#111] border border-[#222] rounded-3xl p-10 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Proje Başlığı</label>
                        <input type="text" name="baslik" value="<?php echo htmlspecialchars($edit_item['baslik'] ?? ''); ?>" required class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:outline-none focus:border-[#F15A24] transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Kategori</label>
                        <select name="kategori" required class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:outline-none focus:border-[#F15A24] transition-all">
                            <option value="web" <?php echo($edit_item['kategori'] ?? '') == 'web' ? 'selected' : ''; ?>>Web Tasarım</option>
                            <option value="sosyal" <?php echo($edit_item['kategori'] ?? '') == 'sosyal' ? 'selected' : ''; ?>>Sosyal Medya</option>
                            <option value="video" <?php echo($edit_item['kategori'] ?? '') == 'video' ? 'selected' : ''; ?>>Video Prodüksiyon</option>
                            <option value="marka" <?php echo($edit_item['kategori'] ?? '') == 'marka' ? 'selected' : ''; ?>>Marka Kimliği</option>
                        </select>
                    </div>
                </div>

                <div x-data="{ type: '<?php echo $edit_item['medya_turu'] ?? 'resim'; ?>' }">
                    <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Medya Türü</label>
                    <div class="flex gap-4">
                        <label class="flex-1 flex items-center justify-center gap-3 p-4 rounded-xl border transition-all cursor-pointer" :class="type == 'resim' ? 'bg-blue-500/10 border-blue-500 text-blue-500' : 'bg-[#080808] border-[#222] text-gray-500'">
                            <input type="radio" name="medya_turu" value="resim" x-model="type" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            Görsel Yükle
                        </label>
                        <label class="flex-1 flex items-center justify-center gap-3 p-4 rounded-xl border transition-all cursor-pointer" :class="type == 'video' ? 'bg-orange-500/10 border-orange-500 text-orange-500' : 'bg-[#080808] border-[#222] text-gray-500'">
                            <input type="radio" name="medya_turu" value="video" x-model="type" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                            BunnyCDN Video
                        </label>
                    </div>

                    <div class="mt-6 space-y-6">
                        <div x-show="type == 'resim'">
                            <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Dosya Yükle (Resim)</label>
                            <input type="file" name="resim_dosya" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#1a1a1a] file:text-white hover:file:bg-[#222] cursor-pointer mb-4">
                            <input type="hidden" name="medya_url" x-bind:disabled="type != 'resim'" value="<?php echo htmlspecialchars($edit_item['medya_url'] ?? ''); ?>">
                            <?php if (($edit_item['medya_turu'] ?? '') == 'resim' && !empty($edit_item['medya_url'])): ?>
                                <p class="text-xs text-green-500 mt-2">Mevcut görsel yüklü. Değiştirmek isterseniz yeni dosya seçin.</p>
                            <?php endif; ?>
                        </div>

                        <div x-show="type == 'video'">
                            <div class="mb-4">
                                <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Direct Link (MP4)</label>
                                <input type="text" name="medya_url" x-bind:disabled="type != 'video'" value="<?php echo htmlspecialchars($edit_item['medya_url'] ?? ''); ?>" placeholder="https://vz-14e88...b-cdn.net/.../play_720p.mp4" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:outline-none focus:border-orange-500 transition-all">
                            </div>
                            
                            <div class="p-5 bg-orange-500/5 border border-orange-500/20 rounded-xl">
                                <label class="block text-orange-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Mobil Kapak Görseli (Poster - WebP Önerilir)</label>
                                <p class="text-xs text-gray-400 mb-3 px-1">Sayfa hızını korumak için mobilde videolar yerine bu kapak resmi gösterilecektir.</p>
                                <input type="file" name="video_kapak" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#1a1a1a] file:text-white hover:file:bg-[#222] cursor-pointer mb-2">
                                <input type="hidden" name="eski_gorsel_url" value="<?php echo htmlspecialchars($edit_item['gorsel_url'] ?? ''); ?>">
                                <?php if (($edit_item['medya_turu'] ?? '') == 'video' && !empty($edit_item['gorsel_url'])): ?>
                                    <div class="mt-3 flex items-center gap-3">
                                        <img src="<?php echo getAdminAssetPath($edit_item['gorsel_url']); ?>" class="w-16 h-10 object-cover rounded border border-[#333]">
                                        <span class="text-xs text-green-500 font-medium">Mevcut kapak yüklü.</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Görüntüleme Sırası (0-99)</label>
                    <input type="number" name="sira" value="<?php echo htmlspecialchars($edit_item['sira'] ?? '0'); ?>" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:outline-none focus:border-[#F15A24] transition-all">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#F15A24] hover:bg-[#d84a1a] text-white font-bold py-5 rounded-2xl transition-all shadow-lg shadow-orange-600/20">
                        <?php echo $id ? 'DEĞİŞİKLİKLERİ KAYDET' : 'PROJEYİ YAYINLA'; ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>
<?php
/**
 * istatistikler.php - İstatistik Yönetimi
 */
require_once '../includes/db.php';

$message = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($action === 'delete' && $id) {
    $stmt = $db->prepare("DELETE FROM istatistikler WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: istatistikler.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deger = $_POST['deger'];
    $etiket = $_POST['etiket'];
    $sira = (int)$_POST['sira'];

    if ($id) {
        $stmt = $db->prepare("UPDATE istatistikler SET deger=?, etiket=?, sira=? WHERE id=?");
        $stmt->execute([$deger, $etiket, $sira, $id]);
        $message = 'İstatistik başarıyla güncellendi.';
    } else {
        $stmt = $db->prepare("INSERT INTO istatistikler (deger, etiket, sira) VALUES (?, ?, ?)");
        $stmt->execute([$deger, $etiket, $sira]);
        $message = 'Yeni istatistik eklendi.';
    }
    $action = 'list';
}

require_once 'header.php';

$edit_item = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare("SELECT * FROM istatistikler WHERE id = ?");
    $stmt->execute([$id]);
    $edit_item = $stmt->fetch();
}

$stats = $db->query("SELECT * FROM istatistikler ORDER BY sira ASC, id ASC")->fetchAll();
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">İstatistik Yönetimi</h2>
            <p class="text-gray-500 text-sm mt-1">Ana sayfadaki başarı göstergelerini ve sayısal verileri yönetin.</p>
        </div>
        <?php if ($action === 'list'): ?>
            <a href="istatistikler.php?action=add" class="bg-[#F15A24] hover:bg-[#d84a1a] text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                YENİ İSTATİSTİK EKLE
            </a>
        <?php else: ?>
            <a href="istatistikler.php" class="bg-[#1a1a1a] border border-[#222] hover:bg-[#222] text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                İPTAL VE GERİ DÖN
            </a>
        <?php endif; ?>
    </div>

    <?php if ($message || isset($_GET['msg'])): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-2xl text-sm font-medium">
            <?php echo $message ?: ($_GET['msg'] === 'deleted' ? 'İstatistik başarıyla silindi.' : ''); ?>
        </div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($stats)): ?>
                <div class="col-span-full bg-[#111] border border-[#222] rounded-3xl p-10 text-center text-gray-500 italic">
                    Henüz bir istatistik eklenmemiş.
                </div>
            <?php endif; ?>
            
            <?php foreach ($stats as $s): ?>
            <div class="bg-[#111] border border-[#222] rounded-3xl p-8 flex flex-col justify-between group relative overflow-hidden transition-all hover:border-[#333]">
                <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                    <a href="istatistikler.php?action=edit&id=<?php echo $s['id']; ?>" class="p-2 bg-[#1a1a1a] border border-[#333] hover:border-blue-500 text-white rounded-xl transition-all shadow-lg" title="Düzenle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <a href="istatistikler.php?action=delete&id=<?php echo $s['id']; ?>" onclick="return confirm('Bu istatistiği silmek istediğinize emin misiniz?')" class="p-2 bg-[#1a1a1a] border border-[#333] hover:border-red-500 text-white rounded-xl transition-all shadow-lg" title="Sil">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="text-[#F15A24] text-5xl font-black font-barlow tracking-tight"><?php echo htmlspecialchars($s['deger']); ?></div>
                    <div class="text-white font-bold text-lg"><?php echo htmlspecialchars($s['etiket']); ?></div>
                    <span class="inline-block text-[10px] uppercase tracking-widest text-gray-500 font-bold bg-[#222] px-3 py-1 rounded-full">Sıra: <?php echo $s['sira']; ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="max-w-2xl mx-auto">
            <form method="POST" class="bg-[#111] border border-[#222] p-10 rounded-3xl space-y-6 shadow-2xl">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 pl-1">İstatistik Değeri</label>
                    <input type="text" name="deger" value="<?php echo htmlspecialchars($edit_item['deger'] ?? ''); ?>" required placeholder="Örn: 26+ veya 98%"
                           class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl focus:border-[#F15A24] outline-none transition-all">
                </div>
                
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 pl-1">İstatistik Etiketi</label>
                    <input type="text" name="etiket" value="<?php echo htmlspecialchars($edit_item['etiket'] ?? ''); ?>" required placeholder="Örn: Kurulan Dijital Sistem"
                           class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl focus:border-[#F15A24] outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 pl-1">Sıralama (0-99)</label>
                    <input type="number" name="sira" value="<?php echo htmlspecialchars($edit_item['sira'] ?? '0'); ?>"
                           class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl focus:border-[#F15A24] outline-none transition-all">
                </div>

                <div class="pt-6 border-t border-[#222]">
                    <button type="submit" class="w-full bg-[#F15A24] hover:bg-[#d84a1a] text-white font-bold py-5 rounded-2xl transition-all shadow-lg shadow-orange-600/20">
                        <?php echo $id ? 'DEĞİŞİKLİKLERİ KAYDET' : 'İSTATİSTİĞİ KAYDET'; ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>

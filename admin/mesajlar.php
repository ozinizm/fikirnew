<?php
/**
 * mesajlar.php - Gelen Mesaj Yönetimi
 */
require_once '../includes/db.php';

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$message = '';

// Mesaj Silme
if ($action === 'delete' && $id) {
    $stmt = $db->prepare("DELETE FROM mesajlar WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: mesajlar.php?msg=deleted');
    exit;
}

// Okundu Olarak İşaretle
if ($action === 'read' && $id) {
    $stmt = $db->prepare("UPDATE mesajlar SET okundu = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: mesajlar.php');
    exit;
}

require_once 'header.php';

$mesajlar = $db->query("SELECT * FROM mesajlar ORDER BY okundu ASC, tarih DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="space-y-8">
    <div>
        <h2 class="text-3xl font-black text-white tracking-tight uppercase">Gelen Kutusu</h2>
        <p class="text-gray-500 text-sm mt-1">Müşterilerinizden gelen talepleri buradan takip edin.</p>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-2xl text-sm font-bold">
            Mesaj başarıyla silindi.
        </div>
    <?php
endif; ?>

    <div class="grid grid-cols-1 gap-4">
        <?php if (empty($mesajlar)): ?>
            <div class="bg-[#111] border border-[#222] border-dashed p-20 rounded-[2.5rem] text-center">
                <p class="text-gray-600 font-bold uppercase tracking-widest">Henüz mesaj gelmedi.</p>
            </div>
        <?php
endif; ?>

        <?php foreach ($mesajlar as $m): ?>
            <div class="group bg-[#111] border <?php echo $m['okundu'] ? 'border-[#222]' : 'border-orange-500/30 shadow-lg shadow-orange-500/5'; ?> p-8 rounded-[2rem] transition-all hover:border-[#333]">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex-1 space-y-2">
                        <div class="flex items-center gap-3">
                            <h4 class="text-white font-black text-lg"><?php echo htmlspecialchars($m['ad_soyad']); ?></h4>
                            <?php if (!$m['okundu']): ?>
                                <span class="bg-orange-500 text-white text-[9px] font-black px-2 py-0.5 rounded uppercase">Yeni</span>
                            <?php
    endif; ?>
                        </div>
                        <p class="text-[#F15A24] text-xs font-bold"><?php echo htmlspecialchars($m['konu']); ?></p>
                        <p class="text-gray-400 text-sm leading-relaxed max-w-3xl"><?php echo nl2br(htmlspecialchars($m['mesaj'])); ?></p>
                        <div class="flex items-center gap-4 pt-2">
                            <span class="text-gray-600 text-[10px] font-bold uppercase"><?php echo htmlspecialchars($m['eposta']); ?></span>
                            <span class="text-gray-600 text-[10px] font-bold uppercase border-l border-gray-800 pl-4"><?php echo date('d.m.Y H:i', strtotime($m['tarih'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <?php if (!$m['okundu']): ?>
                            <a href="mesajlar.php?action=read&id=<?php echo $m['id']; ?>" class="bg-green-500/10 text-green-500 border border-green-500/20 hover:bg-green-500 hover:text-white p-3 rounded-xl transition-all" title="Okundu İşaretle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </a>
                        <?php
    endif; ?>
                        <a href="mesajlar.php?action=delete&id=<?php echo $m['id']; ?>" onclick="return confirm('Silmek istiyor musun?')" class="bg-red-500/10 text-red-500 border border-red-500/20 hover:bg-red-500 hover:text-white p-3 rounded-xl transition-all" title="Sil">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        <?php
endforeach; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
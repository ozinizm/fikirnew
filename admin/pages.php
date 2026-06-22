<?php
/**
 * admin/pages.php - Tüm Sayfaların Listelenmesi
 */
require_once '../includes/db.php';
require_once 'includes/auth.php';

$message = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Silme İşlemi (Soft delete yerine basit delete, talep edilirse soft eklenebilir)
if ($action === 'delete' && $id) {
    $stmt = $db->prepare("DELETE FROM pages WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: pages.php?msg=deleted');
    exit;
}

// Durum Değiştirme (Hızlı yayına al / pasife çek)
if ($action === 'toggle_status' && $id) {
    $current = $db->query("SELECT status FROM pages WHERE id = $id")->fetchColumn();
    $new_status = ($current === 'published') ? 'passive' : 'published';
    $stmt = $db->prepare("UPDATE pages SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    header('Location: pages.php?msg=status_updated');
    exit;
}

require_once 'header.php';

// Filtreler
$where = " WHERE 1=1 ";
$params = [];
if (!empty($_GET['type'])) {
    $where .= " AND page_type = ? ";
    $params[] = $_GET['type'];
}
if (!empty($_GET['status'])) {
    $where .= " AND status = ? ";
    $params[] = $_GET['status'];
}
if (!empty($_GET['q'])) {
    $where .= " AND (title LIKE ? OR slug LIKE ?) ";
    $params[] = "%".$_GET['q']."%";
    $params[] = "%".$_GET['q']."%";
}

$stmt = $db->prepare("SELECT * FROM pages $where ORDER BY updated_at DESC");
$stmt->execute($params);
$pages = $stmt->fetchAll();
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Sayfa Yönetimi</h2>
            <p class="text-gray-500 text-sm mt-1">Hizmet, lokasyon ve blog içeriklerini dinamik olarak yönetin.</p>
        </div>
        <a href="page_edit.php" class="bg-[#F15A24] hover:bg-[#d84a1a] text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
            YENİ SAYFA OLUŞTUR
        </a>
    </div>

    <!-- Filtreleme Alanı -->
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-[#111] p-6 rounded-3xl border border-[#222]">
        <input type="text" name="q" placeholder="Ara (Başlık veya Slug)..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="bg-[#080808] border border-[#222] text-white px-4 py-2.5 rounded-xl text-sm focus:border-orange outline-none">
        <select name="type" class="bg-[#080808] border border-[#222] text-white px-4 py-2.5 rounded-xl text-sm outline-none focus:border-orange">
            <option value="">Tüm Sayfa Tipleri</option>
            <option value="service" <?= ($_GET['type'] ?? '') == 'service' ? 'selected' : '' ?>>Hizmet Sayfası</option>
            <option value="location" <?= ($_GET['type'] ?? '') == 'location' ? 'selected' : '' ?>>Lokasyon Sayfası</option>
            <option value="blog" <?= ($_GET['type'] ?? '') == 'blog' ? 'selected' : '' ?>>Blog Yazısı</option>
            <option value="portfolio" <?= ($_GET['type'] ?? '') == 'portfolio' ? 'selected' : '' ?>>Portfolyo / Vaka</option>
            <option value="standard" <?= ($_GET['type'] ?? '') == 'standard' ? 'selected' : '' ?>>Standart Sayfa</option>
        </select>
        <select name="status" class="bg-[#080808] border border-[#222] text-white px-4 py-2.5 rounded-xl text-sm outline-none focus:border-orange">
            <option value="">Tüm Durumlar</option>
            <option value="published" <?= ($_GET['status'] ?? '') == 'published' ? 'selected' : '' ?>>Yayında</option>
            <option value="draft" <?= ($_GET['status'] ?? '') == 'draft' ? 'selected' : '' ?>>Taslak</option>
            <option value="passive" <?= ($_GET['status'] ?? '') == 'passive' ? 'selected' : '' ?>>Pasif</option>
        </select>
        <button type="submit" class="bg-[#1a1a1a] border border-[#222] text-white font-bold py-2.5 rounded-xl hover:bg-[#222] transition-all text-sm uppercase">Filtrele</button>
    </form>

    <?php if (isset($_GET['msg'])): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-2xl text-sm font-medium">
            İşlem başarıyla tamamlandı.
        </div>
    <?php endif; ?>

    <div class="bg-[#111] border border-[#222] rounded-3xl overflow-hidden shadow-xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#1a1a1a] text-gray-500 text-[10px] uppercase font-black tracking-widest border-b border-[#222]">
                    <th class="px-6 py-5">Başlık / Slug</th>
                    <th class="px-6 py-5">Tip</th>
                    <th class="px-6 py-5">Durum</th>
                    <th class="px-6 py-5">Son Güncelleme</th>
                    <th class="px-6 py-5 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#222]">
                <?php if (empty($pages)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-600 text-sm italic">Henüz sayfa oluşturulmamış.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($pages as $p): ?>
                <tr class="group hover:bg-[#0d0d0d] transition-colors">
                    <td class="px-6 py-5">
                        <p class="text-white font-bold text-sm mb-1"><?= htmlspecialchars($p['title']) ?></p>
                        <p class="text-gray-600 text-[11px] font-medium flex items-center gap-1 italic">
                            /<?= htmlspecialchars($p['slug']) ?>
                            <a href="../<?= htmlspecialchars($p['slug']) ?>" target="_blank" class="text-blue-500/50 hover:text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            </a>
                        </p>
                    </td>
                    <td class="px-6 py-5">
                        <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-lg bg-[#222] text-gray-400">
                            <?= $p['page_type'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-5">
                        <?php if ($p['status'] === 'published'): ?>
                            <span class="inline-flex items-center gap-1.5 text-green-500 text-[10px] font-bold uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Yayında
                            </span>
                        <?php elseif ($p['status'] === 'draft'): ?>
                            <span class="inline-flex items-center gap-1.5 text-gray-500 text-[10px] font-bold uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Taslak
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 text-red-500 text-[10px] font-bold uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Pasif
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-5 text-gray-500 text-xs font-medium italic">
                        <?= date('d.m.Y H:i', strtotime($p['updated_at'])) ?>
                    </td>
                    <td class="px-6 py-5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="pages.php?action=toggle_status&id=<?= $p['id'] ?>" title="<?= $p['status'] === 'published' ? 'Pasife Al' : 'Yayına Al' ?>" class="p-2.5 bg-[#1a1a1a] border border-[#222] text-gray-400 hover:text-orange rounded-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <a href="page_edit.php?id=<?= $p['id'] ?>" title="Düzenle" class="p-2.5 bg-[#1a1a1a] border border-[#222] text-gray-400 hover:text-blue-500 rounded-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <a href="pages.php?action=delete&id=<?= $p['id'] ?>" onclick="return confirm('Bu sayfayı silmek istediğinize emin misiniz?')" title="Sil" class="p-2.5 bg-[#1a1a1a] border border-[#222] text-gray-400 hover:text-red-500 rounded-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>

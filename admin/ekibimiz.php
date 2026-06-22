<?php
/**
 * ekip.php - Ekip Üyeleri Yönetimi
 */
require_once '../includes/db.php';

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$message = '';

// SİLME İŞLEMİ
if ($action === 'delete' && $id) {
    $stmt = $db->prepare("SELECT foto FROM ekip WHERE id = ?");
    $stmt->execute([$id]);
    $eski = $stmt->fetch();
    if ($eski && !empty($eski['foto']) && file_exists(ROOT_DIR . '/' . $eski['foto'])) {
        unlink(ROOT_DIR . '/' . $eski['foto']);
    }

    $db->prepare("DELETE FROM ekip WHERE id = ?")->execute([$id]);
    header('Location: ekibimiz.php?msg=deleted');
    exit;
}

// EKLEME / GÜNCELLEME
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_soyad = $_POST['ad_soyad'];
    $gorev = $_POST['gorev'];
    $bio = $_POST['bio'];
    $instagram = $_POST['instagram'];
    $linkedin = $_POST['linkedin'];
    $sira = $_POST['sira'] ?: 0;
    $foto = $_POST['mevcut_foto'];

    // Fotoğraf Yükleme
    if (isset($_FILES['ekip_foto']) && $_FILES['ekip_foto']['error'] === 0) {
        $ext = pathinfo($_FILES['ekip_foto']['name'], PATHINFO_EXTENSION);
        $filename = 'ekip_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['ekip_foto']['tmp_name'], ROOT_DIR . '/images/' . $filename)) {
            $foto = 'images/' . $filename;
        }
    }


    if ($id) {
        $stmt = $db->prepare("UPDATE ekip SET ad_soyad=?, gorev=?, bio=?, instagram=?, linkedin=?, sira=?, foto=? WHERE id=?");
        $stmt->execute([$ad_soyad, $gorev, $bio, $instagram, $linkedin, $sira, $foto, $id]);
    }
    else {
        $stmt = $db->prepare("INSERT INTO ekip (ad_soyad, gorev, bio, instagram, linkedin, sira, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$ad_soyad, $gorev, $bio, $instagram, $linkedin, $sira, $foto]);
    }
    header('Location: ekibimiz.php?msg=success');
    exit;
}

require_once 'header.php';
$ekip = $db->query("SELECT * FROM ekip ORDER BY sira ASC")->fetchAll(PDO::FETCH_ASSOC);

// Düzenleme Modu
$edit = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare("SELECT * FROM ekip WHERE id = ?");
    $stmt->execute([$id]);
    $edit = $stmt->fetch();
}
?>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight uppercase">Ekip Yönetimi</h2>
            <p class="text-gray-500 text-sm mt-1">Fikir Creative mutfağındaki yetenekleri buradan yönetin.</p>
        </div>
        <?php if ($action === 'list'): ?>
            <a href="ekibimiz.php?action=add" class="bg-[#F15A24] text-white px-6 py-3 rounded-2xl font-bold text-sm">YENİ ÜYE EKLE</a>
        <?php
else: ?>
            <a href="ekibimiz.php" class="bg-[#111] border border-[#222] text-white px-6 py-3 rounded-2xl font-bold text-sm">İPTAL</a>
        <?php
endif; ?>
    </div>

    <?php if ($action === 'list'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($ekip as $u): ?>
                <div class="bg-[#111] border border-[#222] rounded-[2rem] overflow-hidden group">
                    <div class="aspect-square bg-gray-900 relative">
                        <img src="<?php echo getAdminAssetPath($u['foto'] ?: 'images/avatar-placeholder.jpg'); ?>" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                        <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-all">
                            <a href="ekibimiz.php?action=edit&id=<?php echo $u['id']; ?>" class="p-2 bg-black/80 rounded-lg text-blue-500 hover:bg-blue-500 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <h4 class="text-white font-bold text-lg"><?php echo htmlspecialchars($u['ad_soyad']); ?></h4>
                        <p class="text-orange-500 text-[10px] font-black uppercase tracking-widest mt-1"><?php echo htmlspecialchars($u['gorev']); ?></p>
                    </div>
                </div>
            <?php
    endforeach; ?>
        </div>
    <?php
else: ?>
        <form method="POST" enctype="multipart/form-data" class="max-w-4xl mx-auto bg-[#111] border border-[#222] p-10 rounded-[2.5rem] space-y-6">
            <input type="hidden" name="mevcut_foto" value="<?php echo $edit['foto'] ?? ''; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Ad Soyad</label>
                    <input type="text" name="ad_soyad" value="<?php echo htmlspecialchars($edit['ad_soyad'] ?? ''); ?>" required class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Görev / Ünvan</label>
                    <input type="text" name="gorev" value="<?php echo htmlspecialchars($edit['gorev'] ?? ''); ?>" placeholder="Örn: Creative Director" required class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none">
                </div>
            </div>

            <div>
                <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Kısa Bio</label>
                <textarea name="bio" rows="3" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-orange-500 transition-all"><?php echo htmlspecialchars($edit['bio'] ?? ''); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Instagram Link</label>
                    <input type="text" name="instagram" value="<?php echo htmlspecialchars($edit['instagram'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none">
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">LinkedIn Link</label>
                    <input type="text" name="linkedin" value="<?php echo htmlspecialchars($edit['linkedin'] ?? ''); ?>" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Profil Fotoğrafı</label>
                    <input type="file" name="ekip_foto" accept="image/*" class="text-xs text-gray-500">
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Sıralama</label>
                    <input type="number" name="sira" value="<?php echo $edit['sira'] ?? 0; ?>" class="w-20 bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none">
                </div>
            </div>

            <button type="submit" class="w-full bg-[#F15A24] text-white font-black py-5 rounded-2xl transition-all shadow-xl shadow-orange-600/20 uppercase tracking-widest text-sm">
                EKİP ÜYESİNİ KAYDET
            </button>
        </form>
    <?php
endif; ?>
</div>

<?php require_once 'footer.php'; ?>
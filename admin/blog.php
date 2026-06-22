<?php
/**
 * blog.php - Blog Management (Finalized with Meta Description & Categories)
 */

// 1. VERİTABANI VE MANTIKSAL İŞLEMLER (HTML Öncesi)
require_once '../includes/db.php';

$message = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// SİLME İŞLEMİ
if ($action === 'delete' && $id) {
    $stmt = $db->prepare("SELECT resim FROM blog WHERE id = ?");
    $stmt->execute([$id]);
    $eski = $stmt->fetch();
    if ($eski && !empty($eski['resim']) && file_exists(ROOT_DIR . '/' . $eski['resim'])) {
        unlink(ROOT_DIR . '/' . $eski['resim']);
    }

    $stmt = $db->prepare("DELETE FROM blog WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: blog.php?msg=deleted');
    exit;
}

// EKLEME VE GÜNCELLEME İŞLEMİ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baslik = $_POST['baslik'];
    $slug = $_POST['slug'];
    $kategori = $_POST['kategori']; 
    $icerik = $_POST['icerik'];
    $yazar = $_POST['yazar'] ?: 'Fikir Creative';
    $meta_desc = $_POST['meta_desc'] ?? ''; // YENİ: Meta Açıklaması eklendi
    $resim = $_POST['resim']; 

    // Görsel Yükleme
    if (isset($_FILES['blog_resim']) && $_FILES['blog_resim']['error'] === 0) {
        $ext = pathinfo($_FILES['blog_resim']['name'], PATHINFO_EXTENSION);
        $filename = 'blog_' . time() . '_' . uniqid() . '.' . $ext;
        $upload_path = ROOT_DIR . '/images/' . $filename;

        if (!is_dir(ROOT_DIR . '/images'))
            mkdir(ROOT_DIR . '/images', 0777, true);

        if (move_uploaded_file($_FILES['blog_resim']['tmp_name'], $upload_path)) {
            if (!empty($resim) && file_exists(ROOT_DIR . '/' . $resim))
                unlink(ROOT_DIR . '/' . $resim);
            $resim = 'images/' . $filename;
        }
    }


    if ($id) {
        // YENİ: UPDATE sorgusuna meta_desc eklendi
        $stmt = $db->prepare("UPDATE blog SET baslik=?, slug=?, kategori=?, icerik=?, yazar=?, meta_desc=?, resim=? WHERE id=?");
        $stmt->execute([$baslik, $slug, $kategori, $icerik, $yazar, $meta_desc, $resim, $id]);
        $message = 'Yazı başarıyla güncellendi.';
    }
    else {
        // YENİ: INSERT sorgusuna meta_desc eklendi
        $stmt = $db->prepare("INSERT INTO blog (baslik, slug, kategori, icerik, yazar, meta_desc, resim) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$baslik, $slug, $kategori, $icerik, $yazar, $meta_desc, $resim]);
        $message = 'Yeni yazı başarıyla eklendi.';
    }
    $action = 'list';
}

require_once 'header.php';

// Düzenleme verisini çek
$edit_item = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare("SELECT * FROM blog WHERE id = ?");
    $stmt->execute([$id]);
    $edit_item = $stmt->fetch();
}

$posts = $db->query("SELECT * FROM blog ORDER BY id DESC")->fetchAll();
?>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<style>
    .ck-editor__editable_inline { min-height: 400px; background-color: #080808 !important; color: #fff !important; border: 1px solid #222 !important; border-bottom-left-radius: 12px !important; border-bottom-right-radius: 12px !important; }
    .ck.ck-toolbar { background-color: #1a1a1a !important; border: 1px solid #222 !important; border-top-left-radius: 12px !important; border-top-right-radius: 12px !important; }
    .ck.ck-button { color: #fff !important; }
    .ck.ck-icon { fill: currentColor !important; }
</style>

<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Blog Yönetimi</h2>
            <p class="text-gray-500 text-sm mt-1">Yazılarınızı kategorize edin ve yayınlayın.</p>
        </div>
        <?php if ($action === 'list'): ?>
            <a href="blog.php?action=add" class="bg-[#F15A24] hover:bg-[#d84a1a] text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all flex items-center gap-2">
                YENİ YAZI EKLE
            </a>
        <?php else: ?>
            <a href="blog.php" class="bg-[#1a1a1a] border border-[#222] text-white px-6 py-3 rounded-2xl font-bold text-sm">İPTAL</a>
        <?php endif; ?>
    </div>

    <?php if ($message || isset($_GET['msg'])): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-2xl text-sm font-medium">
            <?php echo $message ?: 'İşlem başarıyla tamamlandı.'; ?>
        </div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($posts as $post): ?>
            <div class="bg-[#111] border border-[#222] rounded-3xl overflow-hidden group hover:border-[#333] transition-all relative">
                
                <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                    <a href="blog.php?action=edit&id=<?php echo $post['id']; ?>" class="p-2 bg-[#1a1a1a]/90 border border-[#333] text-gray-400 hover:text-blue-500 rounded-xl transition-all shadow-xl"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                    <a href="blog.php?action=delete&id=<?php echo $post['id']; ?>" onclick="return confirm('Silinsin mi?')" class="p-2 bg-[#1a1a1a]/90 border border-[#333] text-gray-400 hover:text-red-500 rounded-xl transition-all shadow-xl"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></a>
                </div>

                <div class="aspect-[16/10] relative overflow-hidden bg-[#080808]">
                    <?php if (!empty($post['resim'])): ?>
                        <img src="<?php echo getAdminAssetPath($post['resim']); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-800 text-[10px] uppercase font-bold">Görsel Yok</div>
                    <?php endif; ?>
                    <div class="absolute top-4 left-4">
                        <span class="text-[9px] font-black text-white uppercase tracking-widest bg-[#F15A24] px-2.5 py-1 rounded-lg shadow-lg">
                            <?php echo date('d.m.Y', strtotime($post['tarih'])); ?>
                        </span>
                    </div>
                </div>

                <div class="p-7">
                    <span class="text-[10px] font-bold text-orange uppercase tracking-widest mb-2 block"><?php echo htmlspecialchars($post['kategori'] ?? 'sosyal'); ?></span>
                    <h3 class="text-white font-bold text-lg mb-4 line-clamp-2 leading-snug"><?php echo htmlspecialchars($post['baslik']); ?></h3>
                    <div class="flex items-center justify-between pt-4 border-t border-[#222]">
                        <span class="text-[11px] text-gray-500 font-medium"><?php echo htmlspecialchars($post['yazar']); ?></span>
                        <a href="blog.php?action=edit&id=<?php echo $post['id']; ?>" class="text-[10px] font-bold text-blue-500 hover:text-white transition-colors uppercase">Düzenle</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="max-w-5xl mx-auto">
            <form method="POST" enctype="multipart/form-data" class="bg-[#111] border border-[#222] rounded-3xl p-10 space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Başlık</label>
                        <input type="text" name="baslik" id="titleInput" value="<?php echo htmlspecialchars($edit_item['baslik'] ?? ''); ?>" required class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-[#F15A24] outline-none text-lg font-bold">
                    </div>
                    <div>
                        <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Kategori</label>
                        <select name="kategori" required class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-[#F15A24] outline-none">
                            <option value="sosyal" <?php echo($edit_item['kategori'] ?? '') == 'sosyal' ? 'selected' : ''; ?>>Sosyal Medya</option>
                            <option value="web" <?php echo($edit_item['kategori'] ?? '') == 'web' ? 'selected' : ''; ?>>Web & SEO</option>
                            <option value="marka" <?php echo($edit_item['kategori'] ?? '') == 'marka' ? 'selected' : ''; ?>>Marka Kimliği</option>
                            <option value="video" <?php echo($edit_item['kategori'] ?? '') == 'video' ? 'selected' : ''; ?>>Video Prodüksiyon</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between px-1 mb-2">
                        <label class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">SEO Meta Açıklama (Meta Description)</label>
                        <span id="meta-count" class="text-[10px] font-bold text-gray-600">0 / 155</span>
                    </div>
                    <textarea name="meta_desc" id="meta_desc" rows="2" placeholder="Google aramalarında başlığın altında görünecek, tıklamaya teşvik eden 1-2 cümlelik özet yazın." class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-[#F15A24] outline-none text-sm transition-all resize-none"><?php echo htmlspecialchars($edit_item['meta_desc'] ?? ''); ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">URL / Slug</label>
                        <input type="text" name="slug" id="slugInput" value="<?php echo htmlspecialchars($edit_item['slug'] ?? ''); ?>" required class="w-full bg-[#1a1a1a] border border-[#222] text-white px-4 py-4 rounded-xl text-xs focus:border-[#F15A24] outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Yazar</label>
                        <input type="text" name="yazar" value="<?php echo htmlspecialchars($edit_item['yazar'] ?? 'Fikir Creative'); ?>" class="w-full bg-[#1a1a1a] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-[#F15A24] outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">Kapak Görseli</label>
                    <input type="file" name="blog_resim" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#1a1a1a] file:text-white hover:file:bg-[#222] cursor-pointer">
                    <input type="hidden" name="resim" value="<?php echo htmlspecialchars($edit_item['resim'] ?? ''); ?>">
                    
                    <?php if (!empty($edit_item['resim'])): ?>
                        <p class="text-xs text-green-500 mt-2">Mevcut görsel yüklü. Değiştirmek isterseniz yeni dosya seçin.</p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-gray-500 text-[10px] font-bold uppercase tracking-widest mb-2 px-1">İçerik</label>
                    <textarea name="icerik" id="editor"><?php echo htmlspecialchars($edit_item['icerik'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="w-full bg-[#F15A24] hover:bg-[#d84a1a] text-white font-black py-5 rounded-2xl transition-all shadow-lg shadow-orange-600/20 uppercase tracking-widest text-sm">
                    KAYDET
                </button>
            </form>
        </div>

        <script>
            // Slug Generator
            const titleInput = document.getElementById('titleInput');
            const slugInput = document.getElementById('slugInput');
            titleInput.addEventListener('input', function(e) {
                if(!slugInput.dataset.edited) {
                    let text = e.target.value.toLowerCase();
                    const trMap = { 'ç':'c', 'ğ':'g', 'ş':'s', 'ü':'u', 'ı':'i', 'ö':'o' };
                    for(let key in trMap) text = text.replace(new RegExp(key, 'g'), trMap[key]);
                    text = text.replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
                    slugInput.value = text;
                }
            });
            slugInput.addEventListener('input', () => slugInput.dataset.edited = true);

            // YENİ: Meta Açıklama Sayacı
            const metaInput = document.getElementById('meta_desc');
            const metaCount = document.getElementById('meta-count');
            
            function updateMetaCount() {
                const len = metaInput.value.length;
                metaCount.textContent = len + ' / 155';
                if (len > 155) {
                    metaCount.classList.remove('text-gray-600');
                    metaCount.classList.add('text-red-500');
                    metaInput.classList.add('border-red-500');
                } else {
                    metaCount.classList.add('text-gray-600');
                    metaCount.classList.remove('text-red-500');
                    metaInput.classList.remove('border-red-500');
                }
            }
            if(metaInput) {
                metaInput.addEventListener('input', updateMetaCount);
                updateMetaCount(); // Sayfa yüklendiğinde mevcut sayıyı göster
            }

            // CKEditor
            ClassicEditor.create(document.querySelector('#editor'), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
            }).catch(error => console.error(error));
        </script>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
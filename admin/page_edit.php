<?php
/**
 * admin/page_edit.php - Sayfa Oluşturma ve Düzenleme (Gelişmiş SEO & Blok Sistemi)
 */
require_once '../includes/db.php';
require_once 'includes/auth.php';

$id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Sayfa Verisini Çek
$page = null;
if ($id) {
    $stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$id]);
    $page = $stmt->fetch();
    if (!$page) die("Sayfa bulunamadı.");
}

// POST İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();

        $title = $_POST['title'];
        $slug = $_POST['slug'] ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title'])));
        $page_type = $_POST['page_type'];
        $h1 = $_POST['h1'];
        $excerpt = $_POST['excerpt'];
        $content = $_POST['content'];
        $status = $_POST['status'];

        // SEO
        $seo_title = $_POST['seo_title'];
        $meta_description = $_POST['meta_description'];
        $canonical_url = $_POST['canonical_url'];
        $meta_robots = $_POST['meta_robots'];
        $og_title = $_POST['og_title'];
        $og_description = $_POST['og_description'];
        $focus_keyword = $_POST['focus_keyword'];
        $related_keywords = $_POST['related_keywords'];

        // CTA
        $cta_title = $_POST['cta_title'];
        $cta_text = $_POST['cta_text'];
        $primary_button_text = $_POST['primary_button_text'];
        $primary_button_url = $_POST['primary_button_url'];
        $secondary_button_text = $_POST['secondary_button_text'];
        $secondary_button_url = $_POST['secondary_button_url'];

        // Görseller
        $cover_image = $page['cover_image'] ?? '';
        $og_image = $page['og_image'] ?? '';

        if (!is_dir(ROOT_DIR . '/uploads/pages')) mkdir(ROOT_DIR . '/uploads/pages', 0777, true);

        if (isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] === 0) {
            $ext = pathinfo($_FILES['cover_file']['name'], PATHINFO_EXTENSION);
            $fname = 'cover_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['cover_file']['tmp_name'], ROOT_DIR . '/uploads/pages/' . $fname)) {
                $cover_image = 'uploads/pages/' . $fname;
            }
        }
        if (isset($_FILES['og_file']) && $_FILES['og_file']['error'] === 0) {
            $ext = pathinfo($_FILES['og_file']['name'], PATHINFO_EXTENSION);
            $fname = 'og_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['og_file']['tmp_name'], ROOT_DIR . '/uploads/pages/' . $fname)) {
                $og_image = 'uploads/pages/' . $fname;
            }
        }


        if ($id) {
            $sql = "UPDATE pages SET 
                title=?, slug=?, page_type=?, h1=?, excerpt=?, content=?, status=?,
                seo_title=?, meta_description=?, canonical_url=?, meta_robots=?, og_title=?, og_description=?, og_image=?,
                focus_keyword=?, related_keywords=?, 
                cta_title=?, cta_text=?, primary_button_text=?, primary_button_url=?, secondary_button_text=?, secondary_button_url=?,
                cover_image=?
                WHERE id=?";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $title, $slug, $page_type, $h1, $excerpt, $content, $status,
                $seo_title, $meta_description, $canonical_url, $meta_robots, $og_title, $og_description, $og_image,
                $focus_keyword, $related_keywords,
                $cta_title, $cta_text, $primary_button_text, $primary_button_url, $secondary_button_text, $secondary_button_url,
                $cover_image, $id
            ]);
        } else {
            $sql = "INSERT INTO pages (
                title, slug, page_type, h1, excerpt, content, status,
                seo_title, meta_description, canonical_url, meta_robots, og_title, og_description, og_image,
                focus_keyword, related_keywords,
                cta_title, cta_text, primary_button_text, primary_button_url, secondary_button_text, secondary_button_url,
                cover_image
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $title, $slug, $page_type, $h1, $excerpt, $content, $status,
                $seo_title, $meta_description, $canonical_url, $meta_robots, $og_title, $og_description, $og_image,
                $focus_keyword, $related_keywords,
                $cta_title, $cta_text, $primary_button_text, $primary_button_url, $secondary_button_text, $secondary_button_url,
                $cover_image
            ]);
            $id = $db->lastInsertId();
        }

        // FAQ Güncelleme
        $db->prepare("DELETE FROM page_faqs WHERE page_id = ?")->execute([$id]);
        if (!empty($_POST['faqs'])) {
            $faq_stmt = $db->prepare("INSERT INTO page_faqs (page_id, question, answer, sort_order) VALUES (?, ?, ?, ?)");
            foreach ($_POST['faqs'] as $index => $faq) {
                if (!empty($faq['q']) && !empty($faq['a'])) {
                    $faq_stmt->execute([$id, $faq['q'], $faq['a'], $index]);
                }
            }
        }

        // Blok Güncelleme (Basit JSON Saklama örneği veya tabloya yazma)
        $db->prepare("DELETE FROM page_blocks WHERE page_id = ?")->execute([$id]);
        if (!empty($_POST['blocks'])) {
            $block_stmt = $db->prepare("INSERT INTO page_blocks (page_id, block_type, block_data, sort_order) VALUES (?, ?, ?, ?)");
            foreach ($_POST['blocks'] as $index => $block) {
                $block_stmt->execute([$id, $block['type'], json_encode($block['data'], JSON_UNESCAPED_UNICODE), $index]);
            }
        }

        $db->commit();
        header("Location: page_edit.php?id=$id&msg=saved");
        exit;

    } catch (Exception $e) {
        $db->rollBack();
        $error = "Hata oluştu: " . $e->getMessage();
    }
}

// FAQ ve Blokları Çek
$faqs = $id ? $db->query("SELECT * FROM page_faqs WHERE page_id = $id ORDER BY sort_order ASC")->fetchAll() : [];
$blocks = $id ? $db->query("SELECT * FROM page_blocks WHERE page_id = $id ORDER BY sort_order ASC")->fetchAll() : [];

require_once 'header.php';
?>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<form method="POST" class="space-y-10 pb-20" x-data="pageEditor()">
    
    <!-- Üst Bar -->
    <div class="flex items-center justify-between sticky top-20 z-30 bg-[#080808]/80 backdrop-blur-md py-4 border-b border-[#1a1a1a]">
        <div>
            <h2 class="text-xl font-bold text-white"><?= $id ? 'Sayfayı Düzenle' : 'Yeni Sayfa Oluştur' ?></h2>
            <p class="text-xs text-gray-500 italic"><?= $id ? '/'.$page['slug'] : 'Taslak aşamasında...' ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="pages.php" class="text-xs font-bold text-gray-400 px-5 py-3 hover:text-white">İPTAL</a>
            <button type="submit" class="bg-[#F15A24] hover:bg-[#d84a1a] text-white px-8 py-3 rounded-2xl font-bold text-sm shadow-lg shadow-orange-600/20 transition-all uppercase tracking-widest">KAYDET</button>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-5 rounded-3xl text-sm font-bold">
            <?= $error ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['msg'])): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-5 rounded-3xl text-sm font-bold">
            Değişiklikler başarıyla kaydedildi.
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-10">
        
        <!-- Ana Form Alanı -->
        <div class="space-y-8">
            
            <!-- Temel Bilgiler -->
            <div class="bg-[#111] border border-[#222] rounded-3xl p-8 space-y-6">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-orange mb-4">Temel Bilgiler</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">Sayfa Başlığı (İç İsim)</label>
                        <input type="text" name="title" x-model="title" required class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-orange outline-none text-sm font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">URL / Slug</label>
                        <input type="text" name="slug" x-model="slug" @input="slugManual = true" class="w-full bg-[#1a1a1a] border border-[#222] text-gray-400 px-5 py-4 rounded-xl focus:border-orange outline-none text-xs italic">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">H1 Başlığı</label>
                        <input type="text" name="h1" value="<?= htmlspecialchars($page['h1'] ?? '') ?>" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-orange outline-none text-sm font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">Sayfa Tipi</label>
                        <select name="page_type" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl outline-none focus:border-orange text-sm font-bold">
                            <option value="service" <?= ($page['page_type'] ?? '') == 'service' ? 'selected' : '' ?>>Hizmet Sayfası</option>
                            <option value="location" <?= ($page['page_type'] ?? '') == 'location' ? 'selected' : '' ?>>Lokasyon Sayfası</option>
                            <option value="blog" <?= ($page['page_type'] ?? '') == 'blog' ? 'selected' : '' ?>>Blog Yazısı</option>
                            <option value="portfolio" <?= ($page['page_type'] ?? '') == 'portfolio' ? 'selected' : '' ?>>Portfolyo / Vaka Analizi</option>
                            <option value="standard" <?= ($page['page_type'] ?? '') == 'standard' ? 'selected' : '' ?>>Standart Sayfa</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">Kısa Açıklama (Özet)</label>
                    <textarea name="excerpt" rows="3" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-orange outline-none text-sm resize-none"><?= htmlspecialchars($page['excerpt'] ?? '') ?></textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">Ana İçerik</label>
                    <div class="editor-container">
                        <textarea name="content" id="editor"><?= htmlspecialchars($page['content'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- SEO Ayarları -->
            <div class="bg-[#111] border border-[#222] rounded-3xl p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-orange">SEO & Arama Motoru Optimizasyonu</h3>
                    <div class="bg-blue-500/10 text-blue-500 text-[9px] font-bold px-3 py-1 rounded-full border border-blue-500/20 italic uppercase tracking-widest">Kritik Alanlar</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">SEO Title</label>
                        <input type="text" name="seo_title" value="<?= htmlspecialchars($page['seo_title'] ?? '') ?>" placeholder="Max 60 Karakter" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-orange outline-none text-sm font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">Canonical URL</label>
                        <input type="text" name="canonical_url" value="<?= htmlspecialchars($page['canonical_url'] ?? '') ?>" placeholder="Opsiyonel (Boşsa sayfa linki kullanılır)" class="w-full bg-[#1a1a1a] border border-[#222] text-gray-500 px-5 py-4 rounded-xl focus:border-orange outline-none text-xs italic">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">Meta Description</label>
                    <textarea name="meta_description" rows="2" placeholder="Arama sonuçlarında görünecek açıklama (Max 155 karakter)" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-orange outline-none text-sm resize-none transition-all"><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">Odak Anahtar Kelime</label>
                        <input type="text" name="focus_keyword" value="<?= htmlspecialchars($page['focus_keyword'] ?? '') ?>" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-orange outline-none text-sm font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">Meta Robots</label>
                        <select name="meta_robots" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl outline-none focus:border-orange text-sm font-bold">
                            <option value="index, follow" <?= ($page['meta_robots'] ?? '') == 'index, follow' ? 'selected' : '' ?>>Index, Follow (Tavsiye Edilen)</option>
                            <option value="noindex, follow" <?= ($page['meta_robots'] ?? '') == 'noindex, follow' ? 'selected' : '' ?>>NoIndex, Follow</option>
                            <option value="noindex, nofollow" <?= ($page['meta_robots'] ?? '') == 'noindex, nofollow' ? 'selected' : '' ?>>Gizli (NoIndex, NoFollow)</option>
                        </select>
                    </div>
                </div>

                <div class="p-6 bg-[#080808] rounded-2xl border border-[#1a1a1a] space-y-4">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Open Graph (Sosyal Medya Paylaşımı)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="og_title" value="<?= htmlspecialchars($page['og_title'] ?? '') ?>" placeholder="Paylaşım Başlığı" class="bg-[#1a1a1a] border border-[#222] text-white px-4 py-3 rounded-xl text-xs outline-none">
                        <input type="text" name="og_image" value="<?= htmlspecialchars($page['og_image'] ?? '') ?>" placeholder="Paylaşım Görseli URL" class="bg-[#1a1a1a] border border-[#222] text-white px-4 py-3 rounded-xl text-xs outline-none">
                    </div>
                </div>
            </div>

            <!-- CTA Bölümü -->
            <div class="bg-[#111] border border-[#222] rounded-3xl p-8 space-y-6">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-orange mb-4">Dönüşüm (CTA) Alanı</h3>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">CTA Başlığı</label>
                    <input type="text" name="cta_title" value="<?= htmlspecialchars($page['cta_title'] ?? '') ?>" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-orange outline-none text-sm font-bold">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">CTA Alt Yazısı</label>
                    <textarea name="cta_text" rows="2" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-orange outline-none text-sm resize-none"><?= htmlspecialchars($page['cta_text'] ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">Birincil Buton Metni</label>
                        <input type="text" name="primary_button_text" value="<?= htmlspecialchars($page['primary_button_text'] ?? '') ?>" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-orange outline-none text-sm font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-1">Birincil Buton Linki</label>
                        <input type="text" name="primary_button_url" value="<?= htmlspecialchars($page['primary_button_url'] ?? '') ?>" class="w-full bg-[#080808] border border-[#222] text-white px-5 py-4 rounded-xl focus:border-orange outline-none text-sm">
                    </div>
                </div>
            </div>

            <!-- SSS Bölümü -->
            <div class="bg-[#111] border border-[#222] rounded-3xl p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-orange">Sık Sorulan Sorular (SSS)</h3>
                    <button type="button" @click="addFaq()" class="bg-[#1a1a1a] border border-orange/30 text-orange px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange hover:text-white transition-all">+ SORU EKLE</button>
                </div>
                
                <div class="space-y-4">
                    <template x-for="(faq, index) in faqs" :key="index">
                        <div class="bg-[#080808] border border-[#222] rounded-2xl p-6 relative group">
                            <button type="button" @click="removeFaq(index)" class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">&times;</button>
                            <div class="space-y-4">
                                <input type="text" :name="`faqs[${index}][q]`" x-model="faq.q" placeholder="Soru..." class="w-full bg-[#111] border border-[#222] text-white px-4 py-3 rounded-xl outline-none focus:border-orange text-sm font-bold">
                                <textarea :name="`faqs[${index}][a]`" x-model="faq.a" rows="2" placeholder="Cevap..." class="w-full bg-[#111] border border-[#222] text-white px-4 py-3 rounded-xl outline-none focus:border-orange text-sm resize-none"></textarea>
                            </div>
                        </div>
                    </template>
                    <div x-show="faqs.length === 0" class="text-center py-6 border-2 border-dashed border-[#222] rounded-2xl text-gray-600 text-xs italic">Soru eklenmemiş.</div>
                </div>
            </div>

            <!-- BLOK SİSTEMİ (Basit Bloklar) -->
            <div class="bg-[#111] border border-[#222] rounded-3xl p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-orange">Sayfa Blokları</h3>
                    <div class="flex gap-2">
                         <select x-model="newBlockType" class="bg-[#080808] border border-[#222] text-white text-[10px] font-bold uppercase px-3 py-1.5 rounded-xl outline-none">
                             <option value="text">Başlık + Metin</option>
                             <option value="image_text">Görsel + Metin</option>
                             <option value="grid">3'lü Kart Yapısı</option>
                             <option value="process">Süreç Adımları</option>
                         </select>
                         <button type="button" @click="addBlock()" class="bg-[#1a1a1a] border border-white/10 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-white hover:text-black transition-all">+ BLOK EKLE</button>
                    </div>
                </div>

                <div class="space-y-6">
                    <template x-for="(block, index) in blocks" :key="index">
                        <div class="bg-[#080808] border border-[#222] rounded-2xl p-6 relative group">
                            <div class="flex items-center justify-between mb-4 border-b border-[#1a1a1a] pb-3">
                                <span class="text-[9px] font-black text-orange uppercase tracking-widest" x-text="block.type"></span>
                                <div class="flex gap-2">
                                    <button type="button" @click="moveBlock(index, -1)" class="text-gray-500 hover:text-white"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 15l-6-6-6 6"/></svg></button>
                                    <button type="button" @click="moveBlock(index, 1)" class="text-gray-500 hover:text-white"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></button>
                                    <button type="button" @click="removeBlock(index)" class="text-red-500/50 hover:text-red-500 ml-2"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                                </div>
                            </div>
                            <input type="hidden" :name="`blocks[${index}][type]`" :value="block.type">
                            
                            <!-- Blok İçerikleri (Tipine Göre) -->
                            <div class="space-y-4">
                                <template x-if="block.type === 'text'">
                                    <div class="space-y-3">
                                        <input type="text" :name="`blocks[${index}][data][title]`" x-model="block.data.title" placeholder="Blok Başlığı..." class="w-full bg-[#111] border border-[#222] text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                                        <textarea :name="`blocks[${index}][data][text]`" x-model="block.data.text" rows="3" placeholder="Blok İçeriği..." class="w-full bg-[#111] border border-[#222] text-white px-4 py-2.5 rounded-xl text-sm"></textarea>
                                    </div>
                                </template>
                                <template x-if="block.type === 'image_text'">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-3">
                                            <input type="text" :name="`blocks[${index}][data][image]`" x-model="block.data.image" placeholder="Görsel URL..." class="w-full bg-[#111] border border-[#222] text-white px-4 py-2.5 rounded-xl text-xs">
                                            <select :name="`blocks[${index}][data][align]`" x-model="block.data.align" class="w-full bg-[#111] border border-[#222] text-white px-4 py-2.5 rounded-xl text-xs">
                                                <option value="left">Görsel Solda</option>
                                                <option value="right">Görsel Sağda</option>
                                            </select>
                                        </div>
                                        <div class="space-y-3">
                                            <input type="text" :name="`blocks[${index}][data][title]`" x-model="block.data.title" placeholder="Başlık..." class="w-full bg-[#111] border border-[#222] text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                                            <textarea :name="`blocks[${index}][data][text]`" x-model="block.data.text" rows="2" placeholder="Metin..." class="w-full bg-[#111] border border-[#222] text-white px-4 py-2.5 rounded-xl text-sm"></textarea>
                                        </div>
                                    </div>
                                </template>
                                <!-- Diğer blok tipleri buraya eklenebilir -->
                                <template x-if="block.type !== 'text' && block.type !== 'image_text'">
                                    <p class="text-[10px] text-gray-500 italic uppercase">Bu blok tipi için geliştirme devam ediyor veya JSON olarak saklanıyor.</p>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- Sağ Sidebar (Ayarlar) -->
        <div class="space-y-8">
            
            <!-- Yayınlama Ayarları -->
            <div class="bg-[#111] border border-[#222] rounded-3xl p-6 space-y-6 sticky top-32">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-4 px-1">Sayfa Durumu</h3>
                
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-4 bg-[#080808] border border-[#222] rounded-2xl cursor-pointer hover:border-orange transition-all" @click="status = 'published'" :class="status === 'published' ? 'border-green-500/50' : ''">
                        <input type="radio" name="status" value="published" x-model="status" class="hidden">
                        <span class="w-3 h-3 rounded-full border-2 border-[#333] flex items-center justify-center" :class="status === 'published' ? 'border-green-500 bg-green-500' : ''"></span>
                        <span class="text-xs font-bold uppercase tracking-widest" :class="status === 'published' ? 'text-green-500' : 'text-gray-500'">Yayında</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-[#080808] border border-[#222] rounded-2xl cursor-pointer hover:border-orange transition-all" @click="status = 'draft'" :class="status === 'draft' ? 'border-orange/50' : ''">
                        <input type="radio" name="status" value="draft" x-model="status" class="hidden">
                        <span class="w-3 h-3 rounded-full border-2 border-[#333] flex items-center justify-center" :class="status === 'draft' ? 'border-orange bg-orange' : ''"></span>
                        <span class="text-xs font-bold uppercase tracking-widest" :class="status === 'draft' ? 'text-orange' : 'text-gray-500'">Taslak</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-[#080808] border border-[#222] rounded-2xl cursor-pointer hover:border-orange transition-all" @click="status = 'passive'" :class="status === 'passive' ? 'border-red-500/50' : ''">
                        <input type="radio" name="status" value="passive" x-model="status" class="hidden">
                        <span class="w-3 h-3 rounded-full border-2 border-[#333] flex items-center justify-center" :class="status === 'passive' ? 'border-red-500 bg-red-500' : ''"></span>
                        <span class="text-xs font-bold uppercase tracking-widest" :class="status === 'passive' ? 'text-red-500' : 'text-gray-500'">Pasif</span>
                    </div>
                </div>

                <hr class="border-[#222]">

                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest px-1">Kapak Görseli</label>
                        <input type="file" name="cover_file" class="block w-full text-[10px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-[#1a1a1a] file:text-white hover:file:bg-[#222] cursor-pointer">
                        <input type="hidden" name="cover_image" value="<?= htmlspecialchars($page['cover_image'] ?? '') ?>">
                        <?php if(!empty($page['cover_image'])): ?>
                            <div class="relative group mt-2">
                                <img src="<?= getAdminAssetPath($page['cover_image']) ?>" class="w-full rounded-xl border border-[#222] opacity-50 group-hover:opacity-100 transition-all">
                                <p class="text-[9px] text-gray-600 mt-1 italic"><?= $page['cover_image'] ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest px-1">OG Görseli (Sosyal Medya)</label>
                        <input type="file" name="og_file" class="block w-full text-[10px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-[#1a1a1a] file:text-white hover:file:bg-[#222] cursor-pointer">
                        <input type="hidden" name="og_image" value="<?= htmlspecialchars($page['og_image'] ?? '') ?>">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#F15A24] text-white font-black py-4 rounded-2xl transition-all shadow-lg shadow-orange-600/20 uppercase tracking-widest text-xs">SAYFAYI KAYDET</button>
                    <?php if($id): ?>
                        <a href="../<?= $page['slug'] ?>" target="_blank" class="block text-center mt-4 text-[10px] font-bold text-gray-500 hover:text-white transition-all uppercase tracking-widest italic">Sitede Görüntüle</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</form>

<script>
function pageEditor() {
    return {
        title: '<?= addslashes($page['title'] ?? '') ?>',
        slug: '<?= addslashes($page['slug'] ?? '') ?>',
        slugManual: <?= ($id) ? 'true' : 'false' ?>,
        status: '<?= $page['status'] ?? 'draft' ?>',
        faqs: <?= json_encode(array_map(function($f){ return ['q'=>$f['question'], 'a'=>$f['answer']]; }, $faqs)) ?>,
        blocks: <?= json_encode(array_map(function($b){ return ['type'=>$b['block_type'], 'data'=>json_decode($b['block_data'], true)]; }, $blocks)) ?>,
        newBlockType: 'text',
        
        init() {
            this.$watch('title', (val) => {
                if (!this.slugManual) {
                    this.slug = val.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                }
            });
        },
        
        addFaq() {
            this.faqs.push({ q: '', a: '' });
        },
        removeFaq(index) {
            this.faqs.splice(index, 1);
        },
        
        addBlock() {
            let data = {};
            if (this.newBlockType === 'text') data = { title: '', text: '' };
            if (this.newBlockType === 'image_text') data = { title: '', text: '', image: '', align: 'left' };
            this.blocks.push({ type: this.newBlockType, data: data });
        },
        removeBlock(index) {
            this.blocks.splice(index, 1);
        },
        moveBlock(index, dir) {
            let newIdx = index + dir;
            if (newIdx < 0 || newIdx >= this.blocks.length) return;
            let temp = this.blocks[index];
            this.blocks[index] = this.blocks[newIdx];
            this.blocks[newIdx] = temp;
        }
    }
}

// CKEditor Entegrasyonu
ClassicEditor.create(document.querySelector('#editor'), {
    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
}).catch(error => console.error(error));
</script>

<style>
/* CKEditor Teması Uyumluluk */
.ck-editor__editable_inline { min-height: 400px; background-color: #080808 !important; color: #fff !important; border: 1px solid #222 !important; border-bottom-left-radius: 12px !important; border-bottom-right-radius: 12px !important; }
.ck.ck-toolbar { background-color: #1a1a1a !important; border: 1px solid #222 !important; border-top-left-radius: 12px !important; border-top-right-radius: 12px !important; }
.ck.ck-button { color: #fff !important; }
.ck.ck-icon { fill: currentColor !important; }
.ck.ck-dropdown__panel { background-color: #1a1a1a !important; border: 1px solid #333 !important; }
.ck.ck-list { background-color: #1a1a1a !important; }
.ck.ck-list__item:hover > .ck-button { background-color: #F15A24 !important; }
</style>

<?php require_once 'footer.php'; ?>

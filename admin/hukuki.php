<?php
/**
 * admin/hukuki.php - Hukuki Metin Yönetimi (Görünürlük Fixli)
 */
require_once '../includes/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['legal'] as $key => $val) {
        // ON DUPLICATE KEY UPDATE kullanarak kayıt yoksa oluşturur, varsa günceller
        $stmt = $db->prepare("INSERT INTO ayarlar (ayar_key, ayar_val) VALUES (?, ?) ON DUPLICATE KEY UPDATE ayar_val = ?");
        $stmt->execute([$key, $val, $val]);
    }
    $message = 'Hukuki metinler başarıyla güncellendi!';
}

// Mevcut metinleri çek
$texts = $db->query("SELECT * FROM ayarlar WHERE ayar_key LIKE 'politika_%'")->fetchAll(PDO::FETCH_ASSOC);
$legal = [];
foreach ($texts as $t) {
    $legal[$t['ayar_key']] = $t['ayar_val'];
}

require_once 'header.php';
?>

<div class="space-y-8 pb-20">
    <div>
        <h2 class="text-3xl font-black text-white tracking-tight uppercase">Hukuki Metinler</h2>
        <p class="text-gray-500 text-sm mt-1">Gizlilik, Kullanım ve Çerez metinlerini buradan düzenleyin.</p>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-4 rounded-2xl text-sm font-bold"><?php echo $message; ?></div>
    <?php
endif; ?>

    <form method="POST" class="space-y-10">
        
        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem]">
            <label class="block text-[#F15A24] text-[10px] font-black uppercase tracking-widest mb-4">Gizlilik Politikası</label>
            <textarea name="legal[politika_gizlilik]" id="editor_gizlilik"><?php echo $legal['politika_gizlilik'] ?? ''; ?></textarea>
        </div>

        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem]">
            <label class="block text-[#F15A24] text-[10px] font-black uppercase tracking-widest mb-4">Kullanım Şartları</label>
            <textarea name="legal[politika_kullanim]" id="editor_kullanim"><?php echo $legal['politika_kullanim'] ?? ''; ?></textarea>
        </div>

        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem]">
            <label class="block text-[#F15A24] text-[10px] font-black uppercase tracking-widest mb-4">Çerez Politikası</label>
            <textarea name="legal[politika_cerezler]" id="editor_cerezler"><?php echo $legal['politika_cerezler'] ?? ''; ?></textarea>
        </div>

        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem]">
            <label class="block text-[#F15A24] text-[10px] font-black uppercase tracking-widest mb-4">KVKK Aydınlatma Metni</label>
            <textarea name="legal[politika_kvkk]" id="editor_kvkk"><?php echo $legal['politika_kvkk'] ?? ''; ?></textarea>
        </div>

        <button type="submit" class="w-full bg-white text-black font-black py-6 rounded-3xl hover:bg-[#F15A24] hover:text-white transition-all uppercase tracking-[0.3em] text-sm shadow-2xl">
            TÜM HUKUKİ METİNLERİ KAYDET
        </button>
    </form>
</div>

<style>
    /* CKEditor içindeki metni karanlık panelde görünür yapma */
    .ck-editor__main .ck-content {
        color: #111 !important; /* Yazıyı siyah yapar */
        background-color: #fff !important; /* Arka planı beyaz yapar */
        min-height: 250px;
    }
    /* Editörün üst barını panele uydurma */
    .ck-toolbar {
        background: #f8f9fa !important;
        border-radius: 20px 20px 0 0 !important;
        border: 1px solid #222 !important;
    }
    .ck-content {
        border-radius: 0 0 20px 20px !important;
        border: 1px solid #222 !important;
    }
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    const editors = ['#editor_gizlilik', '#editor_kullanim', '#editor_cerezler', '#editor_kvkk'];
    editors.forEach(id => {
        ClassicEditor.create(document.querySelector(id), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo' ]
        }).catch(error => { console.error(error); });
    });
</script>

<?php require_once 'footer.php'; ?>

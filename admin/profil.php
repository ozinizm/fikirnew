<?php
require_once 'header.php'; // Veritabanı ve oturum kontrolü header içinde var zaten.

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_pass = $_POST['current_pass'] ?? '';
    $new_pass = $_POST['new_pass'] ?? '';
    $confirm_pass = $_POST['confirm_pass'] ?? '';

    $admin_id = $_SESSION['admin_id'] ?? null;

    if (!$admin_id) {
        $error = 'Oturum bilgisi bulunamadı. Lütfen çıkış yapıp tekrar giriş yapın.';
    } elseif (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        $error = 'Lütfen tüm şifre alanlarını doldurun.';
    } elseif ($new_pass !== $confirm_pass) {
        $error = 'Yeni şifreler birbiriyle eşleşmiyor!';
    } elseif (strlen($new_pass) < 6) {
        $error = 'Yeni şifre en az 6 karakter olmalıdır.';
    } else {
        // Veritabanındaki mevcut şifreyi çek
        $stmt = $db->prepare("SELECT sifre FROM yoneticiler WHERE id = ? LIMIT 1");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            $error = 'Yönetici hesabı bulunamadı.';
        } else {
            // Login.php SHA1 kullandığı için mevcut şifreyi de SHA1 ile kontrol ediyoruz
            $current_pass_hash = sha1($current_pass);

            if (!hash_equals($admin['sifre'], $current_pass_hash)) {
                $error = 'Mevcut şifreniz hatalı!';
            } else {
                // Yeni şifreyi login.php ile uyumlu olacak şekilde SHA1 olarak kaydediyoruz
                $new_pass_hash = sha1($new_pass);

                $update = $db->prepare("UPDATE yoneticiler SET sifre = ? WHERE id = ?");
                $update->execute([$new_pass_hash, $admin_id]);

                $message = 'Şifreniz başarıyla güncellendi!';
            }
        }
    }
}
?>

<div class="max-w-2xl mx-auto space-y-8">
    <div>
        <h2 class="text-3xl font-black text-white tracking-tight uppercase">Profil & Güvenlik</h2>
        <p class="text-gray-500 text-sm mt-1">Panel giriş şifrenizi buradan güncelleyebilirsiniz.</p>
    </div>

    <?php if ($message): ?>
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-5 rounded-3xl text-sm font-bold">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-5 rounded-3xl text-sm font-bold">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] space-y-6 shadow-2xl">
        <div class="space-y-2">
            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Mevcut Şifre</label>
            <input type="password" name="current_pass" required class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24]">
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Yeni Şifre</label>
                <input type="password" name="new_pass" required minlength="6" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24]">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Yeni Şifre (Tekrar)</label>
                <input type="password" name="confirm_pass" required minlength="6" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24]">
            </div>
        </div>

        <button type="submit" class="w-full bg-white text-black font-black py-5 rounded-2xl hover:bg-[#F15A24] hover:text-white transition-all uppercase tracking-widest text-sm">
            ŞİFREYİ GÜNCELLE
        </button>
    </form>
</div>

<?php require_once 'footer.php'; ?>
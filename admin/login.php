<?php
/**
 * login.php - Admin Login Screen
 */
session_start();
require_once '../includes/db.php';

// Zaten giriş yapılmışsa panele yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Mevcut sistem SHA1 kullandığı için login kontrolü SHA1 ile devam ediyor
    $hashed_password = sha1($password);

    try {
        $stmt = $db->prepare("
            SELECT id, kullanici_adi, sifre 
            FROM yoneticiler 
            WHERE kullanici_adi = :username 
              AND sifre = :sifre 
            LIMIT 1
        ");

        $stmt->execute([
            'username' => $username,
            'sifre' => $hashed_password
        ]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            session_regenerate_id(true);

            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_user'] = $admin['kullanici_adi'];

            header('Location: /admin/index.php');
            exit;
        } else {
            $error = 'Hatalı kullanıcı adı veya şifre!';
        }
    } catch (PDOException $e) {
        $error = 'Veritabanı Hatası: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş | Fikir Creative Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Barlow+Condensed:wght@900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0d0d0d; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-md bg-[#141414] border border-[#222] rounded-3xl p-10 shadow-2xl">
        <div class="text-center mb-10">
            <h1 class="font-condensed font-black text-white text-4xl tracking-tight uppercase">
                <span class="text-[#F15A24]">FİKİR</span> CANAVAR
            </h1>
            <p class="text-gray-500 text-sm mt-2 font-medium">Lütfen admin bilgilerinizle giriş yapın.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 text-sm p-4 rounded-xl mb-6 text-center">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6" autocomplete="off">
            <div>
                <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 ml-1">Kullanıcı Adı</label>
                <input 
                    type="text" 
                    name="username" 
                    required
                    autocomplete="username"
                    class="w-full bg-[#1a1a1a] border border-[#333] text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-[#F15A24] transition-all">
            </div>

            <div>
                <label class="block text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 ml-1">Şifre</label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    autocomplete="current-password"
                    class="w-full bg-[#1a1a1a] border border-[#333] text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-[#F15A24] transition-all">
            </div>

            <button type="submit" 
                    class="w-full bg-[#F15A24] hover:bg-[#d84a1a] text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-orange-600/20 mt-4">
                PANELE GİRİŞ YAP
            </button>
        </form>

        <div class="text-center mt-10">
            <a href="/" class="text-gray-600 text-xs hover:text-white transition-colors underline underline-offset-4">Siteye Geri Dön</a>
        </div>
    </div>

</body>
</html>

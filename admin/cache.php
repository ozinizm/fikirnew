<?php
/**
 * cache.php - Cache Yönetimi
 */
require_once 'includes/auth.php';
require_once '../includes/db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$messageType = '';

function log_action($action, $result, $error = '') {
    $log_file = __DIR__ . '/logs/cache_actions.log';
    $date = date('Y-m-d H:i:s');
    $user = "Admin";
    $log_entry = sprintf("[%s] User: %s | Action: %s | Result: %s | Error: %s" . PHP_EOL, $date, $user, $action, $result, $error);
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Geçersiz istek (CSRF Token Hatalı).');
    }
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'clear_app_cache') {
        $allowed_folders = ['../cache', '../tmp'];
        $deletedFiles = 0;
        $folderFound = false;
        
        foreach ($allowed_folders as $folder) {
            $realpath = realpath(__DIR__ . '/' . $folder);
            $basepath = realpath(__DIR__ . '/../');
            
            if ($realpath && strpos($realpath, $basepath) === 0 && is_dir($realpath)) {
                $folderFound = true;
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($realpath, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                
                foreach ($files as $fileinfo) {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    if ($fileinfo->getFilename() !== '.htaccess' && $fileinfo->getFilename() !== 'index.html') {
                        @$todo($fileinfo->getRealPath());
                        $deletedFiles++;
                    }
                }
            }
        }
        
        if ($folderFound) {
            $message = "Uygulama cache temizlendi. ($deletedFiles dosya silindi)";
            $messageType = "success";
            log_action("clear_app_cache", "Success", "$deletedFiles files deleted");
        } else {
            $message = "Temizlenecek uygulama cache klasörü bulunamadı.";
            $messageType = "info";
            log_action("clear_app_cache", "Not Found", "No cache folder exists");
        }
    } 
    elseif ($action === 'clear_opcache') {
        if (function_exists('opcache_reset')) {
            if (opcache_reset()) {
                $message = "OPcache başarıyla temizlendi.";
                $messageType = "success";
                log_action("clear_opcache", "Success");
            } else {
                $message = "OPcache temizlenemedi (sunucu izin vermiyor olabilir).";
                $messageType = "error";
                log_action("clear_opcache", "Failed", "opcache_reset returned false");
            }
        } else {
            $message = "OPcache aktif değil veya bu sunucuda izin verilmiyor.";
            $messageType = "error";
            log_action("clear_opcache", "Failed", "opcache_reset function does not exist");
        }
    }
    elseif ($action === 'renew_asset_version') {
        $new_version = time();
        $stmt = $db->prepare("INSERT INTO ayarlar (ayar_key, ayar_val) VALUES (?, ?) ON DUPLICATE KEY UPDATE ayar_val = ?");
        $stmt->execute(['cache_buster', $new_version, $new_version]);
        $message = "Asset / Schema versiyonu güncellendi. Yeni versiyon: " . $new_version;
        $messageType = "success";
        log_action("renew_asset_version", "Success", "New version: " . $new_version);
    }
    elseif ($action === 'litespeed_purge') {
        if (!headers_sent()) {
            header("X-LiteSpeed-Purge: *");
            $message = "Purge isteği gönderildi. Kaynak koddan doğrulama yapınız.";
            $messageType = "success";
            log_action("litespeed_purge", "Success", "Purge header sent");
        } else {
            $message = "Sunucu veya çıktı sırası nedeniyle purge header gönderilemedi.";
            $messageType = "error";
            log_action("litespeed_purge", "Failed", "Headers already sent");
        }
    }
}

$current_version = $db->query("SELECT ayar_val FROM ayarlar WHERE ayar_key = 'cache_buster'")->fetchColumn() ?: '';

$current_page = 'cache.php';
require_once 'header.php';
?>

<div class="max-w-5xl mx-auto space-y-8 pb-20">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight uppercase">Cache Yönetimi</h2>
            <p class="text-gray-500 text-sm mt-1">Uygulama cache, OPcache, LiteSpeed ve versiyon yenileme ayarlarını buradan yönetin.</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="p-5 rounded-3xl text-sm font-bold animate-pulse <?php echo $messageType === 'success' ? 'bg-green-500/10 border border-green-500/20 text-green-500' : ($messageType === 'info' ? 'bg-blue-500/10 border border-blue-500/20 text-blue-500' : 'bg-red-500/10 border border-red-500/20 text-red-500'); ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="bg-yellow-500/10 border border-yellow-500/20 p-5 rounded-3xl text-yellow-500 text-sm font-semibold mb-6 leading-relaxed">
        <strong>Manuel Kontrol Uyarısı:</strong><br/>
        Cache temizliği Google Search Console veya Google arama sonuçlarını anında güncellemez. GSC ve SERP güncellemeleri birkaç gün/hafta sürebilir.<br/>
        İşlem sonrası kaynak kodda eski /images/logo.png kırık logo yolu görünmemelidir. Güncel schema logo URL'si 200 OK dönmelidir.
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-4 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> Sistem Önbelleği
            </h3>
            <p class="text-gray-400 text-xs mb-6 h-12">Sunucu üzerinde barındırılan sistem içi geçici (/cache, /tmp) klasörlerini temizler.</p>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="clear_app_cache">
                <button type="submit" class="w-full bg-[#222] hover:bg-[#F15A24] hover:text-white text-gray-300 font-black py-4 rounded-2xl transition-all duration-300 uppercase tracking-widest text-xs">
                    Uygulama Cache Temizle
                </button>
            </form>
        </div>

        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-4 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> PHP OPcache
            </h3>
            <p class="text-gray-400 text-xs mb-6 h-12">PHP scriptlerinin derlenmiş hallerini temizleyerek kod değişikliklerinin sunucuda hemen uygulanmasını sağlar.</p>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="clear_opcache">
                <button type="submit" class="w-full bg-[#222] hover:bg-[#F15A24] hover:text-white text-gray-300 font-black py-4 rounded-2xl transition-all duration-300 uppercase tracking-widest text-xs">
                    PHP OPcache Temizle
                </button>
            </form>
        </div>

        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-4 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> Asset & Schema Versiyon
            </h3>
            <p class="text-gray-400 text-xs mb-6 h-12">Logo, favicon ve css/js güncellemelerinden sonra botlara ve tarayıcılara yeni versiyon gösterir. Mevcut sürüm: <strong class="text-white"><?php echo $current_version ? $current_version : 'Yok'; ?></strong></p>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="renew_asset_version">
                <button type="submit" class="w-full bg-[#222] hover:bg-[#F15A24] hover:text-white text-gray-300 font-black py-4 rounded-2xl transition-all duration-300 uppercase tracking-widest text-xs">
                    Versiyon Yenile
                </button>
            </form>
        </div>

        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-4 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> LiteSpeed Cache
            </h3>
            <p class="text-gray-400 text-xs mb-6 h-12">Sunucu destekliyorsa LiteSpeed page cache temizleme isteği gönderir. Desteklenmiyorsa hosting desteği gerekir.</p>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="litespeed_purge">
                <button type="submit" class="w-full bg-[#222] hover:bg-[#F15A24] hover:text-white text-gray-300 font-black py-4 rounded-2xl transition-all duration-300 uppercase tracking-widest text-xs">
                    LiteSpeed Purge İsteği Gönder
                </button>
            </form>
        </div>
    </div>

    <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem] shadow-2xl mt-8">
        <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-6 flex items-center gap-3">
            <span class="w-8 h-[1px] bg-[#F15A24]"></span> Hızlı Kontrol Linkleri
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <a href="view-source:https://fikircreative.com/" target="_blank" class="block bg-[#080808] border border-[#222] hover:border-[#F15A24] p-4 rounded-2xl transition-all group">
                <span class="block text-xs font-bold text-gray-400 group-hover:text-white">Ana Sayfa Kaynak Kodu</span>
            </a>
            <a href="https://fikircreative.com/sitemap.xml" target="_blank" class="block bg-[#080808] border border-[#222] hover:border-[#F15A24] p-4 rounded-2xl transition-all group">
                <span class="block text-xs font-bold text-gray-400 group-hover:text-white">Sitemap.xml Testi</span>
            </a>
            <a href="https://fikircreative.com/robots.txt" target="_blank" class="block bg-[#080808] border border-[#222] hover:border-[#F15A24] p-4 rounded-2xl transition-all group">
                <span class="block text-xs font-bold text-gray-400 group-hover:text-white">Robots.txt Testi</span>
            </a>
            <a href="https://fikircreative.com/favicon.ico" target="_blank" class="block bg-[#080808] border border-[#222] hover:border-[#F15A24] p-4 rounded-2xl transition-all group">
                <span class="block text-xs font-bold text-gray-400 group-hover:text-white">Güncel Favicon Testi</span>
            </a>
            <a href="<?php echo htmlspecialchars('../' . ($admin_settings['logo_dark'] ?? '')); ?>" target="_blank" class="block bg-[#080808] border border-[#222] hover:border-[#F15A24] p-4 rounded-2xl transition-all group">
                <span class="block text-xs font-bold text-gray-400 group-hover:text-white">Schema Logo Kontrolü</span>
            </a>
            <a href="https://search.google.com/search-console" target="_blank" class="block bg-[#080808] border border-[#222] hover:border-blue-500 p-4 rounded-2xl transition-all group">
                <span class="block text-xs font-bold text-gray-400 group-hover:text-blue-500">Google Search Console</span>
            </a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

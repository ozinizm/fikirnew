<?php
/**
 * header.php - Admin Panel Header & Sidebar
 */
require_once 'includes/auth.php';
require_once '../includes/db.php';

// Get current page to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);

// Generate API Token
if (empty($_SESSION['admin_api_token'])) {
    $_SESSION['admin_api_token'] = bin2hex(random_bytes(32));
}

// Ayarları Çek
$admin_settings_raw = $db->query("SELECT * FROM ayarlar")->fetchAll();
$admin_settings = [];
foreach ($admin_settings_raw as $s) {
    $k = isset($s['ayar_key']) ? $s['ayar_key'] : $s[0];
    $v = isset($s['ayar_val']) ? $s['ayar_val'] : $s[1];
    $admin_settings[$k] = $v;
}
$admin_logo = !empty($admin_settings['admin_logo']) ? $admin_settings['admin_logo'] : (!empty($admin_settings['logo_on_dark']) ? $admin_settings['logo_on_dark'] : '');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canavar Panel | Fikir Creative</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Barlow+Condensed:wght@900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0d0d0d; color: #fff; }
        .sidebar-link.active { background-color: #F15A24; color: #fff; }
        .sidebar-link:not(.active):hover { background-color: #1a1a1a; }
    </style>
    <script>
        window.ADMIN_API_TOKEN = '<?php echo $_SESSION['admin_api_token']; ?>';
    </script>
</head>
<body class="bg-[#080808]">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 bg-[#0d0d0d] border-r border-[#1a1a1a] w-64 transform transition-transform duration-300 z-50 overflow-y-auto"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="px-8 py-10 flex flex-col h-full">
                <!-- Logo -->
                <div class="mb-12">
                    <a href="index.php" class="block">
                    <?php if(!empty($admin_logo)): ?>
                        <img src="<?php echo getAdminAssetPath($admin_logo); ?>" alt="Admin Logo" class="h-10 object-contain">
                    <?php else: ?>
                        <h2 class="font-condensed font-black text-white text-2xl tracking-tighter">
                            <span class="text-[#F15A24]">FİKİR</span> CANAVAR
                        </h2>
                    <?php endif; ?>
                    </a>
                </div>

                <!-- Nav -->
                <nav class="flex-1 space-y-2">
                    <a href="index.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                         Dashboard
                    </a>
                    
                    <a href="portfolio.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'portfolio.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                         Portfolyo
                    </a>

                    <a href="services.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'services.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.7-3.7a1 1 0 000-1.4l-1.6-1.6a1 1 0 00-1.4 0l-3.7 3.7z"/><path d="M13.4 8.6L9 13"/><path d="M2 22l5-5"/><path d="M8 8l2 2"/><path d="M14 14l2 2"/><path d="M18 18l2 2"/></svg>
                         Hizmetler
                    </a>

                    <a href="istatistikler.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'istatistikler.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                         İstatistikler
                    </a>

                    <a href="referanslar.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'referanslar.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                         Müşteri Yorumları
                    </a>

                    <a href="markalar.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'markalar.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                         Referans Logoları
                    </a>


                    <a href="ekibimiz.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'ekibimiz.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                         Ekip Yönetimi
                    </a>

                    <a href="mesajlar.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'mesajlar.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                         Gelen Mesajlar
                    </a>

                    <a href="blog.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'blog.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                         Blog
                    </a>

                    <a href="pages.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo in_array($current_page, ['pages.php', 'page_edit.php']) ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                         Sayfa Yönetimi
                    </a>

                    <!-- LINK HUB MODÜLÜ -->
                    <div x-data="{ linkHubOpen: <?php echo in_array($current_page, ['link_hub_links.php', 'link_hub_settings.php', 'link_hub_social.php', 'link_hub_seo.php', 'link_hub_stats.php']) ? 'true' : 'false'; ?> }">
                        <button @click="linkHubOpen = !linkHubOpen" class="w-full sidebar-link flex items-center justify-between px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all hover:text-white <?php echo in_array($current_page, ['link_hub_links.php', 'link_hub_settings.php', 'link_hub_social.php', 'link_hub_seo.php', 'link_hub_stats.php']) ? 'text-white' : ''; ?>">
                            <div class="flex items-center gap-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-pink-500"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                <span class="font-bold uppercase tracking-widest text-xs">Link Hub</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-300" :class="linkHubOpen ? 'rotate-180 text-[#F15A24]' : ''"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        
                        <div x-show="linkHubOpen" x-transition class="pl-12 pr-4 pt-2 pb-2 space-y-1">
                            <a href="link_hub_links.php" class="block py-2 text-xs font-semibold transition-all <?php echo $current_page == 'link_hub_links.php' ? 'text-[#F15A24]' : 'text-gray-500 hover:text-white'; ?>">• Bağlantılar</a>
                            <a href="link_hub_settings.php" class="block py-2 text-xs font-semibold transition-all <?php echo $current_page == 'link_hub_settings.php' ? 'text-[#F15A24]' : 'text-gray-500 hover:text-white'; ?>">• Profil / Hero Ayarları</a>
                            <a href="link_hub_social.php" class="block py-2 text-xs font-semibold transition-all <?php echo $current_page == 'link_hub_social.php' ? 'text-[#F15A24]' : 'text-gray-500 hover:text-white'; ?>">• Sosyal Linkler</a>
                            <a href="link_hub_seo.php" class="block py-2 text-xs font-semibold transition-all <?php echo $current_page == 'link_hub_seo.php' ? 'text-[#F15A24]' : 'text-gray-500 hover:text-white'; ?>">• SEO & Tracking</a>
                            <a href="link_hub_stats.php" class="block py-2 text-xs font-semibold transition-all <?php echo $current_page == 'link_hub_stats.php' ? 'text-[#F15A24]' : 'text-gray-500 hover:text-white'; ?>">• Tıklama İstatistikleri</a>
                        </div>
                    </div>

                    <a href="settings.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                         Site Ayarları
                    </a>

                    <a href="cache.php" class="sidebar-link flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-gray-400 transition-all <?php echo $current_page == 'cache.php' ? 'active' : ''; ?>">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                         Cache Yönetimi
                    </a>

                    <a href="hukuki.php" class="flex items-center gap-4 px-6 py-4 text-sm font-bold transition-all <?= (basename($_SERVER['PHP_SELF']) == 'hukuki.php') ? 'bg-[#F15A24] text-white rounded-2xl shadow-lg' : 'text-gray-500 hover:text-white' ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
    <span>Hukuki Metinler</span>
</a>

<a href="profil.php" class="flex items-center gap-4 px-6 py-4 text-sm font-bold transition-all <?= (basename($_SERVER['PHP_SELF']) == 'profil.php') ? 'bg-[#F15A24] text-white rounded-2xl shadow-lg' : 'text-gray-500 hover:text-white' ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
    <span>Profil & Güvenlik</span>
</a>
                </nav>
                    <a href="hukuki.php" class="flex items-center gap-4 px-6 py-4 text-sm font-bold transition-all <?php echo(basename($_SERVER['PHP_SELF']) == 'hukuki.php') ? 'bg-[#F15A24] text-white rounded-2xl shadow-lg shadow-orange-600/20' : 'text-gray-500 hover:text-white'; ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
    <span>Hukuki Metinler</span>
</a>
                <!-- Footer -->
                <div class="mt-auto border-t border-[#1a1a1a] pt-8">
                    <div class="flex items-center gap-3 px-2 mb-6 text-gray-500">
                        <div class="w-8 h-8 rounded-full bg-[#1a1a1a] flex items-center justify-center font-bold text-[#F15A24]">A</div>
                        <div class="text-xs">
                            <p class="text-white font-semibold">Admin User</p>
                            <p>Canavar Panel v1.0</p>
                        </div>
                    </div>
                    <a href="logout.php" class="flex items-center gap-4 px-5 py-3.5 rounded-2xl text-sm font-semibold text-red-500 transition-all hover:bg-red-500/10">
                         <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                         Çıkış Yap
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden overflow-y-auto w-full transition-all duration-300 sm:pl-64"
              :class="sidebarOpen ? '' : 'sm:pl-0'">
            
            <!-- Navbar -->
            <header class="sticky top-0 z-40 bg-[#080808]/80 backdrop-blur-xl border-b border-[#1a1a1a] px-8 py-5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button class="p-2 -ml-2 text-gray-400 hover:bg-[#1a1a1a] rounded-xl transition-all" @click="sidebarOpen = !sidebarOpen">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    <h1 class="text-sm font-bold text-gray-500 uppercase tracking-widest">
                        <?php
switch ($current_page) {
    case 'index.php':
        echo 'Dashboard';
        break;
    case 'portfolio.php':
        echo 'Portfolyo Yönetimi';
        break;
    case 'blog.php':
        echo 'Blog Yönetimi';
        break;
    case 'services.php':
        echo 'Hizmetler';
        break;
    case 'pages.php':
    case 'page_edit.php':
        echo 'Sayfa Yönetimi';
        break;
    case 'settings.php':
        echo 'Site Ayarları';
        break;
    case 'link_hub_links.php':
        echo 'Link Hub | Bağlantılar';
        break;
    case 'link_hub_settings.php':
        echo 'Link Hub | Ayarlar';
        break;
    case 'link_hub_social.php':
        echo 'Link Hub | Sosyal Linkler';
        break;
    case 'link_hub_seo.php':
        echo 'Link Hub | SEO & Tracking';
        break;
    case 'link_hub_stats.php':
        echo 'Link Hub | İstatistikler';
        break;
    default:
        echo 'Canavar Panel';
}
?>
                    </h1>
                </div>

                <div class="flex items-center gap-4">
                    <a href="/" target="_blank" class="text-xs font-bold text-gray-400 hover:text-[#F15A24] transition-all flex items-center gap-2 border border-[#222] px-4 py-2 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        SİTEYİ GÖRÜNTÜLE
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-8 pb-16">

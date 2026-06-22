<?php
/**
 * admin/index.php - Komuta Merkezi (Dashboard)
 */
require_once '../includes/db.php';
require_once 'db_migration_custom.php';
require_once 'header.php';

// --- İSTATİSTİKLERİ ÇEKELİM ---
// 1. Toplam Proje Sayısı
$total_projects = $db->query("SELECT COUNT(*) FROM portfolyo")->fetchColumn();

// 2. Toplam Hizmet Sayısı
$total_services = $db->query("SELECT COUNT(*) FROM hizmetler")->fetchColumn();

// 3. Toplam Blog Yazısı
$total_blog = $db->query("SELECT COUNT(*) FROM blog")->fetchColumn();

// 4. Okunmamış (Yeni) Mesaj Sayısı
$new_messages = $db->query("SELECT COUNT(*) FROM mesajlar WHERE okundu = 0")->fetchColumn();

// 5. Son Gelen 5 Mesajı Listeleme İçin Çekelim
$recent_messages = $db->query("SELECT * FROM mesajlar ORDER BY tarih DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="space-y-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight uppercase">Komuta Merkezi</h2>
            <p class="text-gray-500 text-sm mt-1">Hoş geldin Kaptan! İşte Fikir Creative'in dijital nabzı.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-gray-400 bg-[#111] px-4 py-2 rounded-full border border-[#222]">
                <?php echo date('d F Y'); ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem] relative overflow-hidden group hover:border-orange-500/50 transition-all">
            <div class="relative z-10">
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Yeni Mesajlar</p>
                <h3 class="text-white text-4xl font-black"><?php echo $new_messages; ?></h3>
                <a href="mesajlar.php" class="text-orange-500 text-[10px] font-bold mt-4 inline-block hover:underline">MESAJLARA GİT &rarr;</a>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
        </div>

        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem] relative overflow-hidden group hover:border-blue-500/50 transition-all">
            <div class="relative z-10">
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Toplam Proje</p>
                <h3 class="text-white text-4xl font-black"><?php echo $total_projects; ?></h3>
                <a href="portfolio.php" class="text-blue-500 text-[10px] font-bold mt-4 inline-block hover:underline">İŞLERİ YÖNET &rarr;</a>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
            </div>
        </div>

        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem] relative overflow-hidden group hover:border-green-500/50 transition-all">
            <div class="relative z-10">
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Blog Yazıları</p>
                <h3 class="text-white text-4xl font-black"><?php echo $total_blog; ?></h3>
                <a href="blog.php" class="text-green-500 text-[10px] font-bold mt-4 inline-block hover:underline">İÇERİKLER &rarr;</a>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            </div>
        </div>

        <div class="bg-[#111] border border-[#222] p-8 rounded-[2.5rem] relative overflow-hidden group hover:border-purple-500/50 transition-all">
            <div class="relative z-10">
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Aktif Hizmet</p>
                <h3 class="text-white text-4xl font-black"><?php echo $total_services; ?></h3>
                <a href="services.php" class="text-purple-500 text-[10px] font-bold mt-4 inline-block hover:underline">HİZMET LİSTESİ &rarr;</a>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-4">
            <h4 class="text-white font-black text-xs uppercase tracking-widest mb-6 px-2">Hızlı Aksiyonlar</h4>
            <a href="portfolio.php?action=add" class="flex items-center justify-between bg-white hover:bg-orange-500 hover:text-white text-black p-5 rounded-2xl transition-all font-bold text-sm">
                Yeni Proje Ekle
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            </a>
            <a href="blog.php?action=add" class="flex items-center justify-between bg-[#111] border border-[#222] hover:border-orange-500 text-white p-5 rounded-2xl transition-all font-bold text-sm">
                Blog Yazısı Yaz
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </a>
            <a href="settings.php" class="flex items-center justify-between bg-[#111] border border-[#222] hover:border-blue-500 text-white p-5 rounded-2xl transition-all font-bold text-sm">
                Site Ayarlarını Düzenle
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </a>
        </div>

        <div class="lg:col-span-2 bg-[#111] border border-[#222] rounded-[2.5rem] p-10 overflow-hidden">
            <h4 class="text-white font-black text-xs uppercase tracking-widest mb-8">Son Mesajlar</h4>
            <div class="space-y-6">
                <?php if (empty($recent_messages)): ?>
                    <p class="text-gray-600 text-sm italic">Henüz bir mesaj alınmadı.</p>
                <?php
endif; ?>
                
                <?php foreach ($recent_messages as $rm): ?>
                    <div class="flex items-center justify-between gap-4 group">
                        <div class="flex-1">
                            <p class="text-white font-bold text-sm group-hover:text-orange-500 transition-colors"><?php echo htmlspecialchars($rm['ad_soyad']); ?></p>
                            <p class="text-gray-500 text-xs mt-1 line-clamp-1"><?php echo htmlspecialchars($rm['konu']); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-600 text-[10px] font-bold"><?php echo date('d.m.Y', strtotime($rm['tarih'])); ?></p>
                            <a href="mesajlar.php" class="text-orange-500 text-[9px] font-black uppercase tracking-tighter hover:underline">Oku</a>
                        </div>
                    </div>
                    <div class="border-b border-[#222] last:hidden"></div>
                <?php
endforeach; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once 'footer.php'; ?>

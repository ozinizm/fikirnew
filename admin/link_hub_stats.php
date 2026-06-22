<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';
?>

<div class="max-w-6xl mx-auto pb-20" x-data="linkHubStats()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight uppercase">Link Hub | İstatistikler</h2>
            <p class="text-gray-500 text-sm mt-1">Ziyaretçi tıklamalarını ve etkileşimleri analiz edin.</p>
        </div>
        <button @click="fetchAll()" class="bg-[#222] text-white px-6 py-3 rounded-2xl font-bold uppercase tracking-widest text-xs hover:bg-white hover:text-[#222] transition-all">
            YENİLE
        </button>
    </div>

    <div x-show="loading" class="flex items-center justify-center py-20">
        <svg class="animate-spin h-8 w-8 text-[#F15A24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
    </div>

    <div x-show="!loading" style="display:none;" class="space-y-8">
        <!-- ÖZET KARTLARI -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-[#111] border border-[#222] p-8 rounded-3xl shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 opacity-5 group-hover:opacity-10 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20v-6M6 20V10M18 20V4"/></svg>
                </div>
                <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest mb-2">Toplam Tıklama</p>
                <h3 class="text-5xl font-black text-white" x-text="summary.total_clicks">0</h3>
            </div>
            
            <div class="bg-[#111] border border-[#222] p-8 rounded-3xl shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 opacity-5 group-hover:opacity-10 transition-all text-[#F15A24]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <p class="text-[10px] text-[#F15A24] font-black uppercase tracking-widest mb-2">Bugün Tıklama</p>
                <h3 class="text-5xl font-black text-[#F15A24]" x-text="summary.today_clicks">0</h3>
            </div>
            
            <div class="bg-[#111] border border-[#222] p-8 rounded-3xl shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 opacity-5 group-hover:opacity-10 transition-all text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                </div>
                <p class="text-[10px] text-blue-500 font-black uppercase tracking-widest mb-2">Aktif Link Sayısı</p>
                <h3 class="text-5xl font-black text-white" x-text="summary.active_links">0</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- EN ÇOK TIKLANANLAR -->
            <div class="bg-[#111] border border-[#222] rounded-[2.5rem] p-8 shadow-2xl">
                <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                    <span class="w-8 h-[1px] bg-[#F15A24]"></span> En Popüler Linkler (Tüm Zamanlar)
                </h3>
                
                <div class="space-y-4">
                    <template x-for="(link, idx) in topLinks" :key="idx">
                        <div class="flex items-center justify-between border-b border-[#222] pb-4 last:border-0 last:pb-0">
                            <div>
                                <p class="text-white font-bold text-sm" x-text="link.title"></p>
                                <p class="text-gray-500 text-[10px] mt-1" x-text="link.url"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-black text-lg" x-text="link.click_count"></p>
                                <p class="text-gray-500 text-[9px] uppercase tracking-widest">Tıklama</p>
                            </div>
                        </div>
                    </template>
                    <template x-if="topLinks.length === 0">
                        <p class="text-gray-500 text-sm text-center py-4">Veri bulunamadı.</p>
                    </template>
                </div>
            </div>

            <!-- SON TIKLAMALAR -->
            <div class="bg-[#111] border border-[#222] rounded-[2.5rem] p-8 shadow-2xl">
                <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                    <span class="w-8 h-[1px] bg-[#F15A24]"></span> Son Tıklamalar
                </h3>
                
                <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    <template x-for="(click, idx) in recentClicks" :key="idx">
                        <div class="flex items-center justify-between border-b border-[#222] pb-4 last:border-0 last:pb-0">
                            <div>
                                <p class="text-white font-bold text-sm" x-text="click.link_title"></p>
                                <p class="text-gray-500 text-[10px] mt-1" x-text="formatDate(click.clicked_at)"></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span x-show="click.device_type === 'desktop'" class="text-[9px] bg-[#222] text-gray-400 px-2 py-0.5 rounded uppercase tracking-widest font-bold">Masaüstü</span>
                                    <span x-show="click.device_type === 'mobile'" class="text-[9px] bg-[#222] text-gray-400 px-2 py-0.5 rounded uppercase tracking-widest font-bold">Mobil</span>
                                    <span x-show="click.utm_source" class="text-[9px] bg-blue-500/20 text-blue-500 px-2 py-0.5 rounded tracking-widest" x-text="click.utm_source"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="recentClicks.length === 0">
                        <p class="text-gray-500 text-sm text-center py-4">Veri bulunamadı.</p>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
</style>

<script>
function linkHubStats() {
    return {
        loading: true,
        summary: { total_clicks: 0, today_clicks: 0, active_links: 0 },
        topLinks: [],
        recentClicks: [],

        init() {
            this.fetchAll();
        },
        
        async fetchAll() {
            this.loading = true;
            try {
                const headers = { 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN };
                
                const [sumRes, topRes, recRes] = await Promise.all([
                    fetch('../api/index.php?request=admin/link-hub/stats/summary', { headers }),
                    fetch('../api/index.php?request=admin/link-hub/stats/top-links', { headers }),
                    fetch('../api/index.php?request=admin/link-hub/stats/recent-clicks', { headers })
                ]);
                
                if(sumRes.ok) this.summary = await sumRes.json();
                if(topRes.ok) this.topLinks = await topRes.json();
                if(recRes.ok) this.recentClicks = await recRes.json();
                
            } catch(e) {}
            this.loading = false;
        },

        formatDate(dateString) {
            if(!dateString) return '';
            const d = new Date(dateString);
            return d.toLocaleDateString('tr-TR', { day: '2-digit', month: 'short', hour: '2-digit', minute:'2-digit' });
        }
    }
}
</script>

<?php require_once 'footer.php'; ?>

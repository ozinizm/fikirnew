<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';
?>

<div class="max-w-5xl mx-auto space-y-8 pb-20" x-data="linkHubSeo()">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight uppercase">Link Hub | SEO & Tracking</h2>
            <p class="text-gray-500 text-sm mt-1">Arama motoru ayarlarını ve analiz kodlarını yönetin.</p>
        </div>
    </div>

    <!-- TOAST -->
    <div x-show="toast.show" x-transition class="fixed top-5 right-5 z-[9999] px-6 py-4 rounded-xl shadow-2xl font-bold text-sm" :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" style="display:none;">
        <span x-text="toast.message"></span>
    </div>

    <form @submit.prevent="saveSeo" class="space-y-8">
        
        <div class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] shadow-2xl relative">
            <div x-show="loading" class="absolute inset-0 bg-[#111]/80 backdrop-blur flex items-center justify-center rounded-[2.5rem] z-10">
                <svg class="animate-spin h-8 w-8 text-[#F15A24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle></svg>
            </div>

            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> SEO Ayarları
            </h3>
            
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">SEO Başlık (Title)</label>
                    <input type="text" x-model="form.title" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all" required>
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">SEO Açıklama (Meta Description)</label>
                    <textarea x-model="form.description" rows="3" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all"></textarea>
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Canonical URL</label>
                    <input type="text" x-model="form.canonical_url" placeholder="https://fikircreative.com/link" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-[#222] pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" x-model="form.robots_index" class="w-5 h-5 accent-[#F15A24]">
                        <span class="text-sm font-bold text-white">Arama Motorları İndekslesin (index)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" x-model="form.robots_follow" class="w-5 h-5 accent-[#F15A24]">
                        <span class="text-sm font-bold text-white">Linkleri Takip Etsin (follow)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> Open Graph (Sosyal Medya)
            </h3>
            
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">OG Başlık</label>
                    <input type="text" x-model="form.og_title" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">OG Açıklama</label>
                    <textarea x-model="form.og_description" rows="2" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all"></textarea>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">OG Görsel URL (1200x630)</label>
                    <input type="text" x-model="form.og_image_url" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-[#F15A24] transition-all">
                </div>
            </div>
        </div>

        <div class="bg-[#111] border border-[#222] p-10 rounded-[2.5rem] shadow-2xl">
            <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-10 flex items-center gap-3">
                <span class="w-8 h-[1px] bg-[#F15A24]"></span> Tracking & Analitik
            </h3>
            
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-500 uppercase tracking-widest pl-2">Google Analytics 4 ID (Örn: G-XXXXXXXXXX)</label>
                    <input type="text" x-model="form.ga4_id" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-blue-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-yellow-500 uppercase tracking-widest pl-2">Google Tag Manager ID (Örn: GTM-XXXXXXX)</label>
                    <input type="text" x-model="form.gtm_id" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-yellow-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest pl-2">Meta Pixel ID (Örn: 1234567890)</label>
                    <input type="text" x-model="form.meta_pixel_id" class="w-full bg-[#080808] border border-[#222] text-white p-5 rounded-2xl outline-none focus:border-blue-400 transition-all">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-[#F15A24] hover:bg-white hover:text-[#F15A24] text-white font-black py-6 rounded-3xl transition-all duration-500 uppercase tracking-[0.4em] text-sm shadow-xl shadow-orange-600/20" :disabled="loading">
            <span x-show="!loading">AYARLARI KAYDET</span>
            <span x-show="loading">KAYDEDİLİYOR...</span>
        </button>
    </form>
</div>

<script>
function linkHubSeo() {
    return {
        loading: true,
        toast: { show: false, message: '', type: 'success' },
        form: {
            title: '', description: '', canonical_url: '', 
            og_title: '', og_description: '', og_image_url: '', 
            robots_index: true, robots_follow: true, 
            ga4_id: '', gtm_id: '', meta_pixel_id: ''
        },
        init() {
            this.fetchSeo();
        },
        async fetchSeo() {
            this.loading = true;
            try {
                const res = await fetch('../api/index.php?request=admin/link-hub/seo', {
                    headers: { 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN }
                });
                const data = await res.json();
                if(res.ok && data) {
                    for(let k in this.form) {
                        if(data[k] !== undefined) {
                            if (k === 'robots_index' || k === 'robots_follow') {
                                this.form[k] = (data[k] == 1);
                            } else {
                                this.form[k] = data[k] || '';
                            }
                        }
                    }
                }
            } catch(e) {}
            this.loading = false;
        },
        async saveSeo() {
            this.loading = true;
            const payload = { ...this.form };
            payload.robots_index = payload.robots_index ? 1 : 0;
            payload.robots_follow = payload.robots_follow ? 1 : 0;

            try {
                const res = await fetch('../api/index.php?request=admin/link-hub/seo', {
                    method: 'PUT',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN 
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if(res.ok && data.success) {
                    this.showToast('Ayarlar başarıyla kaydedildi!', 'success');
                } else {
                    this.showToast('Bir hata oluştu.', 'error');
                }
            } catch(e) {
                this.showToast('Bağlantı hatası.', 'error');
            }
            this.loading = false;
        },
        showToast(msg, type) {
            this.toast.message = msg;
            this.toast.type = type;
            this.toast.show = true;
            setTimeout(() => { this.toast.show = false; }, 3000);
        }
    }
}
</script>

<?php require_once 'footer.php'; ?>

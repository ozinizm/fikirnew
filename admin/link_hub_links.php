<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';
?>

<div class="max-w-6xl mx-auto pb-20" x-data="linkHubLinks()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight uppercase">Link Hub | Bağlantılar</h2>
            <p class="text-gray-500 text-sm mt-1">Bio sayfanızda görünecek bağlantıları yönetin.</p>
        </div>
        <button @click="openModal()" class="bg-[#F15A24] text-white px-6 py-3 rounded-2xl font-bold uppercase tracking-widest text-xs hover:bg-white hover:text-[#F15A24] transition-all">
            + YENİ LİNK EKLE
        </button>
    </div>

    <!-- TOAST -->
    <div x-show="toast.show" x-transition class="fixed top-5 right-5 z-[9999] px-6 py-4 rounded-xl shadow-2xl font-bold text-sm" :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" style="display:none;">
        <span x-text="toast.message"></span>
    </div>

    <!-- LİSTE -->
    <div class="bg-[#111] border border-[#222] rounded-[2.5rem] p-8 shadow-2xl relative">
        <div x-show="loading" class="absolute inset-0 bg-[#111]/80 backdrop-blur flex items-center justify-center rounded-[2.5rem] z-10">
            <svg class="animate-spin h-8 w-8 text-[#F15A24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>

        <template x-if="links.length === 0 && !loading">
            <div class="text-center py-20">
                <div class="bg-[#080808] border border-[#222] p-12 rounded-3xl inline-block max-w-lg shadow-2xl">
                    <div class="w-16 h-16 bg-[#F15A24]/10 text-[#F15A24] rounded-2xl flex items-center justify-center text-2xl mx-auto mb-6">
                        <i class="fa-solid fa-link"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Henüz bağlantı eklenmedi.</h3>
                    <p class="text-gray-500 text-sm mb-8 leading-relaxed">İlk bağlantını ekleyerek Link Hub sayfanı oluşturmaya başla.</p>
                    <button @click="openModal()" class="bg-[#F15A24] text-white px-8 py-4 rounded-xl font-black uppercase tracking-widest text-xs hover:bg-white hover:text-[#F15A24] transition-all shadow-[0_10px_20px_rgba(241,90,36,0.3)] hover:shadow-[0_10px_20px_rgba(255,255,255,0.2)]">
                        + Yeni Link Ekle
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-3" x-show="links.length > 0">
            <template x-for="(link, index) in links" :key="link.id">
                <div class="group flex flex-col sm:flex-row sm:items-center justify-between bg-[#111] hover:bg-[#141414] border border-[#222] hover:border-[#2a2a2a] p-4 rounded-2xl transition-all duration-200 shadow-sm gap-3">
                    
                    <!-- Sol Taraf: Sıralama + İkon + Bilgiler -->
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <!-- Sıralama Okları -->
                        <div class="flex flex-col gap-0.5 shrink-0">
                            <button @click="moveUp(index)" :disabled="index === 0" class="w-6 h-6 flex items-center justify-center rounded text-gray-600 hover:text-white hover:bg-white/10 disabled:opacity-20 transition-all" title="Yukarı Taşı">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            </button>
                            <button @click="moveDown(index)" :disabled="index === links.length - 1" class="w-6 h-6 flex items-center justify-center rounded text-gray-600 hover:text-white hover:bg-white/10 disabled:opacity-20 transition-all" title="Aşağı Taşı">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                        </div>

                        <!-- İkon Badge -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center border shrink-0"
                             :class="link.color_mode === 'light' ? 'bg-white text-gray-800 border-white/20' : (link.color_mode === 'brand' ? 'bg-[#F15A24] text-white border-[#F15A24]/30' : 'bg-white/5 text-gray-300 border-white/10')">
                            <template x-if="link.icon">
                                <i :class="link.icon" class="text-sm"></i>
                            </template>
                            <template x-if="!link.icon">
                                <i class="fa-solid fa-link text-xs text-gray-500"></i>
                            </template>
                        </div>

                        <!-- Bilgiler -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-white font-semibold text-sm" x-text="link.title"></h4>
                                <!-- Status Badges -->
                                <span x-show="link.is_featured == 1" class="inline-flex items-center gap-1 bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[9px] px-1.5 py-0.5 rounded-md uppercase tracking-widest font-bold">
                                    <i class="fa-solid fa-star text-[8px]"></i> Öne Çıkan
                                </span>
                                <span x-show="link.is_active == 0" class="bg-red-500/10 border border-red-500/20 text-red-400 text-[9px] px-1.5 py-0.5 rounded-md uppercase tracking-widest font-bold">Pasif</span>
                                <span x-show="link.is_active == 1" class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] px-1.5 py-0.5 rounded-md uppercase tracking-widest font-bold">Aktif</span>
                            </div>
                            <p class="text-gray-500 text-[11px] truncate mt-0.5" x-text="link.url"></p>
                        </div>
                    </div>

                    <!-- Sağ Taraf: İstatistik + Aksiyonlar -->
                    <div class="flex items-center gap-3 shrink-0 sm:ml-2">
                        <!-- Tıklama Sayısı -->
                        <div class="flex items-center gap-1.5 bg-white/[0.03] border border-white/8 rounded-xl px-3 py-2" title="Toplam Tıklama">
                            <i class="fa-solid fa-chart-simple text-[#F15A24]/70 text-xs"></i>
                            <span class="text-white font-bold text-sm leading-none" x-text="link.click_count"></span>
                        </div>

                        <!-- Separator -->
                        <div class="w-px h-6 bg-white/10"></div>

                        <!-- Aksiyon Butonları -->
                        <div class="flex items-center gap-1">
                            <!-- Öne Çıkar Toggle -->
                            <button @click="toggleField(link, 'is_featured')" 
                                    class="relative group/btn w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200"
                                    :class="link.is_featured == 1 ? 'bg-amber-500/15 text-amber-400 hover:bg-amber-500/25' : 'text-gray-500 hover:bg-white/8 hover:text-gray-300'"
                                    title="Öne Çıkar">
                                <i class="fa-star text-sm" :class="link.is_featured == 1 ? 'fa-solid' : 'fa-regular'"></i>
                            </button>

                            <!-- Aktif/Pasif Toggle -->
                            <button @click="toggleField(link, 'is_active')" 
                                    class="relative w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200"
                                    :class="link.is_active == 1 ? 'bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/25' : 'bg-red-500/10 text-red-400 hover:bg-red-500/20'"
                                    :title="link.is_active == 1 ? 'Pasife Al' : 'Aktif Et'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                            </button>

                            <!-- Düzenle -->
                            <button @click="openModal(link)" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-blue-500/15 hover:text-blue-400 transition-all duration-200"
                                    title="Düzenle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>

                            <!-- Sil -->
                            <button @click="confirmDelete(link.id)" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-red-500/15 hover:text-red-400 transition-all duration-200"
                                    title="Sil">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- MODAL -->
    <div x-show="modal.show" style="display:none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-[#080808]/90 backdrop-blur-md" @click="closeModal()"></div>
        <div class="relative bg-[#111] border border-[#222] rounded-[2rem] p-6 md:p-8 w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-[#222]">
                <h3 class="text-xl font-black text-white uppercase tracking-widest flex items-center gap-3">
                    <span class="w-8 h-[2px] bg-[#F15A24]"></span>
                    <span x-text="modal.isEdit ? 'Bağlantıyı Düzenle' : 'Yeni Bağlantı Ekle'"></span>
                </h3>
                <button @click="closeModal()" class="text-gray-500 hover:text-white transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <form @submit.prevent="saveLink">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    
                    <!-- Sol Kolon: Temel Bilgiler -->
                    <div class="space-y-5">
                        <h4 class="text-[#F15A24] font-black text-[10px] uppercase tracking-[0.2em] mb-4">Temel Bilgiler</h4>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Başlık (Zorunlu)</label>
                            <input type="text" x-model="form.title" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-2xl outline-none focus:border-[#F15A24] transition-colors" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">URL (Zorunlu)</label>
                            <input type="url" x-model="form.url" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-2xl outline-none focus:border-[#F15A24] transition-colors" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Açıklama (Opsiyonel)</label>
                            <textarea x-model="form.description" rows="2" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-2xl outline-none focus:border-[#F15A24] transition-colors"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4 border-t border-[#222] pt-5">
                            <label class="flex items-center gap-3 cursor-pointer p-3 bg-[#080808] rounded-2xl border border-[#222] hover:border-[#F15A24]/50 transition-colors">
                                <input type="checkbox" x-model="form.is_active" class="w-5 h-5 accent-[#F15A24]">
                                <span class="text-xs font-bold text-white uppercase tracking-wider">Aktif</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer p-3 bg-pink-500/5 rounded-2xl border border-pink-500/20 hover:border-pink-500/50 transition-colors">
                                <input type="checkbox" x-model="form.is_featured" class="w-5 h-5 accent-pink-500">
                                <span class="text-xs font-bold text-pink-500 uppercase tracking-wider">Öne Çıkan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Sağ Kolon: Tasarım & UTM -->
                    <div class="space-y-5">
                        <h4 class="text-[#F15A24] font-black text-[10px] uppercase tracking-[0.2em] mb-4">Tasarım & Takip</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">İkon (FontAwesome)</label>
                                <input type="text" x-model="form.icon" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-2xl outline-none focus:border-[#F15A24] transition-colors">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Renk Modu</label>
                                <select x-model="form.color_mode" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-2xl outline-none focus:border-[#F15A24] transition-colors appearance-none">
                                    <option value="dark">Cam Efekti (Dark)</option>
                                    <option value="light">Beyaz (Light)</option>
                                    <option value="brand">Marka Rengi</option>
                                    <option value="gradient">Gradient</option>
                                </select>
                            </div>
                        </div>

                        <div x-show="form.color_mode === 'gradient'" class="grid grid-cols-2 gap-4 bg-[#080808] p-4 rounded-2xl border border-[#222]">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Grad Başlangıç</label>
                                <input type="text" x-model="form.gradient_from" class="w-full bg-[#111] border border-[#222] text-white p-3 rounded-xl outline-none" placeholder="#F15A24">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Grad Bitiş</label>
                                <input type="text" x-model="form.gradient_to" class="w-full bg-[#111] border border-[#222] text-white p-3 rounded-xl outline-none" placeholder="#FF0080">
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <label class="text-[10px] font-black text-blue-500 uppercase tracking-widest pl-2">UTM Etiketleri (Opsiyonel)</label>
                            <div class="grid grid-cols-3 gap-3">
                                <input type="text" x-model="form.utm_source" placeholder="Source (ig)" class="w-full bg-[#080808] border border-[#222] text-white p-3 rounded-xl outline-none text-xs focus:border-blue-500 transition-colors">
                                <input type="text" x-model="form.utm_medium" placeholder="Medium (bio)" class="w-full bg-[#080808] border border-[#222] text-white p-3 rounded-xl outline-none text-xs focus:border-blue-500 transition-colors">
                                <input type="text" x-model="form.utm_campaign" placeholder="Campaign" class="w-full bg-[#080808] border border-[#222] text-white p-3 rounded-xl outline-none text-xs focus:border-blue-500 transition-colors">
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="flex items-center gap-3 cursor-pointer p-3 bg-[#080808] rounded-2xl border border-[#222] hover:border-[#F15A24]/50 transition-colors">
                                <input type="checkbox" x-model="form.open_in_new_tab" class="w-5 h-5 accent-[#F15A24]">
                                <span class="text-xs font-bold text-white uppercase tracking-wider">Yeni Sekmede Aç</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-6 border-t border-[#222]">
                    <button type="button" @click="closeModal()" class="w-1/3 bg-transparent border border-[#333] text-white hover:bg-[#222] font-black text-xs uppercase tracking-widest py-5 rounded-2xl transition-all">İPTAL</button>
                    <button type="submit" class="w-2/3 bg-[#F15A24] hover:bg-white hover:text-[#F15A24] text-white font-black text-xs uppercase tracking-widest py-5 rounded-2xl transition-all shadow-[0_10px_20px_rgba(241,90,36,0.3)]">
                        <span x-show="!loading">KAYDET</span>
                        <span x-show="loading"><i class="fa-solid fa-spinner fa-spin mr-2"></i> KAYDEDİLİYOR...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function linkHubLinks() {
    const emptyForm = {
        id: null, title: '', description: '', url: '', icon: '', color_mode: 'dark',
        gradient_from: '', gradient_to: '', is_active: true, is_featured: false, open_in_new_tab: true,
        utm_source: '', utm_medium: '', utm_campaign: ''
    };

    return {
        loading: false,
        links: [],
        toast: { show: false, message: '', type: 'success' },
        modal: { show: false, isEdit: false },
        form: { ...emptyForm },
        
        getEmptyForm() {
            return { ...emptyForm };
        },

        init() {
            this.fetchLinks();
        },
        
        async fetchLinks() {
            this.loading = true;
            try {
                const res = await fetch('../api/index.php?request=admin/link-hub/links', {
                    headers: { 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN }
                });
                const data = await res.json();
                if(res.ok) this.links = data;
            } catch(e) {
                this.showToast('Linkler yüklenemedi.', 'error');
            }
            this.loading = false;
        },
        
        openModal(link = null) {
            if(link) {
                this.modal.isEdit = true;
                this.form = { ...link };
                // Convert DB 1/0 to true/false for checkboxes
                this.form.is_active = (this.form.is_active == 1);
                this.form.is_featured = (this.form.is_featured == 1);
                this.form.open_in_new_tab = (this.form.open_in_new_tab == 1);
            } else {
                this.modal.isEdit = false;
                this.form = this.getEmptyForm();
            }
            this.modal.show = true;
        },
        
        closeModal() {
            this.modal.show = false;
        },
        
        async saveLink() {
            this.loading = true;
            const url = this.modal.isEdit ? `../api/index.php?request=admin/link-hub/links/${this.form.id}` : `../api/index.php?request=admin/link-hub/links`;
            const method = this.modal.isEdit ? 'PUT' : 'POST';
            
            // Format before sending
            const payload = { ...this.form };
            payload.is_active = payload.is_active ? 1 : 0;
            payload.is_featured = payload.is_featured ? 1 : 0;
            payload.open_in_new_tab = payload.open_in_new_tab ? 1 : 0;

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN },
                    body: JSON.stringify(payload)
                });
                
                if(res.ok) {
                    const data = await res.json();
                    if(data.success) {
                        this.showToast('Başarıyla kaydedildi.', 'success');
                        this.closeModal();
                        this.fetchLinks();
                    } else {
                        this.showToast(data.message || 'Hata oluştu.', 'error');
                    }
                } else {
                    let errorMsg = 'Sunucu Hatası: ' + res.status;
                    try {
                        const errorData = await res.json();
                        errorMsg = errorData.error || errorData.message || errorMsg;
                        if(errorData.details) console.error("DB Error:", errorData.details);
                    } catch(jsonErr) {
                        const textData = await res.text();
                        console.error("Non-JSON Error Response:", textData);
                    }
                    this.showToast(errorMsg, 'error');
                }
            } catch(e) {
                console.error("Network Fetch Error:", e);
                this.showToast('Sunucuya ulaşılamadı.', 'error');
            }
            this.loading = false;
        },
        
        async confirmDelete(id) {
            if(!confirm('Bu linki silmek istediğinize emin misiniz?')) return;
            this.loading = true;
            try {
                const res = await fetch(`../api/index.php?request=admin/link-hub/links/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN }
                });
                if(res.ok) {
                    this.showToast('Link silindi.', 'success');
                    this.fetchLinks();
                }
            } catch(e) {
                this.showToast('Bağlantı hatası.', 'error');
            }
            this.loading = false;
        },
        
        async toggleField(link, field) {
            try {
                const res = await fetch(`../api/index.php?request=admin/link-hub/links/${link.id}/toggle`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN },
                    body: JSON.stringify({ field: field })
                });
                if(res.ok) {
                    link[field] = (link[field] == 1) ? 0 : 1;
                }
            } catch(e) {
                this.showToast('Hata.', 'error');
            }
        },

        async moveUp(index) {
            if (index === 0) return;
            const current = this.links[index];
            const prev = this.links[index - 1];
            // Swap array
            this.links[index] = prev;
            this.links[index - 1] = current;
            await this.updateSortOrders();
        },

        async moveDown(index) {
            if (index === this.links.length - 1) return;
            const current = this.links[index];
            const next = this.links[index + 1];
            // Swap array
            this.links[index] = next;
            this.links[index + 1] = current;
            await this.updateSortOrders();
        },

        async updateSortOrders() {
            const orders = this.links.map((link, idx) => ({ id: link.id, sort_order: idx }));
            try {
                await fetch(`../api/index.php?request=admin/link-hub/links/reorder`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN },
                    body: JSON.stringify({ orders: orders })
                });
            } catch(e) {
                console.error(e);
            }
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

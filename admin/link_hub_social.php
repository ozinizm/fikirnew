<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';
?>

<div class="max-w-6xl mx-auto pb-20" x-data="linkHubSocial()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight uppercase">Link Hub | Sosyal Linkler</h2>
            <p class="text-gray-500 text-sm mt-1">Bio sayfanızın altında görünecek ikon bağlantılarını yönetin.</p>
        </div>
        <button @click="openModal()" class="bg-[#F15A24] text-white px-6 py-3 rounded-2xl font-bold uppercase tracking-widest text-xs hover:bg-white hover:text-[#F15A24] transition-all">
            + SOSYAL LİNK EKLE
        </button>
    </div>

    <!-- TOAST -->
    <div x-show="toast.show" x-transition class="fixed top-5 right-5 z-[9999] px-6 py-4 rounded-xl shadow-2xl font-bold text-sm" :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" style="display:none;">
        <span x-text="toast.message"></span>
    </div>

    <!-- LİSTE -->
    <div class="bg-[#111] border border-[#222] rounded-[2.5rem] p-8 shadow-2xl relative">
        <div x-show="loading" class="absolute inset-0 bg-[#111]/80 backdrop-blur flex items-center justify-center rounded-[2.5rem] z-10">
            <svg class="animate-spin h-8 w-8 text-[#F15A24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle></svg>
        </div>

        <template x-if="links.length === 0 && !loading">
            <div class="text-center py-20">
                <div class="bg-[#080808] border border-[#222] p-12 rounded-3xl inline-block max-w-lg shadow-2xl">
                    <div class="w-16 h-16 bg-[#F15A24]/10 text-[#F15A24] rounded-2xl flex items-center justify-center text-2xl mx-auto mb-6">
                        <i class="fa-solid fa-share-nodes"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Henüz sosyal medya linki eklenmedi.</h3>
                    <p class="text-gray-500 text-sm mb-8 leading-relaxed">Ziyaretçilerinizi diğer platformlardaki hesaplarınıza yönlendirmek için ikonlar ekleyin.</p>
                    <button @click="openModal()" class="bg-[#F15A24] text-white px-8 py-4 rounded-xl font-black uppercase tracking-widest text-xs hover:bg-white hover:text-[#F15A24] transition-all shadow-[0_10px_20px_rgba(241,90,36,0.3)] hover:shadow-[0_10px_20px_rgba(255,255,255,0.2)]">
                        + Sosyal Link Ekle
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-4" x-show="links.length > 0">
            <template x-for="(link, index) in links" :key="link.id">
                <div class="group flex flex-col md:flex-row md:items-center justify-between bg-[#111] hover:bg-[#151515] border border-[#222] hover:border-[#F15A24]/50 p-5 rounded-3xl transition-all duration-300 shadow-xl relative overflow-hidden">
                    
                    <!-- Sol Taraf: Sıralama, İkon, Bilgiler -->
                    <div class="flex items-start md:items-center gap-4 md:gap-5 relative z-10">
                        <div class="flex flex-col gap-1 items-center bg-[#080808] p-1.5 rounded-lg border border-[#222]">
                            <button @click="moveUp(index)" :disabled="index === 0" class="text-gray-600 hover:text-[#F15A24] disabled:opacity-20 transition-colors"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"></polyline></svg></button>
                            <button @click="moveDown(index)" :disabled="index === links.length - 1" class="text-gray-600 hover:text-[#F15A24] disabled:opacity-20 transition-colors"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg></button>
                        </div>
                        
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-[#080808] border border-[#222] text-white shrink-0 shadow-lg">
                            <template x-if="link.icon">
                                <i :class="link.icon" class="text-xl"></i>
                            </template>
                            <template x-if="!link.icon">
                                <i class="fa-solid fa-share-nodes text-gray-500"></i>
                            </template>
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h4 class="text-white font-bold text-base tracking-wide" x-text="link.platform"></h4>
                                <span x-show="link.label" class="text-gray-400 text-[10px] bg-[#222] px-2 py-0.5 rounded-md uppercase tracking-wider font-bold" x-text="link.label"></span>
                                <span x-show="link.is_active == 0" class="bg-red-500/10 border border-red-500/20 text-red-500 text-[9px] px-2 py-0.5 rounded-md uppercase tracking-widest font-black">Pasif</span>
                                <span x-show="link.is_active == 1" class="bg-green-500/10 border border-green-500/20 text-green-500 text-[9px] px-2 py-0.5 rounded-md uppercase tracking-widest font-black">Aktif</span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="text-gray-500 text-[10px] font-medium truncate max-w-[200px] md:max-w-xs" x-text="link.url"></span>
                                <a :href="link.url" target="_blank" class="text-gray-600 hover:text-white"><i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Sağ Taraf: Butonlar -->
                    <div class="flex items-center gap-2 mt-4 md:mt-0 relative z-10 pt-4 md:pt-0 border-t md:border-t-0 border-[#222]">
                        <!-- Edit -->
                        <button @click="openModal(link)" class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white transition-all border border-blue-500/20">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </button>
                        <!-- Delete -->
                        <button @click="confirmDelete(link.id)" class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all border border-red-500/20">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
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
                    <span x-text="modal.isEdit ? 'Sosyal Linki Düzenle' : 'Yeni Sosyal Link Ekle'"></span>
                </h3>
                <button @click="closeModal()" class="text-gray-500 hover:text-white transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <form @submit.prevent="saveLink">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Sol Kolon -->
                    <div class="space-y-5">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Platform (Zorunlu)</label>
                            <input type="text" x-model="form.platform" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-2xl outline-none focus:border-[#F15A24] transition-colors" placeholder="Örn: Instagram" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">URL (Zorunlu)</label>
                            <input type="url" x-model="form.url" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-2xl outline-none focus:border-[#F15A24] transition-colors" required>
                        </div>
                    </div>
                    
                    <!-- Sağ Kolon -->
                    <div class="space-y-5">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">İkon (FontAwesome)</label>
                            <input type="text" x-model="form.icon" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-2xl outline-none focus:border-[#F15A24] transition-colors" placeholder="Örn: fab fa-instagram">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Görünür Etiket (Opsiyonel)</label>
                            <input type="text" x-model="form.label" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-2xl outline-none focus:border-[#F15A24] transition-colors" placeholder="Örn: @fikircreative">
                        </div>
                        
                        <div class="pt-3">
                            <label class="flex items-center gap-3 cursor-pointer p-3 bg-[#080808] rounded-2xl border border-[#222] hover:border-[#F15A24]/50 transition-colors">
                                <input type="checkbox" x-model="form.is_active" class="w-5 h-5 accent-[#F15A24]">
                                <span class="text-xs font-bold text-white uppercase tracking-wider">Aktif</span>
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
function linkHubSocial() {
    const emptyForm = { id: null, platform: '', label: '', url: '', icon: '', is_active: true };

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
                const res = await fetch('../api/index.php?request=admin/link-hub/social-links', {
                    headers: { 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN }
                });
                const data = await res.json();
                if(res.ok) this.links = data;
            } catch(e) {}
            this.loading = false;
        },
        
        openModal(link = null) {
            if(link) {
                this.modal.isEdit = true;
                this.form = { ...link };
                this.form.is_active = (this.form.is_active == 1);
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
            const url = this.modal.isEdit ? `../api/index.php?request=admin/link-hub/social-links/${this.form.id}` : `../api/index.php?request=admin/link-hub/social-links`;
            const method = this.modal.isEdit ? 'PUT' : 'POST';
            
            const payload = { ...this.form };
            payload.is_active = payload.is_active ? 1 : 0;

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
            if(!confirm('Bu sosyal linki silmek istediğinize emin misiniz?')) return;
            this.loading = true;
            try {
                const res = await fetch(`../api/index.php?request=admin/link-hub/social-links/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN }
                });
                if(res.ok) {
                    this.showToast('Silindi.', 'success');
                    this.fetchLinks();
                }
            } catch(e) {}
            this.loading = false;
        },

        async moveUp(index) {
            if (index === 0) return;
            const current = this.links[index];
            const prev = this.links[index - 1];
            this.links[index] = prev;
            this.links[index - 1] = current;
            await this.updateSortOrders();
        },

        async moveDown(index) {
            if (index === this.links.length - 1) return;
            const current = this.links[index];
            const next = this.links[index + 1];
            this.links[index] = next;
            this.links[index + 1] = current;
            await this.updateSortOrders();
        },

        async updateSortOrders() {
            const orders = this.links.map((link, idx) => ({ id: link.id, sort_order: idx }));
            try {
                await fetch(`../api/index.php?request=admin/link-hub/social-links/reorder`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN },
                    body: JSON.stringify({ orders: orders })
                });
            } catch(e) {}
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

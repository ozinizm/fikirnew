<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once 'header.php';
?>

<style>
/* Color Picker Styles */
.color-pair { display: flex; align-items: center; gap: 8px; }
.color-pair input[type="color"] {
    width: 40px; height: 40px; border: none; border-radius: 10px;
    cursor: pointer; padding: 2px; background: transparent;
    border: 1px solid rgba(255,255,255,0.1);
    flex-shrink: 0;
}
.color-pair input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; border-radius: 8px; }
.color-pair input[type="color"]::-webkit-color-swatch { border: none; border-radius: 8px; }
.color-pair .hex-input {
    flex: 1; background: #080808; border: 1px solid #222; color: #fff;
    padding: 10px 14px; border-radius: 12px; outline: none;
    font-size: 13px; font-family: 'JetBrains Mono', monospace;
    transition: border-color 0.2s;
}
.color-pair .hex-input:focus { border-color: #F15A24; }
</style>

<div class="max-w-7xl mx-auto space-y-8 pb-20" x-data="linkHubSettings()">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight uppercase">Link Hub | Ayarlar</h2>
            <p class="text-gray-500 text-sm mt-1">Public sayfa görünümünü ve tüm tasarım öğelerini yönetin.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-[10px] text-gray-500 uppercase tracking-widest">Hızlı Tema:</span>
            <button @click="applyPreset('premium_dark')" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider bg-white/5 border border-white/10 rounded-xl hover:border-[#F15A24]/50 hover:text-[#F15A24] transition-all">Fikir Premium</button>
            <button @click="applyPreset('luxury_dark')" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider bg-white/5 border border-white/10 rounded-xl hover:border-purple-500/50 hover:text-purple-400 transition-all">Luxury Dark</button>
            <button @click="applyPreset('warm_creative')" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider bg-white/5 border border-white/10 rounded-xl hover:border-orange-500/50 hover:text-orange-400 transition-all">Warm Creative</button>
            <button @click="applyPreset('soft_contrast')" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider bg-white/5 border border-white/10 rounded-xl hover:border-blue-500/50 hover:text-blue-400 transition-all">Soft Contrast</button>
        </div>
    </div>

    <!-- TOAST -->
    <div x-show="toast.show" x-transition class="fixed top-5 right-5 z-[9999] px-6 py-4 rounded-xl shadow-2xl font-bold text-sm" :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" style="display:none;">
        <span x-text="toast.message"></span>
    </div>

    <form @submit.prevent="saveSettings" class="space-y-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Sol Kolon: Form -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- 1. TEMEL BİLGİLER & LOGO -->
                <div class="bg-[#111] border border-[#222] p-8 rounded-[2rem] shadow-2xl relative">
                    <div x-show="loading" class="absolute inset-0 bg-[#111]/80 backdrop-blur flex items-center justify-center rounded-[2rem] z-10">
                        <svg class="animate-spin h-8 w-8 text-[#F15A24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                    <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                        <span class="w-8 h-[1px] bg-[#F15A24]"></span> 1. Temel Bilgiler & Logo
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Sayfa Adı (Tab Title)</label>
                            <input type="text" x-model="form.page_title" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Hero Başlık</label>
                            <input type="text" x-model="form.hero_title" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all" required>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Hero Açıklama</label>
                            <textarea x-model="form.hero_description" rows="2" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all"></textarea>
                        </div>
                        
                        <div class="md:col-span-2 border-t border-[#222] pt-6 space-y-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Logo Görünüm Tipi</label>
                                <select x-model="form.logo_display_mode" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                                    <option value="image">Görsel Logo Göster</option>
                                    <option value="initials">Sadece Harf (FC) Göster</option>
                                    <option value="none">Logo Gizle</option>
                                </select>
                            </div>
                            <div class="space-y-2" x-show="form.logo_display_mode === 'image'">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Link Hub Özel Logo URL <span class="text-gray-600">(Boşsa Site Ana Logosu Kullanılır)</span></label>
                                <div class="flex gap-3 items-center">
                                    <template x-if="form.logo_url">
                                        <img :src="form.logo_url" class="w-10 h-10 rounded-lg object-contain bg-white/5 border border-[#222]">
                                    </template>
                                    <input type="text" x-model="form.logo_url" placeholder="/brand/logo.png" class="flex-1 bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Logo Boyutu Desktop (px)</label>
                                    <input type="number" x-model="form.logo_size_desktop" min="40" max="160" step="4" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Logo Boyutu Mobil (px)</label>
                                    <input type="number" x-model="form.logo_size_mobile" min="32" max="120" step="4" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. SAYFA ARKA PLANI -->
                <div class="bg-[#111] border border-[#222] p-8 rounded-[2rem] shadow-2xl">
                    <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-6 flex items-center gap-3">
                        <span class="w-8 h-[1px] bg-[#F15A24]"></span> 2. Dış Sayfa Arka Planı (Page Background)
                    </h3>
                    <p class="text-xs text-gray-500 mb-6 pl-1">Ana kartın arkasında kalan sayfa alanının arka planı.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Arka Plan Tipi</label>
                            <select x-model="form.page_background_type" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                                <option value="color">Düz Renk</option>
                                <option value="gradient">Gradient</option>
                                <option value="image">Görsel</option>
                            </select>
                        </div>
                        <div class="space-y-2" x-show="form.page_background_type === 'color' || form.page_background_type === 'image'">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Renk</label>
                            <div class="color-pair">
                                <input type="color" :value="form.page_background_color || '#050505'" @input="form.page_background_color = $event.target.value">
                                <input type="text" class="hex-input" x-model="form.page_background_color" placeholder="#050505" @input="syncColorPicker($event, 'page_background_color')">
                            </div>
                        </div>
                        <div class="space-y-2" x-show="form.page_background_type === 'gradient'">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Başlangıç Rengi</label>
                            <div class="color-pair">
                                <input type="color" :value="form.page_background_gradient_from || '#050505'" @input="form.page_background_gradient_from = $event.target.value">
                                <input type="text" class="hex-input" x-model="form.page_background_gradient_from" placeholder="#050505" @input="syncColorPicker($event, 'page_background_gradient_from')">
                            </div>
                        </div>
                        <div class="space-y-2" x-show="form.page_background_type === 'gradient'">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Bitiş Rengi</label>
                            <div class="color-pair">
                                <input type="color" :value="form.page_background_gradient_to || '#1a0a0a'" @input="form.page_background_gradient_to = $event.target.value">
                                <input type="text" class="hex-input" x-model="form.page_background_gradient_to" placeholder="#1a0a0a" @input="syncColorPicker($event, 'page_background_gradient_to')">
                            </div>
                        </div>
                        <div class="space-y-2 md:col-span-2" x-show="form.page_background_type === 'image'">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Görsel URL</label>
                            <input type="text" x-model="form.page_background_image_url" placeholder="/images/page_bg.jpg" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                        </div>
                    </div>
                </div>

                <!-- 3. KART ARKA PLANI -->
                <div class="bg-[#111] border border-[#222] p-8 rounded-[2rem] shadow-2xl">
                    <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-6 flex items-center gap-3">
                        <span class="w-8 h-[1px] bg-[#F15A24]"></span> 3. İç Kart Arka Planı (Card Background)
                    </h3>
                    <p class="text-xs text-gray-500 mb-6 pl-1">Linklerin bulunduğu ana container'ın arka planı.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Arka Plan Tipi</label>
                            <select x-model="form.card_background_type" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                                <option value="glass">Cam Efekti (Glass)</option>
                                <option value="color">Düz Renk</option>
                                <option value="gradient">Gradient</option>
                                <option value="image">Görsel</option>
                            </select>
                        </div>
                        <div class="space-y-2" x-show="form.card_background_type === 'color' || form.card_background_type === 'image'">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Renk</label>
                            <div class="color-pair">
                                <input type="color" :value="form.card_background_color || '#111111'" @input="form.card_background_color = $event.target.value">
                                <input type="text" class="hex-input" x-model="form.card_background_color" placeholder="#111111" @input="syncColorPicker($event, 'card_background_color')">
                            </div>
                        </div>
                        <div class="space-y-2" x-show="form.card_background_type === 'gradient'">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Başlangıç Rengi</label>
                            <div class="color-pair">
                                <input type="color" :value="form.card_background_gradient_from || '#222222'" @input="form.card_background_gradient_from = $event.target.value">
                                <input type="text" class="hex-input" x-model="form.card_background_gradient_from" placeholder="#222222" @input="syncColorPicker($event, 'card_background_gradient_from')">
                            </div>
                        </div>
                        <div class="space-y-2" x-show="form.card_background_type === 'gradient'">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Bitiş Rengi</label>
                            <div class="color-pair">
                                <input type="color" :value="form.card_background_gradient_to || '#050505'" @input="form.card_background_gradient_to = $event.target.value">
                                <input type="text" class="hex-input" x-model="form.card_background_gradient_to" placeholder="#050505" @input="syncColorPicker($event, 'card_background_gradient_to')">
                            </div>
                        </div>
                        <div class="space-y-2 md:col-span-2" x-show="form.card_background_type === 'image'">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Görsel URL</label>
                            <input type="text" x-model="form.card_background_image_url" placeholder="/images/card_bg.jpg" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                        </div>
                        <div class="space-y-2" x-show="form.card_background_type === 'image'">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Overlay Karartma (0.0–1.0)</label>
                            <input type="number" step="0.05" min="0" max="1" x-model="form.card_background_overlay_opacity" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                        </div>
                        <div class="space-y-2 md:col-span-2 border-t border-[#222] pt-6">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Kart Arka Plan Video (Opsiyonel)</label>
                            <input type="text" x-model="form.background_video_url" placeholder="/images/bg.mp4" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                        </div>
                    </div>
                </div>

                <!-- 4. İLETİŞİM & FOOTER -->
                <div class="bg-[#111] border border-[#222] p-8 rounded-[2rem] shadow-2xl">
                    <h3 class="text-[#F15A24] font-black text-xs uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                        <span class="w-8 h-[1px] bg-[#F15A24]"></span> 4. İletişim & Footer
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Footer Metni</label>
                            <input type="text" x-model="form.footer_text" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">WhatsApp Numarası</label>
                            <input type="text" x-model="form.whatsapp" placeholder="+905551234567" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">E-posta</label>
                            <input type="email" x-model="form.email" class="w-full bg-[#080808] border border-[#222] text-white p-4 rounded-xl outline-none focus:border-[#F15A24] transition-all">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#F15A24] hover:bg-white hover:text-[#F15A24] text-white font-black py-6 rounded-[2rem] transition-all duration-500 uppercase tracking-[0.4em] text-sm shadow-xl shadow-orange-600/20" :disabled="loading">
                    <span x-show="!loading">AYARLARI KAYDET</span>
                    <span x-show="loading">KAYDEDİLİYOR...</span>
                </button>
            </div>
            
            <!-- Sağ Kolon: Canlı Önizleme -->
            <div class="lg:col-span-4">
                <div class="sticky top-10">
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-3 text-center">Canlı Önizleme</p>
                    <div class="border border-white/10 rounded-[3rem] p-3 overflow-hidden shadow-2xl relative" style="height: 680px;">
                        <!-- Page Bg -->
                        <div class="absolute inset-0 z-0 transition-all duration-500" :style="getPageBgStyle()"></div>
                        
                        <!-- Card -->
                        <div class="relative z-10 w-full h-full rounded-[2.5rem] border border-white/10 shadow-2xl overflow-hidden flex flex-col items-center p-5 text-center transition-all duration-500"
                             :class="form.card_background_type === 'glass' ? 'bg-black/30 backdrop-blur-3xl' : ''"
                             :style="getCardBgStyle()">
                            
                            <div x-show="form.card_background_type === 'image'" class="absolute inset-0 bg-black z-0 pointer-events-none" :style="'opacity: ' + (form.card_background_overlay_opacity || 0)"></div>

                            <div class="relative z-10 w-full flex flex-col items-center pt-6">
                                <!-- Share -->
                                <div class="w-full flex justify-end mb-4">
                                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8m-4-6l-4-4-4 4m4-4v13"/></svg>
                                    </div>
                                </div>

                                <!-- Logo Preview -->
                                <div x-show="form.logo_display_mode !== 'none'" class="mb-4">
                                    <div x-show="form.logo_display_mode === 'initials'" class="bg-white/10 border border-white/10 rounded-[1.25rem] flex items-center justify-center font-black text-lg text-white" :style="`width:${Math.min(form.logo_size_desktop||80, 80) * 0.75}px; height:${Math.min(form.logo_size_desktop||80, 80) * 0.75}px;`">FC</div>
                                    <div x-show="form.logo_display_mode === 'image'" class="bg-white/5 border border-white/10 p-2 rounded-[1.25rem] flex items-center justify-center" :style="`width:${Math.min(form.logo_size_desktop||80, 80) * 0.75}px; height:${Math.min(form.logo_size_desktop||80, 80) * 0.75}px;`">
                                        <template x-if="form.logo_url">
                                            <img :src="form.logo_url" class="max-w-full max-h-full object-contain">
                                        </template>
                                        <template x-if="!form.logo_url">
                                            <div class="text-[8px] text-gray-500 text-center">Logo</div>
                                        </template>
                                    </div>
                                </div>
                                
                                <h2 class="text-white font-black text-sm mb-1" x-text="form.hero_title || 'Hero Başlık'"></h2>
                                <p class="text-gray-400 text-[10px] mb-5" x-text="form.hero_description || 'Kısa açıklama...'"></p>
                                
                                <!-- Mock CTA -->
                                <div class="w-full bg-[#F15A24]/15 border border-[#F15A24]/30 rounded-xl p-3 mb-3 flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-[#F15A24] flex items-center justify-center shrink-0">
                                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <div class="text-white font-bold text-[11px] text-left">Teklif Al</div>
                                </div>
                                
                                <!-- Mock Links -->
                                <div class="w-full bg-white/5 border border-white/10 rounded-xl p-3 flex items-center justify-between mb-2">
                                    <div class="text-white font-semibold text-[11px]">Web Tasarım</div>
                                    <svg class="w-3 h-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </div>
                                <div class="w-full bg-white/5 border border-white/10 rounded-xl p-3 flex items-center justify-between">
                                    <div class="text-white font-semibold text-[11px]">Marka Kimliği</div>
                                    <svg class="w-3 h-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function linkHubSettings() {
    const presets = {
        premium_dark: {
            page_background_type: 'color', page_background_color: '#050505',
            card_background_type: 'glass', card_background_color: '',
            card_background_gradient_from: '', card_background_gradient_to: ''
        },
        luxury_dark: {
            page_background_type: 'gradient', page_background_gradient_from: '#07021a', page_background_gradient_to: '#150720',
            card_background_type: 'color', card_background_color: '#0d0a1a',
            card_background_gradient_from: '', card_background_gradient_to: ''
        },
        warm_creative: {
            page_background_type: 'gradient', page_background_gradient_from: '#0a0503', page_background_gradient_to: '#160a04',
            card_background_type: 'gradient', card_background_gradient_from: '#180d06', card_background_gradient_to: '#0a0503',
            card_background_color: ''
        },
        soft_contrast: {
            page_background_type: 'color', page_background_color: '#0a0d14',
            card_background_type: 'color', card_background_color: '#111520',
            card_background_gradient_from: '', card_background_gradient_to: ''
        }
    };

    return {
        loading: true,
        toast: { show: false, message: '', type: 'success' },
        form: {
            page_title: '', hero_title: '', hero_description: '', logo_url: '',
            background_video_url: '', footer_text: '', whatsapp: '', phone: '', email: '',
            page_background_type: 'color', page_background_color: '#050505',
            page_background_gradient_from: '', page_background_gradient_to: '', page_background_image_url: '',
            card_background_type: 'glass', card_background_color: '',
            card_background_gradient_from: '', card_background_gradient_to: '',
            card_background_image_url: '', card_background_overlay_opacity: 0.7,
            card_background_position: 'center', card_background_size: 'cover',
            logo_display_mode: 'image',
            logo_size_desktop: 80, logo_size_mobile: 64
        },
        applyPreset(name) {
            const p = presets[name];
            if (!p) return;
            for (let k in p) this.form[k] = p[k];
            this.showToast('Tema uygulandı. Kaydet\'e basın.', 'success');
        },
        syncColorPicker(e, field) {
            // Validate hex before assigning
            const val = e.target.value;
            if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                this.form[field] = val;
            }
        },
        getPageBgStyle() {
            if (this.form.page_background_type === 'color') return `background-color: ${this.form.page_background_color || '#050505'};`;
            if (this.form.page_background_type === 'gradient') return `background: linear-gradient(to bottom right, ${this.form.page_background_gradient_from || '#050505'}, ${this.form.page_background_gradient_to || '#1a0f0f'});`;
            if (this.form.page_background_type === 'image') return `background-image: url('${this.form.page_background_image_url}'); background-size: cover; background-position: center;`;
            return 'background-color: #050505;';
        },
        getCardBgStyle() {
            if (this.form.card_background_type === 'color') return `background-color: ${this.form.card_background_color || '#111'};`;
            if (this.form.card_background_type === 'gradient') return `background: linear-gradient(to bottom, ${this.form.card_background_gradient_from || '#222'}, ${this.form.card_background_gradient_to || '#050505'});`;
            if (this.form.card_background_type === 'image') return `background-image: url('${this.form.card_background_image_url}'); background-size: cover; background-position: center;`;
            return '';
        },
        init() { this.fetchSettings(); },
        async fetchSettings() {
            this.loading = true;
            try {
                const res = await fetch('../api/index.php?request=admin/link-hub/settings', {
                    headers: { 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN }
                });
                const data = await res.json();
                if (res.ok && data) {
                    for (let k in this.form) {
                        if (data[k] !== undefined && data[k] !== null) this.form[k] = data[k];
                    }
                    if (!this.form.page_background_type) this.form.page_background_type = 'color';
                    if (!this.form.card_background_type) this.form.card_background_type = 'glass';
                    if (!this.form.logo_display_mode) this.form.logo_display_mode = 'image';
                    if (!this.form.logo_size_desktop) this.form.logo_size_desktop = 80;
                    if (!this.form.logo_size_mobile) this.form.logo_size_mobile = 64;
                }
            } catch(e) {
                this.showToast('Veriler yüklenemedi.', 'error');
            }
            this.loading = false;
        },
        async saveSettings() {
            this.loading = true;
            try {
                const res = await fetch('../api/index.php?request=admin/link-hub/settings', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + window.ADMIN_API_TOKEN },
                    body: JSON.stringify(this.form)
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.showToast('Ayarlar kaydedildi!', 'success');
                } else {
                    this.showToast(data.error || 'Bir hata oluştu.', 'error');
                }
            } catch(e) {
                this.showToast('Bağlantı hatası.', 'error');
            }
            this.loading = false;
        },
        showToast(msg, type) {
            this.toast.message = msg; this.toast.type = type; this.toast.show = true;
            setTimeout(() => { this.toast.show = false; }, 3000);
        }
    }
}
</script>

<?php require_once 'footer.php'; ?>

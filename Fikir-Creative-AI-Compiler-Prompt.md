# Fikir Creative - AI Web Compiler Prompt & Master Blueprint

## 🤖 AI COMPILER SİSTEM PROMPTU (SYSTEM PROMPT)
**Rol:** Sen dünya standartlarında, Awwwards ödüllü bir Frontend/Full-Stack geliştiricisi ve UI/UX mühendisisin. Uzmanlık alanın Next.js, React, Tailwind CSS, GSAP, Framer Motion ve Lenis (smooth scroll) kullanarak ultra akıcı, yüksek performanslı ve fütüristik web siteleri inşa etmektir. 

**Görev:** Aşağıdaki referans linkleri ve detaylı mimari yönergeleri kullanarak, "Fikir Creative" adlı dijital büyüme ajansı için sıfırdan, tek sayfalık (single-page) dönüşüm odaklı ve kusursuz animasyonlara sahip bir web sitesi kodu üretmek. Tasarım dili, referans verilen Framer sitesindeki "aydınlık, premium, tipografi odaklı ve mikro-animasyonlu" yapıya birebir sadık kalmalıdır.

---

## 🔗 REFERANS BAĞLANTILARI
- **Mevcut Ajans Sitesi (Marka bağlamı ve mevcut hizmetleri anlamak için):** https://www.fikircreative.com/
- **Hedeflenen Tasarım, Animasyon ve UI Referansı (Bu sitenin ruhu, tipografisi ve animasyonları birebir klonlanıp Fikir Creative'e uyarlanacaktır):** https://graceful-delivers-524315.framer.app/

---

## 🎨 1. TASARIM SİSTEMİ (DESIGN SYSTEM)

* **Renk Paleti:**
    * Arka Plan: Saf Beyaz (`#FFFFFF`) ve İnci Grisi (`#F8F9FA`).
    * Vurgu & Gradientler: Mikro lila ve açık mavi yansımaları (`#E0AAFF` / `#BDE0FE` - sadece %5-%10 opaklık ile blur efekti olarak arka planda).
    * Metin Rengi: Saf Siyah (`#000000`) ve Kömür Grisi (`#212529`).
* **Tipografi:** Başlıklar için büyük, geometrik ve lüks sans-serif (Satoshi, Inter veya Plus Jakarta Sans - Bold/Medium). Gövde metinleri için yüksek okunurluklu düzenli sans-serif. Bolca negatif boşluk (white-space) kullanılmalı.
* **Efektler & Elementler:** Cam Morfizmleri (Glassmorphism), `backdrop-blur-md`, arka planı yarı şeffaf (`rgba(255,255,255,0.4)`) kartlar, çok ince şeffaf kenarlıklar (`border: 1px solid rgba(0,0,0,0.05)`).
* **Animasyon Altyapısı (Zorunlu):** * Sayfa kaydırma: Lenis (Smooth Scroll).
    * Tetikleyiciler ve Reveal efektleri: GSAP ScrollTrigger ve Framer Motion (fizik tabanlı spring animasyonları, kelime kelime text-reveal).
    * İmleç: Özel (Custom) imleç ve hover anında genişleyen/parlayan elementler.

---

## 🏗️ 2. SAYFA YAPISI VE METİN YAZARLIĞI (COPYWRITING)

### Bölüm A: Global Header (Navigasyon)
* **Düzen:** Sticky, `backdrop-blur` şeffaf şerit. Sol köşede minimal logo. Ortada menü linkleri (Çalışmalar, Hizmetler, Ajans, Paketler). Sağ köşede siyah dolgulu, pürüzsüz açılan "İletişim" butonu.
* **Sol Etiket:** "Yeni projeler için uygun" (Yanında yeşil renkte parlayan fütüristik canlı bir nokta).

### Bölüm B: Hero Section (Giriş Deneyimi)
* **Animasyon:** Yazılar ekrana kelime kelime aşağıdan yukarıya akıcı bir "spring" efektiyle girmeli. Ana başlığın kelimeleri arasına yerleştirilmiş mikro maskeli görseller veya looping video pencereleri (inline images) yer almalı.
* **Alt Etiket:** Yerel işletmeler için dijital büyüme sistemi.
* **Ana Başlık (H1):** "Sadece [İmaj 1] görünürlük değil, 
    müşteri [İmaj 2] kazandıran sistem kuran 
    Fikir [İmaj 3] Creative."
* **Açıklama:** Web sitesi, performans odaklı reklam yönetimi, video içerik üretimi ve WhatsApp dönüşüm altyapısıyla markanız için ölçülebilir bir dijital büyüme motoru inşa ediyoruz.
* **CTA:** "Ücretsiz Analiz Al" (Geniş oval yapı, hover anında hafif parlama).

### Bölüm C: Sosyal Kanıt Şeridi (Infinite Marquee)
* **Düzen:** Hero'nun altında, sonsuz döngüde sola kayan monokrom logo şeridi (Aura Studio, La Ruota Pizza, vb. yerel marka isimleri/logoları).

### Bölüm D: Ajans Manifestosu (Scroll-Reveal Alanı)
* **Animasyon:** Ekranın ortasında sabitlenen dev metnin kelimeleri, scroll edildikçe sırayla opasitesini %10'dan %100'e çıkararak parlar.
* **Metin:** "Geleneksel reklamcılık öldü. Günümüzde sıradan bir web sitesine veya sosyal medya hesabına sahip olmak yetersiz. Gerçek farkı yaratan, kanalların birbiriyle konuşmasıdır. Stratejiyi, tasarımı, reklamları ve dönüşüm mimarisini tek merkezde topluyoruz. Amacımız sadece estetik değil, somut yatırım getirisi (ROI) ve kesintisiz müşteri akışı sağlamaktır."

### Bölüm E: Hizmetler (Bento Grid / Cam Kartlar)
* **Düzen:** Asimetrik Bento Grid. Hover anında dış kenarlığı ışık çizgisiyle parlayan `backdrop-blur` cam kartlar.
* **Kart 1 (Geniş):** Web Tasarım & Yazılım - SEO/GEO uyumlu, ultra hızlı, dönüşüm odaklı premium web mimarisi.
* **Kart 2 (Dikey):** Performans Pazarlaması - Veri odaklı Meta ve Google Ads kampanyaları, retargeting ve ciro ölçeklendirme.
* **Kart 3 (Kare):** Reels & Video Prodüksiyon - Algoritma dostu, senaryodan kurguya etkileşim odaklı kreatif videolar.
* **Kart 4 (Geniş):** WhatsApp Dönüşüm Sistemi - Gelen trafiği n8n otomasyonları ile doğrudan satış ve lead'e çeviren anlık kazanım mimarisi.

### Bölüm F: Çalışmalar & Vaka Analizleri (Horizontal Scroll)
* **Animasyon:** Scroll dikey akışı durdurur, ekran sağa doğru pürüzsüzce kaymaya başlar. Projeler dev yatay kartlar halinde ekrana gelir.
* **Kart İçerikleri:** Görsel üzerinde fütüristik etiketler (Örn: MusicaDent - "Sürdürülebilir Hasta Edinimi", La Ruota Pizza - "İlk Ayda Yoğun Lead Akışı").

### Bölüm G: Kapanış (Footer CTA)
* **Düzen:** Ekranı kaplayan, devasa tipografi içeren etkileşimli alan.
* **Başlık:** "[Sistemi Başlat]" (Hover anında mikro spring animasyonu).
* **Alt:** İletişim bilgileri, takvim/WhatsApp bağlantısı ve minimal telif hakkı yazısı.

---

## ⚙️ 3. KODLAMA KURALLARI VE CHOREOGRAPHY (ÖNEMLİ)
1. **Yumuşaklık:** Sayfa geçişleri ve eleman animasyonları `ease: "power4.out"` veya Framer `stiffness: 100, damping: 20` spring değerleriyle kurgulanmalıdır. Sert efektler kullanma.
2. **Optimizasyon:** Animasyonlar transform ve opacity üzerinden tetiklenmeli, browser performansı korunmalıdır.
3. **Lüks Hissiyat:** Elemanlar arasındaki margin ve padding boşlukları geniş tutulmalıdır (White-space kullanımı).

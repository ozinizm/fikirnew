"use client";

import React, { useEffect, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";

export default function Services() {
  const [activeTab, setActiveTab] = useState(0);

  const fallbackServices = [
    {
      title: "Web Tasarım & Yazılım",
      tabLabel: "Web Tasarım",
      description: "Dönüşüm odaklı web siteleri, kampanya sayfaları ve yerel işletmeler için müşteri kazanım altyapısı kuruyoruz.",
      bullets: ["UI/UX Tasarım", "Mobil Uyumlu Arayüz", "Web Geliştirme"],
      image: "https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png",
    },
    {
      title: "Performans Pazarlaması",
      tabLabel: "Reklam Yönetimi",
      description: "Veri odaklı Meta ve Google Ads kampanyaları ile bütçenizi optimize ederek ölçülebilir müşteri dönüşümü sağlıyoruz.",
      bullets: ["Meta Ads", "Google Ads", "Retargeting"],
      image: "https://framerusercontent.com/images/L3jNOIvjVNNJ9KYGN7ZewlhM4.png",
    },
    {
      title: "WhatsApp Dönüşüm Sistemi",
      tabLabel: "WhatsApp Otomasyonu",
      description: "Gelen web ve sosyal medya trafiğini n8n entegrasyonları ile otomatik lead toplama ve karşılama sistemine dönüştürüyoruz.",
      bullets: ["n8n İş Akışları", "Otomatik Karşılama", "CRM Entegrasyonu"],
      image: "https://framerusercontent.com/images/yOPV9nZRSJXmNPqyeWfZSThWAc.png",
    },
  ];

  const [services, setServices] = useState(fallbackServices);

  useEffect(() => {
    fetch("/api/services")
      .then((res) => res.json())
      .then((data) => {
        if (Array.isArray(data.services) && data.services.length > 0) {
          setServices(
            data.services.map((item: any) => ({
              title: item.title,
              tabLabel: item.tabLabel || item.title.split(" & ")[0].split(" ")[0],
              description: item.description,
              bullets: Array.isArray(item.bullets) ? item.bullets : (item.bullets ? item.bullets.split(",") : ["Dönüşüm"]),
              image: item.image || "https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png",
            }))
          );
          setActiveTab(0);
        }
      })
      .catch(() => {});
  }, []);

  const current = services[activeTab] ?? services[0];

  // Parse description and bullets cleanly to prevent duplicates in rendering
  const lines = current.description.split(/\r?\n|•|-/).map(l => l.trim()).filter(Boolean);
  const cleanDescription = lines[0] || current.description;
  const rawBullets = lines.slice(1);
  const displayBullets = rawBullets.filter(b => b.length > 0 && b.length <= 40);
  const fallbackBullets = fallbackServices[activeTab]?.bullets || ["Dönüşüm", "Tasarım", "Yazılım"];
  const finalBullets = displayBullets.length > 0 ? displayBullets : fallbackBullets;

  return (
    <section id="services" className="py-24 bg-layout-gray px-6 md:px-12 border-t border-black/5 overflow-hidden">
      <div className="max-w-6xl mx-auto flex flex-col items-center">
        
        {/* Label */}
        <span className="font-plus-jakarta text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-500 block mb-6 select-none text-center">
          ( Hizmetler )
        </span>

        {/* Headline */}
        <h2 className="font-plus-jakarta text-4xl md:text-5xl font-extrabold tracking-tight text-[#1c1c1e] leading-tight text-center mb-16 select-none">
          Ne yapıyoruz
        </h2>

        {/* Tab Selection Buttons on a Horizontal Line */}
        <div className="w-full relative select-none">
          {/* Horizontal Divider Line */}
          <div className="absolute top-1/2 left-0 right-0 h-[1px] bg-black/10 -translate-y-1/2 z-0 hidden sm:block" />
          
          {/* Spaced out tab links */}
          <div className="relative z-10 flex flex-col sm:flex-row justify-between items-center w-full gap-4 sm:gap-0 px-2 sm:px-8">
            {services.map((service, index) => {
              const isActive = activeTab === index;
              return (
                <button
                  key={index}
                  onClick={() => setActiveTab(index)}
                  className={`font-inter text-xs sm:text-sm font-semibold transition-all duration-300 bg-layout-gray px-4 py-2 flex items-center gap-2 clickable rounded-full sm:rounded-none border sm:border-0 border-black/5 sm:border-transparent ${
                    isActive ? "text-accent-orange font-extrabold" : "text-neutral-500 hover:text-[#1c1c1e]"
                  }`}
                >
                  <span className={`w-1.5 h-1.5 rounded-full bg-accent-orange transition-transform duration-300 ${isActive ? "scale-100" : "scale-0 w-0"}`} />
                  {service.tabLabel}
                </button>
              );
            })}
          </div>
        </div>

        {/* Mockup Card & Background Scrolling Marquee Container */}
        <div className="relative w-full flex items-center justify-center my-16 select-none min-h-[300px]">
          
          {/* Background Scrolling Marquee with Edge Fading (Stretched to screen edges) */}
          <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-screen overflow-hidden pointer-events-none select-none z-0">
            {/* Edge fading overlays using hardware-accelerated background gradients instead of expensive CSS mask-image */}
            <div className="absolute left-0 top-0 bottom-0 w-[20vw] bg-gradient-to-r from-[#dcdcdc] to-transparent z-10" />
            <div className="absolute right-0 top-0 bottom-0 w-[20vw] bg-gradient-to-l from-[#dcdcdc] to-transparent z-10" />

            <div 
              className="animate-marquee-container flex items-center whitespace-nowrap gap-16"
              style={{ animationDuration: "120s" }}
            >
              {Array(10)
                .fill(0)
                .map((_, idx) => (
                  <span
                    key={idx}
                    className="font-plus-jakarta text-[8vw] font-black text-accent-orange select-none flex items-center gap-12"
                  >
                    <span>{current.tabLabel}</span>
                    <svg
                      width="20"
                      height="20"
                      viewBox="0 0 24 24"
                      fill="currentColor"
                      className="text-accent-orange/60 shrink-0 mx-2"
                    >
                      <path d="M12 0c0 6.627-5.373 12-12 12 6.627 0 12 5.373 12 12 0-6.627 5.373-12 12-12-6.627 0-12-5.373-12-12z" />
                    </svg>
                  </span>
                ))}
            </div>
          </div>

          {/* Centered Mockup Card (Sized smaller to max-w-[480px]) */}
          <AnimatePresence mode="wait">
            <motion.div
              key={activeTab}
              initial={{ opacity: 0, scale: 0.96, y: 15 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.96, y: -15 }}
              transition={{ duration: 0.5, ease: [0.16, 1, 0.3, 1] }}
              className="relative z-10 w-full max-w-[480px] aspect-[1.4] rounded-[32px] overflow-hidden border border-black/5 bg-[#eee]/50 shadow-2xl shadow-black/10 group clickable cursor-pointer"
              data-cursor-text="GÖRÜNTÜLE"
            >
              <img
                src={current.image}
                alt={current.title}
                loading="lazy"
                decoding="async"
                className="w-full h-full object-cover select-none group-hover:scale-[1.02] transition-transform duration-700 ease-out"
              />
            </motion.div>
          </AnimatePresence>

        </div>

        {/* Centered Description & Sub-features */}
        <div className="flex flex-col items-center justify-center text-center max-w-xl px-4 mt-2 select-none">
          <AnimatePresence mode="wait">
            <motion.p
              key={activeTab}
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              transition={{ duration: 0.4 }}
              className="font-inter text-neutral-600 text-sm sm:text-base leading-relaxed"
            >
              {cleanDescription}
            </motion.p>
          </AnimatePresence>

          <AnimatePresence mode="wait">
            <motion.div
              key={activeTab}
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -10 }}
              transition={{ duration: 0.4, delay: 0.05 }}
              className="flex flex-wrap items-center justify-center gap-2.5 mt-8 select-none"
            >
              {finalBullets.map((bullet, idx) => (
                <span
                  key={idx}
                  className="px-3.5 py-1.5 rounded-full bg-[#2c2c2e] hover:bg-[#3a3a3c] text-[#f5f5f7] font-inter text-[10px] sm:text-[11px] font-semibold tracking-wide border border-white/5 shadow-md shadow-black/5 transition-all duration-300"
                >
                  {bullet}
                </span>
              ))}
            </motion.div>
          </AnimatePresence>
        </div>

      </div>
    </section>
  );
}

"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";

function LocalTimeClock() {
  const [time, setTime] = useState<string>("");

  useEffect(() => {
    const updateTime = () => {
      const now = new Date();
      const options: Intl.DateTimeFormatOptions = {
        timeZone: "Europe/Istanbul",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
      };
      setTime(new Intl.DateTimeFormat("tr-TR", options).format(now));
    };

    updateTime();
    const interval = setInterval(updateTime, 1000);
    return () => clearInterval(interval);
  }, []);

  if (!time) return null;

  return (
    <span className="font-mono text-white/50">
      İstanbul &rarr; {time}
    </span>
  );
}

export default function Footer({ settings = {} }: { settings?: Record<string, string> }) {
  return (
    <footer className="relative w-[calc(100%-32px)] md:w-[calc(100%-48px)] mx-auto max-w-[1920px] rounded-[32px] bg-gradient-to-t from-[#ff4d00] via-[#0c0c0e] to-[#0c0c0e] pt-20 pb-8 mb-6 overflow-hidden border border-white/5 shadow-2xl">
      {/* Background image representing the smokey orange glow */}
      <div 
        className="absolute inset-0 bg-[url('https://framerusercontent.com/images/PdDBhDZBbpUwCIAstL4W9sLO5M.png')] bg-cover bg-center pointer-events-none opacity-50 mix-blend-screen -z-10" 
      />

      <div className="max-w-6xl mx-auto px-8 md:px-12 flex flex-col relative z-20">
        {/* Three Columns */}
        <div className="grid grid-cols-2 md:grid-cols-3 gap-10 md:gap-16 mb-20">
          {/* Navigation Column */}
          <div className="flex flex-col text-left">
            <span className="text-xs font-semibold text-white/30 tracking-wider uppercase mb-6">Navigasyon</span>
            <div className="flex flex-col gap-3">
              <a href="/#works" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">Çalışmalar</a>
              <a href="/#services" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">Hizmetler</a>
              <a href="/#agency" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">Ajans</a>
              <a href="/portfolio" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">Portfolyo</a>
            </div>
          </div>

          {/* Social Column */}
          <div className="flex flex-col text-left">
            <span className="text-xs font-semibold text-white/30 tracking-wider uppercase mb-6">Sosyal Medya</span>
            <div className="flex flex-col gap-3">
              <a href={settings.instagram || "https://instagram.com"} target="_blank" rel="noopener noreferrer" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">Instagram</a>
              <a href={settings.linkedin || "https://linkedin.com"} target="_blank" rel="noopener noreferrer" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">LinkedIn</a>
              <a href={settings.youtube || "https://youtube.com"} target="_blank" rel="noopener noreferrer" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">YouTube</a>
            </div>
          </div>

          {/* Legals Column */}
          <div className="flex flex-col text-left col-span-2 md:col-span-1">
            <span className="text-xs font-semibold text-white/30 tracking-wider uppercase mb-6">Hukuki</span>
            <div className="flex flex-col gap-3">
              <Link href="/gizlilik-politikasi" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">Gizlilik Politikası</Link>
              <Link href="/kullanim-sartlari" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">Kullanım Şartları</Link>
              <Link href="/cerez-politikasi" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">Çerez Politikası</Link>
              <Link href="/kvkk" className="font-plus-jakarta text-[19px] font-bold text-white hover:text-accent-orange transition-colors">KVKK</Link>
            </div>
          </div>
        </div>

        {/* Bottom Row */}
        <div className="w-full flex flex-col sm:flex-row items-center justify-between pt-8 border-t border-white/5 text-white/40 text-[13px] gap-4">
          <span>{settings.footer_text || `© ${new Date().getFullYear()} Fikir. Tüm hakları saklıdır.`}</span>
          
          {/* Dynamic Clock */}
          <LocalTimeClock />

          <button 
            onClick={() => window.scrollTo({ top: 0, behavior: "smooth" })}
            className="hover:text-white transition-colors cursor-pointer"
          >
            Yukarı dön
          </button>
        </div>
      </div>

      {/* Giant Text bleeding off the bottom card border */}
      <div className="mt-16 select-none pointer-events-none w-full flex justify-center relative z-0 mb-[-4.5vw]">
        <h2 className="font-plus-jakarta text-[11vw] font-black text-transparent bg-clip-text bg-gradient-to-b from-white via-white to-white/95 tracking-tighter text-center leading-none whitespace-nowrap drop-shadow-[0_10px_20px_rgba(0,0,0,0.5)]">
          {settings.site_baslik || "Fikir Creative"}
        </h2>
      </div>

      {/* Blur overlay for the bottom of the text */}
      <div className="absolute bottom-0 left-0 w-full h-[5vw] bg-gradient-to-t from-[#ff4d00] via-[#ff4d00]/20 to-transparent backdrop-blur-[4px] pointer-events-none z-10" />
    </footer>
  );
}

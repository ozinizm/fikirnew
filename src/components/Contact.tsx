"use client";

import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";

const fallbackCovers = [
  "https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png",
  "https://framerusercontent.com/images/QhPkJGJBXS8kPS7IhPj7ZBGZpII.png",
  "https://framerusercontent.com/images/yOPV9nZRSJXmNPqyeWfZSThWAc.png",
];

export default function Contact() {
  const [settings, setSettings] = useState<Record<string, string>>({});
  const [projects, setProjects] = useState<string[]>(fallbackCovers);
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    goal: "",
  });

  useEffect(() => {
    // Fetch settings
    fetch("/api/settings")
      .then((res) => res.json())
      .then((data) => {
        if (data.settings) setSettings(data.settings);
      })
      .catch(() => {});

    // Fetch projects to use as background mockups
    fetch("/api/portfolio")
      .then((res) => res.json())
      .then((data) => {
        const portfolio = data.items || data.portfolio || [];
        if (portfolio.length > 0) {
          const covers = portfolio.map((p: any) => p.thumbnailUrl || p.posterUrl || p.mediaUrl || p.cover).filter(Boolean);
          if (covers.length > 0) {
            setProjects(covers);
          }
        }
      })
      .catch(() => {});
  }, []);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const primaryPhone = (settings.telefon || "+90 (532) 000 00 00").split(",")[0].trim();
  const primaryEmail = (settings.email || "hello@fikircreative.com").split(",")[0].trim();
  const whatsappNumber = primaryPhone.replace(/\D/g, "");
  const whatsappHref = `https://wa.me/${whatsappNumber || "905320000000"}`;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    await fetch("/api/contact", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    }).catch(() => {});

    const text = `Merhaba Fikir Creative, teklif almak istiyorum.%0A%0AAd Soyad: ${formData.name}%0AE-posta: ${formData.email}%0AHedef: ${formData.goal}`;
    window.open(`${whatsappHref}?text=${text}`, "_blank");
  };

  // Repeated list for the seamless email marquee inside the card
  const emailMarqueeList = Array(12).fill(primaryEmail);

  return (
    <section 
      id="contact" 
      className="pt-40 pb-3 md:pb-4 bg-layout-gray relative overflow-hidden flex flex-col items-center w-full px-4 md:px-6"
    >
      
      {/* 1. Large background text "iletisime gec" */}
      <div className="absolute top-16 left-1/2 -translate-x-1/2 select-none pointer-events-none z-0 relative">
        <h2 className="font-plus-jakarta text-[12vw] font-black text-black/[0.15] tracking-tighter text-center leading-none whitespace-nowrap blur-[6px]">
          İletişime geç
        </h2>
        {/* Hardware-accelerated fade gradient overlay instead of expensive CSS mask-image */}
        <div className="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#dcdcdc] pointer-events-none" />
      </div>

      {/* 2. Premium Card Container */}
      <div className="relative z-10 w-full rounded-[32px] overflow-hidden bg-[#0c0c0e] shadow-2xl border border-white/5 flex flex-col max-w-[1920px] mx-auto">
        
        {/* Blurred grid of portfolio images as card background */}
        <div className="absolute inset-0 -z-10 opacity-30 select-none pointer-events-none overflow-hidden">
          <div className="grid grid-cols-3 sm:grid-cols-4 gap-4 w-[115%] h-[115%] -translate-x-[7%] -translate-y-[7%] rotate-[-4deg] blur-[6px]">
            {Array(12).fill(0).map((_, idx) => (
              <div key={idx} className="aspect-[4/3] rounded-2xl overflow-hidden bg-white/5 border border-white/10">
                <img
                  src={projects[idx % projects.length]}
                  alt=""
                  loading="lazy"
                  decoding="async"
                  className="w-full h-full object-cover"
                />
              </div>
            ))}
          </div>
          {/* Subtle gradient overlay to fade-out elements and guarantee text contrast */}
          <div className="absolute inset-0 bg-gradient-to-b from-black/80 via-black/90 to-black" />
        </div>

        {/* Content Layout (Centered within max-w-6xl) */}
        <div className="w-full max-w-6xl mx-auto px-8 py-16 md:py-20 md:px-16 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-center">
          
          {/* Left Column: Heading Info */}
          <div className="lg:col-span-6 flex flex-col text-left">
            <h3 className="font-plus-jakarta text-3xl sm:text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-[1.1] max-w-md">
              İşletmeniz için dijital büyüme planı oluşturalım
            </h3>
            <p className="font-inter text-neutral-400 text-sm font-light mt-6 leading-relaxed max-w-sm">
              Web sitesi, reklam yönetimi, video içerik ve WhatsApp dönüşüm altyapısını birlikte planlayalım.
            </p>
          </div>

          {/* Right Column: Form */}
          <div className="lg:col-span-6 w-full text-left">
            <form onSubmit={handleSubmit} className="flex flex-col gap-8 w-full">
              
              {/* Name Input */}
              <div className="flex flex-col gap-2 relative">
                <label htmlFor="name" className="text-xs font-bold text-neutral-400 tracking-wider">
                  Ad Soyad
                </label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  required
                  value={formData.name}
                  onChange={handleChange}
                  placeholder="Enter your Name"
                  className="border-b border-white/20 focus:border-white focus:outline-none bg-transparent py-3 w-full text-sm text-white placeholder-white/30 transition-all font-inter"
                />
              </div>

              {/* Email Input */}
              <div className="flex flex-col gap-2 relative">
                <label htmlFor="email" className="text-xs font-bold text-neutral-400 tracking-wider">
                  E-posta
                </label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  required
                  value={formData.email}
                  onChange={handleChange}
                  placeholder="Enter the Email"
                  className="border-b border-white/20 focus:border-white focus:outline-none bg-transparent py-3 w-full text-sm text-white placeholder-white/30 transition-all font-inter"
                />
              </div>

              {/* Goal Input */}
              <div className="flex flex-col gap-2 relative">
                <label htmlFor="goal" className="text-xs font-bold text-neutral-400 tracking-wider">
                  İşletmeniz ve hedefiniz
                </label>
                <input
                  type="text"
                  id="goal"
                  name="goal"
                  required
                  value={formData.goal}
                  onChange={handleChange}
                  placeholder="Type Here..."
                  className="border-b border-white/20 focus:border-white focus:outline-none bg-transparent py-3 w-full text-sm text-white placeholder-white/30 transition-all font-inter"
                />
              </div>

              {/* Submit Button */}
              <button
                type="submit"
                className="mt-6 py-4 px-8 rounded-full bg-[#f0f0f0] text-black font-inter text-xs font-bold tracking-widest uppercase hover:bg-white transition-all shadow-xl shadow-black/20 flex items-center justify-center gap-2 clickable w-full"
                data-cursor-text="GÖNDER"
              >
                Gönder
              </button>

            </form>
          </div>

        </div>

        {/* 3. Bottom Scrolling Marquee inside the card */}
        <div className="w-full py-4 border-t border-white/5 overflow-hidden bg-black/40 relative z-20">
          {/* Hardware-accelerated edge fades instead of expensive CSS mask-image */}
          <div className="absolute left-0 top-0 bottom-0 w-[15%] bg-gradient-to-r from-[#0c0c0e] to-transparent z-30 pointer-events-none" />
          <div className="absolute right-0 top-0 bottom-0 w-[15%] bg-gradient-to-l from-[#0c0c0e] to-transparent z-30 pointer-events-none" />
          <div 
            className="animate-marquee-container flex items-center gap-16 whitespace-nowrap"
            style={{ animationDuration: "35s" }}
          >
            {emailMarqueeList.map((email, idx) => (
              <div key={idx} className="font-plus-jakarta text-xs sm:text-sm font-semibold text-white/30 tracking-wide flex items-center gap-6">
                <span>{email}</span>
                <span className="text-white/10 text-xs">x</span>
              </div>
            ))}
          </div>
        </div>

      </div>

    </section>
  );
}

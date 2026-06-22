"use client";

import React from "react";
import { motion } from "framer-motion";

export default function About({ settings = {} }: { settings?: Record<string, string> }) {
  const badges = [
    {
      label: "Web Tasarım",
      icon: (
        <svg
          width="15"
          height="15"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.8"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <circle cx="12" cy="12" r="10" />
          <line x1="2" y1="12" x2="22" y2="12" />
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
        </svg>
      ),
    },
    {
      label: "Meta Ads",
      icon: (
        <svg
          width="15"
          height="15"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.8"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <path d="M12 12c-2-2.67-4-4-6-4a4 4 0 1 0 0 8c2 0 4-1.33 6-4zm0 0c2 2.67 4 4 6 4a4 4 0 1 0 0-8c-2 0-4 1.33-6 4z" />
        </svg>
      ),
    },
    {
      label: "Google Ads",
      icon: (
        <svg
          width="15"
          height="15"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.8"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <circle cx="12" cy="12" r="10" />
          <circle cx="12" cy="12" r="6" />
          <circle cx="12" cy="12" r="2" />
        </svg>
      ),
    },
    {
      label: "Video İçerik",
      icon: (
        <svg
          width="15"
          height="15"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.8"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <path d="M23 7l-7 5 7 5V7z" />
          <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
        </svg>
      ),
    },
    {
      label: "WhatsApp Sistem",
      icon: (
        <svg
          width="15"
          height="15"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.8"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
        </svg>
      ),
    },
    {
      label: "Yerel SEO",
      icon: (
        <svg
          width="15"
          height="15"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="1.8"
          strokeLinecap="round"
          strokeLinejoin="round"
        >
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
          <circle cx="12" cy="10" r="3" />
        </svg>
      ),
    },
  ];

  return (
    <section className="py-24 md:py-32 bg-layout-gray px-6 md:px-12 overflow-hidden border-t border-black/5">
      <div className="max-w-6xl mx-auto flex flex-col items-center justify-center text-center">
        
        {/* Subtitle: (hello) */}
        <motion.span
          initial={{ opacity: 0, y: 15 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, margin: "-100px" }}
          transition={{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }}
          className="font-serif italic text-accent-orange text-xl md:text-2xl lowercase mb-8 block select-none"
        >
          (hello)
        </motion.span>

        {/* Text Area with Opacity Hierarchy */}
        {(() => {
          const aboutText = settings.seo_desc || "Fikir Creative; klinikler, güzellik merkezleri, restoranlar ve hizmet işletmeleri için web sitesi, reklam yönetimi, video içerik ve WhatsApp dönüşüm sistemi kuran dijital büyüme ajansıdır.";
          const words = aboutText.split(" ");
          const third = Math.ceil(words.length / 3);
          const part1 = words.slice(0, third).join(" ");
          const part2 = words.slice(third, third * 2).join(" ");
          const part3 = words.slice(third * 2).join(" ");
          return (
            <motion.h2
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, margin: "-100px" }}
              transition={{ duration: 1, delay: 0.1, ease: [0.16, 1, 0.3, 1] }}
              className="font-plus-jakarta text-2xl sm:text-[34px] md:text-[44px] lg:text-[48px] font-extrabold leading-[1.3] text-center max-w-5xl tracking-tight"
            >
              <span className="text-text-primary">
                {part1}{" "}
              </span>
              <span className="text-text-primary/65">
                {part2}{" "}
              </span>
              <span className="text-text-primary/40">
                {part3}
              </span>
            </motion.h2>
          );
        })()}

        {/* Pill Badges Grid */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, margin: "-100px" }}
          transition={{ duration: 1, delay: 0.3, ease: [0.16, 1, 0.3, 1] }}
          className="flex flex-wrap items-center justify-center gap-3 mt-12 sm:mt-14 max-w-4xl"
        >
          {badges.map((badge, idx) => (
            <div
              key={idx}
              className="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-[#2c2c2e] hover:bg-[#3a3a3c] text-[#f5f5f7] font-inter text-xs sm:text-[13px] font-semibold tracking-wide border border-white/5 shadow-md shadow-black/5 hover:scale-[1.03] hover:-translate-y-0.5 transition-all duration-300 select-none clickable cursor-pointer"
            >
              {badge.icon}
              <span>{badge.label}</span>
            </div>
          ))}
        </motion.div>

      </div>
    </section>
  );
}

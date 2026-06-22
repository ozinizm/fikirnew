"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";

function getAssetPath(value?: string | null): string {
  if (!value) return "";
  if (/^https?:\/\//i.test(value)) return value;
  return `/${value.replace(/^\/+/, "")}`;
}

export default function Header({ settings = {} }: { settings?: Record<string, string> }) {
  const [isScrolled, setIsScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => setIsScrolled(window.scrollY > 20);
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const navLinks = [
    { label: "Çalışmalar", href: "/#works" },
    { label: "Hizmetler", href: "/#services" },
    { label: "Ajans", href: "/#agency" },
    { label: "Paketler", href: "/#packages" },
    { label: "Portfolyo", href: "/portfolio" },
  ];

  const availabilityText = settings.availability_text || "Yeni projeler için uygun";
  const headerCtaText = settings.header_cta_text || "İletişim";
  const headerCtaUrl = settings.header_cta_url || "/#contact";

  const logoOnLight = settings.logo_on_light ? getAssetPath(settings.logo_on_light) : null;
  const logoOnDark = settings.logo_on_dark ? getAssetPath(settings.logo_on_dark) : null;
  const activeLogo = isScrolled ? (logoOnLight || logoOnDark) : (logoOnDark || logoOnLight);

  return (
    <header className={`fixed top-0 left-0 w-full z-50 transition-all duration-500 ${isScrolled ? "bg-white/90 backdrop-blur-md py-3.5 border-b border-black/5" : "bg-transparent pt-12 pb-5"}`}>
      {/* Top Floating Pill */}
      <motion.div 
        initial={{ opacity: 1, y: 0 }}
        animate={{ opacity: isScrolled ? 0 : 1, y: isScrolled ? -36 : 0 }}
        transition={{ duration: 0.3, ease: "easeInOut" }}
        className="absolute top-0 left-1/2 transform -translate-x-1/2 w-[280px] h-[36px] flex items-center justify-center z-50 select-none pointer-events-none"
      >
        {/* SVG Background for the organic flare */}
        <svg width="280" height="36" viewBox="0 0 280 36" fill="none" xmlns="http://www.w3.org/2000/svg" className="absolute top-0 left-0 w-full h-full -z-10">
          <path d="M0 0C25 0 35 36 60 36H220C245 36 255 0 280 0Z" fill="#1c1c1e" />
        </svg>
        
        {/* Content */}
        <div className="flex items-center gap-2 text-[11.5px] font-medium text-white/90 tracking-wide pt-1">
          <span className="w-1.5 h-1.5 rounded-full bg-[#61c554] relative flex">
            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#61c554] opacity-75"></span>
            <span className="relative inline-flex rounded-full h-1.5 w-1.5 bg-[#61c554]"></span>
          </span>
          <span>{availabilityText}</span>
        </div>
      </motion.div>

      <div className="max-w-7xl mx-auto px-6 md:px-12 flex items-center justify-between relative">
        <Link href="/" className="font-plus-jakarta text-3xl font-black text-accent-orange tracking-tighter hover:opacity-90 transition-opacity flex items-center">
          {activeLogo ? (
            <img src={activeLogo} className="h-8 md:h-[34px] w-auto object-contain" alt={settings.site_baslik || "Fikir"} />
          ) : (
            "Fikir."
          )}
        </Link>
        <nav className="hidden md:flex items-center gap-8">
          {navLinks.map((link, i) => (
            <Link key={i} href={link.href} className="font-inter text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
              {link.label}
            </Link>
          ))}
        </nav>
        <div className="hidden md:block">
          <Link href={headerCtaUrl} className="bg-[#1c1c1e] hover:bg-black text-white px-6 py-2.5 rounded-full font-inter text-sm font-semibold transition-all duration-300">
            {headerCtaText}
          </Link>
        </div>
        
        <button 
          className="md:hidden p-2" 
          onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
          aria-label="Menüyü Aç/Kapat"
          aria-expanded={mobileMenuOpen}
        >
          <div className="w-6 h-0.5 bg-black mb-1.5" />
          <div className="w-6 h-0.5 bg-black" />
        </button>
      </div>

      <AnimatePresence>
        {mobileMenuOpen && (
          <motion.div
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            className="absolute top-full left-0 w-full bg-white border-b border-black/5 p-6 flex flex-col gap-4 md:hidden"
            role="navigation"
            aria-label="Mobil Menü"
          >
            {navLinks.map((link, i) => (
              <Link key={i} href={link.href} onClick={() => setMobileMenuOpen(false)} className="font-plus-jakarta text-xl font-bold text-black">
                {link.label}
              </Link>
            ))}
          </motion.div>
        )}
      </AnimatePresence>
    </header>
  );
}

"use client";

import React from "react";
import { CmsBrand } from "@/lib/cms-data";

export default function Marquee({ brands = [] }: { brands?: CmsBrand[] }) {
  const ticker1Items = [
    "Website Design",
    "Brand Design",
    "Logo Design",
    "SEO & GEO Optimization",
    "WhatsApp Funnels",
    "Meta Ads",
    "n8n Automation",
  ];

  const ticker2Items = [
    "Senior Creative Team",
    "10+ Years of Experience",
    "Over 100 Customers",
    "ROI Focused Campaigning",
    "Award Winning UI/UX",
    "Premium Web Yazılım",
  ];

  // Repeat items to make it seamless (even multiplier ensures 50% translation matches exactly)
  const list1 = [...ticker1Items, ...ticker1Items, ...ticker1Items, ...ticker1Items];
  const list2 = [...ticker2Items, ...ticker2Items, ...ticker2Items, ...ticker2Items];

  // Resolve logos list
  let logoList: React.ReactNode[] = [];

  if (brands.length > 0) {
    const logos = brands.map((brand, index) => {
      if (brand.logo) {
        return (
          <img 
            key={`brand-img-${brand.id}-${index}`} 
            src={brand.logo} 
            alt={brand.name} 
            className="h-6 max-w-[130px] object-contain filter grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all duration-300 inline-block shrink-0" 
          />
        );
      } else if (brand.logoSvg) {
        return (
          <div 
            key={`brand-svg-${brand.id}-${index}`} 
            className="inline-block h-6 shrink-0 [&>svg]:h-6 [&>svg]:w-auto [&>svg]:max-w-[130px]"
            dangerouslySetInnerHTML={{ __html: brand.logoSvg }}
          />
        );
      }
      return (
        <span 
          key={`brand-text-${brand.id}-${index}`} 
          className="font-inter font-bold tracking-tight text-[15px] inline-block shrink-0"
        >
          {brand.name}
        </span>
      );
    });

    // Repeat to ensure smooth marquee looping
    const repeatCount = Math.max(6, Math.ceil(24 / logos.length));
    for (let i = 0; i < repeatCount; i++) {
      logoList.push(...logos);
    }
  } else {
    // Clean vector client logos (Fallback)
    const fallbackLogos = [
      // NovaTech
      (
        <svg key="logo-1" width="110" height="24" viewBox="0 0 110 24" fill="currentColor">
          <circle cx="12" cy="12" r="8" stroke="currentColor" strokeWidth="2.5" fill="none" />
          <circle cx="12" cy="12" r="3" />
          <text x="28" y="17" className="font-inter font-bold tracking-tight text-[15px]">NovaTech</text>
        </svg>
      ),
      // Bloom
      (
        <svg key="logo-2" width="120" height="24" viewBox="0 0 120 24" fill="currentColor">
          <path d="M12 4 L19 12 L12 20 L5 12 Z" stroke="currentColor" strokeWidth="2.5" fill="none" />
          <circle cx="12" cy="12" r="2.5" />
          <text x="30" y="17" className="font-inter font-extrabold tracking-tight text-[15px]">Bloom</text>
        </svg>
      ),
      // HexaStudio
      (
        <svg key="logo-3" width="115" height="24" viewBox="0 0 115 24" fill="currentColor">
          <path d="M12 3 L19 7 L19 15 L12 19 L5 15 L5 7 Z" fill="none" stroke="currentColor" strokeWidth="2" />
          <text x="28" y="17" className="font-inter font-semibold tracking-tight text-[15px]">HexaStudio</text>
        </svg>
      ),
      // Apex
      (
        <svg key="logo-4" width="100" height="24" viewBox="0 0 100 24" fill="currentColor">
          <polygon points="12 4 21 19 3 19" fill="none" stroke="currentColor" strokeWidth="2.5" />
          <text x="28" y="17" className="font-inter font-bold tracking-tight text-[15px]">Apex</text>
        </svg>
      ),
      // Codex
      (
        <svg key="logo-5" width="110" height="24" viewBox="0 0 110 24" fill="currentColor">
          <path d="M8 8 L12 4 L16 8 M16 16 L12 20 L8 16" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" />
          <text x="26" y="17" className="font-inter font-bold tracking-wider text-[15px]">Codex</text>
        </svg>
      ),
      // Aeorim
      (
        <svg key="logo-6" width="105" height="24" viewBox="0 0 105 24" fill="currentColor">
          <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" strokeWidth="2" strokeDasharray="16 4" />
          <text x="28" y="17" className="font-inter font-extrabold tracking-tight text-[15px]">Aeorim</text>
        </svg>
      ),
    ];
    logoList = [...fallbackLogos, ...fallbackLogos, ...fallbackLogos, ...fallbackLogos, ...fallbackLogos, ...fallbackLogos];
  }

  return (
    <div className="w-full flex flex-col bg-layout-gray select-none pointer-events-none">
      
      {/* 1. Client Logos Marquee (Faded edges, Infinite horizontal scroll) */}
      <div className="w-full py-10 overflow-hidden relative border-t border-black/5 bg-layout-gray">
        {/* Edge fading overlays using hardware-accelerated background gradients instead of expensive CSS mask-image */}
        <div className="absolute left-0 top-0 bottom-0 w-[15vw] bg-gradient-to-r from-[#dcdcdc] to-transparent z-10 pointer-events-none" />
        <div className="absolute right-0 top-0 bottom-0 w-[15vw] bg-gradient-to-l from-[#dcdcdc] to-transparent z-10 pointer-events-none" />

        <div 
          className="animate-marquee-container flex items-center gap-24"
          style={{ animationDuration: "35s" }}
        >
          {logoList.map((logo, idx) => (
            <div key={idx} className="text-neutral-500/50 hover:text-neutral-700 transition-colors shrink-0">
              {logo}
            </div>
          ))}
        </div>
      </div>

      {/* 2. Crossing Skewed Marquees Section */}
      <section className="relative flex flex-col justify-center items-center overflow-hidden w-full bg-layout-gray py-28 min-h-[260px] border-t border-black/5">
        
        {/* Ticker 2: Black Background, Scroll Right, Tilted Upward */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 py-5 bg-layout-dark border-y border-white/5 flex overflow-hidden w-[120vw] min-w-[120%] rotate-[3deg] z-0 shadow-lg origin-center">
          <div className="animate-marquee-container-reverse flex items-center gap-16 md:gap-24">
            {list2.map((item, idx) => (
              <div
                key={idx}
                className="font-plus-jakarta text-sm sm:text-base font-semibold tracking-wide text-[#F0F0F0]/90 uppercase flex items-center gap-10 whitespace-nowrap"
              >
                <span>{item}</span>
                <span className="text-[#F0F0F0]/40 font-light lowercase text-xs">x</span>
              </div>
            ))}
          </div>
        </div>

        {/* Ticker 1: Orange Background, Scroll Left, Tilted Downward */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 py-5 bg-accent-orange border-y border-black/5 flex overflow-hidden w-[120vw] min-w-[120%] rotate-[-3.5deg] z-10 shadow-xl origin-center">
          <div className="animate-marquee-container flex items-center gap-16 md:gap-24">
            {list1.map((item, idx) => (
              <div
                key={idx}
                className="font-plus-jakarta text-sm sm:text-base font-extrabold tracking-wide text-white uppercase flex items-center gap-10 whitespace-nowrap"
              >
                <span>{item}</span>
                <span className="text-white/60 font-light lowercase text-xs">x</span>
              </div>
            ))}
          </div>
        </div>

      </section>
      
    </div>
  );
}

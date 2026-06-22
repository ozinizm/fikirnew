"use client";
 
import React from "react";
import { motion } from "framer-motion";
import { CmsTeamMember } from "@/lib/cms-data";

export default function Profile({
  settings = {},
  team = [],
}: {
  settings?: Record<string, string>;
  team?: CmsTeamMember[];
}) {
  const experiences = [
    {
      role: settings.experience_1_role || "Founder at Fikir Creative",
      period: settings.experience_1_period || "2024–Now",
    },
    {
      role: settings.experience_2_role || "Brand Designer at Google",
      period: settings.experience_2_period || "2023–2024",
    },
    {
      role: settings.experience_3_role || "Web Designer at Shopify",
      period: settings.experience_3_period || "2018–2023",
    },
    {
      role: settings.experience_4_role || "Junior Designer at Meta",
      period: settings.experience_4_period || "2015–2018",
    },
  ];

  // Main founder/profile member is the first team member or a default fallback
  const founder = team[0] || {
    name: "Fikir Creative",
    role: "Dijital Büyüme Ajansı",
    bio: "Yerel işletmeler için web sitesi, reklam yönetimi, video içerik ve WhatsApp dönüşüm sistemi kuruyoruz. Amaç; daha profesyonel görünüm, doğru hedef kitle & ölçülebilir müşteri dönüşümüdür.",
    image: "https://framerusercontent.com/images/cdiudTEW8MSbl2008vSYXSq9ndI.png",
    instagram: settings.instagram || "https://instagram.com",
    linkedin: settings.linkedin || "https://linkedin.com",
  };
 
  return (
    <section id="profile" className="py-24 bg-layout-gray px-6 md:px-12 border-t border-black/5 overflow-hidden relative">
      
      {/* Giant Background Title */}
      <div className="absolute top-[8%] left-1/2 -translate-x-1/2 w-full text-center pointer-events-none select-none z-0">
        <span className="font-plus-jakarta text-[12vw] font-black text-black/[0.03] uppercase tracking-tighter leading-none block">
          {settings.site_baslik || "Fikir Creative"}
        </span>
      </div>
 
      <div className="max-w-6xl mx-auto relative z-10">
        
        {/* Label */}
        <div className="text-center mb-12 select-none">
          <span className="font-plus-jakarta text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-500 block">
            ( Ajans )
          </span>
        </div>
 
        {/* Layout Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start mt-8">
          
          {/* Left Column: Image Card & Overlapping Rotating Badge */}
          <div className="lg:col-span-5 relative w-full flex justify-center">
            
            {/* Image card */}
            <div className="relative rounded-[32px] overflow-hidden aspect-[4/5] w-full max-w-[380px] border border-black/5 shadow-lg shadow-black/5 bg-[#eee]/50">
              <img
                src={founder.image}
                alt={founder.name}
                loading="lazy"
                decoding="async"
                className="w-full h-full object-cover select-none"
              />
              
              {/* Social Icons inside bottom-left */}
              <div className="absolute bottom-6 left-6 flex items-center gap-2">
                {founder.linkedin && (
                  <a
                    href={founder.linkedin}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="w-8 h-8 rounded-full bg-black/40 backdrop-blur-md border border-white/10 flex items-center justify-center text-white hover:bg-black/60 transition-colors"
                    aria-label="LinkedIn Profile"
                  >
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                      <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                      <rect x="2" y="9" width="4" height="12" />
                      <circle cx="4" cy="4" r="2" />
                    </svg>
                  </a>
                )}
                {settings.site_url && (
                  <a
                    href={settings.site_url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="w-8 h-8 rounded-full bg-black/40 backdrop-blur-md border border-white/10 flex items-center justify-center text-white hover:bg-black/60 transition-colors"
                    aria-label="Website"
                  >
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                      <circle cx="12" cy="12" r="10" />
                      <line x1="2" y1="12" x2="22" y2="12" />
                      <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                    </svg>
                  </a>
                )}
                {founder.instagram && (
                  <a
                    href={founder.instagram}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="w-8 h-8 rounded-full bg-black/40 backdrop-blur-md border border-white/10 flex items-center justify-center text-white hover:bg-black/60 transition-colors"
                    aria-label="Instagram"
                  >
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                      <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                      <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                      <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                    </svg>
                  </a>
                )}
              </div>
            </div>
            
            {/* Spinning Badge overlapping the bottom right corner */}
            <div
              className="absolute bottom-[-32px] right-[8%] sm:right-[12%] lg:right-[-24px] z-20 w-24 h-24 sm:w-28 sm:h-28 rounded-full select-none pointer-events-none animate-spin"
              style={{ animationDuration: "15s" }}
            >
              <img
                src="https://framerusercontent.com/images/JpJ9ryMkQp811zxkS5X8I8Igdo.png"
                alt="Award Winning Designer Since 2020 Badge"
                className="w-full h-full object-contain filter drop-shadow-md"
              />
            </div>
 
          </div>
 
          {/* Right Column: Description & Timeline Experience */}
          <div className="lg:col-span-7 flex flex-col justify-center text-left lg:pl-10 mt-8 lg:mt-0">
            <h3 className="font-plus-jakarta text-3xl sm:text-4xl md:text-5xl font-black text-black tracking-tight mb-6">
              {founder.role}
            </h3>
            
            <p className="font-inter text-neutral-500 text-sm md:text-base leading-relaxed font-light mb-8 whitespace-pre-line">
              {founder.bio}
            </p>
 
            {/* Separator line */}
            <div className="border-t border-black/10 my-6 w-full" />
 
            {/* Experience list */}
            <div className="flex flex-col w-full">
              {experiences.map((exp, index) => {
                if (!exp.role) return null;
                return (
                  <div
                    key={index}
                    className="flex items-center justify-between py-4 border-b border-black/10 font-inter text-xs md:text-sm"
                  >
                    <span className="font-semibold text-black">{exp.role}</span>
                    <span className="text-neutral-500 font-medium">{exp.period}</span>
                  </div>
                );
              })}
            </div>
          </div>
 
        </div>

        {/* Additional Team Members Grid */}
        {team.length > 1 && (
          <div className="mt-24 border-t border-black/10 pt-16">
            <div className="text-center mb-12 select-none">
              <span className="font-plus-jakarta text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-500 block mb-2">
                ( Canavar Ekip )
              </span>
              <h3 className="font-plus-jakarta text-3xl font-black text-black uppercase tracking-tight">
                Ekibimiz
              </h3>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 mt-8">
              {team.slice(1).map((member, index) => (
                <div key={index} className="flex flex-col items-center text-center p-6 bg-white/5 rounded-3xl border border-black/5 shadow-md">
                  <div className="w-24 h-24 rounded-full overflow-hidden mb-4 border border-black/5 bg-[#eee]">
                    <img src={member.image} alt={member.name} className="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-300" />
                  </div>
                  <h4 className="font-plus-jakarta font-bold text-lg text-black">{member.name}</h4>
                  <p className="font-inter text-xs text-accent-orange font-semibold uppercase mt-1">{member.role}</p>
                  <p className="font-inter text-xs text-neutral-500 mt-3 max-w-[200px]">{member.bio}</p>
                  <div className="flex items-center gap-2 mt-4">
                    {member.instagram && (
                      <a href={member.instagram} target="_blank" rel="noopener noreferrer" className="text-neutral-400 hover:text-accent-orange transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                          <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                          <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                        </svg>
                      </a>
                    )}
                    {member.linkedin && (
                      <a href={member.linkedin} target="_blank" rel="noopener noreferrer" className="text-neutral-400 hover:text-accent-orange transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                          <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                          <rect x="2" y="9" width="4" height="12" />
                          <circle cx="4" cy="4" r="2" />
                        </svg>
                      </a>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
 
      </div>
    </section>
  );
}

"use client";

import React, { useEffect, useRef, useState } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { useGSAP } from "@gsap/react";
import { ArrowUpRight } from "lucide-react";

gsap.registerPlugin(ScrollTrigger);

const fallbackProjects = [
  {
    title: "Archin",
    category: "DESIGN / 2025",
    role: "Lead Designer",
    services: "Website Design, Product Design, Branding, Development",
    cover: "https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png",
    year: "2025",
  },
  {
    title: "VNTNR",
    category: "BRANDING / 2018",
    role: "Logo Designer",
    services: "Designing, Branding, Redesigning, Development",
    cover: "https://framerusercontent.com/images/QhPkJGJBXS8kPS7IhPj7ZBGZpII.png",
    year: "2018",
  },
  {
    title: "Aeorim",
    category: "REVAMP / 2023",
    role: "Website Designer",
    services: "Branding, Revamp, Development, Designing",
    cover: "https://framerusercontent.com/images/yOPV9nZRSJXmNPqyeWfZSThWAc.png",
    year: "2023",
  },
];

export default function Works() {
  const containerRef = useRef<HTMLDivElement>(null);
  const [projects, setProjects] = useState(fallbackProjects);
  const [isLoading, setIsLoading] = useState(true);

  useGSAP(
    () => {
      if (isLoading) return;
      // Only initialize stacked card animation on desktop screens (lg: min-width 1024px)
      let mm = gsap.matchMedia();

      mm.add("(min-width: 1024px)", () => {
        if (!containerRef.current) return;

        const cards = gsap.utils.toArray<HTMLElement>(".project-card");
        if (cards.length <= 1) return;

        // Initial setup for organic 3D-like stacking
        cards.forEach((card, index) => {
          if (index === 0) {
            // First card is active initially, completely straight
            gsap.set(card, { 
              yPercent: 0, 
              scale: 1, 
              rotation: 0,
              transformOrigin: "center center",
              opacity: 1,
              filter: "blur(0px)"
            });
          } else {
            // Other cards are off-screen at bottom, tilted in opposite directions
            const initialTilt = index % 2 === 0 ? -6 : 6;
            gsap.set(card, { 
              yPercent: 110, 
              scale: 0.98,
              rotation: initialTilt,
              transformOrigin: "center center",
              opacity: 1,
              filter: "blur(0px)"
            });
          }
        });

        const tl = gsap.timeline({
          scrollTrigger: {
            trigger: containerRef.current,
            start: "top top",
            end: "bottom bottom",
            scrub: 0.8,
            pin: ".works-sticky-wrapper",
            pinSpacing: true,
            invalidateOnRefresh: true,
          },
        });

        cards.forEach((card, index) => {
          if (index === 0) return;

          // When scrolling down:
          // - previous card goes to the background: scales down, blurs, tilts (e.g. -3 or 3 deg)
          // - current card comes to the foreground: slides up, scales to 1, straightens to 0 deg
          const prevTargetTilt = (index - 1) % 2 === 0 ? -3 : 3;
          const activeTilt = 0; // Straightens to 0 degrees when active!

          tl.to(
            cards[index - 1],
            {
              scale: 0.92,
              opacity: 0.35,
              filter: "blur(4px)",
              yPercent: -8,
              rotation: prevTargetTilt,
              duration: 1,
              ease: "power1.inOut",
            },
            `card-${index}`
          )
          .to(
            card,
            {
              yPercent: 0,
              scale: 1,
              rotation: activeTilt,
              duration: 1,
              ease: "power1.inOut",
            },
            `card-${index}`
          );
        });
      });

      return () => {
        mm.revert();
        // Forcefully kill any ScrollTriggers associated with this section to avoid sticky overlap bugs
        ScrollTrigger.getAll().forEach((t) => {
          if (t.trigger === containerRef.current) {
            t.kill();
          }
        });
      };
    },
    { dependencies: [isLoading], scope: containerRef }
  );



  useEffect(() => {
    fetch("/api/portfolio")
      .then((res) => res.json())
      .then((data) => {
        if (Array.isArray(data.items) && data.items.length > 0) {
          setProjects(
            data.items.slice(0, 3).map((item: any) => ({
              title: item.title,
              category: `${item.category || "PROJE"} / ${item.year || new Date().getFullYear()}`,
              role: item.client || "Fikir Creative",
              services: Array.isArray(item.services) ? item.services.join(", ") : item.category || "Dijital Sistem",
              cover: item.thumbnailUrl || item.posterUrl || item.mediaUrl || "https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png",
              year: item.year || new Date().getFullYear().toString(),
            }))
          );
        } else {
          setProjects(fallbackProjects);
        }
        setIsLoading(false);
      })
      .catch(() => {
        setProjects(fallbackProjects);
        setIsLoading(false);
      });
  }, []);

  return (
    <div
      ref={containerRef}
      id="works"
      style={{
        "--works-height": projects.length <= 1 ? "100vh" : `${projects.length * 100}vh`,
      } as React.CSSProperties}
      className="relative lg:h-[var(--works-height)] bg-layout-gray border-t border-black/5"
    >
      {/* Sticky viewport wrapper (Desktop is pinned by GSAP, Mobile is standard block layout) */}
      <div className="works-sticky-wrapper relative lg:h-screen flex flex-col items-center justify-center py-16 lg:py-0 overflow-hidden w-full">
        
        {/* Section Header */}
        <div className="mb-8 lg:mb-12 text-center max-w-xl px-6 select-none">
          <span className="font-plus-jakarta text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-500 block mb-2">
            ( Seçili Sistemler )
          </span>
          <h2 className="font-plus-jakarta text-3xl sm:text-4xl font-extrabold tracking-tight text-black">
            Ajans Yaklaşımı
          </h2>
        </div>

        {/* Stack Container (Desktop has fixed height for absolute cards, Mobile stacks vertically) */}
        <div className="relative flex flex-col lg:block gap-8 w-full max-w-[90vw] md:max-w-[85vw] lg:max-w-[82vw] h-auto lg:h-[72vh] px-2 md:px-0">
          
          {projects.map((project, index) => (
            <a
              href="/portfolio"
              key={index}
              className="project-card relative lg:absolute lg:inset-0 w-full h-[55vh] sm:h-[60vh] lg:h-full rounded-[32px] overflow-hidden group border border-black/5 bg-[#eee] flex flex-col justify-end p-8 md:p-12 clickable shadow-lg shadow-black/5 block"
              style={{ zIndex: (index + 1) * 10 }}
              data-cursor-text="GÖRÜNTÜLE"
            >
              {/* Background Project Image */}
              <div className="absolute inset-0 z-0 select-none">
                <img
                  src={project.cover}
                  alt={project.title}
                  loading="lazy"
                  decoding="async"
                  className="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-700 ease-out"
                />
                {/* Visual overlay gradient */}
                <div className="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent z-10" />
              </div>

              {/* Top Category Badge */}
              <div className="absolute top-6 left-6 md:top-8 md:left-8 z-20">
                <span className="text-[9px] md:text-[10px] font-bold tracking-widest text-white/80 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/10 uppercase">
                  {project.category}
                </span>
              </div>

              {/* Bottom Content Area */}
              <div className="relative z-20 text-white text-left">
                <span className="font-plus-jakarta text-2xl md:text-3xl font-extrabold tracking-tight">
                  {project.title}
                </span>
                
                {/* Project details table */}
                <div className="mt-6 border-t border-white/10 pt-6 flex flex-col gap-2 font-inter text-xs font-light text-white/70">
                  <div className="flex justify-between border-b border-white/5 pb-2">
                    <span className="font-medium text-white/50">Yıl</span>
                    <span>{project.year}</span>
                  </div>
                  <div className="flex justify-between border-b border-white/5 pb-2">
                    <span className="font-medium text-white/50">Rol</span>
                    <span>{project.role}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="font-medium text-white/50">Hizmetler</span>
                    <span className="text-right max-w-[70%] truncate">{project.services}</span>
                  </div>
                </div>
              </div>

              {/* Hover link icon indicator */}
              <div className="absolute bottom-8 right-8 z-20 w-10 h-10 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <ArrowUpRight size={18} className="text-white" />
              </div>
            </a>
          ))}

        </div>
      </div>
    </div>
  );
}

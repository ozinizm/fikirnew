"use client";

import React, { useRef } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { useGSAP } from "@gsap/react";

gsap.registerPlugin(ScrollTrigger);

export default function Manifesto() {
  const containerRef = useRef<HTMLDivElement>(null);
  const textRef = useRef<HTMLParagraphElement>(null);

  useGSAP(
    () => {
      if (!textRef.current || !containerRef.current) return;

      const text = textRef.current;
      const textContent = text.innerText.trim();
      const words = textContent.split(/\s+/);
      
      // Wrap each word in a span with opacity-10
      text.innerHTML = words
        .map(
          (word) =>
            `<span class="manifesto-word opacity-[0.1] inline-block mr-[0.25em] transition-all duration-300">${word}</span>`
        )
        .join("");

      const spans = text.querySelectorAll(".manifesto-word");

      // Pin the section and animate opacity of words on scroll
      gsap.to(spans, {
        opacity: 1,
        stagger: 0.1,
        duration: 1,
        scrollTrigger: {
          trigger: containerRef.current,
          start: "top top",
          end: "+=100% top",
          scrub: 0.8,
          pin: true,
          anticipatePin: 1,
        },
      });
    },
    { scope: containerRef }
  );

  return (
    <div
      ref={containerRef}
      id="manifesto"
      className="relative h-screen w-full flex items-center justify-center bg-[#F8F9FA] px-6 md:px-12 overflow-hidden border-b border-black/5"
    >
      {/* Background radial highlight */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[60vw] h-[60vw] rounded-full bg-accent-lila/5 blur-[120px]" />
      </div>

      <div className="max-w-6xl w-full text-center relative z-10">
        
        {/* Label */}
        <div className="mb-8 md:mb-12">
          <span className="font-plus-jakarta text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400">
            Ajans Manifestosu
          </span>
        </div>

        {/* Big manifesto text */}
        <p
          ref={textRef}
          className="font-plus-jakarta text-2xl sm:text-3xl md:text-5xl lg:text-[54px] font-bold tracking-tight text-black leading-[1.3] max-w-5xl mx-auto select-none"
        >
          Geleneksel reklamcılık öldü. Günümüzde sıradan bir web sitesine veya sosyal medya
          hesabına sahip olmak yetersiz. Gerçek farkı yaratan, kanalların birbiriyle
          konuşmasıdır. Stratejiyi, tasarımı, reklamları ve dönüşüm mimarisini tek merkezde
          topluyoruz. Amacımız sadece estetik değil, somut yatırım getirisi (ROI) ve
          kesintisiz müşteri akışı sağlamaktır.
        </p>

      </div>
    </div>
  );
}

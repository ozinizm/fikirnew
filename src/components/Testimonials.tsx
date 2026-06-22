"use client";

import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { ArrowLeft, ArrowRight } from "lucide-react";

export default function Testimonials() {
  const [activeIndex, setActiveIndex] = useState(0);

  const reviews = [
    {
      quote:
        "Fikir Creative, fikirlerimizi keskin ve temiz bir markaya dönüştürdü. Hızlı, pratik ve doğrudan hedefe ulaştık.",
      author: "Ethan Moore",
      role: "Kurucu Ortak, NovaTech",
      avatar: "https://framerusercontent.com/images/nURHcgFo9S6zVF3j0ly85sSmvE.png",
    },
    {
      quote:
        "Açık, düşünceli ve hızlılar. Web sitemizi kurup reklamlarımızı optimize etme sürecini tamamen zahmetsiz hale getirdiler.",
      author: "Olivia Tran",
      role: "Kreatif Direktör, Bloom Agency",
      avatar: "https://framerusercontent.com/images/j4kitBgDVx6ElGDAGtpxlM7RoHg.png",
    },
    {
      quote:
        "Akıllıca bir web mimarisi, sorunsuz teslimat. Fikir Creative ekibiyle çalışmak yerel büyümemiz için mükemmel bir adımdı.",
      author: "Lucas Bennett",
      role: "Ürün Yöneticisi, Hexa Studio",
      avatar: "https://framerusercontent.com/images/4be4S5coR2QthuRAfsb7USMjRZ0.png",
    },
  ];

  const handleNext = () => {
    setActiveIndex((prev) => (prev + 1) % reviews.length);
  };

  const handlePrev = () => {
    setActiveIndex((prev) => (prev - 1 + reviews.length) % reviews.length);
  };

  const current = reviews[activeIndex];

  return (
    <section id="testimonials" className="py-24 bg-layout-gray px-6 md:px-12 border-t border-black/5 overflow-hidden">
      <div className="max-w-4xl mx-auto flex flex-col items-center">
        
        {/* Label */}
        <span className="font-plus-jakarta text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-500 block mb-12">
          ( Müşteri Yorumları )
        </span>

        {/* Slider Card */}
        <div className="relative w-full bg-layout-light rounded-3xl p-8 md:p-14 border border-black/5 overflow-hidden flex flex-col justify-between min-h-[340px] shadow-lg shadow-black/5">
          {/* Noise mask background overlay */}
          <div
            className="absolute inset-0 z-0 pointer-events-none opacity-20"
            style={{
              backgroundImage: "url('https://framerusercontent.com/images/qDuGmDXhhbdrJsP16G4zNCDX8.png')",
              backgroundSize: "cover",
            }}
          />

          {/* Testimonial Quote */}
          <div className="relative z-10 flex-grow flex items-center">
            <AnimatePresence mode="wait">
              <motion.p
                key={activeIndex}
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -20 }}
                transition={{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }}
                className="font-plus-jakarta text-xl sm:text-2xl md:text-3xl font-semibold text-black leading-relaxed italic text-center w-full"
              >
                “{current.quote}”
              </motion.p>
            </AnimatePresence>
          </div>

          {/* Bottom Row: Author details and slider controls */}
          <div className="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-6 border-t border-black/5 pt-8 mt-10 w-full">
            {/* Author Avatar and Names */}
            <div className="flex items-center gap-4">
              <AnimatePresence mode="wait">
                <motion.img
                  key={activeIndex}
                  initial={{ opacity: 0, scale: 0.8 }}
                  animate={{ opacity: 1, scale: 1 }}
                  exit={{ opacity: 0, scale: 0.8 }}
                  transition={{ duration: 0.3 }}
                  src={current.avatar}
                  alt={current.author}
                  className="w-12 h-12 rounded-full border border-black/5 object-cover bg-neutral-100 select-none"
                />
              </AnimatePresence>
              <div className="text-left font-inter">
                <AnimatePresence mode="wait">
                  <motion.p
                    key={activeIndex}
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    transition={{ duration: 0.3 }}
                    className="text-sm font-bold text-black"
                  >
                    {current.author}
                  </motion.p>
                </AnimatePresence>
                <AnimatePresence mode="wait">
                  <motion.p
                    key={activeIndex}
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    exit={{ opacity: 0 }}
                    transition={{ duration: 0.3 }}
                    className="text-xs text-neutral-500 mt-0.5"
                  >
                    {current.role}
                  </motion.p>
                </AnimatePresence>
              </div>
            </div>

            {/* Slider Controls */}
            <div className="flex items-center gap-3">
              <button
                onClick={handlePrev}
                className="w-11 h-11 rounded-full bg-white hover:bg-neutral-100 flex items-center justify-center border border-black/5 text-black transition-colors shadow-sm clickable"
                data-cursor-text="GERİ"
                aria-label="Previous Review"
              >
                <ArrowLeft size={16} />
              </button>
              <button
                onClick={handleNext}
                className="w-11 h-11 rounded-full bg-white hover:bg-neutral-100 flex items-center justify-center border border-black/5 text-black transition-colors shadow-sm clickable"
                data-cursor-text="İLERİ"
                aria-label="Next Review"
              >
                <ArrowRight size={16} />
              </button>
            </div>
          </div>

        </div>

      </div>
    </section>
  );
}

"use client";

import React, { useState, useRef } from "react";
import { motion, AnimatePresence, useScroll, useTransform } from "framer-motion";
import { ArrowLeft, ArrowRight } from "lucide-react";
import { CmsStat, CmsTestimonial } from "@/lib/cms-data";

export default function StatsGrid({
  stats: dbStats,
  testimonials: dbSlides,
}: {
  stats?: CmsStat[];
  testimonials?: CmsTestimonial[];
}) {
  const containerRef = useRef<HTMLDivElement>(null);
  const [activeSlide, setActiveSlide] = useState(0);

  // Set up parallax scroll effect on the background text
  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ["start end", "end start"],
  });

  // Map scroll progress to horizontal translation (x-axis)
  // When scroll progresses from 0 to 1, translate text from -12% to 12%
  const x = useTransform(scrollYProgress, [0, 1], ["-12%", "12%"]);

  const defaultStats = [
    {
      number: "26+",
      label: "Kurulan Dijital Sistem",
    },
    {
      number: "98%",
      label: "Müşteri Kazanım Odağı",
    },
    {
      number: "10M",
      label: "Yerel İşletme Deneyimi",
    },
  ];

  const defaultSlides = [
    {
      quote: "Fikir Creative, fikirlerimizi keskin ve temiz bir markaya dönüştürdü. Hızlı, pratik ve doğrudan hedefe ulaştık.",
      author: "Ethan Moore",
      role: "Kurucu Ortak, NovaTech",
      image: "/why-slide-1.png",
    },
    {
      quote: "Açık, düşünceli ve hızlılar. Web sitemizi kurup reklamlarımızı optimize etme sürecini tamamen zahmetsiz hale getirdiler.",
      author: "Olivia Tran",
      role: "Kreatif Direktör, Bloom Agency",
      image: "/why-slide-2.png",
    },
    {
      quote: "Akıllıca bir web mimarisi, sorunsuz teslimat. Fikir Creative ekibiyle çalışmak yerel büyümemiz için mükemmel bir adımdı.",
      author: "Lucas Bennett",
      role: "Ürün Yöneticisi, Hexa Studio",
      image: "/why-slide-3.png",
    },
  ];

  const stats = dbStats && dbStats.length > 0 ? dbStats : defaultStats;
  const slides = dbSlides && dbSlides.length > 0 ? dbSlides : defaultSlides;

  const handleNext = () => {
    setActiveSlide((prev) => (prev + 1) % slides.length);
  };

  const handlePrev = () => {
    setActiveSlide((prev) => (prev - 1 + slides.length) % slides.length);
  };

  return (
    <section
      id="stats"
      ref={containerRef}
      className="py-24 bg-layout-gray px-6 md:px-12 border-t border-black/5 overflow-hidden relative"
    >
      
      {/* Background Giant Text with Parallax & Fade Effect */}
      <div className="absolute top-[8%] sm:top-[6%] left-0 w-full overflow-hidden pointer-events-none select-none z-0">
        <motion.div
          style={{ x }}
          className="w-[140%] -ml-[20%] text-center whitespace-nowrap"
        >
          <span 
            className="font-plus-jakarta text-[16vw] font-black tracking-tighter leading-none block text-[#1c1c1e]/[0.08]"
            style={{
              WebkitMaskImage: "linear-gradient(to bottom, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 100%)",
              maskImage: "linear-gradient(to bottom, rgba(0,0,0,1) 30%, rgba(0,0,0,0) 100%)",
            }}
          >
            Dönüşüm
          </span>
        </motion.div>
      </div>

      <div className="max-w-6xl mx-auto flex flex-col items-center relative z-10">
        
        {/* Section Label */}
        <span className="font-plus-jakarta text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-500 block mb-12 select-none text-center">
          ( Neden Fikir Creative )
        </span>

        {/* Bento Stats & Slider Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 w-full mt-4">
          
          {/* Left Block: Stats Card (Narrow Rectangular Box) */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: "-50px" }}
            transition={{ type: "spring", stiffness: 100, damping: 20 }}
            className="lg:col-span-4 relative rounded-[32px] overflow-hidden p-8 sm:p-10 flex flex-col justify-between min-h-[380px] md:min-h-[400px] bg-neutral-900 border border-black/10 shadow-xl"
          >
            {/* Background Image */}
            <div
              className="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat"
              style={{ backgroundImage: "url('/dark-smoke.png')" }}
            />
            {/* Dark Overlay */}
            <div className="absolute inset-0 z-0 bg-black/45" />

            {/* Stats list */}
            <div className="relative z-10 flex flex-col justify-between h-full w-full gap-6">
              {stats.map((stat, idx) => (
                <React.Fragment key={idx}>
                  <div className="flex flex-col text-left">
                    <span className="font-plus-jakarta text-4xl sm:text-5xl font-black text-white leading-none">
                      {stat.number}
                    </span>
                    <span className="font-inter text-neutral-300 text-xs sm:text-sm font-semibold tracking-wide mt-2">
                      {stat.label}
                    </span>
                  </div>
                  {idx < stats.length - 1 && (
                    <div className="border-t border-white/10 w-full" />
                  )}
                </React.Fragment>
              ))}
            </div>
          </motion.div>

          {/* Right Block: Testimonial Image Slider (Wide Rectangular Box) */}
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, margin: "-50px" }}
            transition={{ type: "spring", stiffness: 100, damping: 20, delay: 0.1 }}
            className="lg:col-span-8 relative rounded-[32px] overflow-hidden p-8 sm:p-10 md:p-12 flex flex-col justify-between min-h-[380px] md:min-h-[400px] bg-neutral-800 border border-black/10 shadow-xl group"
          >
            {/* Slider Background Image with zoom animation */}
            <div className="absolute inset-0 z-0 overflow-hidden">
              <AnimatePresence mode="wait">
                <motion.div
                  key={activeSlide}
                  initial={{ opacity: 0, scale: 1.05 }}
                  animate={{ opacity: 1, scale: 1 }}
                  exit={{ opacity: 0 }}
                  transition={{ duration: 0.6, ease: [0.16, 1, 0.3, 1] }}
                  className="absolute inset-0 bg-cover bg-center transition-transform duration-700 ease-out group-hover:scale-105"
                  style={{ backgroundImage: `url('${slides[activeSlide].image}')` }}
                />
              </AnimatePresence>
              {/* Dark Overlay */}
              <div className="absolute inset-0 bg-black/55 backdrop-blur-[0.5px]" />
            </div>

            {/* Slide Content */}
            <div className="relative z-10 flex flex-col justify-between h-full w-full">
              
              {/* Top Row: Pagination Indicator */}
              <div className="text-left select-none">
                <span className="font-mono text-xs text-white/70 tracking-widest bg-black/35 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/5">
                  0{activeSlide + 1} / 0{slides.length}
                </span>
              </div>

              {/* Middle Row: Quote */}
              <div className="my-auto py-6">
                <AnimatePresence mode="wait">
                  <motion.p
                    key={activeSlide}
                    initial={{ opacity: 0, y: 15 }}
                    animate={{ opacity: 1, y: 0 }}
                    exit={{ opacity: 0, y: -15 }}
                    transition={{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }}
                    className="font-plus-jakarta text-lg sm:text-xl md:text-2xl font-semibold text-white leading-relaxed text-left max-w-xl"
                  >
                    “{slides[activeSlide].quote}”
                  </motion.p>
                </AnimatePresence>
              </div>

              {/* Bottom Row: Author details & controls */}
              <div className="flex items-end justify-between gap-4 mt-4">
                
                {/* Author Info */}
                <div className="text-left font-inter">
                  <AnimatePresence mode="wait">
                    <motion.p
                      key={activeSlide}
                      initial={{ opacity: 0 }}
                      animate={{ opacity: 1 }}
                      exit={{ opacity: 0 }}
                      transition={{ duration: 0.3 }}
                      className="text-sm sm:text-base font-bold text-white"
                    >
                      {slides[activeSlide].author}
                    </motion.p>
                  </AnimatePresence>
                  <AnimatePresence mode="wait">
                    <motion.p
                      key={activeSlide}
                      initial={{ opacity: 0 }}
                      animate={{ opacity: 1 }}
                      exit={{ opacity: 0 }}
                      transition={{ duration: 0.3 }}
                      className="text-xs text-neutral-300 mt-0.5"
                    >
                      {slides[activeSlide].role}
                    </motion.p>
                  </AnimatePresence>
                </div>

                {/* Slider Arrow Buttons */}
                <div className="flex items-center gap-2 select-none">
                  <button
                    onClick={handlePrev}
                    className="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 flex items-center justify-center text-white transition-all clickable cursor-pointer"
                    aria-label="Previous Slide"
                  >
                    <ArrowLeft size={16} />
                  </button>
                  <button
                    onClick={handleNext}
                    className="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 flex items-center justify-center text-white transition-all clickable cursor-pointer"
                    aria-label="Next Slide"
                  >
                    <ArrowRight size={16} />
                  </button>
                </div>

              </div>

            </div>
          </motion.div>

        </div>

      </div>
    </section>
  );
}

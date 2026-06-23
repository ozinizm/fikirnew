"use client";
 
import React from "react";
import { motion } from "framer-motion";
import Link from "next/link";

 
const containerVariants = {
  hidden: { opacity: 0 },
  visible: {
    opacity: 1,
    transition: {
      staggerChildren: 0.012,
    },
  },
};
 
const wordVariants = {
  hidden: { opacity: 0, filter: "blur(12px)", y: 10 },
  visible: {
    opacity: 1,
    filter: "blur(0px)",
    y: 0,
    transition: {
      type: "spring" as const,
      damping: 12,
      stiffness: 100,
    },
  },
};
 
const AnimatedText = ({ text, className }: { text: string; className?: string }) => {
  const words = text.split(" ");
  return (
    <span className={className}>
      {words.map((word, wordIndex) => (
        <motion.span
          key={wordIndex}
          variants={wordVariants}
          className="inline-block"
        >
          {word}
          {wordIndex < words.length - 1 && "\u00A0"}
        </motion.span>
      ))}
    </span>
  );
};
 
export default function Hero({ settings = {} }: { settings?: Record<string, string> }) {
  const normalizePath = (val?: string | null) => {
    if (!val) return "";
    if (/^https?:\/\//i.test(val)) return val;
    return `/${val.replace(/^\/+/, "")}`;
  };

  const slogan = settings.site_slogan || "Web sitesi, reklam, video içerik ve WhatsApp dönüşüm altyapısıyla yerel işletmeler için ölçülebilir müşteri kazanımı kuruyoruz.";
  const siteTitle = settings.site_baslik || "Fikir Creative";
 
  const titleParts = siteTitle.split(" ");
  const brandFirst = titleParts[0] || "Fikir";
  const brandSecond = titleParts.slice(1).join(" ") || "Creative";
 
  // Dynamic Hero Configuration
  const line1Left = settings.hero_title_line1_left || "Sadece";
  const line1Right = settings.hero_title_line1_right || "görünürlük değil,";
  const line2Left = settings.hero_title_line2_left || "müşteri";
  const line2Right = settings.hero_title_line2_right || "kazandıran sistem";
  const line3Left = settings.hero_title_line3_left || "kuran";
 
  const inlineImage1 = normalizePath(settings.hero_inline_image_1 || "/hero-1.png");
  const inlineImage2 = normalizePath(settings.hero_inline_image_2 || "/hero-2.png");
  const inlineImage3 = normalizePath(settings.hero_inline_image_3 || "/hero-3.png");
 
  const showcaseImage = normalizePath(settings.hero_showcase_image || "https://framerusercontent.com/images/dT5S1njJpyHvznBNeTmMAwfBcqQ.png");
 
  return (
    <section className="relative min-h-screen flex flex-col items-center pt-40 pb-24 px-4 sm:px-6 md:px-8 overflow-hidden bg-layout-gray">
      <motion.div
        className="w-full max-w-6xl mx-auto flex flex-col items-center text-center relative z-10"
        variants={containerVariants}
        initial="hidden"
        animate="visible"
      >
        {/* Social proof avatars */}
        <motion.div variants={wordVariants} className="flex items-center gap-2 mb-8 select-none">
          <div className="flex -space-x-2">
            <img className="w-7 h-7 rounded-full border-2 border-layout-gray object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=80&q=80" alt="Mutlu Hizmet İşletmesi Sahibi" />
            <img className="w-7 h-7 rounded-full border-2 border-layout-gray object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=80&q=80" alt="Fikir Creative İş Ortağı" />
            <img className="w-7 h-7 rounded-full border-2 border-layout-gray object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=80&q=80" alt="Klinik Yöneticisi" />
          </div>
          <span className="text-[13px] text-text-secondary font-inter font-medium tracking-wide">Markalar tarafından güvenilen.</span>
        </motion.div>
        
        {/* Desktop Title (Structured Flex Layout) */}
        <h1 className="hidden lg:flex flex-col gap-y-3 font-plus-jakarta text-[64px] xl:text-[72px] font-extrabold tracking-[-0.03em] leading-[1.2] text-text-primary mb-8">
          {/* Satır 1 */}
          <div className="flex items-center justify-center gap-x-3">
            <AnimatedText text={line1Left} />
            <motion.div variants={wordVariants} className="inline-block flex-shrink-0 w-28 h-16 bg-[#eee] rounded-full overflow-hidden border border-black/5 shadow-sm">
              <img src={inlineImage1} alt="Fikir Creative Web Tasarım Örneği" className="w-full h-full object-cover" />
            </motion.div>
            <AnimatedText text={line1Right.split(" ")[0]} className="text-accent-orange" />
            {line1Right.split(" ").slice(1).join(" ") && (
              <AnimatedText text={line1Right.split(" ").slice(1).join(" ")} className="text-text-secondary/50" />
            )}
          </div>
  
          {/* Satır 2 */}
          <div className="flex items-center justify-center gap-x-3">
            <AnimatedText text={line2Left} />
            <motion.div variants={wordVariants} className="inline-block flex-shrink-0 w-24 h-24 bg-[#eee] rounded-full overflow-hidden border border-black/5 shadow-sm">
              <img src={inlineImage2} alt="Reklam Performans Raporu" className="w-full h-full object-cover" />
            </motion.div>
            <AnimatedText text={line2Right} />
          </div>
  
          {/* Satır 3 */}
          <div className="flex items-center justify-center gap-x-3">
            <AnimatedText text={line3Left} className="text-text-secondary/50" />
            <AnimatedText text={brandFirst} className="text-accent-orange" />
            <motion.div variants={wordVariants} className="inline-block flex-shrink-0 w-24 h-24 bg-[#eee] rounded-full overflow-hidden border border-black/5 shadow-sm">
              <img src={inlineImage3} alt="Dijital Büyüme Analiz Grafiği" className="w-full h-full object-cover" />
            </motion.div>
            <AnimatedText text={brandSecond} className="text-accent-orange" />
          </div>
        </h1>

        {/* Mobile/Tablet Title (Naturally Flowing Paragraph Layout) */}
        <h1 className="block lg:hidden font-plus-jakarta text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-[-0.03em] leading-[1.3] text-text-primary mb-8 px-2 max-w-xl mx-auto">
          <AnimatedText text={line1Left} />{" "}
          <motion.div variants={wordVariants} className="inline-block align-middle w-12 h-7 bg-[#eee] rounded-full overflow-hidden border border-black/5 shadow-sm mx-1">
            <img src={inlineImage1} alt="Fikir Creative Web Tasarım Örneği" className="w-full h-full object-cover" />
          </motion.div>{" "}
          <AnimatedText text={line1Right.split(" ")[0]} className="text-accent-orange" />{" "}
          {line1Right.split(" ").slice(1).join(" ") && (
            <AnimatedText text={line1Right.split(" ").slice(1).join(" ")} className="text-text-secondary/50" />
          )}{" "}
          <AnimatedText text={line2Left} />{" "}
          <motion.div variants={wordVariants} className="inline-block align-middle w-9 h-9 bg-[#eee] rounded-full overflow-hidden border border-black/5 shadow-sm mx-1">
            <img src={inlineImage2} alt="Reklam Performans Raporu" className="w-full h-full object-cover" />
          </motion.div>{" "}
          <AnimatedText text={line2Right} />{" "}
          <AnimatedText text={line3Left} className="text-text-secondary/50" />{" "}
          <AnimatedText text={brandFirst} className="text-accent-orange" />{" "}
          <motion.div variants={wordVariants} className="inline-block align-middle w-9 h-9 bg-[#eee] rounded-full overflow-hidden border border-black/5 shadow-sm mx-1">
            <img src={inlineImage3} alt="Dijital Büyüme Analiz Grafiği" className="w-full h-full object-cover" />
          </motion.div>{" "}
          <AnimatedText text={brandSecond} className="text-accent-orange" />
        </h1>

        <motion.p variants={wordVariants} className="max-w-2xl text-base md:text-lg text-text-secondary font-inter leading-relaxed mb-12">
          <AnimatedText text={slogan} />
        </motion.p>
        
        <motion.div variants={wordVariants} className="mb-6">
          <button className="group bg-[#1c1c1e] text-white px-8 py-3.5 rounded-full font-inter text-[15px] font-semibold tracking-wide hover:bg-black transition-all shadow-[0_4px_14px_0_rgba(0,0,0,0.1)] hover:shadow-[0_6px_20px_0_rgba(0,0,0,0.15)] hover:-translate-y-0.5 flex items-center gap-2">
            Ücretsiz Analiz Al
            <span className="transform group-hover:translate-x-0.5 transition-transform duration-300">→</span>
          </button>
        </motion.div>
      </motion.div>
 
      {/* Showcase Banner Image Link */}
      <Link 
        href={settings.hero_showcase_url || "/portfolio"}
        className="block w-[calc(100%-16px)] sm:w-[calc(100%-32px)] md:w-[calc(100%-48px)] max-w-[1800px] mt-16 sm:mt-20 rounded-[20px] sm:rounded-[32px] overflow-hidden border border-black/5 shadow-2xl shadow-black/10 aspect-[16/9] bg-[#eee]/50 relative group cursor-pointer z-10"
        data-cursor-text="İNCELE"
      >
        <motion.div 
          variants={wordVariants}
          initial="hidden"
          animate="visible"
          className="w-full h-full"
        >
          <img 
            src={showcaseImage} 
            alt="Fikir Creative Dijital Büyüme Motoru" 
            className="w-full h-full object-cover select-none group-hover:scale-[1.01] transition-transform duration-700 ease-out"
            loading="eager"
          />
        </motion.div>
      </Link>

    </section>
  );
}

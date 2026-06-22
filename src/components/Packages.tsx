"use client";

import React from "react";
import { Check, Sparkles, Layers } from "lucide-react";
import { motion } from "framer-motion";

export default function Packages() {
  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1,
      },
    },
  };

  const tiers = [
    {
      name: "BAŞLANGIÇ PAKETİ",
      priceLabel: "Sabit Ücret",
      price: "15.000 ₺",
      period: "/ ay",
      timeline: "2–3 hafta",
      description: "Tek sayfa web sitesi veya landing page kurulumu ile Meta Ads temel kampanya yapısını hayata geçirin.",
      features: [
        "Tek sayfa web sitesi veya landing page",
        "Meta Ads temel kampanya kurulumu",
        "Aylık içerik planı ve görsel yönlendirme",
        "WhatsApp iletişim yönlendirmesi",
      ],
      cta: "Teklif al",
      popular: false,
      icon: <Sparkles size={20} className="text-accent-orange" />,
    },
    {
      name: "BÜYÜME PAKETİ",
      priceLabel: "Başlayan fiyatlarla",
      price: "30.000 ₺",
      period: "/ ay",
      timeline: "3–5 hafta",
      description: "Uçtan uca tasarım, reklam yönetimi, video kurgusu ve anlık WhatsApp lead dönüşüm altyapısı.",
      features: [
        "Web tasarım ve dönüşüm odaklı sayfa yapısı",
        "Meta Ads ve Google Ads kampanya yönetimi",
        "Reels/video içerik üretimi ve kurgu",
        "WhatsApp dönüşüm sistemi ve danışmanlık",
      ],
      cta: "Dijital plan oluştur",
      popular: true,
      icon: <Layers size={20} className="text-white" />,
    },
  ];

  return (
    <section id="packages" className="py-32 bg-layout-gray px-6 md:px-12 relative border-t border-black/5">
      {/* Background Subtle Gradient Blobs */}
      <div className="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
        <div className="absolute top-[20%] left-[5%] w-[45vw] h-[45vw] rounded-full bg-accent-orange/5 blur-[130px]" />
        <div className="absolute bottom-[10%] right-[10%] w-[35vw] h-[35vw] rounded-full bg-accent-orange/5 blur-[120px]" />
      </div>

      <div className="max-w-4xl mx-auto">
        {/* Section Header */}
        <div className="mb-20 text-center">
          <span className="font-plus-jakarta text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-500 block mb-3">
            ( Paketler )
          </span>
          <h2 className="font-plus-jakarta text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-black max-w-2xl mx-auto leading-tight">
            İşletmeniz için büyüme paketleri
          </h2>
        </div>

        {/* Sticky Stacking Cards Container */}
        <div className="relative flex flex-col gap-12">
          {tiers.map((tier, idx) => (
            <div
              key={idx}
              style={{
                "--sticky-top": `${100 + idx * 32}px`,
                zIndex: (idx + 1) * 10
              } as React.CSSProperties}
              className="w-full lg:sticky lg:top-[var(--sticky-top)] relative top-0"
            >
              <motion.div
                initial={{ y: 60, opacity: 0 }}
                whileInView={{ y: 0, opacity: 1 }}
                viewport={{ once: true, margin: "-100px" }}
                transition={{ duration: 0.6, ease: "easeOut" }}
                className={`w-full rounded-[32px] overflow-hidden border ${
                  tier.popular
                    ? "bg-[#0c0c0e] bg-[radial-gradient(ellipse_at_bottom_left,_var(--tw-gradient-stops))] from-[#ff4d00]/15 via-[#0c0c0e] to-[#0c0c0e] text-white shadow-2xl border-white/5"
                    : "bg-white text-[#1c1c1e] shadow-lg shadow-black/[0.03] border-black/5 hover:border-black/10"
                }`}
              >
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 p-8 sm:p-10 lg:p-12">
                  {/* Left Column: Info (7 cols) */}
                  <div className="lg:col-span-7 flex flex-col justify-between text-left">
                    <div>
                      {/* Icon */}
                      <div className={`w-12 h-12 rounded-2xl flex items-center justify-center mb-6 shadow-md ${
                        tier.popular ? "bg-white/10 text-white shadow-white/5" : "bg-[#1c1c1e] text-white shadow-black/10"
                      }`}>
                        {tier.icon}
                      </div>
                      {/* Plan Name */}
                      <span className={`font-plus-jakarta text-[11px] font-extrabold uppercase tracking-wider ${
                        tier.popular ? "text-accent-orange" : "text-neutral-400"
                      }`}>
                        {tier.name}
                      </span>
                      {/* Plan Description */}
                      <p className={`font-inter text-[15px] leading-relaxed mt-6 font-light max-w-md mb-8 ${
                        tier.popular ? "text-neutral-300" : "text-neutral-500"
                      }`}>
                        {tier.description}
                      </p>
                    </div>
                    
                    {/* Delivery Time (with line above it) */}
                    <div className={`pt-6 border-t ${
                      tier.popular ? "border-white/10" : "border-black/5"
                    } mt-auto`}>
                      <div className="flex justify-between items-center text-xs font-semibold tracking-wide">
                        <span className={tier.popular ? "text-neutral-500" : "text-neutral-400"}>Teslimat Süresi</span>
                        <span className={tier.popular ? "text-accent-orange" : "text-neutral-700"}>{tier.timeline}</span>
                      </div>
                    </div>
                  </div>

                  {/* Right Column: Pricing & Features (5 cols) */}
                  <div className={`lg:col-span-5 flex flex-col justify-between text-left lg:border-l ${
                    tier.popular ? "lg:border-white/10 lg:pl-10" : "lg:border-black/5 lg:pl-10"
                  }`}>
                    <div>
                      {/* Price Label & Price */}
                      <div className="flex flex-col mb-4">
                        <span className={`font-inter text-xs font-semibold uppercase tracking-wider mb-1 ${
                          tier.popular ? "text-white/40" : "text-neutral-400"
                        }`}>
                          {tier.priceLabel}
                        </span>
                        <div className="flex items-baseline">
                          <span className={`font-plus-jakarta text-4xl sm:text-5xl font-black tracking-tight ${
                            tier.popular ? "text-accent-orange" : "text-[#1c1c1e]"
                          }`}>
                            {tier.price}
                          </span>
                          <span className={`font-inter text-sm font-bold ml-2 ${
                            tier.popular ? "text-neutral-400" : "text-neutral-500"
                          }`}>
                            {tier.period}
                          </span>
                        </div>
                      </div>
                      
                      <div className={`w-full h-px ${
                        tier.popular ? "bg-white/10" : "bg-black/5"
                      } mb-6`} />

                      {/* Features List */}
                      <ul className="flex flex-col gap-3.5 mb-8">
                        {tier.features.map((feature, fIdx) => (
                          <li key={fIdx} className="flex items-start gap-3">
                            <div className={`p-0.5 rounded-full shrink-0 mt-0.5 ${
                              tier.popular ? "bg-white/10 text-white" : "bg-black/5 text-[#1c1c1e]"
                            }`}>
                              <Check size={12} strokeWidth={3} />
                            </div>
                            <span className={`font-inter text-[13px] leading-normal font-medium ${
                              tier.popular ? "text-neutral-300" : "text-neutral-600"
                            }`}>
                              {feature}
                            </span>
                          </li>
                        ))}
                      </ul>
                    </div>

                    {/* CTA Button */}
                    <a
                      href="https://wa.me/905320000000"
                      target="_blank"
                      rel="noopener noreferrer"
                      className={`w-full py-3.5 px-6 rounded-full font-inter text-sm font-semibold tracking-wide flex items-center justify-center gap-2 shadow-sm transition-all duration-300 clickable ${
                        tier.popular
                          ? "bg-[#2b2b2b] text-white hover:bg-neutral-800 border border-white/5"
                          : "bg-[#1c1c1e] text-white hover:bg-black"
                      }`}
                    >
                      {tier.cta} &rarr;
                    </a>
                  </div>
                </div>
              </motion.div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

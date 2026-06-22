"use client";

import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Plus } from "lucide-react";

export default function FAQ() {
  const [openIndex, setOpenIndex] = useState<number | null>(null);

  const faqs = [
    {
      question: "Fikir Creative hangi işletmelerle çalışır?",
      answer: "Yerel klinikler, güzellik merkezleri, mimarlık ofisleri, yerel restoranlar ve müşteri hacmini dijital kanallarla büyütmek isteyen tüm butik/yerel hizmet firmalarıyla çalışıyoruz.",
    },
    {
      question: "Sadece sosyal medya yönetimi mi yapıyorsunuz?",
      answer: "Hayır. Sadece görsel paylaşımı yapmak ciro getirmez. Biz web sitesi, reklam yönetimi, video içerik, yerel SEO ve WhatsApp dönüşüm sistemini birlikte planlayan uçtan uca müşteri kazanım altyapısı kuruyoruz.",
    },
    {
      question: "Süreç nasıl ilerliyor?",
      answer: "Önce işletmenizi analiz eder, zayıf ve güçlü yanlarınızı belirleriz. Anlaştığımız plan doğrultusunda web tasarım ve otomasyon kurulumlarını 2-3 hafta içinde tamamlayıp reklam çıkışlarını ve optimizasyonu başlatırız.",
    },
    {
      question: "Reklam bütçesi paketlere dahil mi?",
      answer: "Hayır. Reklam bütçesi doğrudan Meta ve Google reklam hesaplarınıza bağlı kartınızdan çekilir. Paket fiyatlarımız sadece tasarım, yazılım, video prodüksiyon ve kampanya yönetim hizmetlerini kapsar.",
    },
    {
      question: "WhatsApp dönüşüm sistemi nedir?",
      answer: "Web sitenize veya Instagram Reels reklamlarınıza gelen potansiyel müşterileri anında karşılayan, onlara 7/24 otomatik cevaplar verip randevu veya lead bilgisi toplayan n8n tabanlı bir otomasyondur.",
    },
    {
      question: "Teklif almak için ne paylaşmalıyım?",
      answer: "Aşağıdaki form üzerinden veya doğrudan WhatsApp ile işletme adınızı, web sitenizi (varsa) ve dijital reklam bütçe/hedefinizi bizimle paylaşmanız yeterlidir.",
    },
  ];

  const toggleFAQ = (index: number) => {
    setOpenIndex(openIndex === index ? null : index);
  };

  const faqSchema = {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": faqs.map((faq) => ({
      "@type": "Question",
      "name": faq.question,
      "acceptedAnswer": {
        "@type": "Answer",
        "text": faq.answer,
      },
    })),
  };

  return (
    <section id="faq" className="py-32 bg-layout-gray px-6 md:px-12 border-t border-black/5 overflow-hidden">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }}
      />
      <div className="max-w-4xl mx-auto flex flex-col items-center">
        
        {/* Label */}
        <span className="font-plus-jakarta text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-500 block mb-5">
          ( Sıkça Sorulan Sorular )
        </span>

        {/* Headline */}
        <h2 className="font-plus-jakarta text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-[#1c1c1e] text-center mb-16">
          Merak Edilenler
        </h2>

        {/* Accordion List */}
        <div className="w-full flex flex-col gap-5">
          {faqs.map((faq, index) => {
            const isOpen = openIndex === index;
            return (
              <div
                key={index}
                className="group bg-white rounded-[28px] border border-black/[0.04] overflow-hidden transition-all duration-500 shadow-sm hover:shadow-md hover:border-black/10"
              >
                {/* Accordion Header */}
                <button
                  onClick={() => toggleFAQ(index)}
                  className="w-full py-6 px-6 md:px-10 flex items-center justify-between gap-6 text-left clickable focus:outline-none"
                  data-cursor-text={isOpen ? "KAPAT" : "AÇ"}
                >
                  <span className="font-plus-jakarta text-[15px] md:text-[17px] font-bold text-[#1c1c1e] select-none transition-colors duration-300 group-hover:text-accent-orange">
                    {faq.question}
                  </span>
                  <div 
                    className={`p-2.5 rounded-full bg-layout-light text-[#1c1c1e] shrink-0 transition-transform duration-500 ease-[0.16,1,0.3,1] ${isOpen ? 'rotate-45' : 'rotate-0'}`}
                  >
                    <Plus size={18} strokeWidth={2.5} />
                  </div>
                </button>

                {/* Accordion Content */}
                <AnimatePresence initial={false}>
                  {isOpen && (
                    <motion.div
                      initial={{ height: 0, opacity: 0 }}
                      animate={{ height: "auto", opacity: 1 }}
                      exit={{ height: 0, opacity: 0 }}
                      transition={{ duration: 0.4, ease: [0.16, 1, 0.3, 1] }}
                    >
                      <div className="pb-8 px-6 md:px-10 text-left font-inter text-neutral-500 text-[14px] md:text-[15px] leading-relaxed font-normal select-none">
                        {faq.answer}
                      </div>
                    </motion.div>
                  )}
                </AnimatePresence>
              </div>
            );
          })}
        </div>

      </div>
    </section>
  );
}

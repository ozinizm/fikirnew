"use client";

import React, { useState, useEffect, useRef } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Play, X, ArrowUpRight, Filter } from "lucide-react";
import {
  getBunnyEmbedUrl,
  getBunnyThumbnailUrl,
  portfolioItems as staticItems,
  type PortfolioItem,
} from "@/lib/portfolio-data";

type DisplayPortfolioItem = PortfolioItem & {
  mediaType?: "image" | "video";
  mediaUrl?: string;
  posterUrl?: string;
};



/* ─── Bunny Video Modal ─── */
function VideoModal({
  item,
  onClose,
}: {
  item: DisplayPortfolioItem;
  onClose: () => void;
}) {
  const hasDirectVideo = item.mediaType === "video" && Boolean(item.mediaUrl);
  const hasImage = item.mediaType === "image" && Boolean(item.mediaUrl || item.thumbnailUrl);
  const isPlaceholder =
    !hasDirectVideo &&
    !hasImage &&
    (!item.bunnyVideoId ||
      item.bunnyVideoId === "BUNNY_VIDEO_ID_PLACEHOLDER" ||
      !item.bunnyLibraryId ||
      item.bunnyLibraryId === "BUNNY_LIBRARY_ID_PLACEHOLDER");

  const embedUrl = isPlaceholder
    ? ""
    : getBunnyEmbedUrl(item.bunnyLibraryId, item.bunnyVideoId);

  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      transition={{ duration: 0.25 }}
      className="fixed inset-0 z-[200] flex items-center justify-center bg-black/85 backdrop-blur-md px-4"
      onClick={onClose}
    >
      <motion.div
        initial={{ scale: 0.92, opacity: 0, y: 24 }}
        animate={{ scale: 1, opacity: 1, y: 0 }}
        exit={{ scale: 0.92, opacity: 0, y: 24 }}
        transition={{ type: "spring", stiffness: 340, damping: 28 }}
        className="relative w-full max-w-5xl bg-[#111] rounded-3xl overflow-hidden shadow-2xl"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Close */}
        <button
          onClick={onClose}
          className="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 transition-colors flex items-center justify-center text-white"
          aria-label="Kapat"
        >
          <X size={18} />
        </button>

        {/* Video Player */}
        <div className="aspect-video w-full bg-black">
          {hasDirectVideo ? (
            <video
              src={item.mediaUrl}
              poster={item.posterUrl || item.thumbnailUrl}
              className="w-full h-full object-contain"
              controls
              playsInline
            />
          ) : hasImage ? (
            <img
              src={item.mediaUrl || item.thumbnailUrl || ""}
              alt={item.title}
              className="w-full h-full object-contain"
            />
          ) : isPlaceholder ? (
            <div className="w-full h-full flex flex-col items-center justify-center text-white/40 gap-3">
              <Play size={48} className="opacity-30" />
              <span className="font-inter text-sm">
                Video henüz yüklenmedi — Bunny.net Library ID ve Video GUID gerekli
              </span>
            </div>
          ) : (
            <iframe
              src={embedUrl}
              className="w-full h-full"
              allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;"
              allowFullScreen
              title={item.title}
              style={{ border: "none" }}
            />
          )}
        </div>

        {/* Info */}
        <div className="p-8 flex flex-col md:flex-row gap-6 md:items-start">
          <div className="flex-1">
            <span className="text-[10px] font-bold tracking-widest text-[#f97316] uppercase mb-2 block">
              {item.category} / {item.year}
            </span>
            <h3 className="font-plus-jakarta text-2xl font-extrabold text-white mb-3">
              {item.title}
            </h3>
            <p className="font-inter text-sm text-neutral-400 leading-relaxed">
              {item.description}
            </p>
          </div>
          {item.services && item.services.length > 0 && (
            <div className="flex flex-col gap-2 min-w-[200px]">
              <span className="text-[10px] font-bold tracking-widest text-neutral-500 uppercase">
                Hizmetler
              </span>
              <div className="flex flex-wrap gap-2 mt-1">
                {item.services.map((s) => (
                  <span
                    key={s}
                    className="text-[11px] font-medium text-white/70 bg-white/5 border border-white/10 px-3 py-1 rounded-full"
                  >
                    {s}
                  </span>
                ))}
              </div>
            </div>
          )}
        </div>
      </motion.div>
    </motion.div>
  );
}

/* ─── Portfolio Card ─── */
function PortfolioCard({
  item,
  index,
  onPlay,
}: {
  item: DisplayPortfolioItem;
  index: number;
  onPlay: (item: DisplayPortfolioItem) => void;
}) {
  const hasBunny =
    item.bunnyLibraryId &&
    item.bunnyLibraryId !== "BUNNY_LIBRARY_ID_PLACEHOLDER" &&
    item.bunnyVideoId &&
    item.bunnyVideoId !== "BUNNY_VIDEO_ID_PLACEHOLDER";

  const thumbnailUrl =
    item.thumbnailUrl ||
    (item.mediaType === "image" ? item.mediaUrl : "") ||
    (hasBunny
      ? getBunnyThumbnailUrl(item.bunnyLibraryId, item.bunnyVideoId)
      : "");

  return (
    <motion.article
      initial={{ opacity: 0, y: 40 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: "-80px" }}
      transition={{ duration: 0.6, delay: index * 0.08, ease: [0.16, 1, 0.3, 1] }}
      className="group relative rounded-[28px] overflow-hidden bg-[#111] border border-white/5 cursor-pointer"
      onClick={() => onPlay(item)}
    >
      {/* Thumbnail */}
      <div className="relative aspect-video overflow-hidden bg-[#1a1a1a] rounded-t-[28px]">
        {thumbnailUrl ? (
          <img
            src={thumbnailUrl}
            alt={item.title}
            loading="lazy"
            decoding="async"
            className="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105 rounded-t-[28px]"
          />
        ) : (
          /* No thumbnail — show gradient placeholder */
          <div className="w-full h-full bg-gradient-to-br from-[#1e1e1e] to-[#111] flex items-center justify-center">
            <Play size={32} className="text-white/10" />
          </div>
        )}

        {/* Overlay */}
        <div className="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent" />

        {/* Play button */}
        <div className="absolute inset-0 flex items-center justify-center">
          <motion.div
            whileHover={{ scale: 1.1 }}
            whileTap={{ scale: 0.95 }}
            className="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
          >
            <Play size={22} className="text-white ml-1" fill="white" />
          </motion.div>
        </div>

        {/* Category badge */}
        <div className="absolute top-4 left-4">
          <span className="text-[9px] font-bold tracking-widest text-white/80 bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10 uppercase">
            {item.category}
          </span>
        </div>

        {/* Arrow */}
        <div className="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          <div className="w-8 h-8 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center">
            <ArrowUpRight size={14} className="text-white" />
          </div>
        </div>
      </div>

      {/* Card Footer */}
      <div className="p-6">
        <div className="flex items-start justify-between gap-4">
          <div>
            <h3 className="font-plus-jakarta text-lg font-bold text-white mb-1 group-hover:text-[#f97316] transition-colors">
              {item.title}
            </h3>
            <p className="font-inter text-xs text-neutral-500 line-clamp-2">
              {item.description}
            </p>
          </div>
          <span className="font-plus-jakarta text-xs font-bold text-neutral-600 shrink-0 mt-0.5">
            {item.year}
          </span>
        </div>

        {item.services && item.services.length > 0 && (
          <div className="flex flex-wrap gap-1.5 mt-4">
            {item.services.slice(0, 3).map((s) => (
              <span
                key={s}
                className="text-[10px] font-medium text-neutral-500 bg-white/3 border border-white/8 px-2.5 py-1 rounded-full"
              >
                {s}
              </span>
            ))}
          </div>
        )}
      </div>
    </motion.article>
  );
}

/* ─── Main Portfolio Page ─── */
export default function PortfolioPage() {
  const [items, setItems] = useState<DisplayPortfolioItem[]>(staticItems);
  const [activeCategory, setActiveCategory] = useState("Tümü");
  const [activeVideo, setActiveVideo] = useState<DisplayPortfolioItem | null>(null);

  // Fetch from public API and fall back to static data if it is unavailable.
  useEffect(() => {
    fetch("/api/portfolio")
      .then((r) => r.json())
      .then((data) => {
        if (data.items && data.items.length > 0) {
          setItems(data.items);
        }
      })
      .catch(() => {
        // Fallback to static data silently
      });
  }, []);

  // Build categories from current items
  const categories = [
    "Tümü",
    ...Array.from(new Set(items.map((p) => p.category))),
  ];

  const filtered =
    activeCategory === "Tümü"
      ? items
      : items.filter((p) => p.category === activeCategory);

  return (
    <div className="min-h-screen text-white pt-32 pb-0">
      
      {/* Centered Portfolio Card matching the footer shape, size and styling */}
      <div className="relative w-[calc(100%-32px)] md:w-[calc(100%-48px)] mx-auto max-w-[1920px] rounded-[32px] bg-[#0c0c0e] border border-white/5 shadow-2xl mb-4 overflow-hidden">
        
        {/* Background glow effects inside the card */}
        <div className="absolute inset-0 -z-10 pointer-events-none overflow-hidden">
          <div className="absolute top-0 left-1/4 w-[50vw] h-[50vw] rounded-full bg-[#f97316]/5 blur-[140px]" />
          <div className="absolute bottom-0 right-1/4 w-[40vw] h-[40vw] rounded-full bg-[#f97316]/3 blur-[120px]" />
        </div>

        {/* ─── Hero ─── */}
        <section className="relative pt-16 pb-10 px-6 md:px-12">
          <div className="max-w-6xl mx-auto">
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.7, ease: [0.16, 1, 0.3, 1] }}
            >
              <span className="font-plus-jakarta text-[11px] font-bold uppercase tracking-[0.25em] text-neutral-500 block mb-5">
                ( Seçili Çalışmalar )
              </span>
              <h1 className="font-plus-jakarta text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-white leading-[0.95] mb-6">
                Portfolyo
              </h1>
              <p className="font-inter text-base text-neutral-400 max-w-xl leading-relaxed">
                Yerel işletmeler için ürettiğimiz dijital büyüme sistemleri, web
                tasarımları ve marka kimlikleri.
              </p>
            </motion.div>

            {/* Stats */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.7, delay: 0.15, ease: [0.16, 1, 0.3, 1] }}
              className="flex flex-wrap gap-10 mt-10 border-t border-white/8 pt-8"
            >
              {[
                { value: "40+", label: "Tamamlanan Proje" },
                { value: "4 Yıl", label: "Sektör Deneyimi" },
                { value: "%93", label: "Müşteri Memnuniyeti" },
              ].map((stat) => (
                <div key={stat.label} className="flex flex-col gap-1">
                  <span className="font-plus-jakarta text-2xl sm:text-3xl font-extrabold text-white">
                    {stat.value}
                  </span>
                  <span className="font-inter text-[10px] sm:text-xs text-neutral-500">
                    {stat.label}
                  </span>
                </div>
              ))}
            </motion.div>
          </div>
        </section>

        {/* ─── Filter Bar ─── */}
        <section className="px-6 md:px-12 pb-8">
          <div className="max-w-6xl mx-auto">
            <motion.div
              initial={{ opacity: 0, y: 16 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: 0.2 }}
              className="flex flex-wrap items-center gap-2"
            >
              <Filter size={14} className="text-neutral-600 mr-1" />
              {categories.map((cat) => (
                <button
                  key={cat}
                  onClick={() => setActiveCategory(cat)}
                  className={`font-inter text-xs font-semibold px-4 py-2 rounded-full border transition-all duration-300 ${
                    activeCategory === cat
                      ? "bg-white text-black border-white"
                      : "bg-white/5 text-neutral-400 border-white/10 hover:border-white/20 hover:text-white"
                  }`}
                >
                  {cat}
                </button>
              ))}
            </motion.div>
          </div>
        </section>

        {/* ─── Grid ─── */}
        <section className="px-6 md:px-12 pb-24">
          <div className="max-w-6xl mx-auto">
            <AnimatePresence mode="wait">
              <motion.div
                key={activeCategory}
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                transition={{ duration: 0.3 }}
                className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"
              >
                {filtered.map((item, index) => (
                  <PortfolioCard
                    key={item.id}
                    item={item}
                    index={index}
                    onPlay={(item) => setActiveVideo(item)}
                  />
                ))}
              </motion.div>
            </AnimatePresence>

            {filtered.length === 0 && (
              <div className="text-center py-20 text-neutral-600 font-inter text-sm">
                Bu kategoride henüz proje yok.
              </div>
            )}
          </div>
        </section>

      </div>

      {/* ─── Video Modal ─── */}
      <AnimatePresence>
        {activeVideo && (
          <VideoModal
            item={activeVideo}
            onClose={() => setActiveVideo(null)}
          />
        )}
      </AnimatePresence>
    </div>
  );
}

import { query, execute, type SqlParam } from "@/lib/db";
import type { RowDataPacket } from "mysql2/promise";
import { portfolioItems, type PortfolioItem } from "@/lib/portfolio-data";

export type SiteSettings = Record<string, string>;

type SettingRow = {
  ayar_key: string;
  ayar_val: string | null;
} & RowDataPacket;

type ServiceRow = {
  id: number;
  baslik: string;
  aciklama: string | null;
  ikon_svg: string | null;
  sira: number;
} & RowDataPacket;

type PortfolioRow = {
  id: number;
  baslik: string;
  kategori: string;
  medya_turu: "resim" | "video";
  medya_url: string | null;
  gorsel_url: string | null;
  sira: number;
} & RowDataPacket;

type PageRow = {
  slug: string;
  updated_at: Date | string | null;
  published_at: Date | string | null;
} & RowDataPacket;

export type CmsService = {
  id: string;
  title: string;
  tabLabel: string;
  description: string;
  bullets: string[];
  image: string;
};

export type CmsPortfolioItem = PortfolioItem & {
  mediaType?: "image" | "video";
  mediaUrl?: string;
  posterUrl?: string;
};

export function normalizeAssetPath(value?: string | null): string {
  if (!value) return "";
  if (/^https?:\/\//i.test(value)) return value;
  return `/${value.replace(/^\/+/, "")}`;
}

function splitBullets(text: string): string[] {
  return text
    .split(/\r?\n|•|-/)
    .map((item) => item.trim())
    .filter((item) => item.length > 8)
    .slice(0, 3);
}

async function safeQuery<T extends RowDataPacket>(sql: string, params?: SqlParam[]): Promise<T[]> {
  if (process.env.IS_BUILDING) {
    return [];
  }
  try {
    return await query<T>(sql, params);
  } catch {
    return [];
  }
}

export function getSetting(settings: SiteSettings, key: string, fallback = ""): string {
  const value = settings[key];
  return value && value.trim() ? value.trim() : fallback;
}

export function getSiteUrl(settings: SiteSettings): string {
  return getSetting(settings, "site_url", "https://www.fikircreative.com").replace(/\/+$/, "");
}

export async function getSiteSettings(): Promise<SiteSettings> {
  const rows = await safeQuery<SettingRow>("SELECT ayar_key, ayar_val FROM ayarlar");
  return rows.reduce<SiteSettings>((acc, row) => {
    acc[row.ayar_key] = row.ayar_val ?? "";
    return acc;
  }, {});
}

export async function getPublishedPagePaths(): Promise<string[]> {
  const rows = await safeQuery<PageRow>(
    "SELECT slug, updated_at, published_at FROM pages WHERE status = 'published' ORDER BY updated_at DESC"
  );
  return rows.map((row) => `/${String(row.slug).replace(/^\/+/, "")}`);
}

export async function getCmsServices(): Promise<CmsService[]> {
  const rows = await safeQuery<ServiceRow>(
    "SELECT id, baslik, aciklama, ikon_svg, sira FROM hizmetler ORDER BY sira ASC, id ASC"
  );

  return rows.map((row) => ({
    id: String(row.id),
    title: row.baslik,
    tabLabel: row.baslik,
    description: row.aciklama ?? "",
    bullets: splitBullets(row.aciklama ?? ""),
    image: normalizeAssetPath(row.ikon_svg) || "https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png",
  }));
}

export async function getCmsPortfolioItems(): Promise<CmsPortfolioItem[]> {
  const rows = await safeQuery<PortfolioRow>(
    "SELECT id, baslik, kategori, medya_turu, medya_url, gorsel_url, sira FROM portfolyo ORDER BY sira ASC, id DESC"
  );

  if (rows.length === 0) {
    return portfolioItems;
  }

  return rows.map((row) => {
    const mediaUrl = normalizeAssetPath(row.medya_url);
    const posterUrl = normalizeAssetPath(row.gorsel_url);
    return {
      id: `panel-${row.id}`,
      title: row.baslik,
      category: row.kategori,
      description: row.kategori,
      client: row.baslik,
      year: new Date().getFullYear().toString(),
      services: [row.kategori],
      bunnyLibraryId: "",
      bunnyVideoId: "",
      thumbnailUrl: posterUrl || (row.medya_turu === "resim" ? mediaUrl : ""),
      featured: true,
      order: row.sira,
      mediaType: row.medya_turu === "video" ? "video" : "image",
      mediaUrl,
      posterUrl,
    };
  });
}

export async function saveContactMessage(input: {
  name: string;
  email: string;
  subject: string;
  message: string;
}) {
  await execute(
    "INSERT INTO mesajlar (ad_soyad, eposta, konu, mesaj, okundu) VALUES (?, ?, ?, ?, 0)",
    [input.name, input.email, input.subject, input.message]
  );
}

type StatRow = {
  id: number;
  deger: string;
  etiket: string;
  sira: number;
} & RowDataPacket;

type TestimonialRow = {
  id: number;
  isim: string;
  unvan: string;
  mesaj: string;
  foto: string | null;
  sira: number;
} & RowDataPacket;

type TeamRow = {
  id: number;
  ad_soyad: string;
  gorev: string | null;
  bio: string | null;
  instagram: string | null;
  linkedin: string | null;
  foto: string | null;
  sira: number;
} & RowDataPacket;

export type CmsStat = {
  number: string;
  label: string;
};

export type CmsTestimonial = {
  quote: string;
  author: string;
  role: string;
  image: string;
};

export type CmsTeamMember = {
  name: string;
  role: string;
  bio: string;
  instagram: string;
  linkedin: string;
  image: string;
};

export async function getCmsStats(): Promise<CmsStat[]> {
  const rows = await safeQuery<StatRow>(
    "SELECT deger, etiket FROM istatistikler ORDER BY sira ASC, id ASC"
  );
  if (rows.length === 0) {
    return [
      { number: "26+", label: "Kurulan Dijital Sistem" },
      { number: "98%", label: "Müşteri Kazanım Odağı" },
      { number: "10M", label: "Yerel İşletme Deneyimi" }
    ];
  }
  return rows.map((row) => ({
    number: row.deger,
    label: row.etiket
  }));
}

export async function getCmsTestimonials(): Promise<CmsTestimonial[]> {
  const rows = await safeQuery<TestimonialRow>(
    "SELECT isim, unvan, mesaj, foto FROM referanslar ORDER BY sira ASC, id DESC"
  );
  if (rows.length === 0) {
    return [
      {
        quote: "Fikir Creative, fikirlerimizi keskin ve temiz bir markaya dönüştürdü. Hızlı, pratik ve doğrudan hedefe ulaştık.",
        author: "Ethan Moore",
        role: "Kurucu Ortak, NovaTech",
        image: "/why-slide-1.png"
      },
      {
        quote: "Açık, düşünceli ve hızlılar. Web sitemizi kurup reklamlarımızı optimize etme sürecini tamamen zahmetsiz hale getirdiler.",
        author: "Olivia Tran",
        role: "Kreatif Direktör, Bloom Agency",
        image: "/why-slide-2.png"
      },
      {
        quote: "Akıllıca bir web mimarisi, sorunsuz teslimat. Fikir Creative ekibiyle çalışmak yerel büyümemiz için mükemmel bir adımdı.",
        author: "Lucas Bennett",
        role: "Ürün Yöneticisi, Hexa Studio",
        image: "/why-slide-3.png"
      }
    ];
  }
  return rows.map((row) => ({
    quote: row.mesaj,
    author: row.isim,
    role: row.unvan,
    image: normalizeAssetPath(row.foto) || "/why-slide-1.png"
  }));
}

export async function getCmsTeam(): Promise<CmsTeamMember[]> {
  const rows = await safeQuery<TeamRow>(
    "SELECT ad_soyad, gorev, bio, instagram, linkedin, foto FROM ekip ORDER BY sira ASC, id ASC"
  );
  return rows.map((row) => ({
    name: row.ad_soyad,
    role: row.gorev ?? "",
    bio: row.bio ?? "",
    instagram: row.instagram ?? "",
    linkedin: row.linkedin ?? "",
    image: normalizeAssetPath(row.foto) || "https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=80&q=80"
  }));
}

export interface CmsBrand {
  id: string;
  name: string;
  logo: string | null;
  logoSvg: string | null;
  sortOrder: number;
}

export async function getCmsBrands(): Promise<CmsBrand[]> {
  const rows = await safeQuery<{ id: number; isim: string; logo: string | null; logo_svg: string | null; sira: number } & RowDataPacket>(
    "SELECT id, isim, logo, logo_svg, sira FROM markalar ORDER BY sira ASC, id ASC"
  );
  return rows.map((row) => ({
    id: String(row.id),
    name: row.isim,
    logo: row.logo ? normalizeAssetPath(row.logo) : null,
    logoSvg: row.logo_svg,
    sortOrder: row.sira,
  }));
}


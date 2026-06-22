/**
 * Portfolio item data types and static store.
 * These defaults are used when no external portfolio data file is available.
 */

export interface PortfolioItem {
  id: string;
  title: string;
  category: string;
  description: string;
  client?: string;
  year: string;
  services: string[];
  /** Bunny.net Stream Video Library ID */
  bunnyLibraryId: string;
  /** Bunny.net Stream Video GUID */
  bunnyVideoId: string;
  /** Optional static thumbnail override (if not using Bunny thumbnail) */
  thumbnailUrl?: string;
  /** Featured on homepage Works section */
  featured: boolean;
  order: number;
}

/** Bunny.net embed URL builder */
export function getBunnyEmbedUrl(libraryId: string, videoId: string): string {
  return `https://iframe.mediadelivery.net/embed/${libraryId}/${videoId}?autoplay=false&loop=false&muted=false&preload=true&responsive=true`;
}

/** Bunny.net thumbnail URL builder */
export function getBunnyThumbnailUrl(libraryId: string, videoId: string): string {
  return `https://vz-${libraryId}.b-cdn.net/${videoId}/thumbnail.jpg`;
}

/**
 * Static portfolio data used as a safe fallback.
 * libraryId and videoId values are placeholders; fill them from Bunny dashboard.
 */
export const portfolioItems: PortfolioItem[] = [
  {
    id: "archin-2025",
    title: "Archin",
    category: "Web Tasarım",
    description:
      "Mimarlık stüdyosu için modern portfolio sitesi ve marka kimliği tasarımı. Minimalist yaklaşım, maksimum etki.",
    client: "Archin Studio",
    year: "2025",
    services: ["Web Tasarım", "Marka Kimliği", "Geliştirme"],
    bunnyLibraryId: "BUNNY_LIBRARY_ID_PLACEHOLDER",
    bunnyVideoId: "BUNNY_VIDEO_ID_PLACEHOLDER",
    thumbnailUrl:
      "https://framerusercontent.com/images/olR1jd1vAg59BKYSorw26ZNxY.png",
    featured: true,
    order: 1,
  },
  {
    id: "vntnr-2018",
    title: "VNTNR",
    category: "Marka Kimliği",
    description:
      "Girişim için özgün logo tasarımı ve kapsamlı marka kimliği yenileme süreci.",
    client: "VNTNR Startup",
    year: "2018",
    services: ["Logo Tasarım", "Marka Kimliği", "Yenileme"],
    bunnyLibraryId: "BUNNY_LIBRARY_ID_PLACEHOLDER",
    bunnyVideoId: "BUNNY_VIDEO_ID_PLACEHOLDER",
    thumbnailUrl:
      "https://framerusercontent.com/images/QhPkJGJBXS8kPS7IhPj7ZBGZpII.png",
    featured: true,
    order: 2,
  },
  {
    id: "aeorim-2023",
    title: "Aeorim",
    category: "Revamp",
    description:
      "Teknoloji firması için kapsamlı web sitesi revampı ve kullanıcı deneyimi optimizasyonu.",
    client: "Aeorim Tech",
    year: "2023",
    services: ["Marka Kimliği", "Revamp", "Geliştirme"],
    bunnyLibraryId: "BUNNY_LIBRARY_ID_PLACEHOLDER",
    bunnyVideoId: "BUNNY_VIDEO_ID_PLACEHOLDER",
    thumbnailUrl:
      "https://framerusercontent.com/images/yOPV9nZRSJXmNPqyeWfZSThWAc.png",
    featured: true,
    order: 3,
  },
];

/** Categories derived from portfolio items */
export const portfolioCategories = [
  "Tümü",
  ...Array.from(new Set(portfolioItems.map((p) => p.category))),
];

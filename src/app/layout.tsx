import type { Metadata } from "next";
import { Plus_Jakarta_Sans, Inter } from "next/font/google";
import "./globals.css";
import SmoothScroll from "@/components/SmoothScroll";
import CustomCursor from "@/components/CustomCursor";
import TrackingScripts from "@/components/TrackingScripts";
import { getSiteSettings, getSetting, normalizeAssetPath } from "@/lib/cms-data";

const plusJakarta = Plus_Jakarta_Sans({
  variable: "--font-plus-jakarta-sans",
  subsets: ["latin"],
  weight: ["500", "600", "700", "800"],
});

const inter = Inter({
  variable: "--font-inter",
  subsets: ["latin"],
  weight: ["300", "400", "500", "600", "700"],
});

export async function generateMetadata(): Promise<Metadata> {
  const settings = await getSiteSettings();
  
  const siteTitle = getSetting(settings, "seo_title", "Fikir Creative — Dijital Büyüme Ajansı");
  const siteDesc = getSetting(settings, "seo_desc", "Fikir Creative, yerel işletmeler için web sitesi, performans pazarlaması, video prodüksiyon ve WhatsApp dönüşüm sistemiyle müşteri kazandıran dijital büyüme motoru kurar.");
  const siteKeys = getSetting(settings, "seo_keys", "dijital ajans, yerel işletme pazarlaması, web tasarım, performans pazarlaması, reklam ajansı, whatsapp otomasyonu, dijital büyüme");
  const siteUrl = getSetting(settings, "site_url", "https://www.fikircreative.com").replace(/\/+$/, "");
  const ogImg = normalizeAssetPath(getSetting(settings, "og_image_default", "brand/og-image-default.png"));
  const geoRegion = getSetting(settings, "geo_region", "TR-34");
  const geoPlacename = getSetting(settings, "geo_placename", "Istanbul");
  const geoPosition = getSetting(settings, "geo_position", "");
  const icbm = getSetting(settings, "icbm", "");

  return {
    title: siteTitle,
    description: siteDesc,
    keywords: siteKeys.split(",").map(k => k.trim()),
    authors: [{ name: "Fikir Creative" }],
    creator: "Fikir Creative",
    publisher: "Fikir Creative",
    robots: {
      index: true,
      follow: true,
      googleBot: {
        index: true,
        follow: true,
        'max-video-preview': -1,
        'max-image-preview': 'large',
        'max-snippet': -1,
      },
    },
    alternates: {
      canonical: siteUrl,
    },
    icons: {
      icon: "/api/site-icon",
      shortcut: "/api/site-icon",
      apple: "/brand/apple-touch-icon.png",
    },
    openGraph: {
      title: siteTitle,
      description: siteDesc,
      type: "website",
      locale: "tr_TR",
      url: siteUrl + "/",
      siteName: "Fikir Creative",
      images: [
        {
          url: ogImg,
          width: 1200,
          height: 630,
          alt: siteTitle,
        }
      ]
    },
    twitter: {
      card: "summary_large_image",
      title: siteTitle,
      description: siteDesc,
      images: [ogImg],
    },
    other: {
      "geo.region": geoRegion,
      "geo.placename": geoPlacename,
      ...(geoPosition ? { "geo.position": geoPosition } : {}),
      ...(icbm ? { "ICBM": icbm } : {}),
    }
  };
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html
      lang="tr"
      className={`${plusJakarta.variable} ${inter.variable} h-full antialiased`}
    >
      <body className="min-h-full flex flex-col">
        <TrackingScripts />
        <SmoothScroll>
          <CustomCursor />
          {children}
        </SmoothScroll>
      </body>
    </html>
  );
}

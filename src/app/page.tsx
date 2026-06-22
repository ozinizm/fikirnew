import Header from "@/components/Header";
import Hero from "@/components/Hero";
import Marquee from "@/components/Marquee";
import About from "@/components/About";
import StatsGrid from "@/components/StatsGrid";
import Works from "@/components/Works";
import Services from "@/components/Services";
import Profile from "@/components/Profile";
import Packages from "@/components/Packages";
import FAQ from "@/components/FAQ";
import Contact from "@/components/Contact";
import Footer from "@/components/Footer";
import { getSiteSettings, getCmsStats, getCmsTestimonials, getCmsTeam, getCmsBrands, getSetting } from "@/lib/cms-data";

export const dynamic = "force-dynamic";

export default async function Home() {
  const settings = await getSiteSettings();
  const stats = await getCmsStats();
  const testimonials = await getCmsTestimonials();
  const team = await getCmsTeam();
  const brands = await getCmsBrands();

  // AEO/GEO/SEO Schema Builder
  const siteUrl = getSetting(settings, "site_url", "https://www.fikircreative.com").replace(/\/+$/, "");
  const siteDesc = getSetting(settings, "seo_desc", "Fikir Creative, yerel işletmeler için web sitesi, performans pazarlaması, video prodüksiyon ve WhatsApp dönüşüm sistemiyle müşteri kazandıran dijital büyüme motoru kurar.");
  const phone = getSetting(settings, "telefon", "");
  const email = getSetting(settings, "email", "");
  const address = getSetting(settings, "adres", "İstanbul, Türkiye");
  const geoPosition = getSetting(settings, "geo_position", "");
  const geoRegion = getSetting(settings, "geo_region", "TR-34");
  const geoPlacename = getSetting(settings, "geo_placename", "Istanbul");

  let geoCoordinates = null;
  if (geoPosition && geoPosition.includes(";")) {
    const [lat, lon] = geoPosition.split(";").map(c => c.trim());
    if (lat && lon) {
      geoCoordinates = {
        "@type": "GeoCoordinates",
        "latitude": lat,
        "longitude": lon
      };
    }
  }

  const socialLinks = [
    getSetting(settings, "instagram"),
    getSetting(settings, "linkedin"),
    getSetting(settings, "youtube")
  ].filter(Boolean);

  const businessSchema = {
    "@context": "https://schema.org",
    "@type": "ProfessionalService",
    "name": getSetting(settings, "site_baslik", "Fikir Creative"),
    "description": siteDesc,
    "url": siteUrl + "/",
    "logo": siteUrl + "/api/site-icon",
    "image": siteUrl + "/brand/og-image-default.png",
    "telephone": phone || "+905320000000",
    "email": email || "hello@fikircreative.com",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": geoPlacename,
      "addressRegion": geoRegion,
      "addressCountry": "TR",
      "streetAddress": address
    },
    ...(geoCoordinates ? { "geo": geoCoordinates } : {}),
    "sameAs": socialLinks.length > 0 ? socialLinks : [
      "https://instagram.com/fikircreative",
      "https://linkedin.com/company/fikircreative"
    ],
    "priceRange": "$$"
  };

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(businessSchema) }}
      />
      <Header settings={settings} />
      <main className="flex-1 w-full">
        <Hero settings={settings} />
        <Marquee brands={brands} />
        <About settings={settings} />
        <StatsGrid stats={stats} testimonials={testimonials} />
        <Works />
        <Services />
        <Profile settings={settings} team={team} />
        <Packages />
        <FAQ />
        <Contact />
        <Footer settings={settings} />
      </main>
    </>
  );
}


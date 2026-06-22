import type { Metadata } from "next";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import PortfolioPage from "@/components/PortfolioPage";

export const metadata: Metadata = {
  title: "Portfolyo — Fikir Creative | Dijital Büyüme Ajansı",
  description:
    "Fikir Creative tarafından tasarlanan web siteleri, marka kimlikleri ve dijital büyüme sistemleri. Tüm çalışmalarımızı inceleyin.",
  openGraph: {
    title: "Portfolyo — Fikir Creative | Dijital Büyüme Ajansı",
    description:
      "Yerel işletmeler için ürettiğimiz dijital büyüme sistemleri, web tasarımları ve marka kimlikleri.",
    type: "website",
    locale: "tr_TR",
    url: "https://www.fikircreative.com/portfolio",
  },
};

export default function Portfolio() {
  return (
    <>
      <Header />
      <main className="flex-1 w-full">
        <PortfolioPage />
      </main>
      <Footer />
    </>
  );
}

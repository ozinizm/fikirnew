import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { getSetting, getSiteSettings } from "@/lib/cms-data";

type LegalContentProps = {
  title: string;
  settingKey: string;
  fallback: string;
};

export default async function LegalContent({ title, settingKey, fallback }: LegalContentProps) {
  const settings = await getSiteSettings();
  const html = getSetting(settings, settingKey, fallback);

  return (
    <main className="bg-layout-gray min-h-screen text-text-primary">
      <Header settings={settings} />
      <section className="pt-40 pb-20 px-6">
        <div className="max-w-4xl mx-auto">
          <p className="font-inter text-xs font-bold uppercase tracking-[0.28em] text-accent-orange mb-5">
            Hukuki Metin
          </p>
          <h1 className="font-plus-jakarta text-4xl md:text-6xl font-black tracking-tight mb-10 text-text-primary">
            {title}
          </h1>
          <article
            className="legal-content font-inter text-base md:text-lg leading-8 text-text-secondary"
            dangerouslySetInnerHTML={{ __html: html }}
          />
        </div>
      </section>
      <Footer settings={settings} />
    </main>
  );
}


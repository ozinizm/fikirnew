import type { Metadata } from "next";
import LegalContent from "@/components/LegalContent";

export const metadata: Metadata = {
  title: "Çerez Politikası",
};

export default function Page() {
  return (
    <LegalContent
      title="Çerez Politikası"
      settingKey="politika_cerezler"
      fallback="<p>Çerez politikası metni admin panelinden düzenlenebilir.</p>"
    />
  );
}


import type { Metadata } from "next";
import LegalContent from "@/components/LegalContent";

export const metadata: Metadata = {
  title: "Gizlilik Politikası",
};

export default function Page() {
  return (
    <LegalContent
      title="Gizlilik Politikası"
      settingKey="politika_gizlilik"
      fallback="<p>Gizlilik politikası metni admin panelinden düzenlenebilir.</p>"
    />
  );
}


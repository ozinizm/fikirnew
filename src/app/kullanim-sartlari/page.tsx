import type { Metadata } from "next";
import LegalContent from "@/components/LegalContent";

export const metadata: Metadata = {
  title: "Kullanım Şartları",
};

export default function Page() {
  return (
    <LegalContent
      title="Kullanım Şartları"
      settingKey="politika_kullanim"
      fallback="<p>Kullanım şartları metni admin panelinden düzenlenebilir.</p>"
    />
  );
}


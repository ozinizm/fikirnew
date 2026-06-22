import type { Metadata } from "next";
import LegalContent from "@/components/LegalContent";

export const metadata: Metadata = {
  title: "KVKK Aydınlatma Metni",
};

export default function Page() {
  return (
    <LegalContent
      title="KVKK Aydınlatma Metni"
      settingKey="politika_kvkk"
      fallback="<p>KVKK aydınlatma metni admin panelinden düzenlenebilir.</p>"
    />
  );
}


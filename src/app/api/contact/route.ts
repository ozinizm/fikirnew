import { NextRequest, NextResponse } from "next/server";
import { saveContactMessage } from "@/lib/cms-data";

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const name = String(body.name ?? "").trim();
    const email = String(body.email ?? "").trim();
    const goal = String(body.goal ?? body.message ?? "").trim();

    if (!name || !email || !goal) {
      return NextResponse.json({ error: "Eksik alan var." }, { status: 400 });
    }

    await saveContactMessage({
      name,
      email,
      subject: "Web sitesi teklif formu",
      message: goal,
    });

    return NextResponse.json({ success: true });
  } catch {
    return NextResponse.json({ error: "Mesaj kaydedilemedi." }, { status: 500 });
  }
}

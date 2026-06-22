import { NextRequest, NextResponse } from "next/server";
import { getSetting, getSiteSettings, normalizeAssetPath } from "@/lib/cms-data";

export async function GET(request: NextRequest) {
  const settings = await getSiteSettings();
  const image = normalizeAssetPath(getSetting(settings, "og_image_default", "public/hero-1.png"));
  return NextResponse.redirect(new URL(image === "/public/hero-1.png" ? "/hero-1.png" : image, request.url));
}


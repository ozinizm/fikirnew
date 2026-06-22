import { NextRequest, NextResponse } from "next/server";
import { getSetting, getSiteSettings, normalizeAssetPath } from "@/lib/cms-data";

export async function GET(request: NextRequest) {
  const settings = await getSiteSettings();
  const icon = normalizeAssetPath(getSetting(settings, "favicon_master", "brand/favicon-32x32.png"));
  return NextResponse.redirect(new URL(icon, request.url));
}


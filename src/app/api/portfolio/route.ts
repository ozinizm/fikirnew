import { NextResponse } from "next/server";
import { getCmsPortfolioItems } from "@/lib/cms-data";

export async function GET() {
  const items = await getCmsPortfolioItems();
  return NextResponse.json({ items });
}

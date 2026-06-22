import { NextResponse } from "next/server";
import { getCmsServices } from "@/lib/cms-data";

export async function GET() {
  const services = await getCmsServices();
  return NextResponse.json({ services });
}

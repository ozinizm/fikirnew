import { serveLocalAsset } from "@/lib/asset-response";

export const runtime = "nodejs";

export async function GET(_: Request, { params }: { params: Promise<{ path: string[] }> }) {
  const { path } = await params;
  return serveLocalAsset("uploads", path);
}

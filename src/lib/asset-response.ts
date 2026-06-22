import { promises as fs } from "fs";
import path from "path";
import { NextResponse } from "next/server";

const ASSET_ROOTS = {
  brand: path.join(process.cwd(), "brand"),
  images: path.join(process.cwd(), "images"),
  uploads: path.join(process.cwd(), "uploads"),
} as const;

const MIME_TYPES: Record<string, string> = {
  ".avif": "image/avif",
  ".gif": "image/gif",
  ".ico": "image/x-icon",
  ".jpg": "image/jpeg",
  ".jpeg": "image/jpeg",
  ".mp4": "video/mp4",
  ".png": "image/png",
  ".svg": "image/svg+xml; charset=utf-8",
  ".webm": "video/webm",
  ".webp": "image/webp",
};

export async function serveLocalAsset(baseDir: keyof typeof ASSET_ROOTS, segments: string[]) {
  const basePath = ASSET_ROOTS[baseDir];
  const requestedPath = path.resolve(basePath, ...segments);

  if (!requestedPath.startsWith(basePath + path.sep)) {
    return new NextResponse("Not found", { status: 404 });
  }

  try {
    const body = await fs.readFile(requestedPath);
    const contentType = MIME_TYPES[path.extname(requestedPath).toLowerCase()] || "application/octet-stream";

    return new NextResponse(body, {
      headers: {
        "Cache-Control": "public, max-age=31536000, immutable",
        "Content-Type": contentType,
      },
    });
  } catch {
    return new NextResponse("Not found", { status: 404 });
  }
}

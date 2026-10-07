export const getAssetUrl = (path: string | undefined | null): string => {
  if (!path || !path.trim()) return "";
  let clean = path.trim();
  if (clean.includes("127.0.0.1:8000")) {
    clean = clean.replace(/https?:\/\/127\.0\.0\.1:8000\/?/, "");
  }
  if (clean.includes("localhost:8000")) {
    clean = clean.replace(/https?:\/\/localhost:8000\/?/, "");
  }
  if (clean.startsWith("http://") || clean.startsWith("https://")) {
    return clean;
  }
  if (clean.includes("http://") || clean.includes("https://")) {
    const httpIdx = clean.indexOf("http");
    return clean.substring(httpIdx);
  }
  const base = (process.env.NEXT_PUBLIC_ASSET_URL || "http://127.0.0.1:8000").replace(/\/$/, "");
  const cleanPath = clean.startsWith("/") ? clean : `/${clean}`;
  return base ? `${base}${cleanPath}` : cleanPath;
};

export const resolveProductImageUrl = (imagePath: string | null | undefined): string | null => {
  if (!imagePath || !imagePath.trim()) return null;
  
  let path = imagePath.trim();
  
  if (path.startsWith("http://") || path.startsWith("https://")) {
    return path;
  }
  
  if (path.includes("127.0.0.1:8000")) {
    path = path.replace(/https?:\/\/127\.0\.0\.1:8000\/?/, "");
  }
  if (path.includes("localhost:8000")) {
    path = path.replace(/https?:\/\/localhost:8000\/?/, "");
  }

  if (path.startsWith("uploads/") || path.startsWith("/uploads/")) {
    return getAssetUrl(path);
  }

  return getAssetUrl(`uploads/products/${path}`);
};

export const getProductFallbackImage = (_productId?: number | string): string => {
  return "";
};

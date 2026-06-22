import { MetadataRoute } from 'next';

export default function sitemap(): MetadataRoute.Sitemap {
  const baseUrl = 'https://www.fikircreative.com';
  const policies = ['gizlilik-politikasi', 'kullanim-sartlari', 'cerez-politikasi', 'kvkk'];

  const sitemapItems: MetadataRoute.Sitemap = [
    {
      url: baseUrl,
      lastModified: new Date(),
      changeFrequency: 'weekly',
      priority: 1.0,
    },
    {
      url: `${baseUrl}/portfolio`,
      lastModified: new Date(),
      changeFrequency: 'monthly',
      priority: 0.8,
    },
  ];

  policies.forEach((policy) => {
    sitemapItems.push({
      url: `${baseUrl}/${policy}`,
      lastModified: new Date(),
      changeFrequency: 'yearly',
      priority: 0.3,
    });
  });

  return sitemapItems;
}

import Script from "next/script";
import { getSetting, getSiteSettings } from "@/lib/cms-data";

function stripScriptTags(value: string) {
  return value.replace(/<\/?script[^>]*>/gi, "").trim();
}

export default async function TrackingScripts() {
  const settings = await getSiteSettings();
  const ga4Id = getSetting(settings, "ga4_id");
  const gtmId = getSetting(settings, "gtm_id");
  const metaPixelId = getSetting(settings, "meta_pixel_id");
  const customAnalytics = stripScriptTags(getSetting(settings, "analytics"));

  return (
    <>
      {gtmId ? (
        <Script
          id="gtm"
          strategy="afterInteractive"
          dangerouslySetInnerHTML={{
            __html: `(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','${gtmId}');`,
          }}
        />
      ) : null}

      {ga4Id ? (
        <>
          <Script src={`https://www.googletagmanager.com/gtag/js?id=${ga4Id}`} strategy="afterInteractive" />
          <Script
            id="ga4"
            strategy="afterInteractive"
            dangerouslySetInnerHTML={{
              __html: `window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','${ga4Id}');`,
            }}
          />
        </>
      ) : null}

      {metaPixelId ? (
        <Script
          id="meta-pixel"
          strategy="afterInteractive"
          dangerouslySetInnerHTML={{
            __html: `!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','${metaPixelId}');fbq('track','PageView');`,
          }}
        />
      ) : null}

      {customAnalytics ? (
        <Script id="custom-analytics" strategy="afterInteractive" dangerouslySetInnerHTML={{ __html: customAnalytics }} />
      ) : null}
    </>
  );
}


User-agent: *
Allow: /
Disallow: /dashboard
Disallow: /profile
Disallow: /my-listings/
Disallow: /my-sales
Disallow: /my-orders
Disallow: /my-coupons
Disallow: /notifications
Disallow: /checkout/
Disallow: /api/
Disallow: /stock-management
{{ $extra ? "\n" . trim($extra) . "\n" : '' }}
# AI Crawlers — see /llms.txt for structured site information
User-agent: GPTBot
Allow: /

User-agent: ChatGPT-User
Allow: /

User-agent: Google-Extended
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: Claude-Web
Allow: /

Sitemap: {{ url('/sitemap.xml') }}

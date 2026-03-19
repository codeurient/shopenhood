<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <sitemap>
        <loc>{{ url('/sitemap-listings.xml') }}</loc>
        @if($listingsUpdatedAt)
        <lastmod>{{ \Carbon\Carbon::parse($listingsUpdatedAt)->format('Y-m-d') }}</lastmod>
        @endif
    </sitemap>

    <sitemap>
        <loc>{{ url('/sitemap-categories.xml') }}</loc>
        @if($categoriesUpdatedAt)
        <lastmod>{{ \Carbon\Carbon::parse($categoriesUpdatedAt)->format('Y-m-d') }}</lastmod>
        @endif
    </sitemap>

</sitemapindex>

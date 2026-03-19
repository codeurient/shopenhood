<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    @foreach($listings as $listing)
    <url>
        <loc>{{ url('/listings/' . $listing->slug) }}</loc>
        <lastmod>{{ $listing->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>{{ $changefreq }}</changefreq>
        <priority>{{ $priority }}</priority>
    </url>
    @endforeach

</urlset>

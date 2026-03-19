# {{ $orgName }}

> {{ $orgDescription ?: $orgName . ' is a modern eCommerce marketplace connecting buyers and sellers.' }}

## About

{{ $orgName }} is an online marketplace where buyers discover products from individual sellers and verified business accounts. The platform supports wholesale listings, product variations, secure checkout, and buyer protection.

## For Buyers

- Browse thousands of listings across categories
- Filter by location, price, and product condition
- Secure checkout with order tracking
- Favorite listings and receive updates

## For Sellers

- Free listings for individual users
- Business accounts with advanced features (wholesale, branded store)
- Verified "Confident Seller" badge for trusted sellers
- Real-time stock management and sales dashboard

## Key Pages

- [Home]({{ url('/') }}): Marketplace homepage with featured and trending listings
- [Browse Listings]({{ url('/listings') }}): Search and filter all available products
@foreach($categories as $category)
- [{{ $category->name }}]({{ url('/categories/' . $category->slug) }}): Browse {{ strtolower($category->name) }} listings
@endforeach

## Technical

- [Sitemap]({{ url('/sitemap.xml') }}): XML sitemap for search engines
- [OpenSearch]({{ url('/opensearch.xml') }}): Browser search integration

@if($custom)

---

{{ $custom }}
@endif

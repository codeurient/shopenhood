# Shopenhood — Pre-Launch Performance & Ranking Implementation Plan

> **Purpose:** This document outlines every step required before moving Shopenhood to production.
> It covers database optimization, product ranking, Redis caching, queue workers, listing
> display strategy, and a privacy-safe recommendation system — designed to support
> 200,000+ listings and 10,000+ concurrent users at launch.
>
> **How to use:** Hand this file to Claude Code and say:
> *"Implement the pre-launch plan from `docs/pre-launch-performance-plan.md`"*

---

## Table of Contents

1. [Database Indexes](#1-database-indexes)
2. [Product Score Column & Ranking](#2-product-score-column--ranking)
3. [Featured & Sponsored Listings](#3-featured--sponsored-listings)
4. [Redis Setup](#4-redis-setup)
5. [Homepage & Category Listing Sections](#5-homepage--category-listing-sections)
6. [Cache Strategy](#6-cache-strategy)
7. [Queue Workers & Background Jobs](#7-queue-workers--background-jobs)
8. [Scheduled Tasks](#8-scheduled-tasks)
9. [Vendor Fairness (Per-Page Diversity)](#9-vendor-fairness-per-page-diversity)
10. [Cookie-Based Browsing History & Recommendations](#10-cookie-based-browsing-history--recommendations)
11. [Tests to Write](#11-tests-to-write)
12. [Production Server Checklist](#12-production-server-checklist)
13. [Implementation Order](#13-implementation-order)

---

## 1. Database Indexes

### Why
Without indexes, a query across 200,000 listings filters every row one by one.
A proper index reduces that to microseconds.

### What to do
Create a new migration: `php artisan make:migration add_performance_indexes_to_listings_table`

```php
Schema::table('listings', function (Blueprint $table) {
    // Homepage & category ranking queries
    $table->index(['status', 'score']);
    $table->index(['category_id', 'status', 'score']);

    // Seller profile pages
    $table->index(['user_id', 'status']);

    // New arrivals section
    $table->index(['created_at', 'status']);

    // Featured listings section
    $table->index(['is_featured', 'featured_until']);

    // Trending: used in score recalculation and trending query
    $table->index(['views_count', 'status']);
    $table->index(['sales_count', 'status']);
});
```

Also index the `orders` table for sales velocity lookups:

```php
Schema::table('orders', function (Blueprint $table) {
    $table->index(['listing_id', 'created_at']);
    $table->index(['seller_id', 'created_at']);
    $table->index(['status', 'created_at']);
});
```

---

## 2. Product Score Column & Ranking

### Why
Sorting 200,000 listings by a formula at query time is too slow.
A pre-computed `score` column lets us do a fast `ORDER BY score DESC` with an index.

### What to do

#### 2a. Add columns to listings table
Create migration: `php artisan make:migration add_ranking_columns_to_listings_table`

```php
Schema::table('listings', function (Blueprint $table) {
    $table->decimal('score', 10, 4)->default(0)->after('status');
    $table->unsignedBigInteger('views_count')->default(0)->after('score');
    $table->unsignedBigInteger('sales_count')->default(0)->after('views_count');
    $table->unsignedBigInteger('wishlist_count')->default(0)->after('sales_count');
    $table->decimal('rating_avg', 3, 2)->default(0)->after('wishlist_count');
    $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
    $table->unsignedBigInteger('recent_views')->default(0)->after('rating_count'); // last 7 days
    $table->unsignedBigInteger('recent_sales')->default(0)->after('recent_views'); // last 7 days
});
```

#### 2b. Score formula

The score is recalculated nightly by a scheduled Artisan command.

```
score =
  (sales_count    × 0.35) +
  (recent_sales   × 0.25) +   ← rewards currently active sellers
  (rating_avg     × 0.15) +
  (views_count    × 0.10) +
  (wishlist_count × 0.10) +
  (recency_bonus  × 0.05) +   ← newer listings get a small boost
  (featured_bonus)             ← flat +50 if is_featured = true
```

**Recency bonus:** A listing created within the last 30 days scores between 0–10.
Formula: `MAX(0, 10 - (days_since_creation / 3))`

#### 2c. Create the Artisan command
`php artisan make:command RecalculateListingScores`

The command must:
- Run only on `status = 'active'` listings
- Use a single bulk `UPDATE` with `DB::raw()` to avoid loading models into memory
- Update `recent_views` and `recent_sales` from the last 7 days
- Log how many rows were updated and how long it took

#### 2d. Add scopes to the Listing model

```php
public function scopeRanked(Builder $query): Builder
{
    return $query->where('status', 'active')->orderByDesc('score');
}

public function scopeTrending(Builder $query): Builder
{
    return $query->where('status', 'active')
                 ->orderByDesc('recent_sales')
                 ->orderByDesc('recent_views');
}

public function scopeNewArrivals(Builder $query): Builder
{
    return $query->where('status', 'active')->latest();
}

public function scopeFeatured(Builder $query): Builder
{
    return $query->where('status', 'active')
                 ->where('is_featured', true)
                 ->where(fn($q) => $q->whereNull('featured_until')
                                     ->orWhere('featured_until', '>', now()))
                 ->orderByDesc('sponsor_level')
                 ->orderByDesc('score');
}
```

---

## 3. Featured & Sponsored Listings

### Why
Allows sellers to pay for premium placement. Featured listings appear at the top of
homepage and category pages, above organic results.

### What to do

#### 3a. Add columns to listings table (same migration as 2a)
```php
$table->boolean('is_featured')->default(false)->after('status');
$table->timestamp('featured_until')->nullable()->after('is_featured');
$table->unsignedTinyInteger('sponsor_level')->default(0)->after('featured_until');
// sponsor_level: 0 = none, 1 = basic, 2 = premium, 3 = top
```

#### 3b. Admin controls
The admin panel must be able to:
- Toggle `is_featured` on any listing
- Set `featured_until` date
- Set `sponsor_level` (0–3)

No payment gateway needed at launch — admin sets it manually after seller pays.

---

## 4. Redis Setup

### Why
Without Redis, every homepage visit hits MySQL directly.
With 10,000 simultaneous users, uncached queries will overload the database.
Redis holds cached results in memory — response time drops from ~200ms to ~2ms.

### What to do

#### 4a. Install Redis on the production server
```bash
sudo apt install redis-server
sudo systemctl enable redis-server
sudo apt install php-redis   # PHP extension
```

#### 4b. Install Laravel Redis package
```bash
composer require predis/predis
```

#### 4c. Update `.env`
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### 4d. Separate Redis databases in `config/database.php`
Use separate databases to prevent key collisions between cache, sessions, and queues:

```php
'redis' => [
    'default'  => ['database' => 0],
    'cache'    => ['database' => 1],
    'sessions' => ['database' => 2],
    'queues'   => ['database' => 3],
],
```

---

## 5. Homepage & Category Listing Sections

### Sections to implement

| Section | Query | Cache TTL |
|---|---|---|
| Featured / Sponsored | `Listing::featured()->take(8)` | 10 minutes |
| Trending Now | `Listing::trending()->take(20)` | 15 minutes |
| Best Sellers | `Listing::ranked()->take(20)` | 30 minutes |
| New Arrivals | `Listing::newArrivals()->take(20)` | 10 minutes |
| You May Also Like | Cookie-based (see Section 10) | Not cached (personalised) |
| By Category | `Listing::ranked()->where('category_id', $id)->take(12)` | 30 minutes |

### What to do

#### 5a. Update HomeController
Replace the current single listing query with the named sections above,
each wrapped in `Cache::remember()`.

#### 5b. Update home view
Add clearly labelled sections using the existing `listing-card` component.
Each section must have a "View All" link.

#### 5c. Category page
Use `Listing::ranked()->where('category_id', $id)` with pagination.
Cache only the first page. Subsequent pages are not cached.

---

## 6. Cache Strategy

### Rules
- Cache only the **first page** of any listing section. Paginated pages are not cached.
- Cache keys must include context: `listings:featured`, `listings:trending`, `category:5:ranked`
- TTL by section volatility (see table in Section 5)
- Invalidate on relevant events:
  - Listing approved → invalidate `listings:new_arrivals`, `category:{id}:ranked`
  - Listing featured → invalidate `listings:featured`
  - Nightly score job → invalidate all ranking keys

### Create a `ListingCacheService` class
Responsibilities:
- Define all cache key constants
- `forgetAll()` — called by the nightly score job
- `forgetCategory(int $categoryId)` — called when a listing in that category changes
- `forgetFeatured()` — called when a listing's featured status changes

---

## 7. Queue Workers & Background Jobs

### Why
With 10,000 users registering at launch, synchronous operations will time out requests.
Queued jobs handle heavy work in the background.

### What to do

#### 7a. Verify all notifications use the database channel (already done)

#### 7b. Create a `RecordListingView` queued job
When a user opens a listing page, instead of updating `views_count` during the HTTP request,
dispatch a background job:

```php
// In ListingController::show()
RecordListingView::dispatch($listing->id);
```

The job increments `views_count` in the database.
This prevents view tracking from adding latency to listing page loads.

#### 7c. Production Supervisor config
Run at least 2 workers in production:

```ini
[program:shopenhood-worker]
command=php /var/www/shopenhood/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
numprocs=2
autostart=true
autorestart=true
user=www-data
```

---

## 8. Scheduled Tasks

Add the following to `app/Console/Kernel.php`:

```php
// Recalculate all listing scores nightly
$schedule->command('listings:recalculate-scores')->dailyAt('02:00');

// Clear expired featured listings
$schedule->command('listings:expire-featured')->hourly();

// Warm homepage cache after score recalculation
$schedule->command('cache:warm-homepage')->dailyAt('02:30');
```

### Commands to create

| Command | Purpose |
|---|---|
| `listings:recalculate-scores` | Bulk UPDATE score, recent_views, recent_sales on all active listings |
| `listings:expire-featured` | Set `is_featured = false` where `featured_until < NOW()` |
| `cache:warm-homepage` | Pre-populate Redis with all homepage sections after scores update |

### Production cron entry
```
* * * * * php /var/www/shopenhood/artisan schedule:run >> /dev/null 2>&1
```

---

## 9. Vendor Fairness (Per-Page Diversity)

### Why
Without diversity controls, one seller with 500 listings could occupy all top spots,
harming user experience and discouraging smaller sellers.

### Rule
No single vendor may appear more than **5 times** per listing section of 20 results.

### What to do
Create `app/Services/ListingDiversityFilter.php`:

```php
public static function apply(Collection $listings, int $maxPerVendor = 5): Collection
{
    $vendorCounts = [];
    return $listings->filter(function ($listing) use (&$vendorCounts, $maxPerVendor) {
        $vendorCounts[$listing->user_id] = ($vendorCounts[$listing->user_id] ?? 0) + 1;
        return $vendorCounts[$listing->user_id] <= $maxPerVendor;
    })->values();
}
```

Apply this filter **after** fetching ranked results and **before** caching them.
Always fetch more than needed (e.g., fetch 50 to display 20) to account for filtering.

---

## 10. Cookie-Based Browsing History & Recommendations

### Why
Large marketplaces (Amazon, eBay, Etsy) show users products similar to what they recently
viewed. This increases engagement and sales. The system must be built without storing
personally identifiable information — product IDs stored in cookies are anonymous.

### Privacy & Security principles
- **No personal data is stored** — only listing IDs (integers), never user identity
- **No server-side user profile** is created for this feature
- The cookie is **httpOnly: false** so JavaScript can read it for display purposes,
  but the value contains no sensitive information (just product ID integers)
- Cookies expire after **30 days**
- Users who are logged in: the same cookie approach applies — no DB table is needed
- Complies with GDPR "legitimate interest" basis since no personal data is involved

### 10a. Cookie structure
Cookie name: `sh_viewed`
Cookie value: a JSON-encoded array of the last 20 listing IDs the user has viewed,
newest first.

```json
[184, 902, 37, 1204, 88]
```

### 10b. Set the cookie when a listing is viewed
In `ListingController::show()`, after loading the listing:

```php
private function recordViewedCookie(Request $request, Response $response, int $listingId): Response
{
    $viewed = json_decode($request->cookie('sh_viewed', '[]'), true);
    $viewed = array_values(array_unique(array_merge([$listingId], $viewed)));
    $viewed = array_slice($viewed, 0, 20); // keep last 20 only

    return $response->withCookie(
        cookie('sh_viewed', json_encode($viewed), 60 * 24 * 30) // 30 days
    );
}
```

This method is called in `show()` and the cookie is attached to the response.
It also dispatches the `RecordListingView` job (Section 7b) for view counting.

### 10c. "Similar Products" recommendation query
Create `app/Services/RecommendationService.php` with a `getSimilarProducts()` method.

**Logic:**
1. Read the `sh_viewed` cookie — get the last 5 listing IDs
2. Load the categories of those listings
3. Query active listings in those same categories, excluding:
   - Listings the user already viewed (in cookie)
   - The current listing being viewed
4. Order by `score DESC`
5. Apply `ListingDiversityFilter` (max 3 per vendor)
6. Return up to 12 listings

```php
public function getSimilarProducts(Request $request, ?int $excludeListingId = null): Collection
{
    $viewedIds = json_decode($request->cookie('sh_viewed', '[]'), true);

    if (empty($viewedIds)) {
        return collect();
    }

    $recentIds  = array_slice($viewedIds, 0, 5);
    $categoryIds = Listing::whereIn('id', $recentIds)
                          ->pluck('category_id')
                          ->unique()
                          ->values();

    $exclude = $viewedIds;
    if ($excludeListingId) {
        $exclude[] = $excludeListingId;
    }

    $results = Listing::query()
        ->where('status', 'active')
        ->whereIn('category_id', $categoryIds)
        ->whereNotIn('id', $exclude)
        ->orderByDesc('score')
        ->take(40) // fetch extra for diversity filter
        ->get();

    return ListingDiversityFilter::apply($results, maxPerVendor: 3)->take(12);
}
```

### 10d. "Recently Viewed" section
On the listing show page and the homepage (for returning visitors), show the last
5–8 listings from the `sh_viewed` cookie:

```php
public function getRecentlyViewed(Request $request): Collection
{
    $viewedIds = json_decode($request->cookie('sh_viewed', '[]'), true);

    if (empty($viewedIds)) {
        return collect();
    }

    $recentIds = array_slice($viewedIds, 0, 8);

    // Preserve the cookie order (most recently viewed first)
    return Listing::whereIn('id', $recentIds)
                  ->where('status', 'active')
                  ->get()
                  ->sortBy(fn($l) => array_search($l->id, $recentIds))
                  ->values();
}
```

### 10e. Where to display recommendations

| Location | Section | Source |
|---|---|---|
| Listing show page | "You May Also Like" | `getSimilarProducts($request, $listing->id)` |
| Listing show page | "Recently Viewed" | `getRecentlyViewed($request)` |
| Homepage (returning visitors) | "Based on Your Activity" | `getSimilarProducts($request)` |
| Homepage (new visitors) | Hidden (no cookie yet) | Nothing shown |

### 10f. Caching note
These sections are **not cached** in Redis because they are personalised per user
(based on their unique cookie). The query is fast because:
- It filters by 1–5 category IDs (indexed)
- It orders by the pre-computed `score` column (indexed)
- It fetches only 40 rows maximum

---

## 11. Tests to Write

All new features must have Pest feature tests before being considered complete.

| Test file | What to test |
|---|---|
| `tests/Feature/Ranking/ScoreRecalculationTest.php` | Command runs, score updates, expired featured clears |
| `tests/Feature/Ranking/ListingDiversityTest.php` | No vendor exceeds max per page after filter |
| `tests/Feature/Listing/FeaturedListingTest.php` | Featured scope excludes expired, respects sponsor_level |
| `tests/Feature/Cache/HomepageCacheTest.php` | Sections are cached, invalidated on listing approval |
| `tests/Feature/Queue/RecordListingViewTest.php` | Viewing a listing dispatches job, job increments views_count |
| `tests/Feature/Recommendation/SimilarProductsTest.php` | Returns products from same category, excludes viewed, respects diversity |
| `tests/Feature/Recommendation/RecentlyViewedTest.php` | Cookie is set on listing view, recently viewed returns correct listings in order |
| `tests/Feature/Recommendation/NewVisitorTest.php` | No cookie → similar products section is empty, no errors |

---

## 12. Production Server Checklist

### Server
- [ ] PHP 8.1+ with `redis` and `opcache` extensions enabled
- [ ] Redis installed and running (`redis-cli ping` → `PONG`)
- [ ] Supervisor installed and queue workers configured
- [ ] System cron set up for Laravel scheduler
- [ ] OPcache enabled in `php.ini`

### Laravel
- [ ] `APP_ENV=production`, `APP_DEBUG=false` in `.env`
- [ ] `CACHE_DRIVER=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan migrate --force`

### Database
- [ ] All performance indexes applied (Step 1)
- [ ] `score`, `views_count`, `sales_count`, `wishlist_count`, `recent_views`, `recent_sales` on `listings`
- [ ] `is_featured`, `featured_until`, `sponsor_level` on `listings`
- [ ] MySQL slow query log enabled temporarily after launch

### First run after deployment
- [ ] `php artisan listings:recalculate-scores` — populate all scores
- [ ] `php artisan cache:warm-homepage` — pre-populate Redis
- [ ] Monitor Redis memory: `redis-cli info memory`
- [ ] Monitor queue worker status in Supervisor

---

## 13. Implementation Order

Execute in this exact order to avoid dependency issues:

1. **Step 1** — Database indexes migration
2. **Step 2a + 3a** — Add ranking and featured columns (one migration)
3. **Step 2c + 2d** — `RecalculateListingScores` command + Listing model scopes
4. **Step 3b** — Admin featured controls
5. **Step 4** — Redis installation and `.env` update
6. **Step 6** — `ListingCacheService`
7. **Step 9** — `ListingDiversityFilter`
8. **Step 10b** — Cookie recording in `ListingController::show()`
9. **Step 10c + 10d** — `RecommendationService`
10. **Step 5 + 10e** — Update HomeController + home view + listing show view sections
11. **Step 7b + 7c** — `RecordListingView` queued job + Supervisor config
12. **Step 8** — Scheduled tasks in `Kernel.php` + all commands
13. **Step 11** — Write all tests
14. **Step 12** — Final production checklist

---

*Document prepared for Shopenhood — Laravel 10 marketplace*
*Revisit this document when product count exceeds 500,000 or concurrent users exceed 50,000
to evaluate Redis Sorted Sets (ZSET) and a dedicated ML-based recommendation engine.*

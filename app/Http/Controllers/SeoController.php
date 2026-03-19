<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\Setting;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemapIndex(): Response
    {
        $listingsUpdatedAt = Listing::where('status', 'active')->latest('updated_at')->value('updated_at');
        $categoriesUpdatedAt = Category::where('is_active', true)->latest('updated_at')->value('updated_at');

        return response()
            ->view('seo.sitemap-index', compact('listingsUpdatedAt', 'categoriesUpdatedAt'))
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    public function sitemapListings(): Response
    {
        $changefreq = Setting::getValue('seo.sitemap_changefreq_listings', 'daily');
        $priority = Setting::getValue('seo.sitemap_priority_listings', '0.8');

        $listings = Listing::query()
            ->where('status', 'active')
            ->select(['slug', 'updated_at'])
            ->latest('updated_at')
            ->limit(50000)
            ->get();

        return response()
            ->view('seo.sitemap-listings', compact('listings', 'changefreq', 'priority'))
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    public function sitemapCategories(): Response
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->select(['slug', 'updated_at'])
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        return response()
            ->view('seo.sitemap-categories', compact('categories'))
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    public function robots(): Response
    {
        $extra = Setting::getValue('seo.robots_txt_extra', '');

        return response()
            ->view('seo.robots', compact('extra'))
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    public function llmsTxt(): Response
    {
        $orgName = Setting::getValue('seo.organization_name', config('app.name'));
        $orgDescription = Setting::getValue('seo.organization_description', '');
        $custom = Setting::getValue('seo.llms_txt_custom', '');

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->limit(20)
            ->get();

        return response()
            ->view('seo.llms', compact('orgName', 'orgDescription', 'custom', 'categories'))
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    public function opensearch(): Response
    {
        $orgName = Setting::getValue('seo.organization_name', config('app.name'));

        return response()
            ->view('seo.opensearch', compact('orgName'))
            ->header('Content-Type', 'application/opensearchdescription+xml; charset=utf-8');
    }
}

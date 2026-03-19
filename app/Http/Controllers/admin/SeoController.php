<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoController extends Controller
{
    public function index(): View
    {
        $settings = [
            'organization_name' => Setting::getValue('seo.organization_name', config('app.name')),
            'organization_description' => Setting::getValue('seo.organization_description', ''),
            'organization_logo' => Setting::getValue('seo.organization_logo', ''),
            'organization_social_profiles' => Setting::getValue('seo.organization_social_profiles', ''),
            'google_verification' => Setting::getValue('seo.google_verification', ''),
            'bing_verification' => Setting::getValue('seo.bing_verification', ''),
            'default_meta_title' => Setting::getValue('seo.default_meta_title', ''),
            'default_meta_description' => Setting::getValue('seo.default_meta_description', ''),
            'robots_txt_extra' => Setting::getValue('seo.robots_txt_extra', ''),
            'llms_txt_custom' => Setting::getValue('seo.llms_txt_custom', ''),
            'sitemap_changefreq_listings' => Setting::getValue('seo.sitemap_changefreq_listings', 'daily'),
            'sitemap_priority_listings' => Setting::getValue('seo.sitemap_priority_listings', '0.8'),
        ];

        return view('admin.seo.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'organization_description' => ['nullable', 'string', 'max:1000'],
            'organization_logo' => ['nullable', 'url', 'max:500'],
            'organization_social_profiles' => ['nullable', 'string'],
            'google_verification' => ['nullable', 'string', 'max:255'],
            'bing_verification' => ['nullable', 'string', 'max:255'],
            'default_meta_title' => ['nullable', 'string', 'max:60'],
            'default_meta_description' => ['nullable', 'string', 'max:160'],
            'robots_txt_extra' => ['nullable', 'string', 'max:5000'],
            'llms_txt_custom' => ['nullable', 'string', 'max:10000'],
            'sitemap_changefreq_listings' => ['required', 'in:always,hourly,daily,weekly,monthly,yearly,never'],
            'sitemap_priority_listings' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue("seo.{$key}", $value ?? '', 'string', 'seo');
        }

        activity()
            ->causedBy(auth('admin')->user())
            ->log('Updated SEO settings');

        return redirect()->route('admin.seo.index')->with('success', 'SEO settings saved successfully.');
    }
}

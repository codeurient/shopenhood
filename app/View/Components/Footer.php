<?php

namespace App\View\Components;

use App\Models\Category;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Footer extends Component
{
    public string $appName;

    public Collection $shopCategories;

    public Collection $supportPages;

    public ?string $socialFacebook;

    public ?string $socialInstagram;

    public ?string $socialTwitter;

    public ?string $socialYoutube;

    public ?string $socialLinkedin;

    public function __construct()
    {
        $this->appName = config('app.name', 'Shopenhood');

        $this->shopCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get(['id', 'name', 'slug']);

        $this->supportPages = Page::where('is_published', true)
            ->orderBy('sort_order')
            ->get(['key', 'title']);

        $this->socialFacebook = Setting::getValue('social_facebook');
        $this->socialInstagram = Setting::getValue('social_instagram');
        $this->socialTwitter = Setting::getValue('social_twitter');
        $this->socialYoutube = Setting::getValue('social_youtube');
        $this->socialLinkedin = Setting::getValue('social_linkedin');
    }

    public function render(): \Illuminate\View\View
    {
        return view('components.footer');
    }
}

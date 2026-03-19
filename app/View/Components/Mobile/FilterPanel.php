<?php

namespace App\View\Components\Mobile;

use App\Models\Category;
use App\Models\ListingType;
use App\Models\Location;
use Illuminate\View\Component;

class FilterPanel extends Component
{
    public $listingTypes;

    public $topCategories;

    public $countries;

    public array $activeCategoryPath = [];

    public function __construct()
    {
        $this->listingTypes = ListingType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $this->countries = Location::where('type', 'country')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $this->topCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->withCount('children')
            ->orderBy('sort_order')
            ->get();

        if (request()->filled('category')) {
            $cat = Category::where('slug', request('category'))
                ->with('parent.parent')
                ->first();
            if ($cat) {
                $this->activeCategoryPath = $this->buildPath($cat);
            }
        }
    }

    private function buildPath(Category $category): array
    {
        $path = [];
        $current = $category;
        while ($current) {
            $hasChildren = $current->children()->where('is_active', true)->exists();
            array_unshift($path, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
                'has_children' => $hasChildren,
            ]);
            $current = $current->parent_id ? $current->parent : null;
        }

        return $path;
    }

    public function render()
    {
        return view('components.mobile.filter-panel');
    }
}

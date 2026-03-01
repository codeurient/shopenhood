<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSuggestionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q'));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Listing::publiclyVisible()
            ->search($query)
            ->with([
                'primaryImage', 'firstImage',
                'defaultVariation.primaryImage', 'defaultVariation.firstImage',
                'category',
            ])
            ->limit(6)
            ->get()
            ->map(function (Listing $listing): array {
                $image = $listing->primaryImage
                    ?? $listing->firstImage
                    ?? $listing->defaultVariation?->primaryImage
                    ?? $listing->defaultVariation?->firstImage;

                $imageUrl = null;
                if ($image) {
                    $path = $image->thumbnail_path ?? $image->image_path;
                    $imageUrl = asset('storage/'.$path);
                }

                return [
                    'title' => $listing->title,
                    'url' => route('listings.show', $listing),
                    'image_url' => $imageUrl,
                    'category_name' => $listing->category?->name,
                    'base_price' => $listing->base_price,
                    'currency' => $listing->currency ?? 'USD',
                ];
            });

        return response()->json($suggestions);
    }
}

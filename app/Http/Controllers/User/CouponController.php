<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponRestriction;
use App\Models\Listing;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')->with('error', 'Only business users can manage coupons.');
        }

        $query = Coupon::forUser($user->id)->withCount(['restrictions', 'usages']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('applicable_to')) {
            $query->where('applicable_to', $request->applicable_to);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    });
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<', now());
            }
        }

        $coupons = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Coupon::forUser($user->id)->count(),
            'active' => Coupon::forUser($user->id)->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })->count(),
            'expired' => Coupon::forUser($user->id)->whereNotNull('expires_at')
                ->where('expires_at', '<', now())->count(),
            'inactive' => Coupon::forUser($user->id)->where('is_active', false)->count(),
        ];

        return view('user.coupons.index', compact('coupons', 'stats'));
    }

    public function create()
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')->with('error', 'Only business users can manage coupons.');
        }

        $categories = $this->getUserCategories($user);
        $listings = $this->getUserListings($user);

        return view('user.coupons.create', compact('categories', 'listings'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')->with('error', 'Only business users can manage coupons.');
        }

        $validated = $request->validate($this->validationRules());

        $validated['user_id'] = $user->id;
        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $coupon = Coupon::create($validated);

        $this->syncRestrictions($coupon, $request, $user);

        return redirect()
            ->route('user.coupons.index')
            ->with('success', "Coupon \"{$coupon->code}\" created successfully.");
    }

    public function edit(Coupon $coupon)
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')->with('error', 'Only business users can manage coupons.');
        }

        $this->authorizeOwnership($coupon);

        $coupon->loadCount(['usages']);
        $coupon->load('restrictions');

        $categories = $this->getUserCategories($user);
        $listings = $this->getUserListings($user);

        $existingRestrictionIds = $coupon->restrictions->pluck('restrictable_id')->toArray();

        return view('user.coupons.edit', compact('coupon', 'categories', 'listings', 'existingRestrictionIds'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')->with('error', 'Only business users can manage coupons.');
        }

        $this->authorizeOwnership($coupon);

        $validated = $request->validate($this->validationRules($coupon->id));

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        $this->syncRestrictions($coupon, $request, $user);

        return redirect()
            ->route('user.coupons.index')
            ->with('success', "Coupon \"{$coupon->code}\" updated successfully.");
    }

    public function destroy(Coupon $coupon)
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')->with('error', 'Only business users can manage coupons.');
        }

        $this->authorizeOwnership($coupon);

        $code = $coupon->code;
        $coupon->delete();

        return redirect()
            ->route('user.coupons.index')
            ->with('success', "Coupon \"{$code}\" deleted successfully.");
    }

    public function toggleStatus(Coupon $coupon)
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')->with('error', 'Only business users can manage coupons.');
        }

        $this->authorizeOwnership($coupon);

        $coupon->update([
            'is_active' => ! $coupon->is_active,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Coupon status updated successfully.');
    }

    private function authorizeOwnership(Coupon $coupon): void
    {
        if (! $coupon->belongsToUser(auth()->id())) {
            abort(403, 'This coupon does not belong to you.');
        }
    }

    private function getUserCategories($user)
    {
        $categoryIds = Listing::forUser($user->id)
            ->whereNotNull('category_id')
            ->distinct()
            ->pluck('category_id');

        return Category::whereIn('id', $categoryIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function getUserListings($user)
    {
        return Listing::forUser($user->id)
            ->where('status', 'active')
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    private function validationRules(?int $couponId = null): array
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code'.($couponId ? ','.$couponId : ''),
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'applicable_to' => 'required|in:all,categories,listings',
            'restrictions' => 'nullable|array',
            'restrictions.*' => 'integer',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ];
    }

    private function syncRestrictions(Coupon $coupon, Request $request, $user): void
    {
        $coupon->restrictions()->delete();

        if ($coupon->applicable_to === 'all' || ! $request->filled('restrictions')) {
            return;
        }

        $typeMap = [
            'categories' => Category::class,
            'listings' => Listing::class,
        ];

        $restrictableType = $typeMap[$coupon->applicable_to] ?? null;

        if (! $restrictableType) {
            return;
        }

        $validIds = $this->getValidRestrictionIds($coupon->applicable_to, $user, $request->restrictions);

        if (empty($validIds)) {
            return;
        }

        $restrictions = collect($validIds)->map(fn ($id) => [
            'coupon_id' => $coupon->id,
            'restrictable_type' => $restrictableType,
            'restrictable_id' => $id,
            'created_at' => now(),
        ])->toArray();

        CouponRestriction::insert($restrictions);
    }

    private function getValidRestrictionIds(string $applicableTo, $user, array $requestedIds): array
    {
        if ($applicableTo === 'categories') {
            $userCategoryIds = Listing::forUser($user->id)
                ->whereNotNull('category_id')
                ->distinct()
                ->pluck('category_id')
                ->toArray();

            return array_intersect($requestedIds, $userCategoryIds);
        }

        if ($applicableTo === 'listings') {
            $userListingIds = Listing::forUser($user->id)
                ->pluck('id')
                ->toArray();

            return array_intersect($requestedIds, $userListingIds);
        }

        return [];
    }
}

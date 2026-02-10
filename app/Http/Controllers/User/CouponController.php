<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreCouponRequest;
use App\Http\Requests\User\UpdateCouponRequest;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponRestriction;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $coupons = Coupon::forUser($user->id)
            ->withCount(['restrictions', 'usages'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Coupon::forUser($user->id)->count(),
            'active' => Coupon::forUser($user->id)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })->count(),
            'expired' => Coupon::forUser($user->id)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())->count(),
            'inactive' => Coupon::forUser($user->id)->where('is_active', false)->count(),
        ];

        return view('user.coupons.index', compact('coupons', 'stats'));
    }

    public function create(): View
    {
        $user = auth()->user();

        $categories = $this->getUserCategories($user->id);
        $listings = $this->getUserListings($user->id);

        return view('user.coupons.create', compact('categories', 'listings'));
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['code'] = strtoupper($validated['code']);
        $validated['user_id'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        $coupon = Coupon::create($validated);

        $this->syncRestrictions($coupon, $request);

        return redirect()
            ->route('user.coupons.index')
            ->with('success', "Coupon \"{$coupon->code}\" created successfully.");
    }

    public function edit(Coupon $coupon): View|RedirectResponse
    {
        if ($coupon->user_id !== auth()->id()) {
            abort(403);
        }

        $user = auth()->user();

        $coupon->loadCount(['usages']);
        $coupon->load('restrictions');

        $categories = $this->getUserCategories($user->id);
        $listings = $this->getUserListings($user->id);

        $existingRestrictionIds = $coupon->restrictions->pluck('restrictable_id')->toArray();

        return view('user.coupons.edit', compact('coupon', 'categories', 'listings', 'existingRestrictionIds'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        if ($coupon->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();
        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        $this->syncRestrictions($coupon, $request);

        return redirect()
            ->route('user.coupons.index')
            ->with('success', "Coupon \"{$coupon->code}\" updated successfully.");
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        if ($coupon->user_id !== auth()->id()) {
            abort(403);
        }

        $code = $coupon->code;
        $coupon->delete();

        return redirect()
            ->route('user.coupons.index')
            ->with('success', "Coupon \"{$code}\" deleted successfully.");
    }

    public function toggleStatus(Coupon $coupon): RedirectResponse
    {
        if ($coupon->user_id !== auth()->id()) {
            abort(403);
        }

        $coupon->update([
            'is_active' => ! $coupon->is_active,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Coupon status updated successfully.');
    }

    /**
     * Get distinct categories from the user's own approved/active listings.
     */
    private function getUserCategories(int $userId)
    {
        $categoryIds = Listing::where('user_id', $userId)
            ->whereIn('status', ['approved', 'active'])
            ->distinct()
            ->pluck('category_id');

        return Category::whereIn('id', $categoryIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get the user's own approved/active listings.
     */
    private function getUserListings(int $userId)
    {
        return Listing::where('user_id', $userId)
            ->whereIn('status', ['approved', 'active'])
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    private function syncRestrictions(Coupon $coupon, $request): void
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

        $restrictions = collect($request->restrictions)->map(fn ($id) => [
            'coupon_id' => $coupon->id,
            'restrictable_type' => $restrictableType,
            'restrictable_id' => $id,
            'created_at' => now(),
        ])->toArray();

        CouponRestriction::insert($restrictions);
    }
}

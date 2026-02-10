<?php

namespace App\Http\Controllers\Admin;

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
        $query = Coupon::withCount(['restrictions', 'usages']);

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
            'total' => Coupon::count(),
            'active' => Coupon::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })->count(),
            'expired' => Coupon::whereNotNull('expires_at')
                ->where('expires_at', '<', now())->count(),
            'inactive' => Coupon::where('is_active', false)->count(),
        ];

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
<<<<<<< HEAD
        $listings = Listing::where('status', 'active')->orderBy('title')->get(['id', 'title']);
=======
        $listings = Listing::where('status', 'approved')->orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e

        return view('admin.coupons.create', compact('categories', 'listings', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $coupon = Coupon::create($validated);

        $this->syncRestrictions($coupon, $request);

        activity()
            ->performedOn($coupon)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Coupon created');

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', "Coupon \"{$coupon->code}\" created successfully.");
    }

    public function edit(Coupon $coupon)
    {
        $coupon->loadCount(['usages']);
        $coupon->load('restrictions');

        $categories = Category::where('is_active', true)->orderBy('name')->get();
<<<<<<< HEAD
        $listings = Listing::where('status', 'active')->orderBy('title')->get(['id', 'title']);

        $existingRestrictionIds = $coupon->restrictions->pluck('restrictable_id')->toArray();

        return view('admin.coupons.edit', compact('coupon', 'categories', 'listings', 'existingRestrictionIds'));
=======
        $listings = Listing::where('status', 'approved')->orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        $existingRestrictionIds = $coupon->restrictions->pluck('restrictable_id')->toArray();

        return view('admin.coupons.edit', compact('coupon', 'categories', 'listings', 'users', 'existingRestrictionIds'));
>>>>>>> 126dacd81adcef53b155a6e3204b9d6deaeaba7e
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate($this->validationRules($coupon->id));

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        $this->syncRestrictions($coupon, $request);

        activity()
            ->performedOn($coupon)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Coupon updated');

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', "Coupon \"{$coupon->code}\" updated successfully.");
    }

    public function destroy(Coupon $coupon)
    {
        $code = $coupon->code;
        $coupon->delete();

        activity()
            ->causedBy(auth()->guard('admin')->user())
            ->log("Coupon \"{$code}\" deleted");

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', "Coupon \"{$code}\" deleted successfully.");
    }

    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update([
            'is_active' => ! $coupon->is_active,
        ]);

        activity()
            ->performedOn($coupon)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Coupon status toggled');

        return redirect()
            ->back()
            ->with('success', 'Coupon status updated successfully.');
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

    private function syncRestrictions(Coupon $coupon, Request $request): void
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

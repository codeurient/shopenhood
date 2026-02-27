<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBusinessProfileRequest;
use App\Http\Requests\Admin\UpdateBusinessProfileRequest;
use App\Models\BusinessProfile;
use App\Models\Location;
use App\Models\User;
use App\Notifications\ConfidentSellerApprovedNotification;
use App\Notifications\ConfidentSellerRejectedNotification;
use App\Services\SensitiveDataEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessProfileController extends Controller
{
    public function __construct(private readonly SensitiveDataEncryptionService $encryption) {}

    public function index(Request $request)
    {
        $query = BusinessProfile::with(['user', 'country']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('industry')) {
            $query->byIndustry($request->industry);
        }

        if ($request->filled('country')) {
            $query->where('country_id', $request->country);
        }

        $profiles = $query->latest()->paginate(20)->withQueryString();

        $industries = BusinessProfile::distinct()->whereNotNull('industry')->pluck('industry');
        $countries = Location::countries()->active()->orderBy('name')->get();

        $stats = [
            'total' => BusinessProfile::count(),
        ];

        return view('admin.business-profiles.index', compact('profiles', 'industries', 'countries', 'stats'));
    }

    public function show(BusinessProfile $businessProfile)
    {
        $businessProfile->load(['user', 'country']);

        $admin = auth()->guard('admin')->user();
        $sensitiveData = $businessProfile->decryptSensitiveData($admin);

        return view('admin.business-profiles.show', compact('businessProfile', 'sensitiveData'));
    }

    public function create(User $user)
    {
        if (! $user->isBusinessUser()) {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'Only business users can have a business profile.');
        }

        if ($user->hasBusinessProfile()) {
            return redirect()->route('admin.business-profiles.edit', $user->businessProfile)
                ->with('info', 'This user already has a business profile.');
        }

        $countries = Location::countries()->active()->orderBy('name')->get();

        return view('admin.business-profiles.create', compact('user', 'countries'));
    }

    public function store(StoreBusinessProfileRequest $request, User $user)
    {
        if (! $user->isBusinessUser()) {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'Only business users can have a business profile.');
        }

        if ($user->hasBusinessProfile()) {
            return redirect()->route('admin.business-profiles.edit', $user->businessProfile)
                ->with('error', 'This user already has a business profile.');
        }

        $data = $request->validated();
        $data['user_id'] = $user->id;
        $data['slug'] = Str::slug($data['business_name']).'-'.Str::random(6);

        // Encrypt admin-entered sensitive fields before persisting
        $data['registration_number'] = $this->encryption->encryptNullable($data['registration_number'] ?? null);
        $data['tax_id'] = $this->encryption->encryptNullable($data['tax_id'] ?? null);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('business/logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('business/banners', 'public');
        }

        $profile = BusinessProfile::create($data);

        return redirect()->route('admin.business-profiles.show', $profile)
            ->with('success', 'Business profile created successfully.');
    }

    public function edit(BusinessProfile $businessProfile)
    {
        $businessProfile->load(['user', 'country']);
        $countries = Location::countries()->active()->orderBy('name')->get();

        $admin = auth()->guard('admin')->user();
        $sensitiveData = $businessProfile->decryptSensitiveData($admin);

        return view('admin.business-profiles.edit', compact('businessProfile', 'countries', 'sensitiveData'));
    }

    public function update(UpdateBusinessProfileRequest $request, BusinessProfile $businessProfile)
    {
        $data = $request->validated();

        // Encrypt admin-entered sensitive fields before persisting
        $data['registration_number'] = $this->encryption->encryptNullable($data['registration_number'] ?? null);
        $data['tax_id'] = $this->encryption->encryptNullable($data['tax_id'] ?? null);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($businessProfile->logo) {
                \Storage::disk('public')->delete($businessProfile->logo);
            }
            $data['logo'] = $request->file('logo')->store('business/logos', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($businessProfile->banner) {
                \Storage::disk('public')->delete($businessProfile->banner);
            }
            $data['banner'] = $request->file('banner')->store('business/banners', 'public');
        }

        $businessProfile->update($data);

        return redirect()->route('admin.business-profiles.show', $businessProfile)
            ->with('success', 'Business profile updated successfully.');
    }

    public function approve(BusinessProfile $businessProfile): \Illuminate\Http\RedirectResponse
    {
        $businessProfile->update(['approved_at' => now()]);

        return redirect()->route('admin.business-profiles.show', $businessProfile)
            ->with('success', 'Business profile approved. The verified badge will now appear on their listings.');
    }

    public function revoke(BusinessProfile $businessProfile): \Illuminate\Http\RedirectResponse
    {
        $businessProfile->update(['approved_at' => null]);

        return redirect()->route('admin.business-profiles.show', $businessProfile)
            ->with('success', 'Verification revoked.');
    }

    public function approveConfidentSeller(BusinessProfile $businessProfile): \Illuminate\Http\RedirectResponse
    {
        $businessProfile->update([
            'confident_seller_status' => 'approved',
            'confident_seller_rejection_reason' => null,
        ]);

        $businessProfile->user->notify(new ConfidentSellerApprovedNotification($businessProfile));

        return redirect()->route('admin.business-profiles.show', $businessProfile)
            ->with('success', 'Confident Seller status approved and user has been notified.');
    }

    public function rejectConfidentSeller(Request $request, BusinessProfile $businessProfile): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $businessProfile->update([
            'confident_seller_status' => 'rejected',
            'confident_seller_rejection_reason' => $request->rejection_reason,
        ]);

        $businessProfile->user->notify(new ConfidentSellerRejectedNotification($businessProfile, $request->rejection_reason));

        return redirect()->route('admin.business-profiles.show', $businessProfile)
            ->with('success', 'Confident Seller application rejected and user has been notified.');
    }

    public function destroy(BusinessProfile $businessProfile)
    {
        // Delete uploaded files
        if ($businessProfile->logo) {
            \Storage::disk('public')->delete($businessProfile->logo);
        }
        if ($businessProfile->banner) {
            \Storage::disk('public')->delete($businessProfile->banner);
        }

        $businessProfile->delete();

        return redirect()->route('admin.business-profiles.index')
            ->with('success', 'Business profile deleted successfully.');
    }
}

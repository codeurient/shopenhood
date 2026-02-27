<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreBusinessProfileRequest;
use App\Http\Requests\User\UpdateBusinessProfileRequest;
use App\Models\BusinessProfile;
use App\Models\Location;
use App\Services\SensitiveDataEncryptionService;
use Illuminate\Support\Str;

class BusinessProfileController extends Controller
{
    public function __construct(private readonly SensitiveDataEncryptionService $encryption) {}

    public function show()
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')
                ->with('error', 'Only business users can access business profiles.');
        }

        $businessProfile = $user->businessProfile;

        if (! $businessProfile) {
            return redirect()->route('business.profile.create')
                ->with('info', 'Please complete your business profile to get started.');
        }

        $businessProfile->load('country');

        $sensitiveData = $businessProfile->decryptForOwner($user);

        return view('user.business.profile', compact('businessProfile', 'sensitiveData'));
    }

    public function create()
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')
                ->with('error', 'Only business users can create business profiles.');
        }

        if ($user->hasBusinessProfile()) {
            return redirect()->route('business.profile');
        }

        $countries = Location::countries()->active()->orderBy('name')->get();

        return view('user.business.create', compact('countries'));
    }

    public function store(StoreBusinessProfileRequest $request)
    {
        $user = auth()->user();

        if ($user->hasBusinessProfile()) {
            return redirect()->route('business.profile')
                ->with('info', 'You already have a business profile.');
        }

        $data = $request->validated();
        $data['user_id'] = $user->id;
        $data['slug'] = Str::slug($data['business_name']).'-'.Str::random(6);
        $data['confident_seller_status'] = 'pending';

        // Encrypt all sensitive fields before persisting
        $data['registration_number'] = $this->encryption->encryptNullable($data['registration_number'] ?? null);
        $data['tax_id'] = $this->encryption->encryptNullable($data['tax_id'] ?? null);
        $data['fin'] = $this->encryption->encryptNullable($data['fin'] ?? null);
        $data['id_number'] = $this->encryption->encryptNullable($data['id_number'] ?? null);
        $data['id_full_name'] = $this->encryption->encryptNullable($data['id_full_name'] ?? null);

        BusinessProfile::create($data);

        return redirect()->route('business.profile')
            ->with('success', 'Business profile created successfully!');
    }

    public function edit()
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')
                ->with('error', 'Only business users can access business profiles.');
        }

        $businessProfile = $user->businessProfile;

        if (! $businessProfile) {
            return redirect()->route('business.profile.create');
        }

        $countries = Location::countries()->active()->orderBy('name')->get();

        $sensitiveData = $businessProfile->decryptForOwner($user);

        return view('user.business.edit', compact('businessProfile', 'countries', 'sensitiveData'));
    }

    public function update(UpdateBusinessProfileRequest $request)
    {
        $user = auth()->user();

        if (! $user->isBusinessUser()) {
            return redirect()->route('dashboard')
                ->with('error', 'Only business users can access business profiles.');
        }

        $businessProfile = $user->businessProfile;

        if (! $businessProfile) {
            return redirect()->route('dashboard')
                ->with('error', 'No business profile found.');
        }

        $businessProfile->update($request->validated());

        return redirect()->route('business.profile')
            ->with('success', 'Business profile updated successfully.');
    }
}

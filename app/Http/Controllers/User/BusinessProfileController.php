<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreBusinessProfileRequest;
use App\Http\Requests\User\UpdateBusinessProfileRequest;
use App\Models\BusinessProfile;
use App\Models\Location;
use Illuminate\Support\Str;

class BusinessProfileController extends Controller
{
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

        return view('user.business.profile', compact('businessProfile'));
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

        return view('user.business.edit', compact('businessProfile', 'countries'));
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

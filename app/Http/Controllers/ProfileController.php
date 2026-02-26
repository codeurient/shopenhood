<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdateProfileBrandingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's avatar (profile picture).
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = $request->file('avatar')->store('avatars', 'public');
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Update the user's banner image.
     */
    public function updateBanner(Request $request): RedirectResponse
    {
        $request->validate([
            'banner' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $user = $request->user();

        if ($user->banner) {
            Storage::disk('public')->delete($user->banner);
        }

        $user->banner = $request->file('banner')->store('banners', 'public');
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'banner-updated');
    }

    /**
     * Update the business user's branding (logo and banner).
     */
    public function updateBranding(UpdateProfileBrandingRequest $request): RedirectResponse
    {
        $businessProfile = $request->user()->businessProfile;

        if ($request->hasFile('logo')) {
            if ($businessProfile->logo) {
                Storage::disk('public')->delete($businessProfile->logo);
            }
            $businessProfile->logo = $request->file('logo')->store('business/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($businessProfile->banner) {
                Storage::disk('public')->delete($businessProfile->banner);
            }
            $businessProfile->banner = $request->file('banner')->store('business/banners', 'public');
        }

        $businessProfile->save();

        return Redirect::route('profile.edit')->with('status', 'branding-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->forceDelete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'listing_default_duration_days'      => Setting::getValue('listing.default_duration_days', 30),
            'listing_soft_delete_retention_days' => Setting::getValue('listing.soft_delete_retention_days', 30),
            'contact_phone'                      => Setting::getValue('contact.phone', ''),
            'contact_email'                      => Setting::getValue('contact.email', ''),
            'social_facebook'                    => Setting::getValue('social.facebook', ''),
            'social_instagram'                   => Setting::getValue('social.instagram', ''),
            'social_twitter'                     => Setting::getValue('social.twitter', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'listing_default_duration_days'      => 'required|integer|min:1|max:365',
            'listing_soft_delete_retention_days' => 'required|integer|min:1|max:365',
            'contact_phone'                      => 'nullable|string|max:30',
            'contact_email'                      => 'nullable|email|max:100',
            'social_facebook'                    => 'nullable|url|max:255',
            'social_instagram'                   => 'nullable|url|max:255',
            'social_twitter'                     => 'nullable|url|max:255',
        ]);

        Setting::setValue('listing.default_duration_days', $validated['listing_default_duration_days'], 'integer', 'listing');
        Setting::setValue('listing.soft_delete_retention_days', $validated['listing_soft_delete_retention_days'], 'integer', 'listing');
        Setting::setValue('contact.phone', $validated['contact_phone'] ?? '', 'string', 'contact');
        Setting::setValue('contact.email', $validated['contact_email'] ?? '', 'string', 'contact');
        Setting::setValue('social.facebook', $validated['social_facebook'] ?? '', 'string', 'social');
        Setting::setValue('social.instagram', $validated['social_instagram'] ?? '', 'string', 'social');
        Setting::setValue('social.twitter', $validated['social_twitter'] ?? '', 'string', 'social');

        activity()
            ->causedBy(auth()->guard('admin')->user())
            ->log('Settings updated');

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}

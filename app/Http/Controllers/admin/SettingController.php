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
            'listing_default_duration_days' => Setting::getValue('listing.default_duration_days', 30),
            'listing_soft_delete_retention_days' => Setting::getValue('listing.soft_delete_retention_days', 30),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'listing_default_duration_days' => 'required|integer|min:1|max:365',
            'listing_soft_delete_retention_days' => 'required|integer|min:1|max:365',
        ]);

        Setting::setValue('listing.default_duration_days', $validated['listing_default_duration_days'], 'integer', 'listing');
        Setting::setValue('listing.soft_delete_retention_days', $validated['listing_soft_delete_retention_days'], 'integer', 'listing');

        activity()
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing settings updated');

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}

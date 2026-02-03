<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Notifications\ListingApprovedNotification;
use App\Notifications\ListingRejectedNotification;
use App\Services\ListingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ListingApprovalController extends Controller
{
    public function __construct(private ListingService $listingService) {}

    public function approve(Listing $listing): RedirectResponse
    {
        if (! $listing->isPending()) {
            return back()->with('error', 'Only pending listings can be approved.');
        }

        $listing->update([
            'status' => 'active',
            'approved_by' => auth()->guard('admin')->id(),
            'approved_at' => now(),
            'expires_at' => $this->listingService->calculateExpiresAt(),
            'rejection_reason' => null,
            'rejected_at' => null,
        ]);

        activity()
            ->performedOn($listing)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing approved');

        $listing->user->notify(new ListingApprovedNotification($listing));

        return back()->with('success', "Listing \"{$listing->title}\" has been approved.");
    }

    public function reject(Request $request, Listing $listing): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if (! $listing->isPending()) {
            return back()->with('error', 'Only pending listings can be rejected.');
        }

        $listing->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        activity()
            ->performedOn($listing)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing rejected');

        $listing->user->notify(new ListingRejectedNotification($listing, $validated['rejection_reason']));

        return back()->with('success', "Listing \"{$listing->title}\" has been rejected.");
    }
}

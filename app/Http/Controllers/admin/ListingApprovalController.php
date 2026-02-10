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

        $user = $listing->user;
        $shouldHideDueToRoleChange = $this->shouldHideDueToRoleChange($listing, $user);

        $listing->update([
            'status' => 'active',
            'approved_by' => auth()->guard('admin')->id(),
            'approved_at' => now(),
            'expires_at' => $this->listingService->calculateExpiresAt(),
            'rejection_reason' => null,
            'rejected_at' => null,
            'hidden_due_to_role_change' => $shouldHideDueToRoleChange,
        ]);

        activity()
            ->performedOn($listing)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Listing approved');

        $listing->user->notify(new ListingApprovedNotification($listing));

        $message = "Listing \"{$listing->title}\" has been approved.";
        if ($shouldHideDueToRoleChange) {
            $message .= ' Note: Listing is hidden due to user role restrictions.';
        }

        return back()->with('success', $message);
    }

    /**
     * Determine if a listing should be hidden due to user role change.
     *
     * If a listing was created as a business user but the user is now a normal user,
     * and the user already has an active visible listing, this listing should be hidden.
     */
    private function shouldHideDueToRoleChange(Listing $listing, $user): bool
    {
        // If the listing was created as a business user but user is now normal user
        if ($listing->created_as_role === 'business_user' && $user->current_role === 'normal_user') {
            // Check if user already has active visible listings
            $activeVisibleCount = Listing::forUser($user->id)
                ->where('status', 'active')
                ->where('hidden_due_to_role_change', false)
                ->where('id', '!=', $listing->id)
                ->count();

            // Normal users can only have 1 visible listing
            return $activeVisibleCount >= 1;
        }

        return false;
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

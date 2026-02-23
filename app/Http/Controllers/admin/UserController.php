<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('listings');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('role')) {
            $query->where('current_role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => User::count(),
            'normal' => User::where('current_role', 'normal_user')->count(),
            'business' => User::where('current_role', 'business_user')->count(),
            'admin' => User::where('current_role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function edit(User $user)
    {
        $user->loadCount('listings');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'current_role' => 'required|in:admin,normal_user,business_user',
            'is_business_enabled' => 'nullable|boolean',
            'listing_limit' => 'nullable|integer|min:1',
            'business_valid_until' => 'nullable|date|after:today',
            'status' => 'required|in:active,suspended,banned',
        ]);

        $oldRole = $user->current_role;
        $newRole = $validated['current_role'];

        // Set guarded fields explicitly (protected from mass assignment for security)
        $user->current_role = $newRole;
        $user->is_business_enabled = $request->has('is_business_enabled');
        $user->listing_limit = $validated['listing_limit'] ?? null;
        $user->business_valid_until = $validated['business_valid_until'] ?? null;
        $user->status = $validated['status'];
        $user->save();

        // Handle role downgrade: business_user → normal_user
        if ($oldRole === 'business_user' && $newRole === 'normal_user') {
            $this->deactivateExcessListings($user);
        }

        // Handle role upgrade: normal_user → business_user
        if ($oldRole === 'normal_user' && $newRole === 'business_user') {
            $this->reactivateRoleRestrictedListings($user);
        }

        activity()
            ->performedOn($user)
            ->causedBy(auth()->guard('admin')->user())
            ->log('User updated');

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" updated successfully.");
    }

    private function deactivateExcessListings(User $user): void
    {
        // Hide all business-mode listings; normal-mode listings remain visible
        Listing::forUser($user->id)
            ->businessMode()
            ->where('status', 'active')
            ->update(['hidden_due_to_role_change' => true]);
    }

    private function reactivateRoleRestrictedListings(User $user): void
    {
        Listing::withTrashed()
            ->forUser($user->id)
            ->businessMode()
            ->where('hidden_due_to_role_change', true)
            ->update(['hidden_due_to_role_change' => false]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->guard('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        activity()
            ->performedOn($user)
            ->causedBy(auth()->guard('admin')->user())
            ->log('User soft-deleted');

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User \"{$name}\" has been deleted.");
    }
}

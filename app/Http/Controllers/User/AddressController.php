<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $addresses = UserAddress::forUser($user->id)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return view('user.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('user.addresses.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate($this->validationRules());

        $validated['user_id'] = $user->id;

        DB::transaction(function () use ($validated, $user) {
            // If this is set as default, unset other defaults
            if (! empty($validated['is_default'])) {
                UserAddress::forUser($user->id)->update(['is_default' => false]);
            }

            // If this is the first address, make it default
            $existingCount = UserAddress::forUser($user->id)->count();
            if ($existingCount === 0) {
                $validated['is_default'] = true;
            }

            UserAddress::create($validated);
        });

        return redirect()
            ->route('user.addresses.index')
            ->with('success', 'Address added successfully.');
    }

    public function edit(UserAddress $address)
    {
        $this->authorizeOwnership($address);

        return view('user.addresses.edit', compact('address'));
    }

    public function update(Request $request, UserAddress $address)
    {
        $this->authorizeOwnership($address);

        $user = auth()->user();

        $validated = $request->validate($this->validationRules());

        DB::transaction(function () use ($validated, $address, $user) {
            // If this is set as default, unset other defaults
            if (! empty($validated['is_default'])) {
                UserAddress::forUser($user->id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($validated);
        });

        return redirect()
            ->route('user.addresses.index')
            ->with('success', 'Address updated successfully.');
    }

    public function destroy(UserAddress $address)
    {
        $this->authorizeOwnership($address);

        $user = auth()->user();
        $wasDefault = $address->is_default;

        $address->delete();

        // If we deleted the default address, set a new one
        if ($wasDefault) {
            $newDefault = UserAddress::forUser($user->id)->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return redirect()
            ->route('user.addresses.index')
            ->with('success', 'Address deleted successfully.');
    }

    public function setDefault(UserAddress $address)
    {
        $this->authorizeOwnership($address);

        $user = auth()->user();

        DB::transaction(function () use ($address, $user) {
            UserAddress::forUser($user->id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return redirect()
            ->route('user.addresses.index')
            ->with('success', 'Default address updated.');
    }

    /**
     * API endpoint to get all user addresses (for checkout).
     */
    public function getAddresses()
    {
        $user = auth()->user();

        $addresses = UserAddress::forUser($user->id)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($address) {
                return [
                    'id' => $address->id,
                    'label' => $address->label,
                    'is_default' => $address->is_default,
                    'recipient_name' => $address->recipient_name,
                    'phone' => $address->phone,
                    'full_address' => $address->full_address,
                    'formatted_address' => $address->formatted_address,
                ];
            });

        return response()->json([
            'success' => true,
            'addresses' => $addresses,
        ]);
    }

    /**
     * API endpoint to get a single address (for order confirmation).
     */
    public function getAddress(UserAddress $address)
    {
        $this->authorizeOwnership($address);

        return response()->json([
            'success' => true,
            'address' => [
                'id' => $address->id,
                'label' => $address->label,
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'email' => $address->email,
                'country' => $address->country,
                'city' => $address->city,
                'district' => $address->district,
                'street' => $address->street,
                'building' => $address->building,
                'apartment' => $address->apartment,
                'postal_code' => $address->postal_code,
                'additional_notes' => $address->additional_notes,
                'full_address' => $address->full_address,
            ],
        ]);
    }

    private function authorizeOwnership(UserAddress $address): void
    {
        if (! $address->belongsToUser(auth()->id())) {
            abort(403, 'This address does not belong to you.');
        }
    }

    private function validationRules(): array
    {
        return [
            'label' => 'required|string|max:50',
            'is_default' => 'nullable|boolean',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:100',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'street' => 'required|string|max:255',
            'building' => 'nullable|string|max:50',
            'apartment' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:20',
            'additional_notes' => 'nullable|string|max:500',
        ];
    }
}

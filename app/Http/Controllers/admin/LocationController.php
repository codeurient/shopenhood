<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::countries()->withCount('cities');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $countries = $query->orderBy('name')->paginate(20);

        $stats = [
            'total_countries' => Location::countries()->count(),
            'active_countries' => Location::countries()->active()->count(),
            'total_cities' => Location::ofTypeCity()->count(),
        ];

        return view('admin.locations.index', compact('countries', 'stats'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        if (Location::countries()->where('name', $validated['name'])->exists()) {
            return back()->withInput()->withErrors(['name' => 'This country already exists.']);
        }

        $validated['type'] = 'country';
        $validated['is_active'] = $request->has('is_active');

        $country = Location::create($validated);

        activity()
            ->performedOn($country)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Country created');

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Country created successfully.');
    }

    public function edit(Location $location)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        return view('admin.locations.edit', ['country' => $location]);
    }

    public function update(Request $request, Location $location)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        if (Location::countries()->where('name', $validated['name'])->where('id', '!=', $location->id)->exists()) {
            return back()->withInput()->withErrors(['name' => 'This country already exists.']);
        }

        $validated['is_active'] = $request->has('is_active');

        $location->update($validated);

        activity()
            ->performedOn($location)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Country updated');

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Country updated successfully.');
    }

    public function destroy(Location $location)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        $location->delete();

        activity()
            ->performedOn($location)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Country deleted');

        return redirect()
            ->route('admin.locations.index')
            ->with('success', 'Country and its cities deleted successfully.');
    }

    public function toggleStatus(Location $location)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        $location->update(['is_active' => ! $location->is_active]);

        activity()
            ->performedOn($location)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Country status toggled');

        return back()->with('success', 'Country status updated.');
    }
}

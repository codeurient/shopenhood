<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationCityController extends Controller
{
    public function index(Location $location)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        $cities = $location->cities()->orderBy('name')->paginate(50);

        return view('admin.locations.cities.index', [
            'country' => $location,
            'cities' => $cities,
        ]);
    }

    public function create(Location $location)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        return view('admin.locations.cities.create', ['country' => $location]);
    }

    public function store(Request $request, Location $location)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        if ($location->cities()->where('name', $validated['name'])->exists()) {
            return back()->withInput()->withErrors(['name' => 'This city already exists in this country.']);
        }

        $validated['type'] = 'city';
        $validated['parent_id'] = $location->id;
        $validated['is_active'] = $request->has('is_active');

        $city = Location::create($validated);

        activity()
            ->performedOn($city)
            ->causedBy(auth()->guard('admin')->user())
            ->log("City created under {$location->name}");

        return redirect()
            ->route('admin.locations.cities.index', $location)
            ->with('success', 'City created successfully.');
    }

    public function edit(Location $location, int $city)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        $city = Location::findOrFail($city);

        if ($city->parent_id !== $location->id) {
            abort(404);
        }

        return view('admin.locations.cities.edit', [
            'country' => $location,
            'city' => $city,
        ]);
    }

    public function update(Request $request, Location $location, int $city)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        $city = Location::findOrFail($city);

        if ($city->parent_id !== $location->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        if ($location->cities()->where('name', $validated['name'])->where('id', '!=', $city->id)->exists()) {
            return back()->withInput()->withErrors(['name' => 'This city already exists in this country.']);
        }

        $validated['is_active'] = $request->has('is_active');

        $city->update($validated);

        activity()
            ->performedOn($city)
            ->causedBy(auth()->guard('admin')->user())
            ->log('City updated');

        return redirect()
            ->route('admin.locations.cities.index', $location)
            ->with('success', 'City updated successfully.');
    }

    public function destroy(Location $location, int $city)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        $city = Location::findOrFail($city);

        if ($city->parent_id !== $location->id) {
            abort(404);
        }

        $city->delete();

        activity()
            ->performedOn($city)
            ->causedBy(auth()->guard('admin')->user())
            ->log('City deleted');

        return redirect()
            ->route('admin.locations.cities.index', $location)
            ->with('success', 'City deleted successfully.');
    }

    public function toggleStatus(Location $location, int $city)
    {
        if ($location->type !== 'country') {
            abort(404);
        }

        $city = Location::findOrFail($city);

        if ($city->parent_id !== $location->id) {
            abort(404);
        }

        $city->update(['is_active' => ! $city->is_active]);

        activity()
            ->performedOn($city)
            ->causedBy(auth()->guard('admin')->user())
            ->log('City status toggled');

        return back()->with('success', 'City status updated.');
    }
}

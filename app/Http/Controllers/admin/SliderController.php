<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSliderRequest;
use App\Http\Requests\Admin\UpdateSliderRequest;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index(Request $request)
    {
        $query = Slider::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $sliders = $query->ordered()
            ->paginate(20)
            ->withQueryString();

        // Statistics
        $stats = [
            'total' => Slider::count(),
            'active_sliders' => Slider::active()->mainSliders()->count(),
            'active_banners' => Slider::active()->smallBanners()->count(),
        ];

        return view('admin.sliders.index', compact('sliders', 'stats'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(StoreSliderRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $slider = Slider::create($validated);

        activity()
            ->performedOn($slider)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Slider created');

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', 'Slider created successfully');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(UpdateSliderRequest $request, Slider $slider)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($slider->image) {
                Storage::disk('public')->delete($slider->image);
            }

            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $slider->update($validated);

        activity()
            ->performedOn($slider)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Slider updated');

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', 'Slider updated successfully');
    }

    public function destroy(Slider $slider)
    {
        $sliderTitle = $slider->title;

        // Delete image file
        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }

        $slider->delete();

        activity()
            ->performedOn($slider)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Slider deleted: '.$sliderTitle);

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', 'Slider deleted successfully');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:sliders,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Slider::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Slider order updated successfully',
        ]);
    }
}

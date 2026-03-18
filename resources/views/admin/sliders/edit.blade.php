@extends('admin.layouts.app')

@section('title', 'Edit Slider/Banner')
@section('page-title', 'Edit Slider/Banner')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Edit Slider/Banner</h2>
            <p class="text-[#37474F] text-sm mt-1">{{ $slider->title }}</p>
        </div>
        <a href="{{ route('admin.sliders.index') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Sliders
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded flex items-start gap-3">
            <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 flex-shrink-0"></i>
            <div>
                <h3 class="text-[13px] font-semibold text-red-800">There were errors with your submission</h3>
                <ul class="mt-1 text-[13px] text-red-700 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="p-6 space-y-6">

                <!-- Slider Type -->
                <div class="pb-5 border-b border-[#E0E0E0]">
                    <h3 class="text-[13px] font-semibold text-[#1A1A1A] mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-[#D4AF37]"></i>
                        Slider Type
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="type-card relative flex cursor-pointer rounded border border-[#E0E0E0] bg-white p-4 hover:border-[#D4AF37] transition" id="card-main_slider">
                            <input type="radio" name="type" value="main_slider" class="sr-only"
                                   {{ old('type', $slider->type) === 'main_slider' ? 'checked' : '' }}>
                            <span class="flex flex-1 gap-3 items-start">
                                <i class="fa-solid fa-film text-[#37474F] mt-0.5"></i>
                                <span class="flex flex-col">
                                    <span class="text-[13px] font-semibold text-[#1A1A1A]">Main Slider</span>
                                    <span class="text-[12px] text-[#37474F] mt-0.5">Large rotating banner (180px height)</span>
                                </span>
                            </span>
                            <i class="fa-solid fa-circle-check text-[#37474F] type-check opacity-30"></i>
                        </label>
                        <label class="type-card relative flex cursor-pointer rounded border border-[#E0E0E0] bg-white p-4 hover:border-[#D4AF37] transition" id="card-banner_small">
                            <input type="radio" name="type" value="banner_small" class="sr-only"
                                   {{ old('type', $slider->type) === 'banner_small' ? 'checked' : '' }}>
                            <span class="flex flex-1 gap-3 items-start">
                                <i class="fa-solid fa-rectangle-ad text-[#37474F] mt-0.5"></i>
                                <span class="flex flex-col">
                                    <span class="text-[13px] font-semibold text-[#1A1A1A]">Small Banner</span>
                                    <span class="text-[12px] text-[#37474F] mt-0.5">Small promotional banner (96px height)</span>
                                </span>
                            </span>
                            <i class="fa-solid fa-circle-check text-[#37474F] type-check opacity-30"></i>
                        </label>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="pb-5 border-b border-[#E0E0E0]">
                    <h3 class="text-[13px] font-semibold text-[#1A1A1A] mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-[#D4AF37]"></i>
                        Basic Information
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title"
                                   value="{{ old('title', $slider->title) }}"
                                   placeholder="e.g., Summer Sale 50% Off"
                                   required autofocus
                                   class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('title') border-red-500 @else border-[#E0E0E0] @enderror">
                            @error('title')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="subtitle" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Subtitle
                            </label>
                            <input type="text" id="subtitle" name="subtitle"
                                   value="{{ old('subtitle', $slider->subtitle) }}"
                                   placeholder="e.g., Limited time offer on all products"
                                   class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('subtitle') border-red-500 @else border-[#E0E0E0] @enderror">
                            @error('subtitle')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Image -->
                <div class="pb-5 border-b border-[#E0E0E0]">
                    <h3 class="text-[13px] font-semibold text-[#1A1A1A] mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-image text-[#D4AF37]"></i>
                        Image
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-[12px] font-medium text-[#37474F] mb-2">Current Image</p>
                            <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}"
                                 class="max-h-40 rounded border border-[#E0E0E0]">
                        </div>
                        <div>
                            <label for="image" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                Upload New Image <span class="text-[12px] font-normal text-[#37474F]">(Optional)</span>
                            </label>
                            <input type="file" id="image" name="image" accept="image/*"
                                   class="block w-full text-[13px] text-[#1A1A1A] border border-[#E0E0E0] rounded cursor-pointer bg-gray-50 focus:outline-none @error('image') border-red-500 @enderror"
                                   onchange="previewImage(event)">
                            <p class="mt-1 text-[12px] text-[#37474F]">Recommended: 1200×180px for main slider, 600×96px for small banner. Max 4MB.</p>
                            @error('image')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                            <div id="imagePreview" class="mt-3 hidden">
                                <p class="text-[12px] font-medium text-[#37474F] mb-1">New Image Preview</p>
                                <img src="" alt="Preview" class="max-h-40 rounded border border-[#E0E0E0]">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Link -->
                <div class="pb-5 border-b border-[#E0E0E0]">
                    <h3 class="text-[13px] font-semibold text-[#1A1A1A] mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-link text-[#D4AF37]"></i>
                        Link <span class="text-[12px] font-normal text-[#37474F]">(Optional)</span>
                    </h3>
                    <div>
                        <label for="link" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">URL</label>
                        <input type="url" id="link" name="link"
                               value="{{ old('link', $slider->link) }}"
                               placeholder="https://example.com/summer-sale"
                               class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('link') border-red-500 @else border-[#E0E0E0] @enderror">
                        <p class="mt-1 text-[12px] text-[#37474F]">URL to redirect when slider/banner is clicked</p>
                        @error('link')
                            <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Display Settings -->
                <div>
                    <h3 class="text-[13px] font-semibold text-[#1A1A1A] mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-[#D4AF37]"></i>
                        Display Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="sort_order" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">Sort Order</label>
                            <input type="number" id="sort_order" name="sort_order"
                                   value="{{ old('sort_order', $slider->sort_order) }}" min="0"
                                   class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('sort_order') border-red-500 @else border-[#E0E0E0] @enderror">
                            <p class="mt-1 text-[12px] text-[#37474F]">Lower numbers appear first</p>
                            @error('sort_order')
                                <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-start gap-4 pt-6">
                            <label for="is_active" class="inline-flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $slider->is_active) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="relative w-11 h-6 bg-[#E0E0E0] peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full peer-checked:after:border-white
                                            after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                                            after:bg-white after:border-gray-300 after:border after:rounded-full
                                            after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4AF37]"></div>
                            </label>
                            <div>
                                <p class="text-[13px] font-medium text-[#1A1A1A]">Active</p>
                                <p class="text-[12px] text-[#37474F] mt-0.5">Only active sliders will be displayed</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-[#E0E0E0] flex justify-end gap-3">
                <a href="{{ route('admin.sliders.index') }}"
                   class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-[#000000] bg-[#D4AF37] rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-check text-xs"></i>
                    Update Slider
                </button>
            </div>
        </div>
    </form>

</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    reader.onload = function() {
        img.src = reader.result;
        preview.classList.remove('hidden');
    }
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

document.querySelectorAll('input[type="radio"][name="type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('input[type="radio"][name="type"]').forEach(r => {
            const card = document.getElementById('card-' + r.value);
            const checkIcon = card.querySelector('.type-check');
            const faIcon = card.querySelector('.flex.flex-1 i');
            if (r.checked) {
                card.classList.add('border-[#D4AF37]');
                card.classList.remove('border-[#E0E0E0]');
                checkIcon.classList.remove('opacity-30');
                checkIcon.classList.add('text-[#D4AF37]');
                checkIcon.classList.remove('text-[#37474F]');
                if (faIcon) { faIcon.classList.add('text-[#D4AF37]'); faIcon.classList.remove('text-[#37474F]'); }
            } else {
                card.classList.remove('border-[#D4AF37]');
                card.classList.add('border-[#E0E0E0]');
                checkIcon.classList.add('opacity-30');
                checkIcon.classList.remove('text-[#D4AF37]');
                checkIcon.classList.add('text-[#37474F]');
                if (faIcon) { faIcon.classList.remove('text-[#D4AF37]'); faIcon.classList.add('text-[#37474F]'); }
            }
        });
    });
});

document.querySelector('input[type="radio"][name="type"]:checked')?.dispatchEvent(new Event('change'));
</script>
@endsection

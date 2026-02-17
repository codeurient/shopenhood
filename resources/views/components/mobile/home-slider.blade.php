@props(['mainSliders', 'smallBanners'])

<!-- Home Slider Section -->
<div class="px-4 py-4 space-y-3" x-data="homeSlider()">
    <!-- Main Slider -->
    @if($mainSliders->count() > 0)
    <div class="relative overflow-hidden rounded-2xl" style="height: 180px;">
        @foreach($mainSliders as $index => $slider)
        <div x-show="currentSlide === {{ $index }}"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform -translate-x-full"
             class="absolute inset-0"
             style="display: none;">

            <a href="{{ $slider->link ?: '#' }}" class="block w-full h-full">
                <img src="{{ asset('storage/' . $slider->image) }}"
                     alt="{{ $slider->title }}"
                     class="w-full h-full object-cover">

                <!-- Overlay with text -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end p-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $slider->title }}</h2>
                        @if($slider->subtitle)
                            <p class="text-sm text-white/90 mt-1">{{ $slider->subtitle }}</p>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        @endforeach

        <!-- Navigation Dots -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2 z-10">
            @foreach($mainSliders as $index => $slider)
            <button @click="currentSlide = {{ $index }}"
                    :class="currentSlide === {{ $index }} ? 'opacity-100' : 'opacity-50'"
                    class="w-2 h-2 rounded-full bg-white transition-opacity"></button>
            @endforeach
        </div>
    </div>
    @else
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-yellow-300 to-yellow-400 flex items-center justify-center" style="height: 180px;">
        <div class="text-center p-4">
            <h2 class="text-2xl font-bold text-white">Welcome to ShopEnhood</h2>
            <p class="text-sm text-white/90 mt-2">Your marketplace for everything</p>
        </div>
    </div>
    @endif

    <!-- Two Small Banners -->
    <div class="grid grid-cols-2 gap-3">
        @foreach($smallBanners->take(2) as $banner)
        <a href="{{ $banner->link ?: '#' }}" class="h-10 rounded-xl overflow-hidden">
            <img src="{{ asset('storage/' . $banner->image) }}"
                 alt="{{ $banner->title }}"
                 class="w-full h-full object-cover">
        </a>
        @endforeach

        @if($smallBanners->count() < 2)
            @for($i = $smallBanners->count(); $i < 2; $i++)
            <div class="h-10 bg-gray-400 rounded-xl flex items-center justify-center">
                <span class="text-sm font-semibold text-white">Banner {{ $i + 1 }}</span>
            </div>
            @endfor
        @endif
    </div>
</div>

<script>
function homeSlider() {
    return {
        currentSlide: 0,
        totalSlides: {{ $mainSliders->count() }},

        init() {
            if (this.totalSlides > 1) {
                setInterval(() => {
                    this.nextSlide();
                }, 5000); // Auto-advance every 5 seconds
            }
        },

        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        }
    }
}
</script>

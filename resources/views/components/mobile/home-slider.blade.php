@props(['mainSliders', 'smallBanners'])

<!-- Home Slider Section -->
<div x-data="homeSlider()" class="max-w-[1250px] mx-auto px-4 md:px-6 py-4 md:py-6 space-y-3 md:space-y-4">
    <!-- Main Slider -->
    @if($mainSliders->count() > 0)
    <div class="relative overflow-hidden rounded-2xl h-[250px]">
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
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-4 md:p-8">
                    <div>
                        <h2 class="text-xl md:text-3xl lg:text-4xl font-bold text-white">{{ $slider->title }}</h2>
                        @if($slider->subtitle)
                            <p class="text-sm md:text-base text-white/90 mt-1 md:mt-2">{{ $slider->subtitle }}</p>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        @endforeach

        <!-- Navigation Dots -->
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
            @foreach($mainSliders as $index => $slider)
            <button @click="currentSlide = {{ $index }}"
                    :class="currentSlide === {{ $index }} ? 'opacity-100 w-6' : 'opacity-50 w-2'"
                    class="h-2 rounded-full bg-white transition-all duration-300"></button>
            @endforeach
        </div>

        <!-- Prev/Next arrows (desktop only) -->
        @if($mainSliders->count() > 1)
        <button @click="currentSlide = (currentSlide - 1 + totalSlides) % totalSlides"
                class="hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 items-center justify-center bg-black/30 hover:bg-black/50 text-white rounded-full transition-colors z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button @click="currentSlide = (currentSlide + 1) % totalSlides"
                class="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 items-center justify-center bg-black/30 hover:bg-black/50 text-white rounded-full transition-colors z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        @endif
    </div>
    @else
    <div class="relative overflow-hidden rounded-2xl h-[250px] bg-gradient-to-br from-yellow-300 to-yellow-400 flex items-center justify-center">
        <div class="text-center p-4">
            <h2 class="text-2xl md:text-4xl font-bold text-white">Welcome to ShopEnhood</h2>
            <p class="text-sm md:text-lg text-white/90 mt-2">Your marketplace for everything</p>
        </div>
    </div>
    @endif

    <!-- Two Small Banners -->
    <div class="grid grid-cols-2 gap-3 md:gap-4">
        @foreach($smallBanners->take(2) as $banner)
        <a href="{{ $banner->link ?: '#' }}" class="h-10 md:h-16 lg:h-20 rounded-xl overflow-hidden">
            <img src="{{ asset('storage/' . $banner->image) }}"
                 alt="{{ $banner->title }}"
                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-200">
        </a>
        @endforeach
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
                }, 5000);
            }
        },

        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        }
    }
}
</script>

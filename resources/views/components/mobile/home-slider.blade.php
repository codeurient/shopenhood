<!-- Home Slider Section -->
<div class="px-4 py-4 space-y-3" x-data="{ currentSlide: 0 }">
    <!-- Main Slider -->
    <div class="relative overflow-hidden rounded-2xl" style="height: 180px;">
        <!-- Slide 1 -->
        <div class="absolute inset-0 bg-gradient-to-br from-yellow-300 to-yellow-400 flex items-center justify-center">
            <h2 class="text-3xl font-bold text-white">Slide 1</h2>
        </div>

        <!-- Pagination Dots -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
            <button class="w-2 h-2 rounded-full bg-white opacity-100"></button>
            <button class="w-2 h-2 rounded-full bg-white opacity-50"></button>
            <button class="w-2 h-2 rounded-full bg-white opacity-50"></button>
            <button class="w-2 h-2 rounded-full bg-white opacity-50"></button>
            <button class="w-2 h-2 rounded-full bg-white opacity-50"></button>
        </div>
    </div>

    <!-- Two Image Placeholders -->
    <div class="grid grid-cols-2 gap-3">
        <div class="h-10 bg-gray-400 rounded-xl flex items-center justify-center">
            <span class="text-sm font-semibold text-white">image 1</span>
        </div>
        <div class="h-10 bg-gray-400 rounded-xl flex items-center justify-center">
            <span class="text-sm font-semibold text-white">image 2</span>
        </div>
    </div>
</div>
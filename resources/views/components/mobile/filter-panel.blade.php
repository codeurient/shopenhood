<div x-data="filterPanelData(
    {{ Illuminate\Support\Js::from($topCategories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug, 'has_children' => $c->children_count > 0])->values()) }},
    {{ Illuminate\Support\Js::from($listingTypes->map(fn($t) => ['id' => $t->id, 'name' => $t->name])->values()) }},
    {{ Illuminate\Support\Js::from($activeCategoryPath) }}
)"
     x-init="init()"
     @toggle-filter-panel.window="open = !open; document.body.style.overflow = open ? 'hidden' : ''"
     @close-filter-panel.window="open = false; document.body.style.overflow = ''">

    {{-- Backdrop --}}
    <div x-show="open"
         x-cloak
         style="display:none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] bg-black/40"
         @click="open = false; document.body.style.overflow = ''">
    </div>

    {{-- Panel --}}
    <div x-show="open"
         x-cloak
         style="display:none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-[60] w-[320px] max-w-[85vw] bg-white flex flex-col shadow-xl"
         @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-[#E0E0E0] flex-shrink-0">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-filter text-[#37474F] text-sm"></i>
                <h2 class="text-[15px] font-bold text-[#1A1A1A]">Filters</h2>
                <span x-show="activeCount > 0"
                      x-cloak style="display:none;"
                      x-text="activeCount"
                      class="flex items-center justify-center w-5 h-5 text-[10px] font-bold bg-[#D4AF37] text-[#000000] rounded-full">
                </span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                        x-show="activeCount > 0"
                        x-cloak style="display:none;"
                        @click="reset()"
                        class="text-[12px] text-[#37474F] hover:text-[#D4AF37] transition-colors">
                    Reset all
                </button>
                <button type="button"
                        @click="open = false; document.body.style.overflow = ''"
                        class="w-7 h-7 flex items-center justify-center text-[#37474F] hover:text-[#1A1A1A] transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Scrollable body --}}
        <div class="flex-1 overflow-y-auto divide-y divide-[#E0E0E0]">

            {{-- ── 1. LOCATION ── --}}
            <div class="px-4 py-4">
                <p class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-3">Location</p>
                <div class="space-y-2">

                    {{-- Country searchable select --}}
                    <div class="relative" @click.outside="countryOpen = false">
                        <button type="button"
                                @click="countryOpen = !countryOpen"
                                class="w-full flex items-center justify-between h-[34px] px-3 text-[13px] border border-[#E0E0E0] rounded hover:border-[#D4AF37] focus:outline-none transition-colors text-left bg-white">
                            <span :class="country ? 'text-[#1A1A1A]' : 'text-[#37474F]'"
                                  x-text="country || 'Select country...'"></span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-[#37474F] transition-transform"
                               :class="countryOpen ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="countryOpen"
                             style="display:none;"
                             class="absolute left-0 right-0 top-full mt-1 bg-white border border-[#E0E0E0] rounded shadow-xl z-[70] overflow-hidden">
                            <div class="p-2 border-b border-[#E0E0E0]">
                                <input type="text"
                                       x-model="countrySearch"
                                       x-ref="countryInput"
                                       x-effect="if (countryOpen) $nextTick(() => $refs.countryInput?.focus())"
                                       @keydown.escape.stop="countryOpen = false"
                                       placeholder="Search country..."
                                       class="w-full h-[30px] px-2 text-[12px] border border-[#E0E0E0] rounded focus:outline-none focus:border-[#D4AF37] transition-colors">
                            </div>
                            <div class="max-h-44 overflow-y-auto">
                                <button type="button"
                                        @click="country = ''; countryOpen = false; countrySearch = ''"
                                        :class="!country ? 'text-[#D4AF37] bg-[#D4AF37]/5' : 'text-[#37474F] hover:bg-[#D4AF37]/5'"
                                        class="w-full px-3 py-2 text-left text-[12px] transition-colors">
                                    Any country
                                </button>
                                <template x-for="c in filteredCountries" :key="c">
                                    <button type="button"
                                            @click="country = c; countryOpen = false; countrySearch = ''"
                                            :class="country === c ? 'bg-[#D4AF37]/10 text-[#D4AF37]' : 'text-[#1A1A1A] hover:bg-[#D4AF37]/5'"
                                            class="w-full px-3 py-2 text-left text-[12px] transition-colors"
                                            x-text="c">
                                    </button>
                                </template>
                                <div x-show="filteredCountries.length === 0"
                                     class="px-3 py-3 text-[12px] text-[#37474F] text-center">
                                    No country found
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- City text input --}}
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-3 top-1/2 -translate-y-1/2 text-[#37474F] text-xs pointer-events-none"></i>
                        <input type="text"
                               x-model="city"
                               placeholder="City"
                               class="w-full h-[34px] pl-8 pr-3 text-[13px] border border-[#E0E0E0] rounded focus:outline-none focus:ring-0 focus:border-[#D4AF37] placeholder-[#37474F] text-[#1A1A1A] transition-colors">
                    </div>
                </div>
            </div>

            {{-- ── 2. LISTING TYPE ── --}}
            <div class="px-4 py-4">
                <p class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-3">Listing Type</p>
                <div class="relative" @click.outside="typeOpen = false">
                    <button type="button"
                            @click="typeOpen = !typeOpen"
                            class="w-full flex items-center justify-between h-[34px] px-3 text-[13px] border border-[#E0E0E0] rounded hover:border-[#D4AF37] focus:outline-none transition-colors text-left bg-white">
                        <span :class="typeId ? 'text-[#1A1A1A]' : 'text-[#37474F]'"
                              x-text="selectedTypeName || 'All types'"></span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-[#37474F] transition-transform"
                           :class="typeOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="typeOpen"
                         style="display:none;"
                         class="absolute left-0 right-0 top-full mt-1 bg-white border border-[#E0E0E0] rounded shadow-xl z-[70] overflow-hidden">
                        <div class="p-2 border-b border-[#E0E0E0]">
                            <input type="text"
                                   x-model="typeSearch"
                                   x-ref="typeInput"
                                   x-effect="if (typeOpen) $nextTick(() => $refs.typeInput?.focus())"
                                   @keydown.escape.stop="typeOpen = false"
                                   placeholder="Search type..."
                                   class="w-full h-[30px] px-2 text-[12px] border border-[#E0E0E0] rounded focus:outline-none focus:border-[#D4AF37] transition-colors">
                        </div>
                        <div class="max-h-44 overflow-y-auto">
                            <button type="button"
                                    @click="typeId = null; typeOpen = false; typeSearch = ''"
                                    :class="!typeId ? 'text-[#D4AF37] bg-[#D4AF37]/5' : 'text-[#37474F] hover:bg-[#D4AF37]/5'"
                                    class="w-full px-3 py-2 text-left text-[12px] transition-colors">
                                All types
                            </button>
                            <template x-for="type in filteredTypes" :key="type.id">
                                <button type="button"
                                        @click="typeId = type.id; typeOpen = false; typeSearch = ''"
                                        :class="typeId === type.id ? 'bg-[#D4AF37]/10 text-[#D4AF37]' : 'text-[#1A1A1A] hover:bg-[#D4AF37]/5'"
                                        class="w-full px-3 py-2 text-left text-[12px] transition-colors"
                                        x-text="type.name">
                                </button>
                            </template>
                            <div x-show="filteredTypes.length === 0"
                                 class="px-3 py-3 text-[12px] text-[#37474F] text-center">
                                No types found
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 2. CATEGORY (cascading drill-down) ── --}}
            <div class="px-4 py-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider">Category</p>
                    <button type="button"
                            x-show="categorySlug"
                            x-cloak style="display:none;"
                            @click="clearCategory()"
                            class="text-[11px] text-[#37474F] hover:text-[#D4AF37] transition-colors">
                        Clear
                    </button>
                </div>

                {{-- Breadcrumb trail --}}
                <div x-show="breadcrumb.length > 0"
                     x-cloak style="display:none;"
                     class="flex flex-wrap items-center gap-1 mb-3 text-[12px]">
                    <template x-for="(crumb, i) in breadcrumb" :key="crumb.id">
                        <span class="flex items-center gap-1">
                            <span x-text="crumb.name"
                                  :class="i === breadcrumb.length - 1 ? 'text-[#D4AF37] font-medium' : 'text-[#37474F]'">
                            </span>
                            <i x-show="i < breadcrumb.length - 1"
                               class="fa-solid fa-chevron-right text-[9px] text-[#E0E0E0]"></i>
                        </span>
                    </template>
                </div>

                {{-- Level 1: top-level categories --}}
                <div x-show="catLevel === 0" class="space-y-0.5 max-h-52 overflow-y-auto">
                    <template x-for="cat in topCategories" :key="cat.id">
                        <button type="button"
                                @click="selectCat(cat, 1)"
                                class="w-full flex items-center justify-between px-3 py-2 rounded text-left text-[13px] text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37] transition-colors">
                            <span x-text="cat.name"></span>
                            <i x-show="cat.has_children" class="fa-solid fa-chevron-right text-[10px] text-[#37474F]"></i>
                        </button>
                    </template>
                </div>

                {{-- Level 2: children of selected level-1 --}}
                <div x-show="catLevel === 1 && level2Cats.length > 0" class="space-y-0.5">
                    <button type="button"
                            @click="goBack(0)"
                            class="flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#D4AF37] mb-2 transition-colors">
                        <i class="fa-solid fa-chevron-left text-[9px]"></i>
                        All categories
                    </button>
                    <div class="max-h-52 overflow-y-auto space-y-0.5">
                        <template x-for="cat in level2Cats" :key="cat.id">
                            <button type="button"
                                    @click="selectCat(cat, 2)"
                                    :class="breadcrumb.length >= 2 && breadcrumb[1]?.id === cat.id
                                        ? 'bg-[#D4AF37]/10 text-[#D4AF37]'
                                        : 'text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37]'"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded text-left text-[13px] transition-colors">
                                <span x-text="cat.name"></span>
                                <i x-show="cat.has_children" class="fa-solid fa-chevron-right text-[10px] text-[#37474F]"></i>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Level 1 selected, no children --}}
                <div x-show="catLevel === 1 && level2Cats.length === 0 && !catsLoading"
                     class="text-[12px] text-[#37474F] mt-1">
                    Category selected.
                    <button type="button" @click="goBack(0)" class="underline hover:text-[#D4AF37] transition-colors">Change</button>
                </div>

                {{-- Level 3: children of selected level-2 --}}
                <div x-show="catLevel === 2 && level3Cats.length > 0" class="space-y-0.5">
                    <button type="button"
                            @click="goBack(1)"
                            class="flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#D4AF37] mb-2 transition-colors">
                        <i class="fa-solid fa-chevron-left text-[9px]"></i>
                        <span x-text="breadcrumb[0]?.name ?? 'Back'"></span>
                    </button>
                    <div class="max-h-52 overflow-y-auto space-y-0.5">
                        <template x-for="cat in level3Cats" :key="cat.id">
                            <button type="button"
                                    @click="selectCat(cat, 3)"
                                    :class="breadcrumb.length >= 3 && breadcrumb[2]?.id === cat.id
                                        ? 'bg-[#D4AF37]/10 text-[#D4AF37]'
                                        : 'text-[#1A1A1A] hover:bg-[#D4AF37]/5 hover:text-[#D4AF37]'"
                                    class="w-full px-3 py-2 rounded text-left text-[13px] transition-colors">
                                <span x-text="cat.name"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Level 2 selected, no further children --}}
                <div x-show="catLevel === 2 && level3Cats.length === 0 && !catsLoading"
                     class="text-[12px] text-[#37474F] mt-1">
                    Category selected.
                    <button type="button" @click="goBack(1)" class="underline hover:text-[#D4AF37] transition-colors">Change</button>
                </div>

                {{-- Level 3 selected --}}
                <div x-show="catLevel === 3" class="text-[12px] text-[#37474F] mt-1">
                    <button type="button"
                            @click="goBack(2)"
                            class="flex items-center gap-1.5 hover:text-[#D4AF37] transition-colors">
                        <i class="fa-solid fa-chevron-left text-[9px]"></i>
                        Change subcategory
                    </button>
                </div>

                {{-- Loading indicator --}}
                <div x-show="catsLoading" class="flex items-center gap-2 py-2">
                    <i class="fa-solid fa-spinner animate-spin text-[#37474F] text-xs"></i>
                    <span class="text-[12px] text-[#37474F]">Loading...</span>
                </div>
            </div>

            {{-- ── 3. CONDITION ── --}}
            <div class="px-4 py-4">
                <p class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-3">Condition</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(['' => 'Any', 'new' => 'New', 'used' => 'Used', 'refurbished' => 'Refurbished'] as $val => $label)
                        <button type="button"
                                @click="condition = '{{ $val }}'"
                                :class="condition === '{{ $val }}'
                                    ? 'border-[#D4AF37] bg-[#D4AF37]/10 text-[#D4AF37]'
                                    : 'border-[#E0E0E0] text-[#37474F] hover:border-[#D4AF37]/50'"
                                class="px-3 h-[28px] border rounded text-[12px] font-medium transition-colors">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ── 5. PRICE RANGE ── --}}
            <div class="px-4 py-4">
                <p class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-3">Price Range</p>
                <div class="flex items-center gap-2">
                    <input type="number"
                           x-model="minPrice"
                           placeholder="Min"
                           min="0"
                           class="w-full h-[34px] px-3 text-[13px] border border-[#E0E0E0] rounded focus:outline-none focus:ring-0 focus:border-[#D4AF37] placeholder-[#37474F] text-[#1A1A1A] transition-colors">
                    <span class="text-[#37474F] text-xs flex-shrink-0">–</span>
                    <input type="number"
                           x-model="maxPrice"
                           placeholder="Max"
                           min="0"
                           class="w-full h-[34px] px-3 text-[13px] border border-[#E0E0E0] rounded focus:outline-none focus:ring-0 focus:border-[#D4AF37] placeholder-[#37474F] text-[#1A1A1A] transition-colors">
                </div>
            </div>

            {{-- ── 6. VARIANT ATTRIBUTES (dynamic) ── --}}
            <div x-show="variants.length > 0 || variantsLoading"
                 x-cloak style="display:none;"
                 class="px-4 py-4">
                <p class="text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-3">Attributes</p>

                <div x-show="variantsLoading" class="flex items-center gap-2 py-1">
                    <i class="fa-solid fa-spinner animate-spin text-[#37474F] text-xs"></i>
                    <span class="text-[12px] text-[#37474F]">Loading attributes...</span>
                </div>

                <div x-show="!variantsLoading" class="space-y-4">
                    <template x-for="variant in variants" :key="variant.id">
                        <div>
                            <p class="text-[12px] font-medium text-[#1A1A1A] mb-2" x-text="variant.name"></p>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="item in variant.items" :key="item.id">
                                    <button type="button"
                                            @click="variantSelections[variant.id] = (variantSelections[variant.id] === item.id) ? null : item.id"
                                            :class="variantSelections[variant.id] === item.id
                                                ? 'border-[#D4AF37] bg-[#D4AF37]/10 text-[#D4AF37]'
                                                : 'border-[#E0E0E0] text-[#37474F] hover:border-[#D4AF37]/50'"
                                            class="px-3 h-[28px] border rounded text-[12px] font-medium transition-colors"
                                            x-text="item.display_value || item.value">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>{{-- end scrollable body --}}

        {{-- Footer: Apply --}}
        <div class="flex-shrink-0 border-t border-[#E0E0E0] px-4 py-4 bg-white">
            <button type="button"
                    @click="apply()"
                    class="w-full h-[44px] bg-[#D4AF37] text-[#000000] font-semibold text-[13px] rounded hover:brightness-110 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Search</span>
                <span x-show="activeCount > 0"
                      x-cloak style="display:none;"
                      x-text="'(' + activeCount + ' filters)'"
                      class="font-normal text-[12px]">
                </span>
            </button>
        </div>
    </div>
</div>

<script>
function filterPanelData(topCategories, listingTypes, activeCatPath) {
    return {
        // ── Panel state ──────────────────────────────────────────────────
        open: false,

        // ── Static data ──────────────────────────────────────────────────
        topCategories: topCategories,
        listingTypes: listingTypes,

        // ── Filter values (pre-populated from current URL) ───────────────
        typeId: {{ request('type') ? (int) request('type') : 'null' }},
        categorySlug: @js(request('category', '')),
        condition: @js(request('condition', '')),
        country: @js(request('country', '')),
        city: @js(request('city', '')),
        minPrice: @js(request('min_price', '')),
        maxPrice: @js(request('max_price', '')),

        // ── Searchable select state ───────────────────────────────────────
        countryOpen: false,
        countrySearch: '',
        typeOpen: false,
        typeSearch: '',

        // ── Country list ─────────────────────────────────────────────────
        countries: ['Afghanistan','Albania','Algeria','Andorra','Angola','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahrain','Bangladesh','Belarus','Belgium','Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Bulgaria','Cambodia','Cameroon','Canada','Chile','China','Colombia','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Dominican Republic','Ecuador','Egypt','Estonia','Ethiopia','Finland','France','Georgia','Germany','Ghana','Greece','Guatemala','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kuwait','Latvia','Lebanon','Libya','Lithuania','Luxembourg','Malaysia','Malta','Mexico','Moldova','Morocco','Myanmar','Nepal','Netherlands','New Zealand','Nigeria','Norway','Oman','Pakistan','Palestine','Panama','Paraguay','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Saudi Arabia','Senegal','Serbia','Singapore','Slovakia','Slovenia','Somalia','South Africa','South Korea','Spain','Sri Lanka','Sudan','Sweden','Switzerland','Syria','Taiwan','Tanzania','Thailand','Tunisia','Turkey','Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan','Venezuela','Vietnam','Yemen','Zambia','Zimbabwe'],

        // ── Category navigation state ────────────────────────────────────
        breadcrumb: [],   // [{id, name, slug, has_children}]
        level2Cats: [],
        level3Cats: [],
        catsLoading: false,

        // ── Variant state ────────────────────────────────────────────────
        variants: [],
        variantSelections: {},
        variantsLoading: false,

        // ── Init ─────────────────────────────────────────────────────────
        async init() {
            // Restore category path from server-resolved data
            if (activeCatPath && activeCatPath.length > 0) {
                await this.restorePath(activeCatPath);
            }

            // Allow pages to override context (e.g. listing show page)
            const ctx = window.filterPanelContext || {};
            if (ctx.categoryId && !this.categorySlug) {
                await this.preloadCategoryById(ctx.categoryId);
            }
            if (ctx.typeId && !this.typeId) {
                this.typeId = ctx.typeId;
            }
        },

        // ── Category helpers ─────────────────────────────────────────────
        get catLevel() {
            return this.breadcrumb.length;
        },

        async restorePath(path) {
            this.breadcrumb = path;
            this.categorySlug = path[path.length - 1].slug;
            if (path.length >= 1) {
                await this.fetchChildren(path[0].id, 2);
            }
            if (path.length >= 2) {
                await this.fetchChildren(path[1].id, 3);
            }
            this.loadVariants(path[path.length - 1].id);
        },

        async preloadCategoryById(categoryId) {
            try {
                const res = await fetch(`/api/categories/${categoryId}/hierarchy`);
                const data = await res.json();
                if (data.success && data.hierarchy.length > 0) {
                    const path = data.hierarchy.map(h => ({
                        id: h.id, name: h.name, slug: h.slug || '', has_children: !!h.parent_id
                    }));
                    await this.restorePath(path);
                }
            } catch (e) { /* silent */ }
        },

        async selectCat(cat, level) {
            this.clearVariants();
            if (level === 1) {
                this.breadcrumb = [cat];
                this.level2Cats = [];
                this.level3Cats = [];
                this.categorySlug = cat.slug;
                if (cat.has_children) {
                    await this.fetchChildren(cat.id, 2);
                }
            } else if (level === 2) {
                this.breadcrumb = [this.breadcrumb[0], cat];
                this.level3Cats = [];
                this.categorySlug = cat.slug;
                if (cat.has_children) {
                    await this.fetchChildren(cat.id, 3);
                }
            } else {
                this.breadcrumb = [this.breadcrumb[0], this.breadcrumb[1], cat];
                this.categorySlug = cat.slug;
            }
            this.loadVariants(this.breadcrumb[this.breadcrumb.length - 1].id);
        },

        goBack(toLevel) {
            this.clearVariants();
            if (toLevel === 0) {
                this.clearCategory();
            } else if (toLevel === 1) {
                this.breadcrumb = [this.breadcrumb[0]];
                this.level3Cats = [];
                this.categorySlug = this.breadcrumb[0].slug;
                this.loadVariants(this.breadcrumb[0].id);
            } else if (toLevel === 2) {
                this.breadcrumb = this.breadcrumb.slice(0, 2);
                this.categorySlug = this.breadcrumb[1].slug;
                this.loadVariants(this.breadcrumb[1].id);
            }
        },

        clearCategory() {
            this.breadcrumb = [];
            this.level2Cats = [];
            this.level3Cats = [];
            this.categorySlug = '';
            this.clearVariants();
        },

        clearVariants() {
            this.variants = [];
            this.variantSelections = {};
        },

        async fetchChildren(categoryId, targetLevel) {
            this.catsLoading = true;
            try {
                const res = await fetch(`/api/categories/children/${categoryId}`);
                const data = await res.json();
                const cats = (data.categories || []).map(c => ({
                    ...c,
                    has_children: c.has_children || false,
                }));
                if (targetLevel === 2) {
                    this.level2Cats = cats;
                } else {
                    this.level3Cats = cats;
                }
            } catch (e) { /* silent */ }
            this.catsLoading = false;
        },

        async loadVariants(categoryId) {
            this.variantsLoading = true;
            try {
                const res = await fetch(`/api/categories/${categoryId}/variants?show_all=1`);
                const data = await res.json();
                this.variants = (data.variants || []).filter(v => v.items && v.items.length > 0);
            } catch (e) {
                this.variants = [];
            }
            this.variantsLoading = false;
        },

        // ── Apply / Reset ─────────────────────────────────────────────────
        apply() {
            const url = new URL('{{ route('listings.index') }}', window.location.origin);
            if (this.typeId) url.searchParams.set('type', this.typeId);
            if (this.categorySlug) url.searchParams.set('category', this.categorySlug);
            if (this.condition) url.searchParams.set('condition', this.condition);
            if (this.country.trim()) url.searchParams.set('country', this.country.trim());
            if (this.city.trim()) url.searchParams.set('city', this.city.trim());
            if (this.minPrice) url.searchParams.set('min_price', this.minPrice);
            if (this.maxPrice) url.searchParams.set('max_price', this.maxPrice);
            Object.entries(this.variantSelections).forEach(([vId, iId]) => {
                if (iId) url.searchParams.set(`variant[${vId}]`, iId);
            });
            window.location.href = url.toString();
        },

        reset() {
            this.typeId = null;
            this.typeOpen = false;
            this.typeSearch = '';
            this.condition = '';
            this.country = '';
            this.countryOpen = false;
            this.countrySearch = '';
            this.city = '';
            this.minPrice = '';
            this.maxPrice = '';
            this.clearCategory();
        },

        // ── Searchable select computed ────────────────────────────────────
        get filteredCountries() {
            const s = this.countrySearch.toLowerCase().trim();
            if (!s) { return this.countries; }
            return this.countries.filter(c => c.toLowerCase().includes(s));
        },

        get filteredTypes() {
            const s = this.typeSearch.toLowerCase().trim();
            if (!s) { return this.listingTypes; }
            return this.listingTypes.filter(t => t.name.toLowerCase().includes(s));
        },

        get selectedTypeName() {
            if (!this.typeId) { return null; }
            const t = this.listingTypes.find(t => t.id === this.typeId);
            return t ? t.name : null;
        },

        // ── Active filter count ───────────────────────────────────────────
        get activeCount() {
            let n = 0;
            if (this.typeId) n++;
            if (this.categorySlug) n++;
            if (this.condition) n++;
            if (this.country.trim()) n++;
            if (this.city.trim()) n++;
            if (this.minPrice) n++;
            if (this.maxPrice) n++;
            n += Object.values(this.variantSelections).filter(Boolean).length;
            return n;
        },
    };
}
</script>

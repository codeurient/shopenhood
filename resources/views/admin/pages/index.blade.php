@extends('admin.layouts.app')

@section('title', 'Pages')
@section('page-title', 'Pages')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Pages</h2>
            <p class="text-[#37474F] text-sm mt-1">Manage static content pages displayed on the site</p>
        </div>
        <a href="{{ route('admin.pages.create') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-plus text-xs"></i>
            Create Page
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#37474F] border-b border-[#000000]/20">
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider w-8"></th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Page</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Visibility</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Last Updated</th>
                        <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="pages-sortable">
                    @foreach($pages as $page)
                        <tr data-id="{{ $page->id }}" class="bg-white hover:bg-[#D4AF37]/5 transition-colors border-b border-[#E0E0E0] last:border-0">
                            <td class="px-3 py-2.5">
                                <i class="fa-solid fa-grip-vertical text-[#E0E0E0] hover:text-[#37474F] cursor-grab active:cursor-grabbing transition-colors"></i>
                            </td>
                            <td class="px-3 py-2.5">
                                <p class="text-[13px] font-medium text-[#1A1A1A]">{{ $page->title }}</p>
                                <p class="text-[12px] text-[#37474F] font-mono">/pages/{{ $page->key }}</p>
                            </td>
                            <td class="px-3 py-2.5">
                                <button type="button"
                                        onclick="toggleVisibility({{ $page->id }}, this)"
                                        class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold transition {{ $page->is_published ? 'bg-[#D4AF37]/20 text-[#D4AF37] hover:bg-[#D4AF37]/30' : 'bg-gray-100 text-[#37474F] hover:bg-gray-200' }}">
                                    {{ $page->is_published ? 'Published' : 'Hidden' }}
                                </button>
                            </td>
                            <td class="px-3 py-2.5 text-[12px] text-[#37474F]">{{ $page->updated_at->format('M d, Y') }}</td>
                            <td class="px-3 py-2.5">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('pages.show', $page->key) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 h-[28px] px-3 text-[12px] font-medium text-[#37474F] border border-[#E0E0E0] rounded hover:bg-gray-100 transition">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                        View
                                    </a>
                                    <a href="{{ route('admin.pages.edit', $page) }}"
                                       class="inline-flex items-center gap-1.5 h-[28px] px-3 text-[12px] font-medium text-[#37474F] border border-[#E0E0E0] rounded hover:bg-gray-100 transition">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                @click="$dispatch('open-confirm-modal', { message: 'Delete page \'{{ addslashes($page->title) }}\'? This cannot be undone.', form: $el.closest('form') })"
                                                class="inline-flex items-center gap-1.5 h-[28px] px-3 text-[12px] font-medium text-[#C0392B] border border-[#C0392B]/30 rounded hover:bg-red-50 transition">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
// Drag-and-drop reorder
const csrf = document.querySelector('meta[name=csrf-token]')?.content ?? '';

Sortable.create(document.getElementById('pages-sortable'), {
    animation: 150,
    handle: '.fa-grip-vertical',
    ghostClass: 'bg-[#D4AF37]/10',
    onEnd() {
        const order = [...document.querySelectorAll('#pages-sortable tr[data-id]')]
            .map(tr => tr.dataset.id);

        fetch('{{ route('admin.pages.reorder') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ order }),
        });
    },
});

function toggleVisibility(pageId, btn) {
    const csrf = document.querySelector('meta[name=csrf-token]')?.content ?? '';
    fetch(`{{ route('admin.pages.toggle-visibility', ':id') }}`.replace(':id', pageId), {
        method: 'PATCH',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
    })
    .then(r => r.json())
    .then(data => {
        const published = data.is_published;
        btn.textContent = published ? 'Published' : 'Hidden';
        btn.className = 'inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold transition ' +
            (published ? 'bg-[#D4AF37]/20 text-[#D4AF37] hover:bg-[#D4AF37]/30' : 'bg-gray-100 text-[#37474F] hover:bg-gray-200');
    });
}
</script>
@endsection

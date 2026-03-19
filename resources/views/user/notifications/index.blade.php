<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#1A1A1A]">Notifications</h1>
            <p class="text-xs text-[#37474F] mt-0.5">Your account activity and listing updates</p>
        </div>
        @if($notifications->isNotEmpty())
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('user.notifications.read-all') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 h-[34px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                    <i class="fa-solid fa-check-double text-xs"></i>
                    Mark all as read
                </button>
            </form>
            <form method="POST" action="{{ route('user.notifications.delete-all') }}"
                  x-data
                  @submit.prevent="$dispatch('open-confirm-modal', { message: 'Delete all notifications? This cannot be undone.', form: $el })">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 h-[34px] px-4 border border-red-200 text-red-600 text-[13px] font-semibold rounded hover:bg-red-50 transition">
                    <i class="fa-solid fa-trash text-xs"></i>
                    Delete all
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- ── Flash Message ────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-check flex-shrink-0 mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── Notifications List ───────────────────────────────────────────── --}}
    <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">

        @forelse($notifications as $notification)
        @php
            $data     = $notification->data;
            $type     = $data['type'] ?? '';
            $isUnread = is_null($notification->read_at);

            [$iconClass, $iconBg, $iconColor, $badgeClass, $badgeLabel] = match($type) {
                'confident_seller_approved' => ['fa-shield-halved', 'bg-green-100',  'text-green-600',  'bg-green-100 text-green-700',  'Confident Seller — Approved'],
                'confident_seller_rejected' => ['fa-triangle-exclamation', 'bg-yellow-100', 'text-yellow-600', 'bg-yellow-100 text-yellow-700', 'Confident Seller — Rejected'],
                'listing_approved'          => ['fa-circle-check',  'bg-blue-100',   'text-blue-600',   'bg-blue-100 text-blue-700',    'Listing Approved'],
                'listing_rejected'          => ['fa-circle-xmark',  'bg-red-100',    'text-red-500',    'bg-red-100 text-red-600',      'Listing Rejected'],
                default                     => ['fa-bell',           'bg-[#F5F5F5]',  'text-[#37474F]',  '',                             ''],
            };
        @endphp

        <div class="flex items-start gap-4 px-5 py-4 border-b border-[#E0E0E0] last:border-0
                    {{ $isUnread ? 'bg-[#D4AF37]/5' : 'hover:bg-[#F5F5F5]' }} transition">

            {{-- Icon --}}
            <div class="w-9 h-9 rounded-full {{ $iconBg }} flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fa-solid {{ $iconClass }} {{ $iconColor }} text-sm"></i>
            </div>

            {{-- Body --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    @if($badgeLabel)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                            {{ $badgeLabel }}
                        </span>
                    @endif
                    @if($isUnread)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#D4AF37] text-[#000000]">
                            <i class="fa-solid fa-circle text-[6px]"></i>
                            New
                        </span>
                    @endif
                </div>

                <p class="text-[13px] text-[#1A1A1A] leading-relaxed">
                    {{ $data['message'] ?? '' }}
                </p>

                @if(!empty($data['rejection_reason']))
                    <div class="mt-2 px-3 py-2 bg-[#F5F5F5] border border-[#E0E0E0] rounded text-[12px] text-[#37474F]">
                        <span class="font-semibold">Reason:</span> {{ $data['rejection_reason'] }}
                    </div>
                @endif

                <p class="mt-1.5 text-[11px] text-[#37474F]">
                    <i class="fa-regular fa-clock text-[10px] mr-0.5"></i>
                    {{ $notification->created_at->diffForHumans() }}
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 flex-shrink-0 pt-0.5">
                @if($isUnread)
                    <span class="block w-2 h-2 rounded-full bg-[#D4AF37]"></span>
                @endif
                <form method="POST" action="{{ route('user.notifications.destroy', $notification->id) }}"
                      x-data
                      @submit.prevent="$dispatch('open-confirm-modal', { message: 'Delete this notification?', form: $el })">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Delete notification"
                            class="w-7 h-7 flex items-center justify-center rounded text-[#E0E0E0] hover:text-red-500 hover:bg-red-50 transition">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>

        @empty
        {{-- ── Empty State ────────────────────────────────────────────── --}}
        <div class="py-16 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-[#F5F5F5] flex items-center justify-center">
                <i class="fa-solid fa-bell text-[#37474F] text-xl"></i>
            </div>
            <p class="text-[14px] font-semibold text-[#1A1A1A] mb-1">No notifications yet</p>
            <p class="text-[13px] text-[#37474F]">You'll be notified about listing approvals and account updates here.</p>
        </div>
        @endforelse

    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif

</x-app-layout>

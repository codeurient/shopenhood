@extends('admin.layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-users text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['total'] }}</p>
                    <p class="text-[#37474F] text-xs">Total Users</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-user text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['normal'] }}</p>
                    <p class="text-[#37474F] text-xs">Normal Users</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-briefcase text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['business'] }}</p>
                    <p class="text-[#37474F] text-xs">Business Users</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-shield-halved text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ $stats['admin'] }}</p>
                    <p class="text-[#37474F] text-xs">Admins</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 flex-1 min-w-[200px]">

            <select name="role"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Roles</option>
                <option value="normal_user" {{ request('role') === 'normal_user' ? 'selected' : '' }}>Normal User</option>
                <option value="business_user" {{ request('role') === 'business_user' ? 'selected' : '' }}>Business User</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>

            <select name="status"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
            </select>

            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-xmark text-xs"></i>
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#37474F] border-b border-[#000000]/20">
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">User</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Role</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Listings</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Business</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Joined</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-3 py-2.5">
                                    <p class="text-[13px] font-medium text-[#1A1A1A]">{{ $user->name }}</p>
                                    <p class="text-[12px] text-[#37474F]">{{ $user->email }}</p>
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($user->current_role === 'admin')
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#37474F] text-[#E0E0E0]">
                                            <i class="fa-solid fa-shield-halved mr-1 text-[10px]"></i> Admin
                                        </span>
                                    @elseif($user->current_role === 'business_user')
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">
                                            <i class="fa-solid fa-briefcase mr-1 text-[10px]"></i> Business
                                        </span>
                                    @else
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-gray-100 text-gray-600">
                                            <i class="fa-solid fa-user mr-1 text-[10px]"></i> Normal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($user->status === 'active')
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">Active</span>
                                    @elseif($user->status === 'suspended')
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-yellow-50 text-yellow-700">Suspended</span>
                                    @else
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#C0392B]/10 text-[#C0392B]">Banned</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-[13px] font-medium text-[#1A1A1A]">{{ $user->listings_count }}</td>
                                <td class="px-3 py-2.5">
                                    @if($user->business_valid_until)
                                        <p class="text-[12px] text-[#37474F]">{{ $user->business_valid_until->format('M d, Y') }}</p>
                                        @if($user->business_valid_until->isPast())
                                            <span class="text-[11px] text-[#C0392B]">Expired</span>
                                        @endif
                                    @endif
                                    @if($user->businessProfile)
                                        @php $cs = $user->businessProfile->confident_seller_status; @endphp
                                        <div class="mt-1">
                                            @if($cs === 'approved')
                                                <a href="{{ route('admin.business-profiles.show', $user->businessProfile) }}"
                                                   class="inline-flex items-center gap-1 h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37] hover:bg-[#D4AF37]/30 transition">
                                                    <i class="fa-solid fa-circle-check text-[10px]"></i> Confident Seller
                                                </a>
                                            @elseif($cs === 'rejected')
                                                <a href="{{ route('admin.business-profiles.show', $user->businessProfile) }}"
                                                   class="inline-flex items-center gap-1 h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition">
                                                    <i class="fa-solid fa-circle-xmark text-[10px]"></i> Confident Seller
                                                </a>
                                            @elseif($cs === 'pending')
                                                <a href="{{ route('admin.business-profiles.show', $user->businessProfile) }}"
                                                   class="inline-flex items-center gap-1 h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#C0392B]/10 text-[#C0392B] hover:bg-[#C0392B]/20 transition">
                                                    <i class="fa-solid fa-clock text-[10px]"></i> Confident Seller
                                                </a>
                                            @else
                                                <a href="{{ route('admin.business-profiles.show', $user->businessProfile) }}"
                                                   class="text-[12px] text-[#D4AF37] hover:underline">View profile</a>
                                            @endif
                                        </div>
                                    @elseif(!$user->business_valid_until)
                                        <span class="text-[#E0E0E0]">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-[12px] text-[#37474F]">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                           title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        @if($user->id !== auth()->guard('admin')->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete this user?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center w-[28px] h-[28px] bg-[#C0392B] text-white rounded hover:bg-[#a93226] transition"
                                                        title="Delete">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-[#E0E0E0]">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-users text-3xl text-[#E0E0E0] mb-3 block"></i>
                <p class="text-[#37474F] text-[13px]">No users found</p>
            </div>
        @endif
    </div>

</div>
@endsection

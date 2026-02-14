@extends('admin.layouts.app')

@section('title', 'Business Profiles')
@section('page-title', 'Business Profiles')

@section('content')
<div>
    {{-- Stats --}}
    <div class="grid grid-cols-1 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Business Profiles</div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="mb-4 p-4 bg-blue-100 border border-blue-300 text-blue-800 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.business-profiles.index') }}" class="flex gap-4 items-end flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Business name..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                <select name="industry" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Industries</option>
                    @foreach($industries as $industry)
                        <option value="{{ $industry }}" {{ request('industry') === $industry ? 'selected' : '' }}>{{ ucfirst($industry) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                <select name="country" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            <a href="{{ route('admin.business-profiles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Reset</a>
        </form>
    </div>

    {{-- Profiles Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Business</th>
                    <th class="px-4 py-3">Owner</th>
                    <th class="px-4 py-3">Industry</th>
                    <th class="px-4 py-3">Location</th>
                    <th class="px-4 py-3">Created</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($profiles as $profile)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($profile->logo)
                                <img src="{{ asset('storage/' . $profile->logo) }}" alt="{{ $profile->business_name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500 text-sm font-medium">{{ substr($profile->business_name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div>
                                <div class="font-medium text-gray-900">{{ $profile->business_name }}</div>
                                @if($profile->legal_name && $profile->legal_name !== $profile->business_name)
                                    <div class="text-gray-500 text-xs">{{ $profile->legal_name }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-900">{{ $profile->user->name }}</div>
                        <div class="text-gray-500 text-xs">{{ $profile->user->email }}</div>
                    </td>
                    <td class="px-4 py-3">
                        @if($profile->industry)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($profile->industry) }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        @if($profile->city || $profile->country)
                            {{ $profile->city }}{{ $profile->city && $profile->country ? ', ' : '' }}{{ $profile->country?->name }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $profile->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.business-profiles.show', $profile) }}" class="px-3 py-1 text-xs bg-gray-600 text-white rounded hover:bg-gray-700">View</a>
                            <a href="{{ route('admin.business-profiles.edit', $profile) }}" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Edit</a>
                            <form method="POST" action="{{ route('admin.business-profiles.destroy', $profile) }}" onsubmit="return confirm('Are you sure you want to delete this business profile?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No business profiles found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $profiles->links() }}
    </div>
</div>
@endsection

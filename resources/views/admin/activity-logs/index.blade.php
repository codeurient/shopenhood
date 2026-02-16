@extends('admin.layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Activity Logs</h2>
            <p class="text-gray-600 mt-1">Monitor all admin actions and system activities</p>
        </div>
        <form action="{{ route('admin.activity-logs.clear-old') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete old activity logs? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                🗑️ Clear Old Logs
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 text-2xl">📊</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    <p class="text-gray-600 text-sm">Total Logs</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 text-2xl">📅</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['today']) }}</p>
                    <p class="text-gray-600 text-sm">Today</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 text-2xl">📆</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['this_week']) }}</p>
                    <p class="text-gray-600 text-sm">This Week</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600 text-2xl">📈</div>
                <div class="ml-4">
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['this_month']) }}</p>
                    <p class="text-gray-600 text-sm">This Month</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search description..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Log Name</label>
                    <select name="log_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Logs</option>
                        @foreach($logNames as $logName)
                            <option value="{{ $logName }}" {{ request('log_name') == $logName ? 'selected' : '' }}>
                                {{ ucfirst($logName) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject Type</label>
                    <select name="subject_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Types</option>
                        @foreach($subjectTypes as $type)
                            <option value="{{ $type['value'] }}" {{ request('subject_type') == $type['value'] ? 'selected' : '' }}>
                                {{ $type['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600">
                        🔍 Filter
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="flex items-end">
                    <a href="{{ route('admin.activity-logs.index') }}" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-center">
                        🔄 Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Activity Logs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($activities->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Log Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($activities as $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $activity->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $activity->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $activity->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($activity->causer)
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold">
                                                {{ substr($activity->causer->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="font-medium">{{ $activity->causer->name ?? 'Unknown' }}</div>
                                                <div class="text-xs text-gray-500">{{ class_basename($activity->causer_type) }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">System</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($activity->subject)
                                        <div>
                                            <div class="font-medium">{{ class_basename($activity->subject_type) }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $activity->subject_id }}</div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($activity->log_name)
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                            {{ ucfirst($activity->log_name) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.activity-logs.show', $activity) }}"
                                       class="text-primary-600 hover:text-primary-700">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $activities->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📋</div>
                <p class="text-gray-500 text-lg">No activity logs found</p>
                @if(request()->hasAny(['search', 'log_name', 'subject_type', 'date_from', 'date_to']))
                    <a href="{{ route('admin.activity-logs.index') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                        Clear filters
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('content')
<div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Activity Logs</h2>
            <p class="text-[#37474F] text-sm mt-1">Monitor all admin actions and system activities</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('admin.activity-logs.clear-old') }}" method="POST"
                  onsubmit="return confirm('Delete logs older than the configured retention period? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-yellow-600 rounded hover:bg-yellow-700 transition">
                    <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                    Clear Old Logs
                </button>
            </form>
            <form action="{{ route('admin.activity-logs.clear-all') }}" method="POST"
                  onsubmit="return confirm('This will permanently delete ALL activity logs. This action cannot be undone. Continue?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-white bg-[#C0392B] rounded hover:bg-[#a93226] transition">
                    <i class="fa-solid fa-trash text-xs"></i>
                    Clear All Logs
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-list-check text-[#D4AF37]"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['total']) }}</p>
                    <p class="text-[#37474F] text-xs">Total Logs</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-calendar-day text-green-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['today']) }}</p>
                    <p class="text-[#37474F] text-xs">Today</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-calendar-week text-purple-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['this_week']) }}</p>
                    <p class="text-[#37474F] text-xs">This Week</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-chart-line text-orange-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#1A1A1A]">{{ number_format($stats['this_month']) }}</p>
                    <p class="text-[#37474F] text-xs">This Month</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] p-4 mb-5 shadow-[0_1px_4px_rgba(0,0,0,0.08)]">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..."
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 flex-1 min-w-[200px]">

            <select name="log_name"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Logs</option>
                @foreach($logNames as $logName)
                    <option value="{{ $logName }}" {{ request('log_name') == $logName ? 'selected' : '' }}>
                        {{ ucfirst($logName) }}
                    </option>
                @endforeach
            </select>

            <select name="subject_type"
                    class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="">All Types</option>
                @foreach($subjectTypes as $type)
                    <option value="{{ $type['value'] }}" {{ request('subject_type') == $type['value'] ? 'selected' : '' }}>
                        {{ $type['label'] }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">

            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">

            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            <a href="{{ route('admin.activity-logs.index') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                <i class="fa-solid fa-xmark text-xs"></i>
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        @if($activities->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#37474F] border-b border-[#000000]/20">
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Timestamp</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Description</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">User</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Subject</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Log Name</th>
                            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activities as $activity)
                            <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
                                <td class="px-3 py-2.5">
                                    <p class="text-[13px] text-[#1A1A1A]">{{ $activity->created_at->format('M d, Y') }}</p>
                                    <p class="text-[12px] text-[#37474F]">{{ $activity->created_at->format('h:i A') }}</p>
                                </td>
                                <td class="px-3 py-2.5 text-[13px] text-[#1A1A1A]">
                                    {{ $activity->description }}
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($activity->causer)
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-[#37474F] flex items-center justify-center text-[#D4AF37] text-[11px] font-semibold flex-shrink-0">
                                                {{ substr($activity->causer->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-[13px] font-medium text-[#1A1A1A]">{{ $activity->causer->name ?? 'Unknown' }}</p>
                                                <p class="text-[12px] text-[#37474F]">{{ class_basename($activity->causer_type) }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-[13px] text-[#37474F]">System</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($activity->subject)
                                        <p class="text-[13px] font-medium text-[#1A1A1A]">{{ class_basename($activity->subject_type) }}</p>
                                        <p class="text-[12px] text-[#37474F]">ID: {{ $activity->subject_id }}</p>
                                    @else
                                        <span class="text-[#E0E0E0]">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($activity->log_name)
                                        <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-blue-50 text-blue-700">
                                            {{ ucfirst($activity->log_name) }}
                                        </span>
                                    @else
                                        <span class="text-[#E0E0E0]">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <a href="{{ route('admin.activity-logs.show', $activity) }}"
                                       class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                                       title="View Details">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-[#E0E0E0]">
                {{ $activities->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-list-check text-3xl text-[#E0E0E0] mb-3 block"></i>
                <p class="text-[#37474F] text-[13px]">No activity logs found</p>
                @if(request()->hasAny(['search', 'log_name', 'subject_type', 'date_from', 'date_to']))
                    <a href="{{ route('admin.activity-logs.index') }}"
                       class="text-[13px] text-[#37474F] hover:text-[#1A1A1A] mt-2 inline-block underline">
                        Clear filters
                    </a>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection

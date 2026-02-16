@extends('admin.layouts.app')

@section('title', 'Activity Log Details')
@section('page-title', 'Activity Log')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Activity Log Details</h2>
        <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            ← Back to Logs
        </a>
    </div>

    <!-- Main Information Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Description -->
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                <p class="text-gray-700 text-lg">{{ $activity->description }}</p>
            </div>

            <!-- Timestamp -->
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Timestamp</h3>
                <p class="text-gray-900">
                    {{ $activity->created_at->format('F j, Y') }}<br>
                    <span class="text-sm text-gray-600">{{ $activity->created_at->format('g:i A') }}</span>
                </p>
                <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
            </div>

            <!-- Log Name -->
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Log Name</h3>
                @if($activity->log_name)
                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded">
                        {{ ucfirst($activity->log_name) }}
                    </span>
                @else
                    <p class="text-gray-400">Not specified</p>
                @endif
            </div>

            <!-- Event -->
            @if($activity->event)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Event</h3>
                    <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded">
                        {{ ucfirst($activity->event) }}
                    </span>
                </div>
            @endif

            <!-- Batch UUID -->
            @if($activity->batch_uuid)
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Batch UUID</h3>
                    <p class="text-gray-900 font-mono text-sm">{{ $activity->batch_uuid }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Causer (User) Information -->
    @if($activity->causer)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performed By</h3>
            <div class="flex items-center">
                <div class="h-16 w-16 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-2xl">
                    {{ substr($activity->causer->name ?? 'U', 0, 1) }}
                </div>
                <div class="ml-4">
                    <p class="font-semibold text-gray-900 text-lg">{{ $activity->causer->name ?? 'Unknown User' }}</p>
                    <p class="text-sm text-gray-600">{{ $activity->causer->email ?? 'No email' }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        Type: {{ class_basename($activity->causer_type) }} | ID: {{ $activity->causer_id }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Performed By</h3>
            <p class="text-gray-400">System generated (no user)</p>
        </div>
    @endif

    <!-- Subject Information -->
    @if($activity->subject)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Type:</span>
                    <span class="font-medium text-gray-900">{{ class_basename($activity->subject_type) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">ID:</span>
                    <span class="font-medium text-gray-900">{{ $activity->subject_id }}</span>
                </div>
                @if(method_exists($activity->subject, 'name') && $activity->subject->name)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Name:</span>
                        <span class="font-medium text-gray-900">{{ $activity->subject->name }}</span>
                    </div>
                @endif
                @if(method_exists($activity->subject, 'title') && $activity->subject->title)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Title:</span>
                        <span class="font-medium text-gray-900">{{ $activity->subject->title }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Properties -->
    @if($activity->properties && $activity->properties->count() > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Properties</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Old Attributes -->
                @if($activity->properties->has('old'))
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                            <span class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            Old Values
                        </h4>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <pre class="text-sm text-gray-800 overflow-x-auto">{{ json_encode($activity->properties->get('old'), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                @endif

                <!-- New Attributes -->
                @if($activity->properties->has('attributes'))
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                            <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            New Values
                        </h4>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <pre class="text-sm text-gray-800 overflow-x-auto">{{ json_encode($activity->properties->get('attributes'), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Other Properties -->
            @php
                $otherProperties = $activity->properties->except(['old', 'attributes']);
            @endphp
            @if($otherProperties->count() > 0)
                <div class="mt-6">
                    <h4 class="font-medium text-gray-700 mb-3">Additional Data</h4>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <pre class="text-sm text-gray-800 overflow-x-auto">{{ json_encode($otherProperties, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Technical Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Technical Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-gray-600">Activity ID:</span>
                <span class="font-mono text-gray-900">{{ $activity->id }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-gray-600">Created At:</span>
                <span class="font-mono text-gray-900">{{ $activity->created_at }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-gray-600">Causer Type:</span>
                <span class="font-mono text-gray-900">{{ $activity->causer_type ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-gray-600">Causer ID:</span>
                <span class="font-mono text-gray-900">{{ $activity->causer_id ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-gray-600">Subject Type:</span>
                <span class="font-mono text-gray-900">{{ $activity->subject_type ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-200">
                <span class="text-gray-600">Subject ID:</span>
                <span class="font-mono text-gray-900">{{ $activity->subject_id ?? 'N/A' }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

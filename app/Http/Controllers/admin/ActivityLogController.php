<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        // Filter by causer (user who performed the action)
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id)
                ->where('causer_type', 'App\Models\Admin');
        }

        // Filter by subject type (model being acted upon)
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by log name
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filter by description
        if ($request->filled('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search across multiple fields
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%'.$request->search.'%')
                    ->orWhere('log_name', 'like', '%'.$request->search.'%');
            });
        }

        $activities = $query->latest()
            ->paginate(50)
            ->appends($request->all());

        // Get statistics
        $stats = [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Activity::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Get unique subject types for filter
        $subjectTypes = Activity::select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type),
                ];
            });

        // Get unique log names for filter
        $logNames = Activity::select('log_name')
            ->distinct()
            ->whereNotNull('log_name')
            ->orderBy('log_name')
            ->pluck('log_name');

        return view('admin.activity-logs.index', compact(
            'activities',
            'stats',
            'subjectTypes',
            'logNames'
        ));
    }

    /**
     * Display the specified activity log
     */
    public function show(Activity $activity)
    {
        $activity->load(['causer', 'subject']);

        return view('admin.activity-logs.show', compact('activity'));
    }

    /**
     * Clear old activity logs based on configured retention period
     */
    public function clearOld()
    {
        $days = config('activitylog.delete_records_older_than_days', 365);

        $cutoffDate = now()->subDays($days);

        $deletedCount = Activity::where('created_at', '<', $cutoffDate)->delete();

        activity()
            ->causedBy(auth()->guard('admin')->user())
            ->log("Cleared {$deletedCount} old activity logs (older than {$days} days)");

        return redirect()
            ->route('admin.activity-logs.index')
            ->with('success', "Successfully deleted {$deletedCount} old activity logs.");
    }
}

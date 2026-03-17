<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePolicyRequest;
use App\Http\Requests\Admin\UpdatePolicyRequest;
use App\Models\Policy;
use App\Models\PolicyAcceptance;
use App\Models\PolicyPlatformAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalController extends Controller
{
    /**
     * GET /admin/legal
     * List all policies with optional filtering.
     */
    public function index(Request $request): View
    {
        $query = Policy::query()->withCount('acceptances');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('type')) {
            $query->where('policy_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $policies = $query->orderBy('policy_type')->orderBy('title')->paginate(20)->withQueryString();

        return view('admin.legal.index', [
            'policies' => $policies,
            'policyTypes' => Policy::POLICY_TYPES,
        ]);
    }

    /**
     * GET /admin/legal/create
     */
    public function create(): View
    {
        return view('admin.legal.create', [
            'policyTypes' => Policy::POLICY_TYPES,
            'platformActions' => Policy::PLATFORM_ACTIONS,
        ]);
    }

    /**
     * POST /admin/legal
     */
    public function store(StorePolicyRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $actions = $data['platform_actions'] ?? [];
        unset($data['platform_actions']);

        $data['slug'] = Policy::generateSlug($data['title']);
        $data['require_acceptance'] = (bool) ($data['require_acceptance'] ?? false);
        $data['show_in_footer'] = (bool) ($data['show_in_footer'] ?? false);
        $data['show_during_registration'] = (bool) ($data['show_during_registration'] ?? false);
        $data['show_during_checkout'] = (bool) ($data['show_during_checkout'] ?? false);
        $data['show_during_listing'] = (bool) ($data['show_during_listing'] ?? false);
        $data['published_at'] = $data['status'] === 'active' ? now() : null;

        $policy = Policy::create($data);

        foreach ($actions as $action) {
            PolicyPlatformAction::create(['policy_id' => $policy->id, 'action' => $action]);
        }

        activity()
            ->performedOn($policy)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Policy created: '.$policy->title);

        return redirect()->route('admin.legal.index')
            ->with('success', 'Policy "'.$policy->title.'" created successfully.');
    }

    /**
     * GET /admin/legal/{policy}
     * View policy details + acceptance log.
     */
    public function show(Policy $policy, Request $request): View
    {
        $acceptances = PolicyAcceptance::query()
            ->where('policy_id', $policy->id)
            ->with('user')
            ->when($request->filled('version'), fn ($q) => $q->where('policy_version', $request->version))
            ->orderByDesc('accepted_at')
            ->paginate(30)
            ->withQueryString();

        $versionOptions = PolicyAcceptance::where('policy_id', $policy->id)
            ->distinct()
            ->pluck('policy_version')
            ->sort()
            ->values();

        return view('admin.legal.show', [
            'policy' => $policy->load('versions', 'platformActions'),
            'acceptances' => $acceptances,
            'versionOptions' => $versionOptions,
            'platformActions' => Policy::PLATFORM_ACTIONS,
        ]);
    }

    /**
     * GET /admin/legal/{policy}/edit
     */
    public function edit(Policy $policy): View
    {
        return view('admin.legal.edit', [
            'policy' => $policy->load('platformActions'),
            'policyTypes' => Policy::POLICY_TYPES,
            'platformActions' => Policy::PLATFORM_ACTIONS,
        ]);
    }

    /**
     * PUT /admin/legal/{policy}
     */
    public function update(UpdatePolicyRequest $request, Policy $policy): RedirectResponse
    {
        $data = $request->validated();
        $actions = $data['platform_actions'] ?? [];
        $bumpVersion = (bool) ($data['bump_version'] ?? false);
        unset($data['platform_actions'], $data['bump_version']);

        $data['require_acceptance'] = (bool) ($data['require_acceptance'] ?? false);
        $data['show_in_footer'] = (bool) ($data['show_in_footer'] ?? false);
        $data['show_during_registration'] = (bool) ($data['show_during_registration'] ?? false);
        $data['show_during_checkout'] = (bool) ($data['show_during_checkout'] ?? false);
        $data['show_during_listing'] = (bool) ($data['show_during_listing'] ?? false);

        if ($bumpVersion && $data['version'] !== $policy->version) {
            // Archive the current version before saving new content
            $policy->bumpVersion($data['version']);
            unset($data['version']); // already set by bumpVersion
        }

        if ($data['status'] === 'active' && ! $policy->published_at) {
            $data['published_at'] = now();
        }

        $policy->update($data);

        // Resync platform actions
        PolicyPlatformAction::where('policy_id', $policy->id)->delete();
        foreach ($actions as $action) {
            PolicyPlatformAction::create(['policy_id' => $policy->id, 'action' => $action]);
        }

        activity()
            ->performedOn($policy)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Policy updated: '.$policy->title);

        return redirect()->route('admin.legal.edit', $policy)
            ->with('success', 'Policy updated successfully.');
    }

    /**
     * DELETE /admin/legal/{policy}
     */
    public function destroy(Policy $policy): RedirectResponse|JsonResponse
    {
        $title = $policy->title;
        $policy->delete();

        activity()
            ->causedBy(auth()->guard('admin')->user())
            ->log('Policy deleted: '.$title);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.legal.index')
            ->with('success', 'Policy "'.$title.'" deleted.');
    }

    /**
     * PATCH /admin/legal/{policy}/toggle-status
     */
    public function toggleStatus(Policy $policy): JsonResponse|RedirectResponse
    {
        $policy->status = $policy->status === 'active' ? 'inactive' : 'active';
        if ($policy->status === 'active' && ! $policy->published_at) {
            $policy->published_at = now();
        }
        $policy->save();

        activity()
            ->performedOn($policy)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Policy status toggled to '.$policy->status.': '.$policy->title);

        if (request()->expectsJson()) {
            return response()->json(['status' => $policy->status]);
        }

        return back()->with('success', 'Policy status updated.');
    }
}

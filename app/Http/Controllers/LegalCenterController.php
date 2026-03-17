<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\View\View;

class LegalCenterController extends Controller
{
    /**
     * GET /legal
     * Public legal center — lists all footer-visible active policies grouped by type.
     */
    public function index(): View
    {
        $footerPolicies = Policy::showInFooter()
            ->orderBy('policy_type')
            ->orderBy('title')
            ->get();

        $grouped = $footerPolicies->groupBy('policy_type');

        return view('legal.index', [
            'grouped' => $grouped,
            'policyTypes' => Policy::POLICY_TYPES,
        ]);
    }

    /**
     * GET /legal/{policy:slug}
     * Public display of a single active policy with its version history.
     */
    public function show(Policy $policy): View
    {
        abort_unless($policy->isActive(), 404);

        $versions = $policy->versions()->orderByDesc('created_at')->get();

        return view('legal.show', [
            'policy' => $policy,
            'versions' => $versions,
        ]);
    }
}

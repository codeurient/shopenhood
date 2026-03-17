<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyPlatformAction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'policy_id',
        'action',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────────

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }
}

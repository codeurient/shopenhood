<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyVersion extends Model
{
    /**
     * Version history records only have created_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'policy_id',
        'version',
        'content',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────────

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }
}

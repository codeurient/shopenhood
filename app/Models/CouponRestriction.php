<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CouponRestriction extends Model
{
    use HasFactory;

    protected $table = 'coupon_restrictions';

    public $timestamps = false;

    protected $fillable = [
        'coupon_id',
        'restrictable_type',
        'restrictable_id',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function restrictable(): MorphTo
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryVariant extends Model
{
    use HasFactory;

    protected $table = 'category_variants';

    public $incrementing = true;

    // Parent category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Parent variant
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}

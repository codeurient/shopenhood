<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingType extends Model
{
    use HasFactory;

    // Listings of this type (sell, buy, gift, barter, auction)
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}

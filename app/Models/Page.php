<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'content',
        'meta_description',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public const PAGES = [
        'about' => 'About Us',
        'privacy-policy' => 'Privacy Policy',
        'payment-services' => 'Payment Services',
        'advertising-services' => 'Advertising Services',
        'help' => 'Help',
        'contact' => 'Contact',
    ];

    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }
}

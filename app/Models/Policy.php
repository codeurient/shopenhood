<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'policy_type',
        'version',
        'status',
        'require_acceptance',
        'show_in_footer',
        'show_during_registration',
        'show_during_checkout',
        'show_during_listing',
        'published_at',
    ];

    protected $casts = [
        'require_acceptance' => 'boolean',
        'show_in_footer' => 'boolean',
        'show_during_registration' => 'boolean',
        'show_during_checkout' => 'boolean',
        'show_during_listing' => 'boolean',
        'published_at' => 'datetime',
    ];

    // ── Policy Types ─────────────────────────────────────────────────────────────

    public const POLICY_TYPES = [
        'core' => 'Core Platform',
        'marketplace_vendor' => 'Marketplace & Vendor',
        'transaction' => 'Transaction',
        'trading' => 'Trading Models',
        'shipping' => 'Shipping & Logistics',
        'product_content' => 'Product & Content Rules',
        'safety_compliance' => 'Safety & Compliance',
        'dispute_legal' => 'Dispute & Legal',
    ];

    // ── Platform Actions ──────────────────────────────────────────────────────────

    public const PLATFORM_ACTIONS = [
        'registration' => 'User Registration',
        'login' => 'User Login',
        'product_listing' => 'Product Listing',
        'purchase' => 'Product Purchase',
        'checkout' => 'Checkout',
        'auction_creation' => 'Auction Creation',
        'auction_bidding' => 'Auction Bidding',
        'barter_request' => 'Barter Request',
        'gift_transfer' => 'Gift Transfer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────────

    public function acceptances(): HasMany
    {
        return $this->hasMany(PolicyAcceptance::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PolicyVersion::class)->orderByDesc('created_at');
    }

    public function platformActions(): HasMany
    {
        return $this->hasMany(PolicyPlatformAction::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeShowInFooter($query)
    {
        return $query->where('show_in_footer', true)->where('status', 'active');
    }

    public function scopeForAction($query, string $action)
    {
        return $query->whereHas('platformActions', fn ($q) => $q->where('action', $action))
            ->where('status', 'active');
    }

    public function scopeForRegistration($query)
    {
        return $query->where('show_during_registration', true)->where('status', 'active');
    }

    public function scopeForCheckout($query)
    {
        return $query->where('show_during_checkout', true)->where('status', 'active');
    }

    public function scopeForListing($query)
    {
        return $query->where('show_during_listing', true)->where('status', 'active');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function policyTypeLabel(): string
    {
        return self::POLICY_TYPES[$this->policy_type] ?? ucfirst(str_replace('_', ' ', $this->policy_type));
    }

    /**
     * Check whether a given user has accepted the CURRENT version of this policy.
     */
    public function hasBeenAcceptedBy(User $user): bool
    {
        return $this->acceptances()
            ->where('user_id', $user->id)
            ->where('policy_version', $this->version)
            ->exists();
    }

    /**
     * Record a user's acceptance and return the acceptance log entry.
     */
    public function recordAcceptance(User $user, string $context = '', ?string $ipAddress = null, ?string $userAgent = null): PolicyAcceptance
    {
        return PolicyAcceptance::create([
            'policy_id' => $this->id,
            'user_id' => $user->id,
            'policy_version' => $this->version,
            'context' => $context ?: null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'accepted_at' => now(),
        ]);
    }

    /**
     * Bump the version and snapshot the current content into policy_versions history.
     * All existing acceptances are effectively invalidated (new version requires fresh acceptance).
     */
    public function bumpVersion(string $newVersion): void
    {
        PolicyVersion::create([
            'policy_id' => $this->id,
            'version' => $this->version,
            'content' => $this->content,
        ]);

        $this->version = $newVersion;
        $this->save();
    }

    /**
     * Generate a unique slug from a title, optionally excluding a record by ID.
     */
    public static function generateSlug(string $title, ?int $exceptId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (true) {
            $query = static::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
            if (! $query->exists()) {
                break;
            }
            $slug = $original.'-'.$count++;
        }

        return $slug;
    }
}

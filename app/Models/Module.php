<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'color', 'description', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'module_role');
    }

    // ── Access checks ─────────────────────────────────────────────────────

    /**
     * Can the given user see this module?
     * Admin always yes. If no roles assigned → all users. Otherwise role must match.
     */
    public function isAccessibleBy(User $user): bool
    {
        if ($user->isAdmin()) return true;
        if ($this->roles->isEmpty()) return true;
        return $this->roles->contains('id', $user->role_id);
    }

    /**
     * Return only the menu items this user can access.
     */
    public function accessibleItemsFor(User $user)
    {
        return $this->menuItems
            ->where('is_active', true)
            ->filter(fn(MenuItem $item) => $item->isAccessibleBy($user));
    }

    public function activeMenuItems()
    {
        return $this->hasMany(MenuItem::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

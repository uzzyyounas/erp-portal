<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MenuItem extends Model
{
    protected $fillable = [
        'module_id', 'name', 'icon', 'type', 'route_name','slug',
        'route_params', 'description', 'open_in_new_tab', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'route_params'    => 'array',
        'open_in_new_tab' => 'boolean',
        'is_active'       => 'boolean',
        'sort_order'      => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'menu_item_role');
    }

    // ── Access checks ─────────────────────────────────────────────────────

    /**
     * Can the given user see this item?
     * Admin always yes. If no item-level roles → visible to all who can see the module.
     * If item has roles → user's role must be in that list.
     */
    public function isAccessibleBy(User $user): bool
    {
        if ($user->isAdmin()) return true;
        if ($this->roles->isEmpty()) return true;
        return $this->roles->contains('id', $user->role_id);
    }

    // ── URL resolution ────────────────────────────────────────────────────

    /**
     * Resolve the full URL for this menu item.
     * Returns '#' if route_name is blank or the route doesn't exist.
     */
    public function getUrlAttribute(): string
    {
        if (empty($this->route_name)) return '#';

        try {
            return route($this->route_name, $this->route_params ?? []);
        } catch (\Exception) {
            return '#';
        }
    }

    /**
     * Check if this item is currently active (matches the current route).
     */
    public function isCurrentRoute(): bool
    {
        if (empty($this->route_name)) return false;
        try {
            return request()->routeIs($this->route_name);
        } catch (\Exception) {
            return false;
        }
    }

    // ── Type helpers ──────────────────────────────────────────────────────

    public function isReport(): bool   { return $this->type === 'report'; }
    public function isForm(): bool     { return $this->type === 'form'; }
    public function isLink(): bool     { return $this->type === 'link'; }
    public function isDivider(): bool  { return $this->type === 'divider'; }

    public function typeBadgeClass(): string
    {
        return match($this->type) {
            'report'  => 'bg-primary',
            'form'    => 'bg-success',
            'link'    => 'bg-info text-dark',
            'divider' => 'bg-secondary',
            default   => 'bg-secondary',
        };
    }

    public function isActiveRoute(): bool
    {
        if (!$this->route_name) {
            return false;
        }

        return request()->routeIs($this->route_name);
    }


    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = \Str::slug($item->name);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role_id', 'name', 'email', 'username', 'password',
        'is_active', 'phone', 'avatar', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active'          => 'boolean',
        'email_verified_at'  => 'datetime',
        'last_login_at'      => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // ── Permission helpers ────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function canAccessModule(Module $module): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $allowedRoles = $module->roles;

        return $allowedRoles->isEmpty()
            || $allowedRoles->contains('id', $this->role_id);
    }

    /**
     * Can this user see the given menu item?
     * Requires BOTH module-level AND item-level access.
     * Empty pivot = unrestricted at that level.
     */
    public function canAccessMenuItem(MenuItem $item): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $moduleRoles = $item->module->roles;
        $itemRoles   = $item->roles;

        $hasModuleAccess = $moduleRoles->isEmpty()
            || $moduleRoles->contains('id', $this->role_id);

        $hasItemAccess = $itemRoles->isEmpty()
            || $itemRoles->contains('id', $this->role_id);

        return $hasModuleAccess && $hasItemAccess;
    }

    /**
     * Return all active modules (with their accessible items) for use in
     * the sidebar. Empty modules (no accessible items) are filtered out.
     */
    public function accessibleModulesWithItems(): Collection
    {
        if ($this->isAdmin()) {
            return Module::where('is_active', true)
                ->with(['activeMenuItems.roles'])
                ->orderBy('sort_order')
                ->get();
        }

        $roleId = $this->role_id;

        return Module::where('is_active', true)
            ->where(function ($q) use ($roleId) {
                // No role restriction OR role is in the allowed list
                $q->whereDoesntHave('roles')
                    ->orWhereHas('roles', fn ($r) => $r->where('roles.id', $roleId));
            })
            ->with(['activeMenuItems' => function ($q) use ($roleId) {
                $q->where(function ($q2) use ($roleId) {
                    $q2->whereDoesntHave('roles')
                        ->orWhereHas('roles', fn ($r) => $r->where('roles.id', $roleId));
                });
            }])
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($m) => $m->activeMenuItems->isNotEmpty())
            ->values();
    }
}

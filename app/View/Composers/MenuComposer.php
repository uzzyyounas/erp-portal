<?php

namespace App\View\Composers;

use App\Models\Module;
use Illuminate\View\View;

class MenuComposer
{
    public function compose(View $view): void
    {
        if (! auth()->check()) {
            $view->with('sidebarModules', collect());
            return;
        }

        $user = auth()->user();

        $modules = Module::active()
            ->with(['roles', 'menuItems' => fn($q) => $q->active()->with('roles')->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get()
            ->filter(function (Module $module) use ($user) {
                // Must pass module-level access check
                if (! $module->isAccessibleBy($user)) return false;

                // Filter items to only accessible ones
                $module->setRelation(
                    'menuItems',
                    $module->menuItems->filter(fn($item) => $item->isAccessibleBy($user))
                );

                // Only show modules that have at least one accessible item
                return $module->menuItems->isNotEmpty();
            });

        $view->with('sidebarModules', $modules);
    }
}

<?php

namespace App\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view): void
    {
        if (!Auth::check()) {
            $view->with('sidebarModules', collect());
            return;
        }

        // Cache per-request so multiple layout includes don't re-query
        static $cache = null;
        if ($cache === null) {
            $cache = Auth::user()->accessibleModulesWithItems();
        }

        $view->with('sidebarModules', $cache);
    }
}

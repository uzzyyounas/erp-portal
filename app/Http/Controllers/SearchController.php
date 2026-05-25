<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * GET /search?q=...
     * Returns accessible menu items matching the query for the authenticated user.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $q    = trim($request->get('q', ''));
        $user = Auth::user();

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        // Load the user's accessible modules with their active menu items
        $modules = $user->accessibleModulesWithItems();

        $results = $modules
            ->flatMap(fn ($module) =>
            $module->activeMenuItems
                ->filter(fn ($item) =>
                    $item->type !== 'divider'
                    && (
                        str_contains(mb_strtolower($item->name),   mb_strtolower($q))
                        || str_contains(mb_strtolower($module->name), mb_strtolower($q))
                        || str_contains(mb_strtolower($item->type),  mb_strtolower($q))
                    )
                )
                ->map(fn ($item) => [
                    'name'   => $item->name,
                    'url'    => $item->url,
                    'type'   => $item->type,
                    'icon'   => $item->icon ?: 'bi-file-text',
                    'module' => $module->name,
                    'color'  => $module->color,
                ])
            )
            ->values();

        return response()->json($results);
    }
}

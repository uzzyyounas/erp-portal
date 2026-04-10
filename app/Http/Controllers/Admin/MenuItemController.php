<?php

namespace App\Http\Controllers\Admin;

use App\Models\MenuItem;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    public function index()
    {
        $modules = Module::with(['menuItems.roles'])
            ->orderBy('sort_order')
            ->get();

        $roles = Role::where('is_active', true)->get();

        return view('admin.menu-items.index', compact('modules', 'roles'));
    }

    public function create()
    {
        $modules  = Module::where('is_active', true)->orderBy('sort_order')->get();
        $roles    = Role::where('is_active', true)->get();
        $menuItem = null;

        return view('admin.menu-items.form', compact('modules', 'roles', 'menuItem'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module_id'   => 'required|exists:modules,id',
            'name'        => 'required|string|max:100',
            'icon'        => 'nullable|string|max:80',
            'type'        => 'required|in:report,form,link,divider',
            'route_name'  => 'nullable|string|max:150',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'integer|min:0',
        ]);

        $data['slug']        = Str::slug($data['name']);
        $data['is_active']   = true;
        $data['show_in_menu'] = $request->boolean('show_in_menu', true);

        $item = MenuItem::create($data);
        $item->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu item "' . $item->name . '" created.');
    }

    public function edit(MenuItem $menuItem)
    {
        $modules = Module::where('is_active', true)->orderBy('sort_order')->get();
        $roles   = Role::where('is_active', true)->get();

        return view('admin.menu-items.form', compact('menuItem', 'modules', 'roles'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'module_id'   => 'required|exists:modules,id',
            'name'        => ['required', 'string', 'max:100',
                Rule::unique('menu_items', 'name')->ignore($menuItem)],
            'icon'        => 'nullable|string|max:80',
            'type'        => 'required|in:report,form,link,divider',
            'route_name'  => 'nullable|string|max:150',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'integer|min:0',
        ]);

        $data['slug']        = Str::slug($data['name']);
        $data['is_active']   = $request->boolean('is_active', true);
        $data['show_in_menu'] = $request->boolean('show_in_menu', true);

        $menuItem->update($data);
        $menuItem->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return back()->with('success', 'Menu item deleted.');
    }

    /**
     * AJAX: validate a route name — returns whether it resolves.
     */
    public function validateRoute(Request $request)
    {
        $routeName = $request->input('route_name');

        if (!$routeName) {
            return response()->json(['valid' => false, 'message' => 'No route name provided.']);
        }

        try {
            $url = route($routeName);
            return response()->json(['valid' => true, 'url' => $url]);
        } catch (\Exception $e) {
            return response()->json([
                'valid'   => false,
                'message' => "Route \"{$routeName}\" does not exist.",
            ]);
        }
    }
}

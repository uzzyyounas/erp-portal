<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::withCount('menuItems')
            ->with(['roles', 'menuItems.roles'])
            ->orderBy('sort_order')
            ->get();

        $roles = Role::where('is_active', true)->get();

        return view('admin.modules.index', compact('modules', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:80|unique:modules,name',
            'icon'        => 'required|string|max:80',
            'color'       => 'required|string|max:20',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'integer|min:0',
        ]);

        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = true;

        $module = Module::create($data);

        // Assign roles if provided (empty = accessible by all)
        $module->roles()->sync($request->input('roles', []));

        return back()->with('success', "Module \"{$module->name}\" created.");
    }

    public function update(Request $request, Module $module)
    {
        $data = $request->validate([
            'name'        => "required|string|max:80|unique:modules,name,{$module->id}",
            'icon'        => 'required|string|max:80',
            'color'       => 'required|string|max:20',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');

        $module->update($data);
        $module->roles()->sync($request->input('roles', []));

        return back()->with('success', "Module \"{$module->name}\" updated.");
    }

    public function destroy(Module $module)
    {
        if ($module->menuItems()->count() > 0) {
            return back()->withErrors(['error' => 'Remove all menu items from this module before deleting it.']);
        }

        $module->delete();

        return back()->with('success', 'Module deleted.');
    }
}

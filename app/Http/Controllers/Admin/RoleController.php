<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:60|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ]);

        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = true;

        Role::create($data);

        return back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:60', Rule::unique('roles', 'name')->ignore($role)],
            'description' => 'nullable|string|max:255',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $role->update($data);

        return back()->with('success', 'Role updated.');
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $reportIds = $request->input('reports', []);
        $role->reports()->sync($reportIds);

        return back()->with('success', 'Permissions updated successfully.');
    }
}

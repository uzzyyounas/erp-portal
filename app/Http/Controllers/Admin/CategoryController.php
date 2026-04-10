<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReportCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ReportCategory::withCount('reports')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:80|unique:report_categories,name',
            'icon'        => 'required|string|max:80',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'integer|min:0',
        ]);

        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = true;

        ReportCategory::create($data);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, ReportCategory $category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:80', Rule::unique('report_categories', 'name')->ignore($category)],
            'icon'        => 'required|string|max:80',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['slug']      = Str::slug($data['name']);

        $category->update($data);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(ReportCategory $category)
    {
        if ($category->reports()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete a category that has reports assigned to it.']);
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}

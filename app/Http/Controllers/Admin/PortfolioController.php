<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::with('category')->orderBy('order')->paginate(20);
        return view('admin.portfolios.index', compact('portfolios'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.portfolios.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'client' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'completion_date' => 'nullable|date',
            'techniques_used' => 'nullable|array',
            'thumbnail' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'order' => 'nullable|integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('portfolio', 'public');
        }

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('portfolio', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        Portfolio::create($validated);

        return redirect()->route('admin.portfolios.index')
            ->with('success', 'Portfolio created successfully.');
    }

    public function edit(Portfolio $portfolio)
    {
        $categories = Category::active()->get();
        return view('admin.portfolios.edit', compact('portfolio', 'categories'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'client' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'completion_date' => 'nullable|date',
            'techniques_used' => 'nullable|array',
            'thumbnail' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'order' => 'nullable|integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('thumbnail')) {
            if ($portfolio->thumbnail) {
                \Storage::disk('public')->delete($portfolio->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('portfolio', 'public');
        }

        if ($request->hasFile('images')) {
            if ($portfolio->images) {
                foreach ($portfolio->images as $image) {
                    \Storage::disk('public')->delete($image);
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('portfolio', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $portfolio->update($validated);

        return redirect()->route('admin.portfolios.index')
            ->with('success', 'Portfolio updated successfully.');
    }

    public function destroy(Portfolio $portfolio)
    {
        if ($portfolio->thumbnail) {
            \Storage::disk('public')->delete($portfolio->thumbnail);
        }
        if ($portfolio->images) {
            foreach ($portfolio->images as $image) {
                \Storage::disk('public')->delete($image);
            }
        }

        $portfolio->delete();

        return redirect()->route('admin.portfolios.index')
            ->with('success', 'Portfolio deleted successfully.');
    }
}

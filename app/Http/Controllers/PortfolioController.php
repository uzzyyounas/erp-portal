<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Category;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * Display a listing of portfolio items.
     */
    public function index(Request $request)
    {
        $categories = Category::active()->withCount('portfolios')->get();

        $query = Portfolio::active()->with('category');

        if ($request->has('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        $portfolios = $query->ordered()->paginate(12);
        $selectedCategory = $request->category ?? 'all';

        return view('portfolio.index', compact('portfolios', 'categories', 'selectedCategory'));
    }

    /**
     * Display the specified portfolio item.
     */
    public function show($slug)
    {
        $portfolio = Portfolio::where('slug', $slug)
            ->where('is_active', true)
            ->with('category')
            ->firstOrFail();

        $relatedProjects = Portfolio::active()
            ->where('category_id', $portfolio->category_id)
            ->where('id', '!=', $portfolio->id)
            ->ordered()
            ->take(3)
            ->get();

        return view('portfolio.show', compact('portfolio', 'relatedProjects'));
    }
}

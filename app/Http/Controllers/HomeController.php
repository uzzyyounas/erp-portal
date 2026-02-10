<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Testimonial;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        $featuredServices = Service::active()->featured()->ordered()->take(4)->get();
        $featuredPortfolios = Portfolio::active()->featured()->with('category')->ordered()->take(6)->get();
        $testimonials = Testimonial::active()->featured()->ordered()->take(3)->get();
        $latestPosts = BlogPost::published()->latest()->take(3)->get();

        return view('home', compact('featuredServices', 'featuredPortfolios', 'testimonials', 'latestPosts'));
    }
}

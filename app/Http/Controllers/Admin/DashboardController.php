<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Testimonial;
use App\Models\BlogPost;
use App\Models\Contact;
use App\Models\Category;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'services' => Service::count(),
            'portfolios' => Portfolio::count(),
            'testimonials' => Testimonial::count(),
            'blog_posts' => BlogPost::count(),
            'categories' => Category::count(),
            'contacts' => Contact::count(),
            'unread_contacts' => Contact::unread()->count(),
            'published_posts' => BlogPost::published()->count(),
        ];

        $recentContacts = Contact::latest()->take(5)->get();
        $recentPosts = BlogPost::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentContacts', 'recentPosts'));
    }
}

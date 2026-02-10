<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Display the about page.
     */
    public function index()
    {
        $skills = [
            ['name' => 'MIG Welding', 'percentage' => 95],
            ['name' => 'TIG Welding', 'percentage' => 90],
            ['name' => 'Stick Welding', 'percentage' => 88],
            ['name' => 'Arc Welding', 'percentage' => 92],
            ['name' => 'Metal Fabrication', 'percentage' => 85],
            ['name' => 'Aluminum Welding', 'percentage' => 87],
        ];

        $certifications = [
            'ISO & PSQCA Certified Welder',
            'Structural Welding Certification',
            'Pipe Welding Certification',
        ];

        return view('about', compact('skills', 'certifications'));
    }
}

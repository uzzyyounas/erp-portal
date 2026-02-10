<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Testimonial;
use App\Models\BlogPost;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $categories = [
            ['name' => 'Structural Welding', 'slug' => 'structural-welding', 'description' => 'Heavy-duty structural welding projects'],
            ['name' => 'Metal Fabrication', 'slug' => 'metal-fabrication', 'description' => 'Custom metal fabrication work'],
            ['name' => 'Artistic Welding', 'slug' => 'artistic-welding', 'description' => 'Creative and artistic metal work'],
            ['name' => 'Repairs & Maintenance', 'slug' => 'repairs-maintenance', 'description' => 'Welding repairs and maintenance'],
            ['name' => 'Industrial', 'slug' => 'industrial', 'description' => 'Industrial welding projects'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Services
        $services = [
            [
                'title' => 'MIG Welding',
                'slug' => 'mig-welding',
                'description' => 'Metal Inert Gas welding for clean, precise joins on various metals.',
                'full_description' => 'Our MIG welding services provide fast, clean, and efficient welding solutions perfect for both thick and thin materials. Ideal for automotive, manufacturing, and construction applications.',
                'icon' => 'fas fa-fire',
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'title' => 'TIG Welding',
                'slug' => 'tig-welding',
                'description' => 'Tungsten Inert Gas welding for high-precision, clean welds.',
                'full_description' => 'TIG welding offers superior quality and precision, perfect for critical applications requiring the highest quality welds on stainless steel, aluminum, and exotic metals.',
                'icon' => 'fas fa-bolt',
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'title' => 'Stick Welding',
                'slug' => 'stick-welding',
                'description' => 'Versatile shielded metal arc welding for outdoor and heavy-duty applications.',
                'full_description' => 'Also known as SMAW, stick welding is perfect for outdoor work, repair jobs, and heavy fabrication. Works well in windy conditions and on rusty or painted materials.',
                'icon' => 'fas fa-burn',
                'is_featured' => true,
                'order' => 3,
            ],
            [
                'title' => 'Metal Fabrication',
                'slug' => 'metal-fabrication',
                'description' => 'Custom metal fabrication from design to finished product.',
                'full_description' => 'Complete fabrication services including cutting, bending, forming, and assembling metal structures to your specifications. From simple brackets to complex assemblies.',
                'icon' => 'fas fa-tools',
                'is_featured' => true,
                'order' => 4,
            ],
            [
                'title' => 'Structural Welding',
                'slug' => 'structural-welding',
                'description' => 'Certified structural welding for buildings and infrastructure.',
                'full_description' => 'Certified structural welding services for commercial buildings, bridges, and infrastructure projects. All work meets or exceeds AWS D1.1 structural welding code requirements.',
                'icon' => 'fas fa-building',
                'is_featured' => false,
                'order' => 5,
            ],
            [
                'title' => 'Maintenance & Repairs',
                'slug' => 'maintenance-repairs',
                'description' => 'Expert welding repairs and preventive maintenance services.',
                'full_description' => 'Professional repair and maintenance services for industrial equipment, machinery, and metal structures. Quick turnaround to minimize downtime.',
                'icon' => 'fas fa-wrench',
                'is_featured' => false,
                'order' => 6,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Portfolio Items
        $portfolios = [
            [
                'title' => 'Industrial Steel Framework',
                'slug' => 'industrial-steel-framework',
                'description' => 'Complete steel framework for manufacturing facility expansion. Over 5000 sq ft of structural steel work.',
                'client' => 'ABC Manufacturing',
                'location' => 'Industrial District, City',
                'completion_date' => '2024-01-15',
                'techniques_used' => ['MIG Welding', 'Structural Welding', 'Certified Inspection'],
                'category_id' => 1,
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'title' => 'Custom Staircase Railing',
                'slug' => 'custom-staircase-railing',
                'description' => 'Ornamental stainless steel railing with custom design elements. Modern aesthetic with clean lines.',
                'client' => 'Residential Client',
                'location' => 'Downtown Area',
                'completion_date' => '2023-12-10',
                'techniques_used' => ['TIG Welding', 'Metal Fabrication', 'Polishing'],
                'category_id' => 3,
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'title' => 'Warehouse Gate System',
                'slug' => 'warehouse-gate-system',
                'description' => 'Heavy-duty sliding gate system with automated mechanism. Designed for high-traffic industrial use.',
                'client' => 'XYZ Logistics',
                'location' => 'Warehouse Complex',
                'completion_date' => '2024-02-01',
                'techniques_used' => ['Stick Welding', 'Metal Fabrication', 'Installation'],
                'category_id' => 2,
                'is_featured' => true,
                'order' => 3,
            ],
        ];

        foreach ($portfolios as $portfolio) {
            Portfolio::create($portfolio);
        }

        // Testimonials
        $testimonials = [
            [
                'client_name' => 'Ahmed Raza',
                'client_position' => 'Operations Manager',
                'client_company' => 'DHA',
                'testimonial' => 'Excellent welding work on our warehouse structure in Lahore. The team was professional, punctual, and delivered strong, clean finishing. Highly satisfied with the quality and service.',
                'rating' => 5,
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'client_name' => 'Fatima Khan',
                'client_position' => 'Homeowner',
                'client_company' => 'G-9, Islamabad',
                'testimonial' => 'We hired them for custom iron gates and railings for our house in DHA Karachi. The craftsmanship is outstanding and the installation was neat and timely. Highly recommended!',
                'rating' => 5,
                'is_featured' => true,
                'order' => 2,
            ],
            [
                'client_name' => 'Muhammad Usman',
                'client_position' => 'Factory Supervisor',
                'client_company' => 'Faisal Steel Works',
                'testimonial' => 'Very reliable and skilled welders. Our factory shed and safety grills were completed on time with excellent finishing. Great communication throughout the project.',
                'rating' => 5,
                'is_featured' => true,
                'order' => 3,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }

        // Blog Posts
        $posts = [
            [
                'title' => 'The Importance of Certified Welders',
                'slug' => 'importance-of-certified-welders',
                'excerpt' => 'Understanding why certification matters in welding and fabrication work.',
                'content' => '<p>Welding certifications are crucial for ensuring quality, safety, and compliance in construction and manufacturing projects...</p><p>Professional certifications like AWS and ASME demonstrate a welder\'s competency and commitment to industry standards.</p>',
                'category_id' => 1,
                'author' => 'Admin',
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'MIG vs TIG Welding: Which is Right for Your Project?',
                'slug' => 'mig-vs-tig-welding',
                'excerpt' => 'A comprehensive comparison of MIG and TIG welding techniques.',
                'content' => '<p>Choosing between MIG and TIG welding depends on several factors including material type, thickness, and desired finish quality...</p><p>MIG welding is faster and more forgiving, while TIG offers superior precision and cleaner welds.</p>',
                'category_id' => 1,
                'author' => 'Admin',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create($post);
        }
    }
}

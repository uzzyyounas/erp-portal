<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Professional Welding & Fabrication Services')">
    <meta name="keywords" content="@yield('meta_keywords', 'welding, fabrication, MIG, TIG, structural welding')">
    <title>@yield('title', 'Welding Portfolio') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Saira+Condensed:wght@700;900&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Lightbox -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #ff6b18;
            --secondary-color: #1a1a1a;
            --accent-color: #ffd700;
            --text-dark: #2c2c2c;
            --text-light: #f5f5f5;
            --bg-dark: #0a0a0a;
            --bg-light: #f8f9fa;
            --steel-gray: #4a5568;
            --sparks-orange: #ff8c42;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Barlow', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
            background: var(--bg-light);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Saira Condensed', sans-serif;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--secondary-color) 100%);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-family: 'Saira Condensed', sans-serif;
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--primary-color) !important;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
        }

        .navbar-brand::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent-color);
        }

        .nav-link {
            color: var(--text-light) !important;
            font-weight: 600;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            position: relative;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            width: 80%;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                repeating-linear-gradient(
                    90deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 107, 24, 0.03) 2px,
                    rgba(255, 107, 24, 0.03) 4px
                );
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 1rem;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.8);
            line-height: 1.1;
        }

        .hero-title span {
            color: var(--primary-color);
            display: block;
        }

        .hero-tagline {
            font-size: 1.3rem;
            color: var(--accent-color);
            margin-bottom: 2rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--sparks-orange) 100%);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 24, 0.4);
        }

        .btn-primary-custom::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary-custom:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(255, 107, 24, 0.6);
        }

        .btn-outline-custom {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
            padding: 1rem 2.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        /* Section Titles */
        .section-title {
            position: relative;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--sparks-orange));
        }

        .section-subtitle {
            color: var(--steel-gray);
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }

        /* Cards */
        .service-card,
        .portfolio-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            height: 100%;
        }

        .service-card:hover,
        .portfolio-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 40px rgba(255, 107, 24, 0.3);
        }

        .service-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .service-img{
            width: 100%;
            height: 220px;          /* fixed same height */
            object-fit: cover;      /* crop properly */
            border-radius: 8px;
            /*width: 75px;*/
            /*object-fit: contain;*/
            /*height: 75px;*/
        }

        .service-img-home{
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 50%;
            align-self: center;
        }

        /* Portfolio Grid */
        .portfolio-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            cursor: pointer;
        }

        .portfolio-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .portfolio-item:hover img {
            transform: scale(1.1);
        }

        .portfolio-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 107, 24, 0.9), rgba(26, 26, 26, 0.9));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .portfolio-item:hover .portfolio-overlay {
            opacity: 1;
        }

        /* Testimonials */
        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            height: 100%;
        }

        .testimonial-rating {
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--secondary-color) 100%);
            color: var(--text-light);
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }

        .footer a {
            color: var(--text-light);
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: var(--primary-color);
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 107, 24, 0.2);
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-5px);
        }

        /* WhatsApp Float Button */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #25D366;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
            z-index: 999;
            transition: all 0.3s ease;
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(37, 211, 102, 0.6);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .navbar-collapse {
                background: var(--secondary-color);
                padding: 1rem;
                border-radius: 8px;
                margin-top: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    @include('partials.navbar')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', env('WHATSAPP_NUMBER')) }}"
       class="whatsapp-float"
       target="_blank"
       title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Active nav link
        const currentLocation = location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentLocation) {
                link.classList.add('active');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

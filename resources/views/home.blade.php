@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content" data-aos="fade-right">
                <h1 class="hero-title">
                    Certified Welding
                    <span>&</span>
                    <span>Fabrication Expert</span>
                </h1>
                <p class="hero-tagline">
                    <i class="fas fa-fire me-2"></i>
                    Professional Quality | Precision Craftsmanship
                </p>
                <div class="mt-4">
                    <a href="{{ route('portfolio.index') }}" class="btn btn-primary-custom me-3 d-none d-md-inline-block">
                        View Portfolio
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-custom">
                        Get a Quote
                    </a>
                </div>
                <div class="mt-5">
                    <div class="row">
                        <div class="col-4">
                            <div class="text-center">
                                <h3 class="text-warning mb-0">100+</h3>
                                <small class="text-light">Projects Done</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <h3 class="text-warning mb-0">5+</h3>
                                <small class="text-light">Years Experience</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <h3 class="text-warning mb-0">100%</h3>
                                <small class="text-light">Satisfaction</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="position-relative">
                    <img src="{{asset('images/photo-1504917595217-d4dc5ebe6122.jpg')}}"
                         alt="Welding Work"
                         class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title d-inline-block">Why Choose Us</h2>
            <p class="section-subtitle mt-3">Professional Excellence in Every Project</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center p-4">
                    <div class="service-icon">
                        <i class="fas fa-certificate"></i>
{{--                        <img src="{{asset('images/icons/certification.png')}}" alt="" class="service-img">--}}
                    </div>
                    <h5 class="mt-3">Certified Professional</h5>
                    <p class="text-muted">ISO & PSQCA certified with extensive training and expertise</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center p-4">
                    <div class="service-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h5 class="mt-3">Custom Fabrication</h5>
                    <p class="text-muted">Tailored solutions for unique project requirements</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center p-4">
                    <div class="service-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="mt-3">Quality Assurance</h5>
                    <p class="text-muted">Rigorous testing and inspection for all projects</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center p-4">
                    <div class="service-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h5 class="mt-3">On-Site Services</h5>
                    <p class="text-muted">Mobile welding services available at your location</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Services -->
@if($featuredServices->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title d-inline-block">Our Services</h2>
            <p class="section-subtitle mt-3">Comprehensive Welding Solutions</p>
        </div>
        <div class="row g-4">
            @foreach($featuredServices as $index => $service)
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="service-card p-4">
                    <div class="service-icon text-center">
                        <i class="{{ $service->icon ?? 'fas fa-fire' }}"></i>
                    </div>
                    <h5 class="text-center mt-3">{{ $service->title }}</h5>
                    <p class="text-muted text-center">{{ Str::limit($service->description, 100) }}</p>
                    <div class="text-center">
                        <a href="{{ route('services.show', $service->slug) }}" class="btn btn-sm btn-outline-primary">
                            Learn More <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('services.index') }}" class="btn btn-primary-custom">
                View All Services
            </a>
        </div>
    </div>
</section>
@endif

<!-- Featured Portfolio -->
@if($featuredPortfolios->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title d-inline-block">Featured Works</h2>
            <p class="section-subtitle mt-3">Our Latest Projects & Achievements</p>
        </div>
        <div class="row g-4">
            @foreach($featuredPortfolios as $index => $portfolio)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="portfolio-item">
                    <img src="{{ $portfolio->thumbnail ?? 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?w=800' }}"
                         alt="{{ $portfolio->title }}">
                    <div class="portfolio-overlay">
                        <h5 class="text-white mb-2">{{ $portfolio->title }}</h5>
                        <p class="text-white mb-3">
                            <i class="fas fa-tag me-2"></i>{{ $portfolio->category->name ?? 'Welding' }}
                        </p>
                        <a href="{{ route('portfolio.show', $portfolio->slug) }}" class="btn btn-light btn-sm">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('portfolio.index') }}" class="btn btn-primary-custom">
                View Full Portfolio
            </a>
        </div>
    </div>
</section>
@endif

<!-- Testimonials -->
@if($testimonials->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title d-inline-block">Client Testimonials</h2>
            <p class="section-subtitle mt-3">What Our Clients Say About Us</p>
        </div>
        <div class="row g-4">
            @foreach($testimonials as $index => $testimonial)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="testimonial-card">
                    <div class="testimonial-rating mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $testimonial->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="mb-3">"{{ $testimonial->testimonial }}"</p>
                    <div class="d-flex align-items-center">
                        @if($testimonial->client_photo)
                        <img src="{{ $testimonial->client_photo }}"
                             alt="{{ $testimonial->client_name }}"
                             class="rounded-circle me-3"
                             width="50"
                             height="50">
                        @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                             style="width: 50px; height: 50px;">
                            <strong>{{ substr($testimonial->client_name, 0, 1) }}</strong>
                        </div>
                        @endif
                        <div>
                            <strong class="d-block">{{ $testimonial->client_name }}</strong>
                            @if($testimonial->client_position || $testimonial->client_company)
                            <small class="text-muted">
                                {{ $testimonial->client_position }}
                                @if($testimonial->client_company)
                                    , {{ $testimonial->client_company }}
                                @endif
                            </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="mb-3">Ready to Start Your Project?</h2>
                <p class="lead mb-0">Get a free quote today and let's bring your vision to life with professional welding services.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0" data-aos="fade-left">
                <a href="{{ route('contact') }}" class="btn btn-primary-custom btn-lg">
                    Request a Quote
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

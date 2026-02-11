@extends('layouts.app')

@section('title', $portfolio->title)

@section('meta_description', $portfolio->description)

@section('content')
    <!-- Portfolio Hero Section -->
    <section class="py-5 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                    <div class="mb-3">
                        <a href="{{ route('portfolio.index') }}" class="text-white text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Back to Portfolio
                        </a>
                    </div>
                    <span class="badge bg-primary mb-3" style="font-size: 0.9rem;">
                    {{ $portfolio->category->name }}
                </span>
                    <h1 class="display-4 mb-3">{{ $portfolio->title }}</h1>
                    @if($portfolio->client)
                        <p class="lead">
                            <i class="fas fa-user me-2"></i>Client: {{ $portfolio->client }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Project Details -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8" data-aos="fade-right">
                    <!-- Featured Image -->
                    @if($portfolio->thumbnail)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $portfolio->thumbnail) }}"
                                 alt="{{ $portfolio->title }}"
                                 class="img-fluid rounded shadow-lg w-100"
                                 style="max-height: 600px; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Project Description -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h2 class="section-title mb-4">Project Description</h2>
                            <p class="lead">{{ $portfolio->description }}</p>
                        </div>
                    </div>

                    <!-- Project Gallery -->
                    @if($portfolio->images && count($portfolio->images) > 0)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h3 class="section-title mb-4">Project Gallery</h3>
                                <div class="row g-3">
                                    @foreach($portfolio->images as $image)
                                        <div class="col-md-4 col-sm-6">
                                            <a href="{{ asset('storage/' . $image) }}" data-lightbox="portfolio-gallery" data-title="{{ $portfolio->title }}">
                                                <div class="portfolio-item">
                                                    <img src="{{ asset('storage/' . $image) }}"
                                                         alt="{{ $portfolio->title }}"
                                                         class="img-fluid rounded"
                                                         style="width: 100%; height: 250px; object-fit: cover;">
                                                    <div class="portfolio-overlay">
                                                        <i class="fas fa-search-plus fa-2x text-white"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Techniques Used -->
                    @if($portfolio->techniques_used && count($portfolio->techniques_used) > 0)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h3 class="section-title mb-4">Techniques & Methods Used</h3>
                                <div class="row g-3">
                                    @foreach($portfolio->techniques_used as $technique)
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                                <i class="fas fa-fire text-primary me-3" style="font-size: 1.5rem;"></i>
                                                <strong>{{ $technique }}</strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Project Highlights -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h3 class="section-title mb-4">Project Highlights</h3>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                    <strong>Completed on Schedule:</strong> All project milestones met on time
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                    <strong>Quality Standards:</strong> Exceeded industry quality benchmarks
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                    <strong>Safety Compliance:</strong> Zero safety incidents throughout project
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-primary me-2"></i>
                                    <strong>Client Satisfaction:</strong> Received excellent feedback from client
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4" data-aos="fade-left">
                    <!-- Project Info Card -->
                    <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 100px;">
                        <div class="card-body p-4">
                            <h4 class="mb-4">Project Information</h4>

                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Category</h6>
                                <p class="mb-0">
                                    <span class="badge bg-primary">{{ $portfolio->category->name }}</span>
                                </p>
                            </div>

                            @if($portfolio->client)
                                <hr>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Client</h6>
                                    <p class="mb-0">
                                        <i class="fas fa-user text-primary me-2"></i>{{ $portfolio->client }}
                                    </p>
                                </div>
                            @endif

                            @if($portfolio->location)
                                <hr>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Location</h6>
                                    <p class="mb-0">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>{{ $portfolio->location }}
                                    </p>
                                </div>
                            @endif

                            @if($portfolio->completion_date)
                                <hr>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Completion Date</h6>
                                    <p class="mb-0">
                                        <i class="fas fa-calendar text-primary me-2"></i>{{ $portfolio->completion_date->format('F Y') }}
                                    </p>
                                </div>
                            @endif

                            <hr>

                            <div class="d-grid gap-2">
                                <a href="{{ route('contact') }}" class="btn btn-primary-custom">
                                    <i class="fas fa-envelope me-2"></i>Start Your Project
                                </a>
                                <a href="{{ route('portfolio.index') }}" class="btn btn-outline-custom">
                                    <i class="fas fa-th me-2"></i>View All Projects
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Share Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Share This Project</h5>
                            <div class="d-flex gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                   target="_blank"
                                   class="btn btn-outline-primary flex-fill">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($portfolio->title) }}"
                                   target="_blank"
                                   class="btn btn-outline-info flex-fill">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($portfolio->title) }}"
                                   target="_blank"
                                   class="btn btn-outline-primary flex-fill">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($portfolio->title . ' - ' . url()->current()) }}"
                                   target="_blank"
                                   class="btn btn-outline-success flex-fill">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Projects -->
    @if($relatedProjects->count() > 0)
        <section class="py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title d-inline-block">Related Projects</h2>
                    <p class="section-subtitle mt-3">More Work in {{ $portfolio->category->name }}</p>
                </div>
                <div class="row g-4">
                    @foreach($relatedProjects as $index => $related)
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            <div class="portfolio-item">
                                <img src="{{ asset('storage/'.$related->thumbnail) ?? 'https://via.placeholder.com/400x300' }}"
                                     alt="{{ $related->title }}"
                                     style="width: 100%; height: 300px; object-fit: cover;">
                                <div class="portfolio-overlay">
                                    <h5 class="text-white mb-2">{{ $related->title }}</h5>
                                    <p class="text-white mb-3">
                                        <i class="fas fa-tag me-2"></i>{{ $related->category->name }}
                                    </p>
                                    <a href="{{ route('portfolio.show', $related->slug) }}" class="btn btn-light btn-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-5">
                    <a href="{{ route('portfolio.index') }}" class="btn btn-primary-custom">
                        View All Projects
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- CTA Section -->
    <section class="py-5 bg-dark text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h2 class="mb-3">Have a Similar Project?</h2>
                    <p class="lead mb-0">We'd love to bring your welding project to life. Get in touch for a free consultation and quote.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0" data-aos="fade-left">
                    <a href="{{ route('contact') }}" class="btn btn-primary-custom btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Start Your Project
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .sticky-top {
            position: -webkit-sticky;
            position: sticky;
        }
    </style>
@endpush

@extends('layouts.app')

@section('title', $service->title)

@section('meta_description', $service->description)

@section('content')
    <!-- Service Hero Section -->
    <section class="py-5 bg-dark text-white position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="opacity: 0.1; background: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,107,24,0.3) 10px, rgba(255,107,24,0.3) 20px);"></div>
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                    <div class="mb-3">
                        <a href="{{ route('services.index') }}" class="text-white text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Back to Services
                        </a>
                    </div>
                    <div class="service-icon text-center mb-4" style="font-size: 4rem; color: var(--primary-color);">
                        <i class="{{ $service->icon ?? 'fas fa-fire' }}"></i>
                    </div>
                    <h1 class="display-4 mb-3">{{ $service->title }}</h1>
                    <p class="lead">{{ $service->description }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Details Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8" data-aos="fade-right">
                    @if($service->image)
                        <div class="mb-4">
                            <img src="{{ $service->image }}"
                                 alt="{{ $service->title }}"
                                 class="img-fluid rounded shadow-lg w-100"
                                 style="max-height: 500px; object-fit: cover;">
                        </div>
                    @endif

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h2 class="section-title mb-4">Service Overview</h2>
                            <div class="service-content">
                                @if($service->full_description)
                                    {!! nl2br(e($service->full_description)) !!}
                                @else
                                    <p>{{ $service->description }}</p>
                                    <p>Our {{ strtolower($service->title) }} services provide professional, reliable solutions for all your welding needs. With years of experience and certified professionals, we deliver quality workmanship on every project.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Key Features -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h3 class="section-title mb-4">Key Features & Benefits</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Certified Professionals</h6>
                                            <p class="text-muted mb-0">All work performed by AWS certified welders</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Quality Guaranteed</h6>
                                            <p class="text-muted mb-0">Rigorous quality control and testing</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Fast Turnaround</h6>
                                            <p class="text-muted mb-0">Efficient service without compromising quality</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Competitive Pricing</h6>
                                            <p class="text-muted mb-0">Fair, transparent pricing for all projects</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">On-Site Services</h6>
                                            <p class="text-muted mb-0">Mobile welding available at your location</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-primary" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Safety First</h6>
                                            <p class="text-muted mb-0">OSHA compliant and safety certified</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Applications -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h3 class="section-title mb-4">Common Applications</h3>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-industry text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>Industrial</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-building text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>Commercial</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-home text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>Residential</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-car text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>Automotive</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-ship text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>Marine</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-tools text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>Custom Projects</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Process -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h3 class="section-title mb-4">Our Process</h3>
                            <div class="timeline">
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px; font-weight: bold;">
                                            1
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>Consultation</h5>
                                        <p class="text-muted">We discuss your project requirements and provide expert recommendations.</p>
                                    </div>
                                </div>
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px; font-weight: bold;">
                                            2
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>Assessment & Quote</h5>
                                        <p class="text-muted">We evaluate the work and provide a detailed, transparent quote.</p>
                                    </div>
                                </div>
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px; font-weight: bold;">
                                            3
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>Execution</h5>
                                        <p class="text-muted">Our certified welders perform the work with precision and care.</p>
                                    </div>
                                </div>
                                <div class="d-flex mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px; font-weight: bold;">
                                            4
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>Quality Inspection</h5>
                                        <p class="text-muted">Thorough inspection and testing to ensure quality standards.</p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px; font-weight: bold;">
                                            5
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>Delivery & Support</h5>
                                        <p class="text-muted">Project completion with ongoing support and warranty.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4" data-aos="fade-left">
                    <!-- Contact Card -->
                    <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 100px;">
                        <div class="card-body p-4">
                            <h4 class="mb-4">Get a Free Quote</h4>
                            <p class="text-muted mb-4">Interested in this service? Contact us for a free consultation and quote.</p>

                            <div class="d-grid gap-3">
                                <a href="{{ route('contact') }}" class="btn btn-primary-custom">
                                    <i class="fas fa-envelope me-2"></i>Request Quote
                                </a>
                                <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', env('BUSINESS_PHONE')) }}"
                                   class="btn btn-outline-custom">
                                    <i class="fas fa-phone me-2"></i>Call Now
                                </a>
                                <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', env('WHATSAPP_NUMBER')) }}"
                                   target="_blank"
                                   class="btn btn-outline-custom">
                                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                </a>
                            </div>

                            <hr class="my-4">

                            <h6 class="mb-3">Quick Info</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <small>Response Time: 24 hours</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-certificate text-primary me-2"></i>
                                    <small>Certified Professionals</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-shield-alt text-primary me-2"></i>
                                    <small>Quality Guaranteed</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <small>On-Site Available</small>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Business Hours -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="mb-3">
                                <i class="far fa-clock text-primary me-2"></i>
                                Business Hours
                            </h5>
                            <table class="table table-sm mb-0">
                                <tbody>
                                <tr>
                                    <td class="border-0 ps-0">Monday - Friday:</td>
                                    <td class="border-0 text-end pe-0"><strong>8:00 AM - 6:00 PM</strong></td>
                                </tr>
                                <tr>
                                    <td class="border-0 ps-0">Saturday:</td>
                                    <td class="border-0 text-end pe-0"><strong>9:00 AM - 4:00 PM</strong></td>
                                </tr>
                                <tr>
                                    <td class="border-0 ps-0">Sunday:</td>
                                    <td class="border-0 text-end pe-0"><strong>Closed</strong></td>
                                </tr>
                                <tr>
                                    <td class="border-0 ps-0" colspan="2">
                                        <small class="text-muted">Emergency services available 24/7</small>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Services -->
    @if($relatedServices->count() > 0)
        <section class="py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="section-title d-inline-block">Related Services</h2>
                    <p class="section-subtitle mt-3">Explore Our Other Welding Solutions</p>
                </div>
                <div class="row g-4">
                    @foreach($relatedServices as $index => $related)
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            <div class="service-card p-4">
                                <div class="service-icon text-center">
                                    <i class="{{ $related->icon ?? 'fas fa-fire' }}"></i>
                                </div>
                                <h5 class="text-center mt-3">{{ $related->title }}</h5>
                                <p class="text-muted text-center">{{ Str::limit($related->description, 100) }}</p>
                                <div class="text-center">
                                    <a href="{{ route('services.show', $related->slug) }}" class="btn btn-sm btn-outline-primary">
                                        Learn More <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('services.index') }}" class="btn btn-primary-custom">
                        View All Services
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
                    <h2 class="mb-3">Ready to Get Started?</h2>
                    <p class="lead mb-0">Let's discuss your {{ strtolower($service->title) }} needs and create a solution that works for you.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0" data-aos="fade-left">
                    <a href="{{ route('contact') }}" class="btn btn-primary-custom btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Get Free Quote
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

        .service-content {
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .service-content p {
            margin-bottom: 1rem;
        }

        .timeline .d-flex {
            position: relative;
        }

        .timeline .d-flex:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 24px;
            top: 50px;
            width: 2px;
            height: calc(100% - 20px);
            background: #e0e0e0;
        }
    </style>
@endpush

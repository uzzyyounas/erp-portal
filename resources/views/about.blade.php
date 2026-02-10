@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<!-- Page Header -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <div class="text-center">
            <h1 class="display-4 mb-3">About Us</h1>
            <p class="lead">Professional Welding Excellence Since 2008</p>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=800" 
                     alt="About Us" 
                     class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="section-title">Our Story</h2>
                <p class="lead mt-4">With over 15 years of experience in the welding and fabrication industry, we've built a reputation for delivering exceptional quality and reliability.</p>
                <p>We specialize in a wide range of welding techniques including MIG, TIG, Stick, and Arc welding. Our team is fully certified and committed to maintaining the highest standards of workmanship and safety.</p>
                <p>From small repairs to large-scale industrial projects, we bring precision, expertise, and dedication to every job we undertake.</p>
            </div>
        </div>
    </div>
</section>

<!-- Skills Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5" data-aos="fade-up">Our Expertise</h2>
        <div class="row">
            @foreach($skills as $index => $skill)
            <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="mb-2 d-flex justify-content-between">
                    <span class="fw-bold">{{ $skill['name'] }}</span>
                    <span>{{ $skill['percentage'] }}%</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-primary" 
                         role="progressbar" 
                         style="width: {{ $skill['percentage'] }}%" 
                         aria-valuenow="{{ $skill['percentage'] }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Certifications -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5" data-aos="fade-up">Certifications & Training</h2>
        <div class="row justify-content-center">
            @foreach($certifications as $index => $cert)
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="text-center p-4 bg-white rounded shadow-sm h-100">
                    <i class="fas fa-award text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">{{ $cert }}</h5>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Values -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <h2 class="section-title text-center mb-5 text-white" data-aos="fade-up">Our Values</h2>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--primary-color);"></i>
                    <h5 class="mt-3">Quality First</h5>
                    <p>We never compromise on the quality of our work</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <i class="fas fa-clock" style="font-size: 3rem; color: var(--primary-color);"></i>
                    <h5 class="mt-3">Timely Delivery</h5>
                    <p>Projects completed on schedule, every time</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <i class="fas fa-hard-hat" style="font-size: 3rem; color: var(--primary-color);"></i>
                    <h5 class="mt-3">Safety Standards</h5>
                    <p>Strict adherence to safety protocols and regulations</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <i class="fas fa-handshake" style="font-size: 3rem; color: var(--primary-color);"></i>
                    <h5 class="mt-3">Customer Focus</h5>
                    <p>Your satisfaction is our top priority</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.app')

@section('title', 'Services')

@section('content')
<section class="py-5 bg-dark text-white">
    <div class="container text-center">
        <h1 class="display-4">Our Services</h1>
        <p class="lead">Professional Welding & Fabrication Solutions</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @forelse($services as $index => $service)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="service-card p-4">
                    @if($service->image)
                    <img src="{{ $service->image }}" alt="{{ $service->title }}" class="img-fluid rounded mb-3">
                    @endif
                    <div class="service-icon text-center">
                        <i class="{{ $service->icon ?? 'fas fa-fire' }}"></i>
                    </div>
                    <h4 class="mt-3">{{ $service->title }}</h4>
                    <p class="text-muted">{{ $service->description }}</p>
                    <a href="{{ route('services.show', $service->slug) }}" class="btn btn-outline-primary">
                        Learn More <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No services available at the moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

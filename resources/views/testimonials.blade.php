@extends('layouts.app')

@section('title', 'Testimonials')

@section('content')
<section class="py-5 bg-dark text-white">
    <div class="container text-center">
        <h1 class="display-4">Client Testimonials</h1>
        <p class="lead">What Our Clients Say About Us</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @forelse($testimonials as $index => $testimonial)
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
                    <p class="mb-4">"{{ $testimonial->testimonial }}"</p>
                    <div class="d-flex align-items-center">
                        @if($testimonial->client_photo)
                        <img src="{{ $testimonial->client_photo }}" 
                             alt="{{ $testimonial->client_name }}" 
                             class="rounded-circle me-3" 
                             width="60" 
                             height="60">
                        @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px; font-size: 1.5rem;">
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
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No testimonials available at the moment.</p>
            </div>
            @endforelse
        </div>

        @if($testimonials->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $testimonials->links() }}
        </div>
        @endif
    </div>
</section>
@endsection

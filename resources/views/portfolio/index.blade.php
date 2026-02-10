@extends('layouts.app')

@section('title', 'Portfolio')

@section('content')
<section class="py-5 bg-dark text-white">
    <div class="container text-center">
        <h1 class="display-4">Our Portfolio</h1>
        <p class="lead">Showcasing Our Best Work</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <!-- Filter Buttons -->
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="btn-group flex-wrap" role="group">
                <a href="{{ route('portfolio.index') }}"
                   class="btn {{ $selectedCategory == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                    All Projects
                </a>
                @foreach($categories as $category)
                <a href="{{ route('portfolio.index', ['category' => $category->id]) }}"
                   class="btn {{ $selectedCategory == $category->id ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $category->name }} ({{ $category->portfolios_count }})
                </a>
                @endforeach
            </div>
        </div>

        <!-- Portfolio Grid -->
        <div class="row g-4">
            @forelse($portfolios as $index => $portfolio)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="portfolio-item">
                    <img src="{{ $portfolio->thumbnail ?? 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?w=800' }}"
                         alt="{{ $portfolio->title }}">
                    <div class="portfolio-overlay">
                        <h5 class="text-white mb-2">{{ $portfolio->title }}</h5>
                        <p class="text-white mb-2">
                            <i class="fas fa-tag me-2"></i>{{ $portfolio->category->name }}
                        </p>
                        @if($portfolio->client)
                        <p class="text-white-50 small mb-3">
                            <i class="fas fa-user me-2"></i>{{ $portfolio->client }}
                        </p>
                        @endif
                        <a href="{{ route('portfolio.show', $portfolio->slug) }}" class="btn btn-light btn-sm">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No portfolio items found.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($portfolios->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $portfolios->links() }}
        </div>
        @endif
    </div>
</section>
@endsection

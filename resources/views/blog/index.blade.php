@extends('layouts.app')

@section('title', 'Blog')

@section('content')
<section class="py-5 bg-dark text-white">
    <div class="container text-center">
        <h1 class="display-4">Blog & Updates</h1>
        <p class="lead">Latest News and Insights</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if($categories->count() > 0)
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="btn-group flex-wrap" role="group">
                <a href="{{ route('blog.index') }}" 
                   class="btn {{ $selectedCategory == '' ? 'btn-primary' : 'btn-outline-primary' }}">
                    All Posts
                </a>
                @foreach($categories as $category)
                <a href="{{ route('blog.index', ['category' => $category->id]) }}" 
                   class="btn {{ $selectedCategory == $category->id ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $category->name }} ({{ $category->blog_posts_count }})
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="row g-4">
            @forelse($posts as $index => $post)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="card h-100 border-0 shadow">
                    @if($post->featured_image)
                    <img src="{{ $post->featured_image }}" class="card-img-top" alt="{{ $post->title }}">
                    @else
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-blog" style="font-size: 4rem;"></i>
                    </div>
                    @endif
                    <div class="card-body">
                        @if($post->category)
                        <span class="badge bg-primary mb-2">{{ $post->category->name }}</span>
                        @endif
                        <h5 class="card-title">{{ $post->title }}</h5>
                        <p class="card-text text-muted">{{ $post->excerpt }}</p>
                        <div class="d-flex justify-content-between align-items-center text-muted small">
                            <span><i class="far fa-calendar me-1"></i>{{ $post->published_at->format('M d, Y') }}</span>
                            <span><i class="far fa-clock me-1"></i>{{ $post->reading_time }} min read</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-outline-primary w-100">
                            Read More <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">No blog posts available yet.</p>
            </div>
            @endforelse
        </div>

        @if($posts->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</section>
@endsection

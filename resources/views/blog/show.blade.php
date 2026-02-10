@extends('layouts.app')

@section('title', $post->title)

@section('meta_description', $post->excerpt)

@section('content')
    <!-- Blog Post Hero -->
    <section class="py-5 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                    <div class="mb-3">
                        <a href="{{ route('blog.index') }}" class="text-white text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Back to Blog
                        </a>
                    </div>
                    @if($post->category)
                        <span class="badge bg-primary mb-3" style="font-size: 0.9rem;">
                    {{ $post->category->name }}
                </span>
                    @endif
                    <h1 class="display-4 mb-3">{{ $post->title }}</h1>
                    <div class="d-flex justify-content-center align-items-center gap-4 text-white-50">
                    <span>
                        <i class="far fa-user me-2"></i>{{ $post->author }}
                    </span>
                        <span>
                        <i class="far fa-calendar me-2"></i>{{ $post->published_at->format('M d, Y') }}
                    </span>
                        <span>
                        <i class="far fa-clock me-2"></i>{{ $post->reading_time }} min read
                    </span>
                        <span>
                        <i class="far fa-eye me-2"></i>{{ $post->views }} views
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Post Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8 mx-auto" data-aos="fade-up">
                    <!-- Featured Image -->
                    @if($post->featured_image)
                        <div class="mb-5">
                            <img src="{{ $post->featured_image }}"
                                 alt="{{ $post->title }}"
                                 class="img-fluid rounded shadow-lg w-100"
                                 style="max-height: 500px; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Post Content -->
                    <article class="blog-content mb-5">
                        {!! $post->content !!}
                    </article>

                    <!-- Tags (if you add them later) -->
                    <div class="mb-4">
                        <h6 class="d-inline me-3">Tags:</h6>
                        <span class="badge bg-secondary me-2">Welding</span>
                        @if($post->category)
                            <span class="badge bg-secondary me-2">{{ $post->category->name }}</span>
                        @endif
                    </div>

                    <!-- Share Buttons -->
                    <div class="card border-0 shadow-sm mb-5">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Share This Post</h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                   target="_blank"
                                   class="btn btn-outline-primary">
                                    <i class="fab fa-facebook-f me-2"></i>Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}"
                                   target="_blank"
                                   class="btn btn-outline-info">
                                    <i class="fab fa-twitter me-2"></i>Twitter
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($post->title) }}"
                                   target="_blank"
                                   class="btn btn-outline-primary">
                                    <i class="fab fa-linkedin-in me-2"></i>LinkedIn
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($post->title . ' - ' . url()->current()) }}"
                                   target="_blank"
                                   class="btn btn-outline-success">
                                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                </a>
                                <button onclick="copyToClipboard('{{ url()->current() }}')" class="btn btn-outline-secondary">
                                    <i class="fas fa-link me-2"></i>Copy Link
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Author Box -->
                    <div class="card border-0 shadow-sm mb-5 bg-light">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                     style="width: 80px; height: 80px; font-size: 2rem;">
                                    <strong>{{ substr($post->author, 0, 1) }}</strong>
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $post->author }}</h5>
                                    <p class="text-muted mb-2">Professional Welder & Content Writer</p>
                                    <p class="mb-0">Sharing knowledge and insights about welding, fabrication, and metalworking.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Posts -->
                    @if($relatedPosts->count() > 0)
                        <div class="mb-5">
                            <h3 class="section-title mb-4">Related Posts</h3>
                            <div class="row g-4">
                                @foreach($relatedPosts as $related)
                                    <div class="col-md-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            @if($related->featured_image)
                                                <img src="{{ $related->featured_image }}"
                                                     class="card-img-top"
                                                     alt="{{ $related->title }}"
                                                     style="height: 200px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary text-white d-flex align-items-center justify-content-center"
                                                     style="height: 200px;">
                                                    <i class="fas fa-blog" style="font-size: 3rem;"></i>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $related->title }}</h6>
                                                <p class="card-text small text-muted">{{ Str::limit($related->excerpt, 80) }}</p>
                                                <a href="{{ route('blog.show', $related->slug) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    Read More
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Navigation -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('blog.index') }}" class="btn btn-outline-custom">
                            <i class="fas fa-arrow-left me-2"></i>Back to Blog
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-primary-custom">
                            Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-dark text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h2 class="mb-3">Need Professional Welding Services?</h2>
                    <p class="lead mb-0">From small repairs to large fabrication projects, we've got you covered with expert welding solutions.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0" data-aos="fade-left">
                    <a href="{{ route('contact') }}" class="btn btn-primary-custom btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Get a Quote
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .blog-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }

        .blog-content h1,
        .blog-content h2,
        .blog-content h3,
        .blog-content h4,
        .blog-content h5,
        .blog-content h6 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }

        .blog-content p {
            margin-bottom: 1.5rem;
        }

        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 2rem 0;
        }

        .blog-content ul,
        .blog-content ol {
            margin-bottom: 1.5rem;
            padding-left: 2rem;
        }

        .blog-content li {
            margin-bottom: 0.5rem;
        }

        .blog-content blockquote {
            border-left: 4px solid var(--primary-color);
            padding-left: 1.5rem;
            margin: 2rem 0;
            font-style: italic;
            color: #666;
        }

        .blog-content code {
            background: #f5f5f5;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: var(--primary-color);
        }

        .blog-content pre {
            background: #f5f5f5;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 2rem 0;
        }

        .blog-content pre code {
            background: none;
            padding: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Create temporary alert
                const alert = document.createElement('div');
                alert.className = 'alert alert-success position-fixed top-50 start-50 translate-middle';
                alert.style.zIndex = '9999';
                alert.innerHTML = '<i class="fas fa-check me-2"></i>Link copied to clipboard!';
                document.body.appendChild(alert);

                // Remove after 2 seconds
                setTimeout(() => {
                    alert.remove();
                }, 2000);
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
@endpush

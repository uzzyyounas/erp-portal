@extends('admin.layout')

@section('title', isset($blogPost) ? 'Edit Blog Post' : 'Create Blog Post')
@section('page-title', isset($blogPost) ? 'Edit Blog Post' : 'Create New Blog Post')

@section('content')
<form action="{{ isset($blogPost) ? route('admin.blog.update', $blogPost) : route('admin.blog.store') }}" 
      method="POST" 
      enctype="multipart/form-data">
    @csrf
    @if(isset($blogPost))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Content Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="title" class="form-label">Post Title *</label>
                        <input type="text" 
                               class="form-control form-control-lg @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $blogPost->title ?? '') }}" 
                               placeholder="Enter a catchy title..."
                               required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                  id="excerpt" 
                                  name="excerpt" 
                                  rows="2"
                                  placeholder="Brief summary (auto-generated from content if left empty)">{{ old('excerpt', $blogPost->excerpt ?? '') }}</textarea>
                        @error('excerpt')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Leave empty to auto-generate from content</small>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content *</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" 
                                  name="content" 
                                  rows="20"
                                  required>{{ old('content', $blogPost->content ?? '') }}</textarea>
                        @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Supports HTML tags</small>
                    </div>

                    <div class="mb-3">
                        <label for="featured_image" class="form-label">Featured Image</label>
                        <input type="file" 
                               class="form-control @error('featured_image') is-invalid @enderror" 
                               id="featured_image" 
                               name="featured_image" 
                               accept="image/*">
                        @error('featured_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($blogPost) && $blogPost->featured_image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $blogPost->featured_image) }}" 
                                 alt="Featured image" 
                                 class="img-thumbnail" 
                                 style="max-height: 150px;">
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- HTML Guide Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-code me-2"></i>HTML Guide</h6>
                </div>
                <div class="card-body">
                    <div class="row small">
                        <div class="col-md-6">
                            <p class="mb-1"><code>&lt;h2&gt;Heading&lt;/h2&gt;</code></p>
                            <p class="mb-1"><code>&lt;p&gt;Paragraph&lt;/p&gt;</code></p>
                            <p class="mb-1"><code>&lt;strong&gt;Bold&lt;/strong&gt;</code></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><code>&lt;ul&gt;&lt;li&gt;List&lt;/li&gt;&lt;/ul&gt;</code></p>
                            <p class="mb-1"><code>&lt;a href="..."&gt;Link&lt;/a&gt;</code></p>
                            <p class="mb-1"><code>&lt;img src="..." alt="..."&gt;</code></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Publish Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Publish</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="author" class="form-label">Author *</label>
                        <input type="text" 
                               class="form-control @error('author') is-invalid @enderror" 
                               id="author" 
                               name="author" 
                               value="{{ old('author', $blogPost->author ?? 'Admin') }}" 
                               required>
                        @error('author')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                id="category_id" 
                                name="category_id">
                            <option value="">Uncategorized</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    {{ old('category_id', $blogPost->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="published_at" class="form-label">Publish Date</label>
                        <input type="datetime-local" 
                               class="form-control @error('published_at') is-invalid @enderror" 
                               id="published_at" 
                               name="published_at" 
                               value="{{ old('published_at', isset($blogPost) && $blogPost->published_at ? $blogPost->published_at->format('Y-m-d\TH:i') : '') }}">
                        @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Leave empty to use current time</small>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_published" 
                                   name="is_published" 
                                   value="1"
                                   {{ old('is_published', $blogPost->is_published ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">
                                <strong>Published</strong>
                            </label>
                        </div>
                        <small class="text-muted">Unchecked = Draft</small>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ isset($blogPost) ? 'Update' : 'Publish' }} Post
                        </button>
                        <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>

                    @if(isset($blogPost))
                    <hr>
                    <div class="small text-muted">
                        <p class="mb-1"><strong>Views:</strong> {{ $blogPost->views }}</p>
                        <p class="mb-1"><strong>Reading Time:</strong> {{ $blogPost->reading_time }} min</p>
                        <p class="mb-0"><strong>Created:</strong> {{ $blogPost->created_at->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- SEO Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-search me-2"></i>SEO Tips</h6>
                    <ul class="small text-muted ps-3 mb-0">
                        <li class="mb-2">Use descriptive, keyword-rich titles</li>
                        <li class="mb-2">Write compelling excerpts</li>
                        <li class="mb-2">Include relevant images</li>
                        <li class="mb-2">Use headings (H2, H3) in content</li>
                        <li class="mb-2">Link to related posts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@extends('admin.layout')

@section('title', isset($category) ? 'Edit Category' : 'Create Category')
@section('page-title', isset($category) ? 'Edit Category' : 'Create New Category')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" 
                      method="POST">
                    @csrf
                    @if(isset($category))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name *</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $category->name ?? '') }}" 
                               placeholder="e.g., Structural Welding"
                               required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Slug will be auto-generated from name</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Brief description of this category">{{ old('description', $category->description ?? '') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (visible on website)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ isset($category) ? 'Update' : 'Create' }} Category
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Category Info</h5>
                <ul class="small text-muted ps-3">
                    <li class="mb-2">Categories organize portfolio items and blog posts</li>
                    <li class="mb-2">Name should be clear and descriptive</li>
                    <li class="mb-2">Slug is auto-generated from name</li>
                    <li class="mb-2">Inactive categories won't show on website</li>
                    @if(isset($category))
                    <li class="mb-2 text-info">
                        <strong>Currently used in:</strong><br>
                        {{ $category->portfolios()->count() }} portfolio items<br>
                        {{ $category->blogPosts()->count() }} blog posts
                    </li>
                    @endif
                </ul>
            </div>
        </div>

        @if(isset($category))
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <h6 class="mb-3">Category Details</h6>
                <div class="small">
                    <p class="mb-2"><strong>Slug:</strong> <code>{{ $category->slug }}</code></p>
                    <p class="mb-2"><strong>Created:</strong> {{ $category->created_at->format('M d, Y') }}</p>
                    <p class="mb-0"><strong>Updated:</strong> {{ $category->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

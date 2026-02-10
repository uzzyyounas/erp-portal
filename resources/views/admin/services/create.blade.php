@extends('admin.layout')

@section('title', isset($service) ? 'Edit Service' : 'Create Service')
@section('page-title', isset($service) ? 'Edit Service' : 'Create New Service')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ isset($service) ? route('admin.services.update', $service) : route('admin.services.store') }}" 
                      method="POST" 
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($service))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" 
                               value="{{ old('title', $service->title ?? '') }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Short Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" required>{{ old('description', $service->description ?? '') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="full_description" class="form-label">Full Description</label>
                        <textarea class="form-control @error('full_description') is-invalid @enderror" 
                                  id="full_description" name="full_description" rows="6">{{ old('full_description', $service->full_description ?? '') }}</textarea>
                        @error('full_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="icon" class="form-label">Icon Class</label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                   id="icon" name="icon" 
                                   value="{{ old('icon', $service->icon ?? 'fas fa-fire') }}"
                                   placeholder="fas fa-fire">
                            <small class="text-muted">Font Awesome icon class</small>
                            @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="order" class="form-label">Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" 
                                   value="{{ old('order', $service->order ?? 0) }}">
                            @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Service Image</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*">
                        @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($service) && $service->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}" style="max-height: 100px;">
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                   {{ old('is_featured', $service->is_featured ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Featured Service
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ isset($service) ? 'Update' : 'Create' }} Service
                        </button>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
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
                <h5 class="mb-3">Tips</h5>
                <ul class="small text-muted ps-3">
                    <li class="mb-2">Title should be clear and descriptive</li>
                    <li class="mb-2">Short description appears on service cards</li>
                    <li class="mb-2">Full description shows on detail page</li>
                    <li class="mb-2">Use Font Awesome icons (e.g., fas fa-fire)</li>
                    <li class="mb-2">Featured services show on homepage</li>
                    <li class="mb-2">Order determines display sequence</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('admin.layout')

@section('title', isset($testimonial) ? 'Edit Testimonial' : 'Create Testimonial')
@section('page-title', isset($testimonial) ? 'Edit Testimonial' : 'Add New Testimonial')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ isset($testimonial) ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" 
                      method="POST" 
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($testimonial))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="client_name" class="form-label">Client Name *</label>
                            <input type="text" 
                                   class="form-control @error('client_name') is-invalid @enderror" 
                                   id="client_name" 
                                   name="client_name" 
                                   value="{{ old('client_name', $testimonial->client_name ?? '') }}" 
                                   required>
                            @error('client_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="client_position" class="form-label">Position/Title</label>
                            <input type="text" 
                                   class="form-control @error('client_position') is-invalid @enderror" 
                                   id="client_position" 
                                   name="client_position" 
                                   value="{{ old('client_position', $testimonial->client_position ?? '') }}">
                            @error('client_position')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="client_company" class="form-label">Company</label>
                            <input type="text" 
                                   class="form-control @error('client_company') is-invalid @enderror" 
                                   id="client_company" 
                                   name="client_company" 
                                   value="{{ old('client_company', $testimonial->client_company ?? '') }}">
                            @error('client_company')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="rating" class="form-label">Rating *</label>
                            <select class="form-select @error('rating') is-invalid @enderror" 
                                    id="rating" 
                                    name="rating" 
                                    required>
                                @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" 
                                        {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>
                                    {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                </option>
                                @endfor
                            </select>
                            @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="testimonial" class="form-label">Testimonial *</label>
                        <textarea class="form-control @error('testimonial') is-invalid @enderror" 
                                  id="testimonial" 
                                  name="testimonial" 
                                  rows="5" 
                                  required>{{ old('testimonial', $testimonial->testimonial ?? '') }}</textarea>
                        @error('testimonial')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="client_photo" class="form-label">Client Photo</label>
                        <input type="file" 
                               class="form-control @error('client_photo') is-invalid @enderror" 
                               id="client_photo" 
                               name="client_photo" 
                               accept="image/*">
                        @error('client_photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($testimonial) && $testimonial->client_photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $testimonial->client_photo) }}" 
                                 alt="{{ $testimonial->client_name }}" 
                                 class="rounded-circle" 
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="order" class="form-label">Display Order</label>
                        <input type="number" 
                               class="form-control @error('order') is-invalid @enderror" 
                               id="order" 
                               name="order" 
                               value="{{ old('order', $testimonial->order ?? 0) }}">
                        @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_featured" 
                                   name="is_featured" 
                                   value="1"
                                   {{ old('is_featured', $testimonial->is_featured ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Featured (show on homepage)
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (published)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ isset($testimonial) ? 'Update' : 'Create' }} Testimonial
                        </button>
                        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">
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
                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Tips</h5>
                <ul class="small text-muted ps-3">
                    <li class="mb-2">Client photo should be square (recommended: 200x200px)</li>
                    <li class="mb-2">Keep testimonials concise and impactful</li>
                    <li class="mb-2">Include company name for credibility</li>
                    <li class="mb-2">Featured testimonials appear on homepage</li>
                    <li class="mb-2">Order determines display sequence</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

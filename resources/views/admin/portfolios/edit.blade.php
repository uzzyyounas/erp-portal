@extends('admin.layout')

@section('title', isset($portfolio) ? 'Edit Portfolio' : 'Create Portfolio')
@section('page-title', isset($portfolio) ? 'Edit Portfolio Item' : 'Add New Project')

@section('content')
<form action="{{ isset($portfolio) ? route('admin.portfolios.update', $portfolio) : route('admin.portfolios.store') }}" 
      method="POST" 
      enctype="multipart/form-data">
    @csrf
    @if(isset($portfolio))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Project Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="title" class="form-label">Project Title *</label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $portfolio->title ?? '') }}" 
                               required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  required>{{ old('description', $portfolio->description ?? '') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="client" class="form-label">Client Name</label>
                            <input type="text" 
                                   class="form-control @error('client') is-invalid @enderror" 
                                   id="client" 
                                   name="client" 
                                   value="{{ old('client', $portfolio->client ?? '') }}">
                            @error('client')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   id="location" 
                                   name="location" 
                                   value="{{ old('location', $portfolio->location ?? '') }}">
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id" 
                                    required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ old('category_id', $portfolio->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="completion_date" class="form-label">Completion Date</label>
                            <input type="date" 
                                   class="form-control @error('completion_date') is-invalid @enderror" 
                                   id="completion_date" 
                                   name="completion_date" 
                                   value="{{ old('completion_date', isset($portfolio) && $portfolio->completion_date ? $portfolio->completion_date->format('Y-m-d') : '') }}">
                            @error('completion_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="techniques_used" class="form-label">Techniques Used</label>
                        <input type="text" 
                               class="form-control @error('techniques_used') is-invalid @enderror" 
                               id="techniques_used_input" 
                               name="techniques_used_input" 
                               value="{{ old('techniques_used_input', isset($portfolio) && $portfolio->techniques_used ? implode(', ', $portfolio->techniques_used) : '') }}"
                               placeholder="MIG Welding, TIG Welding, Metal Fabrication">
                        <small class="text-muted">Separate multiple techniques with commas</small>
                        @error('techniques_used')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="order" class="form-label">Display Order</label>
                        <input type="number" 
                               class="form-control @error('order') is-invalid @enderror" 
                               id="order" 
                               name="order" 
                               value="{{ old('order', $portfolio->order ?? 0) }}">
                        @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Images Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Project Images</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail Image {{ isset($portfolio) ? '' : '*' }}</label>
                        <input type="file" 
                               class="form-control @error('thumbnail') is-invalid @enderror" 
                               id="thumbnail" 
                               name="thumbnail" 
                               accept="image/*"
                               {{ isset($portfolio) ? '' : 'required' }}>
                        @error('thumbnail')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($portfolio) && $portfolio->thumbnail)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $portfolio->thumbnail) }}" 
                                 alt="Current thumbnail" 
                                 class="img-thumbnail" 
                                 style="max-height: 150px;">
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="images" class="form-label">Gallery Images (Multiple)</label>
                        <input type="file" 
                               class="form-control @error('images') is-invalid @enderror" 
                               id="images" 
                               name="images[]" 
                               accept="image/*" 
                               multiple>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple images</small>
                        @error('images')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($portfolio) && $portfolio->images)
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            @foreach($portfolio->images as $image)
                            <img src="{{ asset('storage/' . $image) }}" 
                                 alt="Gallery image" 
                                 class="img-thumbnail" 
                                 style="max-height: 100px;">
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Settings Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_featured" 
                                   name="is_featured" 
                                   value="1"
                                   {{ old('is_featured', $portfolio->is_featured ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Featured Project
                            </label>
                        </div>
                        <small class="text-muted">Show on homepage</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $portfolio->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (Published)
                            </label>
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ isset($portfolio) ? 'Update' : 'Create' }} Portfolio
                        </button>
                        <a href="{{ route('admin.portfolios.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-lightbulb me-2"></i>Tips</h6>
                    <ul class="small text-muted ps-3 mb-0">
                        <li class="mb-2">Use high-quality images (recommended: 1200x800px)</li>
                        <li class="mb-2">Thumbnail shows in portfolio grid</li>
                        <li class="mb-2">Gallery images appear on detail page</li>
                        <li class="mb-2">Add all relevant techniques</li>
                        <li class="mb-2">Featured items appear on homepage</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Convert comma-separated techniques to array before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const input = document.getElementById('techniques_used_input');
    if (input.value) {
        const techniques = input.value.split(',').map(t => t.trim()).filter(t => t);
        
        // Create hidden inputs for array
        techniques.forEach((tech, i) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = `techniques_used[${i}]`;
            hidden.value = tech;
            this.appendChild(hidden);
        });
    }
});
</script>
@endpush
@endsection

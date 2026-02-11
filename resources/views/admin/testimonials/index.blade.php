@extends('admin.layout')

@section('title', 'Testimonials')
@section('page-title', 'Testimonials Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Testimonials</h2>
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Testimonial
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Client</th>
                <th>Position</th>
                <th>Rating</th>
                <th>Testimonial</th>
                <th>Featured</th>
                <th>Status</th>
                <th style="width: 150px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($testimonials as $testimonial)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        @if($testimonial->client_photo)
                        <img src="{{ asset('storage/' . $testimonial->client_photo) }}" 
                             alt="{{ $testimonial->client_name }}" 
                             class="rounded-circle me-2"
                             style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                             style="width: 40px; height: 40px; font-size: 1.2rem;">
                            {{ substr($testimonial->client_name, 0, 1) }}
                        </div>
                        @endif
                        <strong>{{ $testimonial->client_name }}</strong>
                    </div>
                </td>
                <td>
                    <div>
                        {{ $testimonial->client_position ?? '-' }}<br>
                        @if($testimonial->client_company)
                        <small class="text-muted">{{ $testimonial->client_company }}</small>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $testimonial->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                </td>
                <td>
                    <div style="max-width: 300px;">
                        {{ Str::limit($testimonial->testimonial, 80) }}
                    </div>
                </td>
                <td>
                    @if($testimonial->is_featured)
                    <span class="badge bg-warning">Featured</span>
                    @endif
                </td>
                <td>
                    @if($testimonial->is_active)
                    <span class="badge badge-active">Active</span>
                    @else
                    <span class="badge badge-inactive">Inactive</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('admin.testimonials.edit', $testimonial) }}" 
                           class="btn btn-sm btn-primary" 
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" 
                              method="POST" 
                              class="d-inline" 
                              onsubmit="return confirm('Delete this testimonial?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-star fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                    No testimonials yet. Add your first client review!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $testimonials->links() }}
</div>
@endsection

@extends('admin.layout')

@section('title', 'Portfolio')
@section('page-title', 'Portfolio Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Portfolio Items</h2>
    <a href="{{ route('admin.portfolios.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Project
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th style="width: 80px;">Image</th>
                <th>Title</th>
                <th>Category</th>
                <th>Client</th>
                <th>Date</th>
                <th>Featured</th>
                <th>Status</th>
                <th style="width: 150px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($portfolios as $portfolio)
            <tr>
                <td>
                    @if($portfolio->thumbnail)
                    <img src="{{ asset('storage/' . $portfolio->thumbnail) }}" 
                         alt="{{ $portfolio->title }}" 
                         class="img-thumbnail"
                         style="width: 60px; height: 60px; object-fit: cover;">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-image text-muted"></i>
                    </div>
                    @endif
                </td>
                <td>
                    <strong>{{ $portfolio->title }}</strong><br>
                    <small class="text-muted">{{ Str::limit($portfolio->description, 50) }}</small>
                </td>
                <td>
                    <span class="badge bg-secondary">{{ $portfolio->category->name }}</span>
                </td>
                <td>{{ $portfolio->client ?? '-' }}</td>
                <td>{{ $portfolio->completion_date ? $portfolio->completion_date->format('M Y') : '-' }}</td>
                <td>
                    @if($portfolio->is_featured)
                    <span class="badge bg-warning">Featured</span>
                    @endif
                </td>
                <td>
                    @if($portfolio->is_active)
                    <span class="badge badge-active">Active</span>
                    @else
                    <span class="badge badge-inactive">Inactive</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('portfolio.show', $portfolio->slug) }}" 
                           class="btn btn-sm btn-info" 
                           target="_blank"
                           title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.portfolios.edit', $portfolio) }}" 
                           class="btn btn-sm btn-primary" 
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.portfolios.destroy', $portfolio) }}" 
                              method="POST" 
                              class="d-inline" 
                              onsubmit="return confirm('Delete this portfolio item?')">
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
                <td colspan="8" class="text-center py-5 text-muted">
                    <i class="fas fa-briefcase fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                    No portfolio items yet. Showcase your first project!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $portfolios->links() }}
</div>
@endsection

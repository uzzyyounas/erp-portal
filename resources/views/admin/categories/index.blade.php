@extends('admin.layout')

@section('title', 'Categories')
@section('page-title', 'Categories Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Category
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Portfolios</th>
                <th>Blog Posts</th>
                <th>Status</th>
                <th style="width: 150px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td><strong>{{ $category->name }}</strong></td>
                <td><code>{{ $category->slug }}</code></td>
                <td>
                    <span class="badge bg-info">{{ $category->portfolios_count }}</span>
                </td>
                <td>
                    <span class="badge bg-info">{{ $category->blog_posts_count }}</span>
                </td>
                <td>
                    @if($category->is_active)
                    <span class="badge badge-active">Active</span>
                    @else
                    <span class="badge badge-inactive">Inactive</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('admin.categories.edit', $category) }}" 
                           class="btn btn-sm btn-primary" 
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" 
                              method="POST" 
                              class="d-inline" 
                              onsubmit="return confirm('Delete this category? This will affect {{ $category->portfolios_count + $category->blog_posts_count }} items.')">
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
                <td colspan="6" class="text-center py-4 text-muted">
                    <i class="fas fa-tags fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                    No categories found. Create your first category!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $categories->links() }}
</div>
@endsection

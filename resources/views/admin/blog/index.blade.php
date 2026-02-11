@extends('admin.layout')

@section('title', 'Blog Posts')
@section('page-title', 'Blog Posts Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Blog Posts</h2>
    <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create New Post
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Author</th>
                <th>Views</th>
                <th>Status</th>
                <th>Published</th>
                <th style="width: 150px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
            <tr>
                <td>
                    <strong>{{ $post->title }}</strong><br>
                    <small class="text-muted">{{ Str::limit($post->excerpt, 60) }}</small>
                </td>
                <td>
                    @if($post->category)
                    <span class="badge bg-secondary">{{ $post->category->name }}</span>
                    @else
                    <span class="badge bg-light text-dark">Uncategorized</span>
                    @endif
                </td>
                <td>{{ $post->author }}</td>
                <td>
                    <span class="badge bg-info">
                        <i class="fas fa-eye me-1"></i>{{ $post->views }}
                    </span>
                </td>
                <td>
                    @if($post->is_published)
                    <span class="badge badge-active">Published</span>
                    @else
                    <span class="badge bg-warning">Draft</span>
                    @endif
                </td>
                <td>
                    @if($post->published_at)
                    {{ $post->published_at->format('M d, Y') }}
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group">
                        @if($post->is_published)
                        <a href="{{ route('blog.show', $post->slug) }}" 
                           class="btn btn-sm btn-info" 
                           target="_blank"
                           title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        @endif
                        <a href="{{ route('admin.blog.edit', $post) }}" 
                           class="btn btn-sm btn-primary" 
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.blog.destroy', $post) }}" 
                              method="POST" 
                              class="d-inline" 
                              onsubmit="return confirm('Delete this blog post?')">
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
                    <i class="fas fa-blog fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                    No blog posts yet. Write your first article!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $posts->links() }}
</div>
@endsection

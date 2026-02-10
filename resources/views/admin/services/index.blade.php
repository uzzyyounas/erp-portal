@extends('admin.layout')

@section('title', 'Services')
@section('page-title', 'Services Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Services</h2>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Service
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Order</th>
                <th>Title</th>
                <th>Icon</th>
                <th>Featured</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $service)
            <tr>
                <td>{{ $service->order }}</td>
                <td>{{ $service->title }}</td>
                <td><i class="{{ $service->icon ?? 'fas fa-fire' }}"></i></td>
                <td>
                    @if($service->is_featured)
                    <span class="badge bg-warning">Featured</span>
                    @endif
                </td>
                <td>
                    @if($service->is_active)
                    <span class="badge badge-active">Active</span>
                    @else
                    <span class="badge badge-inactive">Inactive</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4">No services found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $services->links() }}
@endsection

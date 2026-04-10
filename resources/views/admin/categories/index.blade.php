@extends('layouts.app')
@section('title','Report Categories')
@section('breadcrumb')
    <li class="breadcrumb-item active">Report Categories</li>
@endsection

@section('content')
<div class="page-header">
    <h4><i class="bi bi-folder-fill me-2"></i>Report Categories</h4>
    <button class="btn btn-erp btn-sm" data-bs-toggle="modal" data-bs-target="#addCatModal">
        <i class="bi bi-folder-plus me-1"></i>Add Category
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead><tr>
                <th>Sort</th><th>Icon</th><th>Name</th><th>Slug</th>
                <th>Reports</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr>
                        <td>{{ $cat->sort_order }}</td>
                        <td><i class="bi {{ $cat->icon }} fs-5" style="color:#1a3a5c;"></i></td>
                        <td class="fw-semibold">{{ $cat->name }}</td>
                        <td><code>{{ $cat->slug }}</code></td>
                        <td><span class="badge bg-secondary">{{ $cat->reports_count }}</span></td>
                        <td>
                            <span class="badge {{ $cat->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $cat->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-xs btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#editCatModal"
                                    data-cat="{{ $cat->toJson() }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                                  class="d-inline" onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No categories yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Icon helper reference --}}
<div class="card mt-3">
    <div class="card-header light">
        <small class="fw-semibold">Common Bootstrap Icons for Categories</small>
    </div>
    <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-3" style="font-size:.8rem;">
            @foreach([
                'bi-cart-check-fill','bi-bag-check-fill','bi-boxes','bi-currency-dollar',
                'bi-arrow-down-circle-fill','bi-arrow-up-circle-fill','bi-people-fill',
                'bi-building','bi-truck','bi-graph-up-arrow','bi-file-earmark-text',
                'bi-bank','bi-clipboard-data','bi-pie-chart-fill','bi-receipt',
            ] as $icon)
                <span class="d-flex align-items-center gap-1">
                    <i class="bi {{ $icon }}"></i>
                    <code>{{ $icon }}</code>
                </span>
            @endforeach
        </div>
    </div>
</div>

{{-- Add Category Modal --}}
<div class="modal fade" id="addCatModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header" style="background:#1a3a5c;color:#fff;">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.categories._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Category Modal --}}
<div class="modal fade" id="editCatModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editCatForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header" style="background:#1a3a5c;color:#fff;">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.categories._form', ['edit' => true])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('editCatModal').addEventListener('show.bs.modal', function(e) {
    const cat  = JSON.parse(e.relatedTarget.dataset.cat);
    const form = document.getElementById('editCatForm');
    form.action = `/admin/categories/${cat.id}`;
    form.querySelector('[name=name]').value        = cat.name;
    form.querySelector('[name=icon]').value        = cat.icon;
    form.querySelector('[name=description]').value = cat.description || '';
    form.querySelector('[name=sort_order]').value  = cat.sort_order;
    document.getElementById('editCatActive').checked = cat.is_active;
    // Update icon preview
    document.getElementById('editIconPreview').className = 'bi ' + cat.icon + ' fs-3';
});
document.querySelectorAll('[name=icon]').forEach(input => {
    input.addEventListener('input', function() {
        const preview = this.closest('form').querySelector('.icon-preview');
        if (preview) preview.className = 'bi ' + this.value + ' fs-3 icon-preview';
    });
});
</script>
@endpush

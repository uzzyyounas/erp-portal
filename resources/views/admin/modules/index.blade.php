@extends('layouts.app')
@section('title', 'Modules')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active">Modules</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h4><i class="bi bi-grid-3x3 me-2"></i>Modules</h4>
        <small class="text-muted">Group menu items by business function (Sales, Finance, HR…)</small>
    </div>
    <button class="btn btn-erp btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg me-1"></i>Add Module
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead><tr>
                <th>Sort</th><th>Icon</th><th>Name</th><th>Color</th>
                <th>Items</th><th>Roles</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody>
            @forelse($modules as $mod)
                <tr>
                    <td class="text-muted">{{ $mod->sort_order }}</td>
                    <td><i class="bi {{ $mod->icon }} fs-5" style="color:{{ $mod->color }};"></i></td>
                    <td>
                        <div class="fw-semibold">{{ $mod->name }}</div>
                        <small class="text-muted">{{ $mod->description }}</small>
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:5px;">
                            <span style="width:14px;height:14px;border-radius:3px;background:{{ $mod->color }};display:inline-block;border:1px solid rgba(0,0,0,.1);"></span>
                            <code style="font-size:.7rem;">{{ $mod->color }}</code>
                        </span>
                    </td>
                    <td><span class="badge bg-secondary">{{ $mod->menu_items_count }}</span></td>
                    <td>
                        @if($mod->roles->isEmpty())
                            <span class="badge bg-success" style="font-size:.65rem;">All roles</span>
                        @else
                            @foreach($mod->roles as $r)
                                <span class="badge bg-info text-dark" style="font-size:.65rem;">{{ $r->name }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $mod->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $mod->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-xs btn-outline-primary"
                                data-bs-toggle="modal" data-bs-target="#editModal"
                                data-module="{{ $mod->load('roles')->toJson() }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.modules.destroy', $mod) }}"
                              class="d-inline" onsubmit="return confirm('Delete module {{ $mod->name }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-5">
                    No modules yet. <a href="#" data-bs-toggle="modal" data-bs-target="#addModal">Add one →</a>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Tip --}}
<div class="alert alert-info mt-3 d-flex align-items-start gap-2" style="font-size:.8rem;">
    <i class="bi bi-info-circle-fill mt-1"></i>
    <div>
        <strong>Role Restrictions:</strong> Leave roles empty to allow all authenticated users to see the module.
        Add specific roles to restrict access to those roles only. Administrators always have full access.
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('admin.modules.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header" style="background:#1a3a5c;color:#fff;">
                    <h5 class="modal-title"><i class="bi bi-grid-3x3 me-2"></i>Add Module</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.modules._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp">Create Module</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header" style="background:#1a3a5c;color:#fff;">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Module</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.modules._form', ['edit' => true])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp">Update Module</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function(e) {
    const mod  = JSON.parse(e.relatedTarget.dataset.module);
    const form = document.getElementById('editForm');
    form.action = `/admin/modules/${mod.id}`;
    form.querySelector('[name=name]').value        = mod.name;
    form.querySelector('[name=icon]').value        = mod.icon;
    form.querySelector('[name=color]').value       = mod.color || '#1a3a5c';
    form.querySelector('[name=description]').value = mod.description || '';
    form.querySelector('[name=sort_order]').value  = mod.sort_order;
    const activeEl = form.querySelector('[name=is_active]');
    if (activeEl) activeEl.checked = mod.is_active;

    // Set icon preview
    form.querySelector('.icon-preview').className = 'bi ' + mod.icon + ' fs-3 icon-preview';
    form.querySelector('.color-preview').style.background = mod.color;

    // Sync role checkboxes
    const roleIds = (mod.roles || []).map(r => r.id);
    form.querySelectorAll('[name="roles[]"]').forEach(cb => {
        cb.checked = roleIds.includes(parseInt(cb.value));
    });
});

document.querySelectorAll('[name=icon]').forEach(input => {
    input.addEventListener('input', function() {
        const form = this.closest('form');
        form.querySelector('.icon-preview').className = 'bi ' + this.value + ' fs-3 icon-preview';
    });
});
document.querySelectorAll('[name=color]').forEach(input => {
    input.addEventListener('input', function() {
        this.closest('form').querySelector('.color-preview').style.background = this.value;
    });
});
</script>
@endpush

@extends('layouts.app')
@section('title','Roles & Permissions')
@section('breadcrumb')
    <li class="breadcrumb-item active">Roles & Permissions</li>
@endsection

@section('content')
<div class="page-header">
    <h4><i class="bi bi-shield-fill me-2"></i>Roles & Permissions</h4>
    <button class="btn btn-erp btn-sm" data-bs-toggle="modal" data-bs-target="#addRoleModal">
        <i class="bi bi-shield-plus me-1"></i>Add Role
    </button>
</div>

<div class="row g-3">
    @foreach($roles as $role)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header light d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold">{{ $role->name }}</span>
                        <span class="badge bg-secondary ms-2">{{ $role->users_count }} users</span>
                        @if(!$role->is_active)
                            <span class="badge bg-danger ms-1">Inactive</span>
                        @endif
                    </div>
                    <button class="btn btn-xs btn-outline-secondary"
                            data-bs-toggle="modal" data-bs-target="#editRoleModal"
                            data-role="{{ $role->toJson() }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ $role->description ?: 'No description' }}</p>

                    {{-- Report permissions --}}
                    <form method="POST" action="{{ route('admin.roles.permissions', $role) }}">
                        @csrf @method('PATCH')
                        @php
                            $allReports = \App\Models\Report::with('category')
                                ->where('is_active', true)
                                ->orderBy('category_id')->orderBy('sort_order')->get();
                            $roleReportIds = $role->reports->pluck('id')->toArray();
                        @endphp

                        @if($role->slug === 'admin')
                            <div class="text-success small mb-2">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                Administrators have access to all reports
                            </div>
                        @else
                            @foreach($allReports->groupBy('category.name') as $catName => $catReports)
                                <div class="mb-2">
                                    <div class="text-muted fw-semibold" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">
                                        {{ $catName }}
                                    </div>
                                    @foreach($catReports as $r)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox"
                                                   name="reports[]" value="{{ $r->id }}"
                                                   id="r_{{ $role->id }}_{{ $r->id }}"
                                                   {{ in_array($r->id, $roleReportIds) ? 'checked' : '' }}>
                                            <label class="form-check-label small"
                                                   for="r_{{ $role->id }}_{{ $r->id }}">
                                                {{ $r->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                            <button type="submit" class="btn btn-sm btn-erp mt-2">
                                <i class="bi bi-save me-1"></i>Save Permissions
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Add Role Modal --}}
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header" style="background:#1a3a5c;color:#fff;">
                    <h6 class="modal-title">Add Role</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <input type="text" name="description" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp btn-sm">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Role Modal --}}
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="editRoleForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header" style="background:#1a3a5c;color:#fff;">
                    <h6 class="modal-title">Edit Role</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <input type="text" name="description" class="form-control form-control-sm">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="editRoleActive" value="1">
                        <label class="form-check-label" for="editRoleActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp btn-sm">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('editRoleModal').addEventListener('show.bs.modal', function(e) {
    const role = JSON.parse(e.relatedTarget.dataset.role);
    const form = document.getElementById('editRoleForm');
    form.action = `/admin/roles/${role.id}`;
    form.querySelector('[name=name]').value        = role.name;
    form.querySelector('[name=description]').value = role.description || '';
    document.getElementById('editRoleActive').checked = role.is_active;
});
</script>
@endpush

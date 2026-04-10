@extends('layouts.app')
@section('title','Users')
@section('breadcrumb')
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
<div class="page-header">
    <h4><i class="bi bi-people-fill me-2"></i>User Management</h4>
    <button class="btn btn-erp btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus me-1"></i>Add User
    </button>
</div>

{{-- Search --}}
<form class="mb-3 d-flex gap-2" method="GET">
    <input type="text" name="search" class="form-control form-control-sm" style="max-width:280px;"
           placeholder="Search name or email..." value="{{ $search }}">
    <button class="btn btn-sm btn-outline-secondary">Search</button>
    @if($search) <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-danger">Clear</a> @endif
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead><tr>
                <th>#</th><th>Name</th><th>Email / Username</th>
                <th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th>
            </tr></thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $user->name }}</div>
                        </td>
                        <td>
                            <div>{{ $user->email }}</div>
                            @if($user->username)
                                <small class="text-muted">@{{ $user->username }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $user->role->name }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                        <td>
                            <button class="btn btn-xs btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal"
                                    data-user="{{ $user->toJson() }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header" style="background:#1a3a5c;color:#fff;">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.users._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp">Create User</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editUserForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header" style="background:#1a3a5c;color:#fff;">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.users._form', ['edit' => true])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp">Update User</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('editUserModal').addEventListener('show.bs.modal', function (e) {
    const user = JSON.parse(e.relatedTarget.dataset.user);
    const form = document.getElementById('editUserForm');
    form.action = `/admin/users/${user.id}`;
    form.querySelector('[name=name]').value     = user.name;
    form.querySelector('[name=email]').value    = user.email;
    form.querySelector('[name=username]').value = user.username || '';
    form.querySelector('[name=role_id]').value  = user.role_id;
    form.querySelector('[name=is_active]').checked = user.is_active;
    form.querySelector('[name=password]').value = '';
    form.querySelector('[name=password_confirmation]').value = '';
});
</script>
@endpush

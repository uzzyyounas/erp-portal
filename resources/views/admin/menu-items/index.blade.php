@extends('layouts.app')
@section('title', 'Menu Items')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active">Menu Items</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h4><i class="bi bi-list-ul me-2"></i>Menu Items</h4>
        <small class="text-muted">Register reports and forms into the dynamic sidebar menu</small>
    </div>
    <a href="{{ route('admin.menu-items.create') }}" class="btn btn-erp btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Add Menu Item
    </a>
</div>

@foreach($modules as $module)
    @php $items = $module->menuItems; @endphp
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="bi {{ $module->icon }}" style="color:{{ $module->color }};font-size:1rem;"></i>
            <span>{{ $module->name }}</span>
            <span class="badge bg-secondary ms-1" style="font-size:.65rem;">{{ $items->count() }}</span>
            @if($module->roles->isNotEmpty())
                <span class="badge bg-warning text-dark ms-1" style="font-size:.62rem;">
                    <i class="bi bi-lock-fill me-1"></i>Restricted
                </span>
            @endif
            <a href="{{ route('admin.menu-items.create', ['module_id' => $module->id]) }}"
               class="btn btn-xs btn-outline-secondary ms-auto">
                <i class="bi bi-plus me-1"></i>Add Item
            </a>
        </div>
        @if($items->isEmpty())
            <div class="card-body text-center text-muted py-3" style="font-size:.82rem;">
                No items in this module yet.
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead><tr>
                    <th>Sort</th><th>Icon</th><th>Name</th><th>Type</th>
                    <th>Route Name</th><th>Roles</th><th>Menu</th><th>Status</th><th>Actions</th>
                </tr></thead>
                <tbody>
                @foreach($items->sortBy('sort_order') as $item)
                    <tr>
                        <td class="text-muted">{{ $item->sort_order }}</td>
                        <td><i class="bi {{ $item->icon ?: 'bi-file' }}" style="color:#1a3a5c;"></i></td>
                        <td>
                            <div class="fw-semibold" style="font-size:.83rem;">{{ $item->name }}</div>
                            @if($item->description)
                                <small class="text-muted">{{ $item->description }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge
                                @if($item->type==='report') bg-primary
                                @elseif($item->type==='form') bg-warning text-dark
                                @elseif($item->type==='link') bg-secondary
                                @else bg-light text-dark border @endif"
                                style="font-size:.65rem;">
                                <i class="bi {{ $item->type_icon }} me-1"></i>{{ $item->type_label }}
                            </span>
                        </td>
                        <td>
                            @if($item->route_name)
                                <code style="font-size:.7rem;">{{ $item->route_name }}</code>
                                @php
                                    try { $url = route($item->route_name); $valid = true; }
                                    catch(\Exception $e) { $valid = false; }
                                @endphp
                                @if($valid)
                                    <i class="bi bi-check-circle-fill text-success ms-1" title="Route valid"></i>
                                @else
                                    <i class="bi bi-x-circle-fill text-danger ms-1" title="Route not found!"></i>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($item->roles->isEmpty())
                                <span class="text-muted" style="font-size:.75rem;">All</span>
                            @else
                                @foreach($item->roles as $r)
                                    <span class="badge bg-info text-dark" style="font-size:.62rem;">{{ $r->name }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->show_in_menu)
                                <i class="bi bi-check-circle-fill text-success"></i>
                            @else
                                <i class="bi bi-x-circle-fill text-muted"></i>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-danger' }}" style="font-size:.65rem;">
                                {{ $item->is_active ? 'Active' : 'Off' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.menu-items.edit', $item) }}"
                                   class="btn btn-xs btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.menu-items.destroy', $item) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete \'{{ $item->name }}\'?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
@endforeach

@if($modules->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-grid fs-1 d-block mb-2 opacity-25"></i>
            <h6>No modules yet</h6>
            <a href="{{ route('admin.modules.index') }}" class="btn btn-erp btn-sm mt-2">
                Create a module first →
            </a>
        </div>
    </div>
@endif

<div class="alert alert-info mt-2 d-flex gap-2 align-items-start" style="font-size:.8rem;">
    <i class="bi bi-lightbulb-fill mt-1"></i>
    <div>
        <strong>Route Name Convention:</strong> Use <code>reports.sales.monthly</code> for reports,
        <code>forms.sales.order-entry</code> for forms. Add the matching route in
        <code>routes/web.php</code> and its controller before activating the item.
    </div>
</div>
@endsection

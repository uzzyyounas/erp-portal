@extends('layouts.app')
@section('title', isset($menuItem) ? 'Edit Menu Item' : 'Add Menu Item')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.menu-items.index') }}">Menu Items</a></li>
    <li class="breadcrumb-item active">{{ isset($menuItem) ? 'Edit' : 'Add' }}</li>
@endsection

@section('content')
<div class="page-header">
    <h4>
        <i class="bi bi-{{ isset($menuItem) ? 'pencil' : 'plus-lg' }} me-2"></i>
        {{ isset($menuItem) ? 'Edit: ' . $menuItem->name : 'Add Menu Item' }}
    </h4>
</div>

<div class="row">
<div class="col-lg-8">
<div class="card">
<div class="card-body">

<form method="POST"
      action="{{ isset($menuItem) ? route('admin.menu-items.update', $menuItem) : route('admin.menu-items.store') }}">
    @csrf
    @if(isset($menuItem)) @method('PUT') @endif

    <div class="row g-3">

        {{-- Module --}}
        <div class="col-sm-6">
            <label class="form-label fw-semibold">Module <span class="text-danger">*</span></label>
            <select name="module_id" class="form-select form-select-sm" required>
                <option value="">— Select Module —</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod->id }}"
                        {{ old('module_id', $menuItem->module_id ?? request('module_id')) == $mod->id ? 'selected' : '' }}>
                        {{ $mod->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Type --}}
        <div class="col-sm-6">
            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
            <select name="type" class="form-select form-select-sm" id="typeSelect" required>
                @foreach(['report' => 'Report','form' => 'Form','link' => 'External Link','divider' => 'Divider / Separator'] as $v => $l)
                    <option value="{{ $v }}"
                        {{ old('type', $menuItem->type ?? 'report') === $v ? 'selected' : '' }}>
                        {{ $l }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Name --}}
        <div class="col-sm-8">
            <label class="form-label fw-semibold">Display Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control form-control-sm"
                   value="{{ old('name', $menuItem->name ?? '') }}"
                   placeholder="e.g. Monthly Sales Report" required>
        </div>

        {{-- Sort --}}
        <div class="col-sm-2">
            <label class="form-label fw-semibold">Sort</label>
            <input type="number" name="sort_order" class="form-control form-control-sm"
                   value="{{ old('sort_order', $menuItem->sort_order ?? 0) }}" min="0">
        </div>

        {{-- Toggles --}}
        <div class="col-sm-2 d-flex flex-column justify-content-end pb-1 gap-1">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                       id="cbActive"
                       {{ old('is_active', $menuItem->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="cbActive" style="font-size:.78rem;">Active</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="show_in_menu" value="1"
                       id="cbMenu"
                       {{ old('show_in_menu', $menuItem->show_in_menu ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="cbMenu" style="font-size:.78rem;">In Menu</label>
            </div>
        </div>

        {{-- Icon --}}
        <div class="col-sm-6">
            <label class="form-label fw-semibold">Icon Class</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text">
                    <i id="iconPreview" class="bi {{ old('icon', $menuItem->icon ?? 'bi-file-text') }}"></i>
                </span>
                <input type="text" name="icon" id="iconInput" class="form-control form-control-sm"
                       value="{{ old('icon', $menuItem->icon ?? 'bi-file-text') }}"
                       placeholder="bi-bar-chart-line">
            </div>
            <div class="form-text">Bootstrap Icons class</div>
        </div>

        {{-- Route Name --}}
        <div class="col-sm-6" id="routeField">
            <label class="form-label fw-semibold">Route Name</label>
            <div class="input-group input-group-sm">
                <input type="text" name="route_name" id="routeNameInput"
                       class="form-control form-control-sm"
                       value="{{ old('route_name', $menuItem->route_name ?? '') }}"
                       placeholder="reports.sales.monthly">
                <button type="button" class="btn btn-outline-secondary" id="validateRouteBtn"
                        title="Validate route">
                    <i class="bi bi-check-circle"></i>
                </button>
            </div>
            <div id="routeValidationMsg" class="form-text"></div>
        </div>

        {{-- Description --}}
        <div class="col-12">
            <label class="form-label fw-semibold">Description <small class="text-muted">(optional tooltip)</small></label>
            <input type="text" name="description" class="form-control form-control-sm"
                   value="{{ old('description', $menuItem->description ?? '') }}"
                   placeholder="Short description shown as a tooltip or subtitle">
        </div>

        {{-- Role Restrictions --}}
        <div class="col-12">
            <label class="form-label fw-semibold">Item-Level Role Restrictions</label>
            <div class="border rounded p-3" style="background:#f8fafc;">
                <div class="form-text mb-2">
                    <i class="bi bi-info-circle me-1"></i>
                    Leave <strong>all unchecked</strong> so anyone who can see the module can access this item.
                    Check roles to restrict further.
                </div>
                <div class="d-flex flex-wrap gap-3">
                    @php $checkedRoles = $menuItem?->roles->pluck('id')->toArray() ?? []; @endphp
                    @foreach($roles as $role)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="roles[]" value="{{ $role->id }}"
                                   id="ir_{{ $role->id }}"
                                   {{ in_array($role->id, $checkedRoles) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ir_{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>{{-- /row --}}

    <hr class="my-4">

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-erp">
            <i class="bi bi-save me-1"></i>{{ isset($menuItem) ? 'Update' : 'Create' }} Menu Item
        </button>
        <a href="{{ route('admin.menu-items.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>

</form>
</div>
</div>
</div>

{{-- Quick Reference --}}
<div class="col-lg-4">
    <div class="card">
        <div class="card-header light"><i class="bi bi-lightbulb me-2"></i>Route Name Guide</div>
        <div class="card-body" style="font-size:.78rem;">
            <p class="text-muted">Convention: <code>type.module.name</code></p>
            <table class="table table-sm mb-0">
                <thead><tr><th>Type</th><th>Example Route</th></tr></thead>
                <tbody>
                    <tr><td><span class="badge bg-primary" style="font-size:.65rem;">Report</span></td>
                        <td><code style="font-size:.68rem;">reports.sales.monthly</code></td></tr>
                    <tr><td><span class="badge bg-warning text-dark" style="font-size:.65rem;">Form</span></td>
                        <td><code style="font-size:.68rem;">forms.sales.order-entry</code></td></tr>
                    <tr><td><span class="badge bg-secondary" style="font-size:.65rem;">Link</span></td>
                        <td><code style="font-size:.68rem;">dashboard</code></td></tr>
                </tbody>
            </table>
            <hr>
            <p class="text-muted mb-1"><strong>Workflow:</strong></p>
            <ol class="text-muted ps-3" style="font-size:.75rem;">
                <li>Build the controller + blade view</li>
                <li>Register the route in <code>web.php</code></li>
                <li>Add menu item here with that route name</li>
                <li>Assign role access as needed</li>
                <li>Activate → appears in sidebar</li>
            </ol>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
// Icon preview
document.getElementById('iconInput').addEventListener('input', function() {
    document.getElementById('iconPreview').className = 'bi ' + this.value;
});

// Route validation
document.getElementById('validateRouteBtn').addEventListener('click', async function() {
    const routeName = document.getElementById('routeNameInput').value.trim();
    const msg       = document.getElementById('routeValidationMsg');
    if (!routeName) { msg.innerHTML = '<span class="text-danger">Enter a route name first.</span>'; return; }

    msg.innerHTML = '<span class="text-muted"><i class="bi bi-hourglass-split me-1"></i>Checking…</span>';

    try {
        const resp = await fetch('{{ route('admin.menu-items.validate-route') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
            body: JSON.stringify({ route_name: routeName }),
        });
        const data = await resp.json();

        if (data.valid) {
            msg.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Valid → <code>${data.url}</code></span>`;
        } else {
            msg.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>${data.message}</span>`;
        }
    } catch(e) {
        msg.innerHTML = '<span class="text-danger">Validation request failed.</span>';
    }
});

// Hide route field for divider type
document.getElementById('typeSelect').addEventListener('change', function() {
    document.getElementById('routeField').style.opacity = this.value === 'divider' ? '.35' : '1';
});
</script>
@endpush

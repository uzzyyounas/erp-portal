<div class="row g-3">
    <div class="col-sm-7">
        <label class="form-label fw-semibold">Module Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control form-control-sm"
               placeholder="e.g. Finance" required>
    </div>
    <div class="col-sm-3">
        <label class="form-label fw-semibold">Sort Order</label>
        <input type="number" name="sort_order" class="form-control form-control-sm" value="0" min="0">
    </div>
    @if(isset($edit))
    <div class="col-sm-2 d-flex align-items-end pb-1">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="mod_active_cb">
            <label class="form-check-label" for="mod_active_cb">Active</label>
        </div>
    </div>
    @endif

    <div class="col-sm-8">
        <label class="form-label fw-semibold">Icon Class <span class="text-danger">*</span></label>
        <input type="text" name="icon" class="form-control form-control-sm"
               placeholder="e.g. bi-bank" required>
        <div class="form-text">Bootstrap Icons class name</div>
    </div>
    <div class="col-sm-2 d-flex align-items-end pb-2">
        <i class="bi bi-grid fs-3 icon-preview" style="color:#1a3a5c;"></i>
    </div>
    <div class="col-sm-2">
        <label class="form-label fw-semibold">Color</label>
        <div class="d-flex gap-2 align-items-center">
            <input type="color" name="color" class="form-control form-control-sm form-control-color"
                   value="#1a3a5c" style="width:44px;height:34px;padding:2px;">
            <div class="color-preview rounded" style="width:28px;height:28px;background:#1a3a5c;border:1px solid #dee2e6;"></div>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Description</label>
        <input type="text" name="description" class="form-control form-control-sm"
               placeholder="Short description (shown as tooltip)">
    </div>

    {{-- Role restrictions --}}
    <div class="col-12">
        <label class="form-label fw-semibold">Role Restrictions</label>
        <div class="border rounded p-3" style="background:#f8fafc;">
            <div class="form-text mb-2">
                <i class="bi bi-info-circle me-1"></i>
                Leave <strong>all unchecked</strong> to allow every role to see this module.
                Check specific roles to restrict access.
            </div>
            <div class="d-flex flex-wrap gap-3">
                @foreach($roles as $role)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="roles[]" value="{{ $role->id }}"
                               id="{{ isset($edit) ? 'edit_' : 'add_' }}role_{{ $role->id }}">
                        <label class="form-check-label" for="{{ isset($edit) ? 'edit_' : 'add_' }}role_{{ $role->id }}">
                            {{ $role->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Common icon picker --}}
    <div class="col-12">
        <small class="text-muted fw-semibold">Common Icons:</small>
        <div class="d-flex flex-wrap gap-2 mt-1">
            @foreach([
                'bi-bank','bi-cart-check-fill','bi-boxes','bi-people-fill',
                'bi-graph-up-arrow','bi-building','bi-truck','bi-clipboard-data',
                'bi-pie-chart-fill','bi-receipt','bi-journal-text','bi-gear-fill',
            ] as $ic)
                <span class="badge bg-light text-dark border" style="cursor:pointer;font-size:.72rem;"
                      onclick="this.closest('form').querySelector('[name=icon]').value='{{$ic}}';
                               this.closest('form').querySelector('.icon-preview').className='bi {{$ic}} fs-3 icon-preview';">
                    <i class="bi {{ $ic }}"></i> {{ $ic }}
                </span>
            @endforeach
        </div>
    </div>
</div>

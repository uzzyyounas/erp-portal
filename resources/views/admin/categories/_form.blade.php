<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control form-control-sm" required>
    </div>
    <div class="col-sm-8">
        <label class="form-label fw-semibold">Icon Class <span class="text-danger">*</span></label>
        <input type="text" name="icon" class="form-control form-control-sm"
               placeholder="e.g. bi-cart-check-fill" required>
        <div class="form-text">Bootstrap Icons class name</div>
    </div>
    <div class="col-sm-4 d-flex align-items-end pb-1">
        <i class="{{ isset($edit) ? '' : 'bi-folder' }} fs-3 icon-preview {{ isset($edit) ? '' : 'bi' }}"
           id="{{ isset($edit) ? 'editIconPreview' : 'addIconPreview' }}"
           style="color:#1a3a5c;"></i>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Description</label>
        <input type="text" name="description" class="form-control form-control-sm">
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Sort Order</label>
        <input type="number" name="sort_order" class="form-control form-control-sm" value="0" min="0">
    </div>
    @if(isset($edit))
        <div class="col-sm-6 d-flex align-items-end">
            <div class="form-check pb-1">
                <input class="form-check-input" type="checkbox" name="is_active"
                       id="editCatActive" value="1">
                <label class="form-check-label" for="editCatActive">Active</label>
            </div>
        </div>
    @endif
</div>

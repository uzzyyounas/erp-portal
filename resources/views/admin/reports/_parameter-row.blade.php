<div class="border rounded p-3 mb-2 param-row bg-light">
    <div class="row g-2 align-items-start">
        <div class="col-sm-3">
            <label class="form-label fw-semibold" style="font-size:.75rem;">Field Name <span class="text-danger">*</span></label>
            <input type="text" name="parameters[{{ $i }}][name]"
                   class="form-control form-control-sm param-name-input"
                   value="{{ $p->name ?? '' }}"
                   placeholder="e.g. salesman_id" required>
        </div>
        <div class="col-sm-3">
            <label class="form-label fw-semibold" style="font-size:.75rem;">Label <span class="text-danger">*</span></label>
            <input type="text" name="parameters[{{ $i }}][label]"
                   class="form-control form-control-sm"
                   value="{{ $p->label ?? '' }}"
                   placeholder="e.g. Salesman" required>
        </div>
        <div class="col-sm-2">
            <label class="form-label fw-semibold" style="font-size:.75rem;">Type</label>
            <select name="parameters[{{ $i }}][type]" class="form-select form-select-sm param-type-select">
                @foreach(['text','number','date','date_range','select','multiselect','checkbox','company'] as $t)
                    <option value="{{ $t }}" {{ (isset($p) && $p->type === $t) ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-2">
            <label class="form-label fw-semibold" style="font-size:.75rem;">Default</label>
            <input type="text" name="parameters[{{ $i }}][default_value]"
                   class="form-control form-control-sm"
                   value="{{ $p->default_value ?? '' }}"
                   placeholder="Optional">
        </div>
        <div class="col-sm-1 d-flex align-items-end pb-1">
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                       name="parameters[{{ $i }}][is_required]" value="1"
                    {{ (isset($p) && $p->is_required) ? 'checked' : '' }}>
                <label class="form-check-label" style="font-size:.75rem;">Req.</label>
            </div>
        </div>
        <div class="col-sm-1 d-flex align-items-end justify-content-end pb-1">
            <button type="button" class="btn btn-xs btn-outline-danger remove-param-btn">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Options source + query --}}
        <div class="col-sm-2 options-source-col">
            <label class="form-label fw-semibold" style="font-size:.75rem;">Options Source</label>
            <select name="parameters[{{ $i }}][options_source]" class="form-select form-select-sm">
                <option value="static" {{ (isset($p) && $p->options_source === 'static') ? 'selected' : '' }}>Static JSON</option>
                <option value="sql"    {{ (isset($p) && $p->options_source === 'sql') ? 'selected' : '' }}>SQL Query</option>
            </select>
        </div>
        <div class="col-sm-7 options-col">
            <label class="form-label fw-semibold" style="font-size:.75rem;">
                Options
                <small class="text-muted">
                    Static: <code>[{"value":"1","label":"One"}]</code> &nbsp;|&nbsp;
                    SQL: <code>SELECT id as value, name as label FROM `{company_prefix}_table`</code>
                </small>
            </label>
            <input type="text" name="parameters[{{ $i }}][options]"
                   class="form-control form-control-sm"
                   value="{{ $p->options ?? '' }}"
                   placeholder="Leave blank for text/date types">
        </div>

        {{-- Depends On (cascading) --}}
        <div class="col-sm-3 depends-on-col">
            <label class="form-label fw-semibold d-flex align-items-center gap-1" style="font-size:.75rem;">
                Depends On
                <span class="badge bg-info text-dark" style="font-size:.55rem;">Cascading</span>
                <i class="bi bi-question-circle text-muted"
                   data-bs-toggle="tooltip"
                   title="Enter the Field Name of another parameter above. When that selection changes, this dropdown reloads from its SQL using :parent_value as the filter."
                   style="cursor:help;font-size:.8rem;"></i>
            </label>
            <input type="text" name="parameters[{{ $i }}][depends_on]"
                   class="form-control form-control-sm"
                   value="{{ $p->depends_on ?? '' }}"
                   placeholder="e.g. salesman_id (optional)">
            <div style="font-size:.65rem;color:#6c757d;margin-top:2px;">
                Use <code>:parent_value</code> in SQL above
            </div>
        </div>
    </div>
</div>

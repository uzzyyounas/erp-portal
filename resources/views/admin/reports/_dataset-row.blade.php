@php
    $prefix = "datasets[{$di}]";
    $dsId   = $ds->id ?? '__DS_ID__';
@endphp
<div class="dataset-row border-bottom p-3" data-ds="{{ $di }}">
    <input type="hidden" name="{{ $prefix }}[id]" value="{{ $dsId }}">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-semibold text-primary" style="font-size:.85rem;">
            <i class="bi bi-table me-1"></i>Dataset #<span class="ds-num">{{ $di + 1 }}</span>
        </span>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDatasetRow(this)">
            <i class="bi bi-trash"></i>
        </button>
    </div>
    <div class="row g-2 mb-2">
        <div class="col-md-5">
            <label class="form-label" style="font-size:.75rem;font-weight:600;">Table Title</label>
            <input type="text" name="{{ $prefix }}[title]" class="form-control form-control-sm"
                   value="{{ old($prefix.'.title', $ds->title ?? '') }}"
                   placeholder="e.g. Regular Customers Aging">
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.75rem;font-weight:600;">Date Label</label>
            <input type="text" name="{{ $prefix }}[date_label]" class="form-control form-control-sm"
                   value="{{ old($prefix.'.date_label', $ds->date_label ?? 'AS ON') }}"
                   placeholder="AS ON">
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.75rem;font-weight:600;">Date Value / Param</label>
            <input type="text" name="{{ $prefix }}[date_value]" class="form-control form-control-sm"
                   value="{{ old($prefix.'.date_value', $ds->date_value ?? '') }}"
                   placeholder=":as_on_date or 30-JUN-24">
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.75rem;font-weight:600;">Group Column (SQL alias)</label>
            <input type="text" name="{{ $prefix }}[group_column]" class="form-control form-control-sm"
                   value="{{ old($prefix.'.group_column', $ds->group_column ?? '') }}"
                   placeholder="salesman_name">
        </div>
        <div class="col-md-1 d-flex align-items-end pb-1">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="{{ $prefix }}[show_title]"
                       id="ds_show_title_{{ $di }}" value="1"
                       {{ old($prefix.'.show_title', $ds->show_title ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="ds_show_title_{{ $di }}" style="font-size:.72rem;">Show title</label>
            </div>
        </div>
    </div>
    <div class="mb-2">
        <label class="form-label" style="font-size:.75rem;font-weight:600;">SQL Query for this Dataset</label>
        <textarea name="{{ $prefix }}[sql_query]" class="form-control form-control-sm"
                  rows="5" style="font-family:monospace;font-size:.72rem;"
                  placeholder="SELECT&#10;    sm.salesman_name,&#10;    SUM(t.amount) AS total_dues,&#10;    SUM(CASE WHEN DATEDIFF(:as_on_date, t.due_date) BETWEEN 20 AND 30 THEN t.amount ELSE 0 END) AS `20 - 30`&#10;FROM `{company_prefix}_debtor_trans` t&#10;JOIN `{company_prefix}_salesmen` sm ON sm.id = t.salesman_id&#10;WHERE t.due_date &lt;= :as_on_date&#10;GROUP BY sm.salesman_name, sm.id&#10;ORDER BY sm.salesman_name">{{ old($prefix.'.sql_query', $ds->sql_query ?? '') }}</textarea>
        <div class="d-flex gap-2 mt-1">
            <small class="text-muted">Use <code>{company_prefix}</code> for table prefix, <code>:param_name</code> for parameters.
            Row 0 = group/salesman. Row 1 = total. Rows 2+ = aging buckets.</small>
            <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" style="font-size:.7rem;"
                    onclick="validateDatasetSql(this)">
                <i class="bi bi-check-circle me-1"></i>Validate SQL
            </button>
        </div>
    </div>
    <div class="sql-result-{{ $di }}"></div>
</div>

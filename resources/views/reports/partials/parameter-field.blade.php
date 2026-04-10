{{-- Renders a single dynamic report parameter field --}}
{{-- $param = ReportParameter model --}}
{{-- $report = Report model (needed for cascading AJAX URL) --}}
@php
    $colClass   = in_array($param->type, ['date_range']) ? 'col-12' : 'col-sm-6';
    $hasDepends = !empty($param->depends_on);
    $isCascading = $hasDepends && in_array($param->type, ['select','multiselect']);
@endphp

<div class="{{ $colClass }} param-field-wrapper"
     data-param="{{ $param->name }}"
     data-depends-on="{{ $param->depends_on ?? '' }}"
     data-options-url="{{ $isCascading ? route('reports.parameter-options', [$report, $param->name]) : '' }}">

    <label class="form-label fw-semibold" style="font-size:.82rem;">
        {{ $param->label }}
        @if($param->is_required) <span class="text-danger">*</span> @endif
        @if($isCascading)
            <span class="badge bg-info text-dark ms-1" style="font-size:.6rem;" title="Reloads when '{{ $param->depends_on }}' changes">
                <i class="bi bi-link-45deg"></i> Cascading
            </span>
        @endif
    </label>

    @switch($param->type)

        @case('company')
            <select name="{{ $param->name }}"
                    id="param_{{ $param->name }}"
                    class="form-select form-select-sm"
                    {{ $param->is_required ? 'required' : '' }}
                    onchange="syncCompanyParam(this.value)">
                @foreach($param->resolved_options as $opt)
                    <option value="{{ $opt['value'] }}"
                        {{ (old($param->name, $param->default_value) == $opt['value']) ? 'selected' : '' }}>
                        {{ $opt['label'] }}
                    </option>
                @endforeach
            </select>
            @break

        @case('select')
            <div class="input-group input-group-sm">
                <select name="{{ $param->name }}"
                        id="param_{{ $param->name }}"
                        class="form-select form-select-sm {{ $isCascading ? 'cascading-child' : '' }}"
                    {{ $param->is_required ? 'required' : '' }}
                    {{ $isCascading ? 'disabled' : '' }}>
                    <option value="">
                        {{ $isCascading ? '— Select '.$param->depends_on.' first —' : '— Select —' }}
                    </option>
                    @if(!$isCascading)
                        @foreach($param->resolved_options as $opt)
                            <option value="{{ $opt['value'] }}"
                                {{ old($param->name, $param->default_value) == $opt['value'] ? 'selected' : '' }}>
                                {{ $opt['label'] }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @if($isCascading)
                    <span class="input-group-text cascading-spinner d-none" id="spinner_{{ $param->name }}"
                          style="background:#f8fafc;">
                        <span class="spinner-border spinner-border-sm text-secondary" style="width:.75rem;height:.75rem;"></span>
                    </span>
                @endif
            </div>
            @break

        @case('multiselect')
            <div>
                @if($isCascading)
                    <div class="text-muted fst-italic mb-1" style="font-size:.75rem;" id="cascade_hint_{{ $param->name }}">
                        <i class="bi bi-arrow-up-circle me-1"></i>Select <strong>{{ $param->depends_on }}</strong> above to load options
                    </div>
                @endif
                <select name="{{ $param->name }}[]"
                        id="param_{{ $param->name }}"
                        class="form-select form-select-sm {{ $isCascading ? 'cascading-child' : '' }}"
                        multiple size="4"
                    {{ $param->is_required ? 'required' : '' }}
                    {{ $isCascading ? 'disabled' : '' }}>
                    @if(!$isCascading)
                        @foreach($param->resolved_options as $opt)
                            <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            @break

        @case('date')
            <input type="date" name="{{ $param->name }}"
                   id="param_{{ $param->name }}"
                   class="form-control form-control-sm"
                   value="{{ old($param->name, $param->default_value ?? date('Y-m-d')) }}"
                {{ $param->is_required ? 'required' : '' }}>
            @break

        @case('date_range')
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label text-muted" style="font-size:.75rem;">From</label>
                    <input type="date" name="{{ $param->name }}_from"
                           class="form-control form-control-sm"
                           value="{{ old($param->name.'_from', date('Y-m-01')) }}"
                        {{ $param->is_required ? 'required' : '' }}>
                </div>
                <div class="col-6">
                    <label class="form-label text-muted" style="font-size:.75rem;">To</label>
                    <input type="date" name="{{ $param->name }}_to"
                           class="form-control form-control-sm"
                           value="{{ old($param->name.'_to', date('Y-m-d')) }}"
                        {{ $param->is_required ? 'required' : '' }}>
                </div>
            </div>
            @break

        @case('number')
            <input type="number" name="{{ $param->name }}"
                   id="param_{{ $param->name }}"
                   class="form-control form-control-sm"
                   value="{{ old($param->name, $param->default_value) }}"
                   placeholder="{{ $param->placeholder }}"
                {{ $param->is_required ? 'required' : '' }}>
            @break

        @case('checkbox')
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox"
                       name="{{ $param->name }}" value="1" id="param_{{ $param->name }}"
                    {{ old($param->name, $param->default_value) ? 'checked' : '' }}>
                <label class="form-check-label" for="param_{{ $param->name }}">
                    {{ $param->placeholder ?: 'Yes' }}
                </label>
            </div>
            @break

        @default
            <input type="text" name="{{ $param->name }}"
                   id="param_{{ $param->name }}"
                   class="form-control form-control-sm"
                   value="{{ old($param->name, $param->default_value) }}"
                   placeholder="{{ $param->placeholder }}"
                {{ $param->is_required ? 'required' : '' }}>
    @endswitch

    {{-- Validation error --}}
    @error($param->name)
    <div class="text-danger" style="font-size:.75rem;">{{ $message }}</div>
    @enderror
</div>

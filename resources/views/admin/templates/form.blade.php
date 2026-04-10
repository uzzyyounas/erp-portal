@extends('layouts.app')
@section('title', isset($template) ? 'Edit Template' : 'New Template')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.templates.index') }}">Templates</a></li>
    <li class="breadcrumb-item active">{{ isset($template) ? 'Edit: '.$template->name : 'New Template' }}</li>
@endsection

@section('content')
@php
    $cfg = isset($template) ? $template->getEffectiveConfig() : $defaults;
    $co  = \App\Models\CompanySetting::all_settings();
@endphp

<form method="POST"
      action="{{ isset($template) ? route('admin.templates.update', $template) : route('admin.templates.store') }}"
      id="templateForm">
    @csrf
    @if(isset($template)) @method('PUT') @endif

<div class="row g-3">

{{-- ── LEFT: All settings ──────────────────────────────────────────── --}}
<div class="col-xl-7 col-lg-6">

    {{-- Tabs nav --}}
    <ul class="nav nav-tabs mb-3" id="designerTabs">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabInfo"><i class="bi bi-info-circle me-1"></i>Info</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabHeader"><i class="bi bi-layout-text-window-reverse me-1"></i>Header</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabColours"><i class="bi bi-palette me-1"></i>Colours</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabTable"><i class="bi bi-table me-1"></i>Table</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabFooter"><i class="bi bi-layout-text-window me-1"></i>Footer</a></li>
    </ul>

    <div class="tab-content">

        {{-- ═══ TAB: INFO ════════════════════════════════════════════ --}}
        <div class="tab-pane fade show active" id="tabInfo">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-8">
                            <label class="form-label fw-semibold">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                   value="{{ old('name', $template->name ?? '') }}"
                                   placeholder="e.g. Blue Corporate A4 Landscape">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Layout Type <span class="text-danger">*</span></label>
                            <select name="layout" class="form-select" id="layoutSelect" onchange="onLayoutChange(this.value);updatePreview()">
                                @foreach($layouts as $key => $info)
                                    <option value="{{ $key }}" {{ old('layout', $template->layout ?? 'tabular') === $key ? 'selected' : '' }}>
                                        {{ $info['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <input type="text" name="description" class="form-control form-control-sm"
                                   value="{{ old('description', $template->description ?? '') }}"
                                   placeholder="Short note about when to use this template">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control form-control-sm"
                                   value="{{ old('sort_order', $template->sort_order ?? 0) }}" min="0">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info py-2 px-3 d-flex align-items-start gap-2" style="font-size:.78rem;">
                                <i class="bi bi-magic fs-5 flex-shrink-0"></i>
                                <div>
                                    <strong>Visual Designer:</strong> Use the
                                    <a href="#" onclick="window.open('{{ route('admin.templates.index') }}','_blank')" class="alert-link">Report Designer widget</a>
                                    in your admin panel to design the template, then copy the exported JSON and paste it below.
                                    This overrides all other settings.
                                </div>
                            </div>
                            <label class="form-label fw-semibold" style="font-size:.78rem;">
                                Paste Designer JSON <span class="text-muted fw-normal">(optional — overrides all settings above)</span>
                            </label>
                            <textarea name="raw_config_json" class="form-control form-control-sm"
                                      style="height:90px;font-family:monospace;font-size:.72rem;"
                                      placeholder='Paste the JSON exported from the visual designer tool, e.g. {"header_style":"full","logo_position":"left",...}'></textarea>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Paper Size</label>
                            <select name="config_paper_size" class="form-select form-select-sm" onchange="updatePreview()">
                                @foreach(['A4','A3','Letter','Legal'] as $s)
                                    <option {{ $cfg['paper_size']==$s?'selected':'' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Orientation</label>
                            <select name="config_orientation" class="form-select form-select-sm" onchange="updatePreview()">
                                @foreach(['landscape','portrait'] as $o)
                                    <option {{ $cfg['orientation']==$o?'selected':'' }}>{{ $o }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Font Size</label>
                            <select name="config_font_size" class="form-select form-select-sm" onchange="updatePreview()">
                                @foreach(['small'=>'Small (7pt)','medium'=>'Medium (8pt)','large'=>'Large (9.5pt)'] as $v=>$l)
                                    <option value="{{ $v }}" {{ $cfg['font_size']==$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4 d-flex align-items-end pb-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                       {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ TAB: HEADER DESIGNER ══════════════════════════════════ --}}
        <div class="tab-pane fade" id="tabHeader">
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-layout-text-window-reverse me-2"></i>Header Style</div>
                <div class="card-body">

                    {{-- Header style cards --}}
                    <label class="form-label fw-semibold mb-2">Header Layout</label>
                    <div class="row g-2 mb-3" id="headerStyleCards">
                        @php
                            $headerStyles = [
                                'none'     => ['None',          'No header at all',                   'bi-x-square'],
                                'simple'   => ['Simple',        'Report title only',                  'bi-text-left'],
                                'full'     => ['Full',          'Logo + Company info + Title',        'bi-layout-text-window-reverse'],
                                'centered' => ['Centered',      'Logo centered, info below',          'bi-align-center'],
                                'logo-only'=> ['Logo Only',     'Just the logo banner',               'bi-image'],
                            ];
                        @endphp
                        @foreach($headerStyles as $val => [$label, $desc, $icon])
                        @php $isSel = ($cfg['header_style'] ?? 'full') === $val; @endphp
                        <div class="col-sm-4 col-md-4">
                            <div class="header-style-card {{ $isSel ? 'selected' : '' }}"
                                 data-value="{{ $val }}"
                                 onclick="selectHeaderStyle(this)"
                                 style="cursor:pointer;border:2px solid {{ $isSel ? '#2d6a9f' : '#dee2e6' }};
                                        border-radius:.5rem;padding:10px;text-align:center;
                                        {{ $isSel ? 'background:#f0f4ff;' : 'background:#fff;' }}">
                                <i class="bi {{ $icon }} d-block mb-1" style="font-size:1.4rem;color:{{ $isSel ? '#2d6a9f' : '#adb5bd' }};"></i>
                                <div style="font-weight:600;font-size:.78rem;">{{ $label }}</div>
                                <div style="font-size:.65rem;color:#6c757d;">{{ $desc }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="config_header_style" id="headerStyleInput" value="{{ $cfg['header_style'] ?? 'full' }}">

                    {{-- Logo settings --}}
                    <div id="logoSection" class="{{ ($cfg['header_style']??'full') === 'none' || ($cfg['header_style']??'full') === 'simple' ? 'd-none' : '' }}">
                        <hr class="my-3">
                        <label class="form-label fw-semibold">Logo</label>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <label class="form-label" style="font-size:.75rem;">Position</label>
                                <select name="config_logo_position" class="form-select form-select-sm" onchange="updatePreview()">
                                    @foreach(['left'=>'Left','right'=>'Right','center'=>'Center','none'=>'No Logo'] as $v=>$l)
                                        <option value="{{ $v }}" {{ ($cfg['logo_position']??'left')==$v?'selected':'' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label" style="font-size:.75rem;">Height (px)</label>
                                <input type="number" name="config_logo_height" class="form-control form-control-sm"
                                       value="{{ $cfg['logo_height']??55 }}" min="20" max="120" onchange="updatePreview()">
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label" style="font-size:.75rem;">Custom Logo URL</label>
                                <input type="text" name="config_logo_url" class="form-control form-control-sm"
                                       value="{{ $cfg['logo_url']??'' }}"
                                       placeholder="Blank = company setting" onchange="updatePreview()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Company info toggles --}}
            <div class="card mb-3" id="companyInfoSection">
                <div class="card-header"><i class="bi bi-building me-2"></i>Company Info in Header</div>
                <div class="card-body">
                    <div class="row g-2">
                        @php
                            $companyToggles = [
                                'header_show_company_name' => 'Company Name',
                                'header_show_tagline'      => 'Tagline / Slogan',
                                'header_show_address'      => 'Address',
                                'header_show_phone'        => 'Phone',
                                'header_show_email'        => 'Email',
                                'header_show_ntn'          => 'NTN / Tax No.',
                            ];
                        @endphp
                        @foreach($companyToggles as $key => $label)
                        <div class="col-sm-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="config_{{ $key }}" id="cfg_{{ $key }}" value="1"
                                       {{ $cfg[$key]??false ? 'checked' : '' }} onchange="updatePreview()">
                                <label class="form-check-label" for="cfg_{{ $key }}" style="font-size:.8rem;">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-sm-4">
                            <label class="form-label" style="font-size:.75rem;">Company Name Size</label>
                            <select name="config_header_company_name_size" class="form-select form-select-sm" onchange="updatePreview()">
                                @foreach(['small'=>'Small','medium'=>'Medium','large'=>'Large'] as $v=>$l)
                                    <option value="{{ $v }}" {{ ($cfg['header_company_name_size']??'large')==$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Report title block --}}
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-card-heading me-2"></i>Report Title Block</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-4">
                            <label class="form-label" style="font-size:.75rem;">Title Alignment</label>
                            <select name="config_header_title_align" class="form-select form-select-sm" onchange="updatePreview()">
                                @foreach(['left','center','right'] as $v)
                                    <option {{ ($cfg['header_title_align']??'left')==$v?'selected':'' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label" style="font-size:.75rem;">Title Size</label>
                            <select name="config_header_title_size" class="form-select form-select-sm" onchange="updatePreview()">
                                @foreach(['small'=>'Small','medium'=>'Medium','large'=>'Large'] as $v=>$l)
                                    <option value="{{ $v }}" {{ ($cfg['header_title_size']??'large')==$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label" style="font-size:.75rem;">Date Format</label>
                            <select name="config_header_date_format" class="form-select form-select-sm" onchange="updatePreview()">
                                @foreach(['d M Y'=>'13 Mar 2026','d/m/Y'=>'13/03/2026','Y-m-d'=>'2026-03-13','M j, Y'=>'Mar 13, 2026'] as $v=>$l)
                                    <option value="{{ $v }}" {{ ($cfg['header_date_format']??'d M Y')==$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        @php
                            $titleToggles = [
                                'header_show_report_title'  => 'Show Report Title',
                                'header_show_category'      => 'Show Category',
                                'header_show_print_date'    => 'Show Print Date',
                                'header_show_generated_by'  => 'Show Generated By',
                            ];
                        @endphp
                        @foreach($titleToggles as $key => $label)
                        <div class="col-sm-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="config_{{ $key }}" id="cfg_{{ $key }}" value="1"
                                       {{ $cfg[$key]??true ? 'checked' : '' }} onchange="updatePreview()">
                                <label class="form-check-label" for="cfg_{{ $key }}" style="font-size:.78rem;">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-sm-4">
                            <label class="form-label" style="font-size:.75rem;">Header Divider</label>
                            <select name="config_header_divider" class="form-select form-select-sm" onchange="updatePreview()">
                                @foreach(['none'=>'None','thin'=>'Thin line','thick'=>'Thick line (default)','double'=>'Double line','colored'=>'Colored band'] as $v=>$l)
                                    <option value="{{ $v }}" {{ ($cfg['header_divider']??'thick')==$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label" style="font-size:.75rem;">Divider Color</label>
                            <div class="input-group input-group-sm">
                                <input type="color" name="config_header_divider_color"
                                       class="form-control form-control-color"
                                       value="{{ $cfg['header_divider_color'] ?: $cfg['color_accent'] }}"
                                       style="width:40px;" onchange="updatePreview()">
                                <span class="input-group-text" style="font-size:.72rem;">blank = accent</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label" style="font-size:.75rem;">Watermark Text</label>
                            <input type="text" name="config_watermark_text" class="form-control form-control-sm"
                                   value="{{ $cfg['watermark_text']??'' }}"
                                   placeholder="e.g. DRAFT or CONFIDENTIAL" onchange="updatePreview()">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Params bar --}}
            <div class="card">
                <div class="card-header"><i class="bi bi-funnel me-2"></i>Parameters Bar</div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-4">
                            <label class="form-label" style="font-size:.75rem;">Style</label>
                            <select name="config_params_bar_style" class="form-select form-select-sm" onchange="updatePreview()">
                                @foreach(['banner'=>'Banner (default)','inline'=>'Inline','table'=>'Mini table','none'=>'None'] as $v=>$l)
                                    <option value="{{ $v }}" {{ ($cfg['params_bar_style']??'banner')==$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label" style="font-size:.75rem;">Position</label>
                            <select name="config_params_bar_position" class="form-select form-select-sm">
                                @foreach(['below-header'=>'Below Header','above-table'=>'Above Table'] as $v=>$l)
                                    <option value="{{ $v }}" {{ ($cfg['params_bar_position']??'below-header')==$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ TAB: COLOURS ══════════════════════════════════════════ --}}
        <div class="tab-pane fade" id="tabColours">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        @php
                            $colourFields = [
                                'color_page_header'  => 'Page Header / Letterhead',
                                'color_accent'       => 'Accent / Highlight',
                                'color_header_bg'    => 'Table Header Background',
                                'color_header_text'  => 'Table Header Text',
                                'color_row_even'     => 'Alt Row (Even)',
                                'color_row_odd'      => 'Row Odd / Default',
                                'color_subtotal_bg'  => 'Subtotal Row',
                                'color_total_bg'     => 'Grand Total Background',
                                'color_total_text'   => 'Grand Total Text',
                                'color_border'       => 'Border / Separator',
                            ];
                        @endphp
                        @foreach($colourFields as $key => $label)
                        <div class="col-sm-6">
                            <label class="form-label" style="font-size:.78rem;font-weight:600;">{{ $label }}</label>
                            <div class="input-group input-group-sm">
                                <input type="color" name="config_{{ $key }}"
                                       class="form-control form-control-color colour-picker"
                                       data-key="{{ $key }}"
                                       value="{{ $cfg[$key] ?? '#000000' }}"
                                       style="width:40px;padding:2px;cursor:pointer;">
                                <input type="text" class="form-control colour-hex"
                                       data-key="{{ $key }}"
                                       value="{{ $cfg[$key] ?? '#000000' }}"
                                       maxlength="7" style="font-family:monospace;font-size:.78rem;">
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Presets --}}
                    <label class="form-label" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.4pt;color:#6c757d;font-weight:600;">Quick Presets</label>
                    <div class="d-flex gap-2 flex-wrap">
                        @php
                        $presets = [
                            'Navy & Gold'   =>['#1a3a5c','#e8a020','#1a3a5c','#ffffff','#f5f8ff','#ffffff','#dce9f7','#1a3a5c','#ffffff','#dee2e6'],
                            'Forest Green'  =>['#1a5c2a','#3cb371','#1a5c2a','#ffffff','#f0fff4','#ffffff','#d4edda','#1a5c2a','#ffffff','#c3e6cb'],
                            'Deep Purple'   =>['#4a1a7c','#9b59b6','#4a1a7c','#ffffff','#f9f0ff','#ffffff','#e9d8fd','#4a1a7c','#ffffff','#d8b4fe'],
                            'Charcoal'      =>['#2c2c2c','#e8a020','#2c2c2c','#ffffff','#f7f7f7','#ffffff','#e0e0e0','#2c2c2c','#ffffff','#cccccc'],
                            'Ocean Blue'    =>['#0369a1','#0ea5e9','#0369a1','#ffffff','#f0f9ff','#ffffff','#bae6fd','#0369a1','#ffffff','#7dd3fc'],
                            'Slate & Coral' =>['#374151','#ef4444','#374151','#ffffff','#fff7ed','#ffffff','#fed7aa','#374151','#ffffff','#e5e7eb'],
                            'Rose Gold'     =>['#9f1239','#f43f5e','#9f1239','#ffffff','#fff1f2','#ffffff','#fecdd3','#9f1239','#ffffff','#fda4af'],
                            'Teal'          =>['#0f766e','#14b8a6','#0f766e','#ffffff','#f0fdfa','#ffffff','#ccfbf1','#0f766e','#ffffff','#99f6e4'],
                        ];
                        $presetKeys=['color_page_header','color_accent','color_header_bg','color_header_text','color_row_even','color_row_odd','color_subtotal_bg','color_total_bg','color_total_text','color_border'];
                        @endphp
                        @foreach($presets as $pname => $colours)
                        <button type="button" class="btn btn-sm btn-light preset-btn"
                                data-preset="{{ json_encode(array_combine($presetKeys, $colours)) }}"
                                style="font-size:.72rem;">
                            <span style="display:inline-flex;gap:2px;margin-right:4px;">
                                @foreach(array_slice($colours, 0, 3) as $hex)
                                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:{{ $hex }};border:1px solid rgba(0,0,0,.1);"></span>
                                @endforeach
                            </span>{{ $pname }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ TAB: TABLE ══════════════════════════════════════════════ --}}
        <div class="tab-pane fade" id="tabTable">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Column header style --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Column Header Style</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach(['filled'=>'Filled','outline'=>'Outline','minimal'=>'Minimal','gradient'=>'Gradient'] as $v=>$l)
                                @php $isSel = ($cfg['col_header_style']??'filled')===$v; @endphp
                                <div class="col-header-style-card {{ $isSel?'selected':'' }}"
                                     data-value="{{ $v }}"
                                     onclick="selectColHeaderStyle(this)"
                                     style="cursor:pointer;padding:8px 14px;border-radius:.4rem;
                                            border:2px solid {{ $isSel?'#2d6a9f':'#dee2e6' }};
                                            background:{{ $isSel?'#f0f4ff':'#fff' }};
                                            font-size:.78rem;font-weight:600;text-align:center;">
                                    {{ $l }}
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="config_col_header_style" id="colHeaderStyleInput" value="{{ $cfg['col_header_style']??'filled' }}">
                        </div>

                        {{-- Feature toggles --}}
                        @php
                            $tableFeatures = [
                                'show_row_numbers'    => ['Row Numbers (#)',        'bi-list-ol'],
                                'zebra_rows'          => ['Zebra / Alt Row Colours','bi-table'],
                                'show_column_borders' => ['Vertical Column Borders','bi-layout-three-columns'],
                                'show_subtotals'      => ['Subtotal Row per Group', 'bi-dash-square'],
                                'show_totals_row'     => ['Grand Total Row',        'bi-plus-square-fill'],
                                'show_record_count'   => ['Record Count Caption',   'bi-123'],
                            ];
                        @endphp
                        @foreach($tableFeatures as $key => [$label, $icon])
                        <div class="col-sm-6">
                            <div class="form-check d-flex align-items-center gap-2">
                                <input class="form-check-input" type="checkbox"
                                       name="config_{{ $key }}" id="cfg_{{ $key }}" value="1"
                                       {{ $cfg[$key]??true ? 'checked' : '' }} onchange="updatePreview()">
                                <label class="form-check-label" for="cfg_{{ $key }}" style="font-size:.8rem;">
                                    <i class="bi {{ $icon }} me-1 text-muted"></i>{{ $label }}
                                </label>
                            </div>
                        </div>
                        @endforeach

                        {{-- Group column index (for grouped layout) --}}
                        <div id="groupColRow" style="display:{{ ($template->layout??'tabular')==='grouped'?'block':'none' }};" class="col-12">
                            <label class="form-label fw-semibold" style="font-size:.8rem;">Group by column index</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" name="config_group_col_index" class="form-control form-control-sm"
                                       style="width:80px;" min="0" max="20"
                                       value="{{ $cfg['group_col_index']??0 }}">
                                <small class="text-muted">0 = first column</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ TAB: FOOTER ════════════════════════════════════════════ --}}
        <div class="tab-pane fade" id="tabFooter">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $footerZoneOptions = ['company'=>'Company Name','report_name'=>'Report Name','datetime'=>'Date & Time','page'=>'Page Number','custom'=>'Custom Text','blank'=>'Blank'];
                        @endphp
                        @foreach(['footer_left'=>'Footer Left','footer_center'=>'Footer Center','footer_right'=>'Footer Right'] as $key=>$label)
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:.78rem;">{{ $label }}</label>
                            <select name="config_{{ $key }}" class="form-select form-select-sm" onchange="updatePreview()">
                                @foreach($footerZoneOptions as $v=>$l)
                                    <option value="{{ $v }}" {{ ($cfg[$key]??'')==$v?'selected':'' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endforeach

                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:.78rem;">Custom Texts (used when zone is set to "Custom Text")</label>
                            <div class="row g-2">
                                @foreach(['footer_custom_left'=>'Left','footer_custom_center'=>'Center','footer_custom_right'=>'Right'] as $key=>$label)
                                <div class="col-sm-4">
                                    <input type="text" name="config_{{ $key }}" class="form-control form-control-sm"
                                           value="{{ $cfg[$key]??'' }}" placeholder="{{ $label }} custom text">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="config_footer_confidential" id="cfg_footer_confidential" value="1"
                                       {{ $cfg['footer_confidential']??true ? 'checked' : '' }} onchange="updatePreview()">
                                <label class="form-check-label" for="cfg_footer_confidential" style="font-size:.8rem;">
                                    Show CONFIDENTIAL label
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="config_footer_show_divider" id="cfg_footer_show_divider" value="1"
                                       {{ $cfg['footer_show_divider']??true ? 'checked' : '' }} onchange="updatePreview()">
                                <label class="form-check-label" for="cfg_footer_show_divider" style="font-size:.8rem;">
                                    Show footer divider line
                                </label>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:.78rem;">Watermark Text</label>
                            <input type="text" name="config_watermark_text" class="form-control form-control-sm"
                                   value="{{ $cfg['watermark_text']??'' }}"
                                   placeholder="e.g. DRAFT" onchange="updatePreview()">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold" style="font-size:.78rem;">Watermark Opacity %</label>
                            <input type="number" name="config_watermark_opacity" class="form-control form-control-sm"
                                   value="{{ $cfg['watermark_opacity']??8 }}" min="1" max="30" onchange="updatePreview()">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end tab-content --}}
</div>

{{-- ── RIGHT: Live Preview ──────────────────────────────────────────── --}}
<div class="col-xl-5 col-lg-6">
    <div class="sticky-top" style="top:70px;">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-eye me-2"></i>Live Preview</span>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-xs btn-light" onclick="setPreviewMode('pdf')" id="pvModePdf">
                        <i class="bi bi-file-pdf me-1"></i>PDF View
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setPreviewMode('web')" id="pvModeWeb">
                        <i class="bi bi-browser-chrome me-1"></i>Web View
                    </button>
                </div>
            </div>
            <div class="card-body p-0" id="previewPane"
                 style="min-height:500px;background:#f8f8f8;overflow:auto;">
                <div style="width:595px;min-height:420px;background:#fff;margin:12px auto;
                            box-shadow:0 2px 12px rgba(0,0,0,.12);padding:18px;"
                     id="previewPaper"></div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-erp flex-grow-1">
                <i class="bi bi-save me-2"></i>{{ isset($template) ? 'Update Template' : 'Save Template' }}
            </button>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>

</div>{{-- end row --}}
</form>
@endsection

@push('styles')
<style>
.header-style-card:hover, .col-header-style-card:hover { border-color:#2d6a9f!important; }
.header-style-card.selected, .col-header-style-card.selected { border-color:#2d6a9f!important; background:#f0f4ff!important; }
</style>
@endpush

@push('scripts')
<script>
// Company data from DB (for live preview)
const CO = @json($co);

// ── Colour pickers sync ───────────────────────────────────────────────
document.querySelectorAll('.colour-picker').forEach(picker => {
    const hex = document.querySelector(`.colour-hex[data-key="${picker.dataset.key}"]`);
    picker.addEventListener('input', () => { if(hex) hex.value = picker.value; updatePreview(); });
    if (hex) hex.addEventListener('input', () => {
        if (/^#[0-9a-fA-F]{6}$/.test(hex.value)) { picker.value = hex.value; updatePreview(); }
    });
});

// ── Presets ───────────────────────────────────────────────────────────
document.querySelectorAll('.preset-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const preset = JSON.parse(btn.dataset.preset);
        Object.entries(preset).forEach(([key, hex]) => {
            const p = document.querySelector(`.colour-picker[data-key="${key}"]`);
            const h = document.querySelector(`.colour-hex[data-key="${key}"]`);
            if(p) p.value = hex; if(h) h.value = hex;
        });
        updatePreview();
    });
});

// ── Header style cards ────────────────────────────────────────────────
function selectHeaderStyle(card) {
    document.querySelectorAll('.header-style-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    const val = card.dataset.value;
    document.getElementById('headerStyleInput').value = val;
    const showLogo = !['none','simple'].includes(val);
    document.getElementById('logoSection').classList.toggle('d-none', !showLogo);
    updatePreview();
}

function selectColHeaderStyle(card) {
    document.querySelectorAll('.col-header-style-card').forEach(c => {
        c.classList.remove('selected');
        c.style.borderColor = '#dee2e6';
        c.style.background  = '#fff';
    });
    card.classList.add('selected');
    card.style.borderColor = '#2d6a9f';
    card.style.background  = '#f0f4ff';
    document.getElementById('colHeaderStyleInput').value = card.dataset.value;
    updatePreview();
}

function onLayoutChange(val) {
    document.getElementById('groupColRow').style.display = val === 'grouped' ? 'block' : 'none';
    updatePreview();
}

// ── Gather config from form ───────────────────────────────────────────
function getConfig() {
    const cfg = {};
    document.querySelectorAll('.colour-picker').forEach(el => { cfg[el.dataset.key] = el.value; });
    document.querySelectorAll('[name^="config_"]').forEach(el => {
        const key = el.name.replace('config_', '');
        if (el.type === 'checkbox') cfg[key] = el.checked;
        else if (el.type === 'color') {} // handled by colour-picker
        else cfg[key] = el.value;
    });
    cfg.header_style       = document.getElementById('headerStyleInput').value;
    cfg.col_header_style   = document.getElementById('colHeaderStyleInput').value;
    return cfg;
}

// ── Preview modes ─────────────────────────────────────────────────────
let previewMode = 'pdf';
function setPreviewMode(mode) {
    previewMode = mode;
    document.getElementById('pvModePdf').className = mode==='pdf' ? 'btn btn-xs btn-light' : 'btn btn-xs btn-outline-secondary';
    document.getElementById('pvModeWeb').className = mode==='web' ? 'btn btn-xs btn-light' : 'btn btn-xs btn-outline-secondary';
    updatePreview();
}

// ── LIVE PREVIEW RENDERER ─────────────────────────────────────────────
function updatePreview() {
    const cfg    = getConfig();
    const layout = document.getElementById('layoutSelect').value;
    const fontPt = cfg.font_size==='small'?'7pt':cfg.font_size==='large'?'9.5pt':'8pt';
    const fontSm = cfg.font_size==='small'?'6pt':cfg.font_size==='large'?'8pt':'7pt';

    const coName    = CO.company_name    || 'Your Company Name';
    const coTagline = CO.company_tagline || '';
    const coAddr    = CO.company_address || '';
    const coCity    = CO.company_city    || '';
    const coPhone   = CO.company_phone   || '';
    const coEmail   = CO.company_email   || '';
    const coNtn     = CO.company_ntn     || '';
    const coLogo    = cfg.logo_url || CO.company_logo_url || '';
    const logoH     = (cfg.logo_height||55) + 'px';

    const titleSizePt = cfg.header_title_size==='small'?'8pt':cfg.header_title_size==='large'?'13pt':'10pt';
    const nameSizePt  = cfg.header_company_name_size==='small'?'9pt':cfg.header_company_name_size==='large'?'14pt':'11pt';
    const accentColor = cfg.color_accent || '#e8a020';
    const headerColor = cfg.color_page_header || '#1a3a5c';

    let html = `<div style="font-family:Arial,sans-serif;font-size:${fontPt};color:#1e1e2e;background:#fff;padding:${previewMode==='pdf'?'0':'8px'};">`;

    // ── Watermark ──────────────────────────────────────────────────────
    if (cfg.watermark_text) {
        const op = (parseInt(cfg.watermark_opacity)||8)/100;
        html += `<div style="position:fixed;top:40%;left:50%;transform:translate(-50%,-50%) rotate(-30deg);
                 font-size:48pt;font-weight:bold;color:rgba(0,0,0,${op});pointer-events:none;z-index:0;white-space:nowrap;">
                 ${cfg.watermark_text}</div>`;
    }

    // ── HEADER ─────────────────────────────────────────────────────────
    if (cfg.header_style !== 'none') {
        const showLogo = cfg.logo_position !== 'none' && coLogo && cfg.header_style !== 'simple';
        const showRight = cfg.header_title_align !== 'center';

        let dividerStyle = '';
        const divColor = cfg.header_divider_color || accentColor;
        if (cfg.header_divider === 'thin')    dividerStyle = `border-bottom:1px solid ${divColor};`;
        else if (cfg.header_divider === 'thick') dividerStyle = `border-bottom:3px solid ${divColor};`;
        else if (cfg.header_divider === 'double') dividerStyle = `border-bottom:3px double ${divColor};`;
        else if (cfg.header_divider === 'colored') dividerStyle = `border-bottom:5px solid ${divColor};`;

        if (cfg.header_style === 'centered') {
            html += `<div style="text-align:center;padding-bottom:8px;margin-bottom:8px;${dividerStyle}">
                ${showLogo ? `<img src="${coLogo}" style="max-height:${logoH};max-width:120px;object-fit:contain;margin-bottom:4px;" onerror="this.style.display='none'"><br>` : ''}
                ${cfg.header_show_company_name ? `<div style="font-size:${nameSizePt};font-weight:bold;color:${headerColor};">${coName}</div>` : ''}
                ${cfg.header_show_tagline && coTagline ? `<div style="color:${accentColor};font-style:italic;font-size:${fontSm};">${coTagline}</div>` : ''}
                ${cfg.header_show_report_title ? `<div style="font-size:${titleSizePt};font-weight:bold;color:${accentColor};margin-top:4px;">Sample Report Title</div>` : ''}
                <div style="font-size:${fontSm};color:#6c757d;">Finance & GL · 13 Mar 2026 · By: Admin</div>
            </div>`;
        } else if (cfg.header_style === 'logo-only') {
            html += `<div style="padding-bottom:8px;margin-bottom:8px;${dividerStyle};text-align:${cfg.logo_position||'left'};">
                ${coLogo ? `<img src="${coLogo}" style="max-height:${logoH};max-width:200px;object-fit:contain;" onerror="this.style.display='none'">` : `<span style="font-size:${nameSizePt};font-weight:bold;color:${headerColor};">${coName}</span>`}
            </div>`;
        } else if (cfg.header_style === 'simple') {
            html += `<div style="padding-bottom:8px;margin-bottom:8px;${dividerStyle};display:flex;justify-content:space-between;align-items:flex-end;">
                <div>
                    ${cfg.header_show_report_title ? `<div style="font-size:${titleSizePt};font-weight:bold;color:${accentColor};">Sample Report Title</div>` : ''}
                    ${cfg.header_show_category ? `<div style="font-size:${fontSm};color:#6c757d;">Finance & GL</div>` : ''}
                </div>
                <div style="text-align:right;font-size:${fontSm};color:#6c757d;">
                    ${cfg.header_show_print_date ? '13 Mar 2026<br>' : ''}
                    ${cfg.header_show_generated_by ? 'By: Admin' : ''}
                </div>
            </div>`;
        } else {
            // FULL layout
            const logoLeft  = (cfg.logo_position||'left') === 'left';
            const logoRight = (cfg.logo_position||'left') === 'right';
            const logoHtml  = showLogo ? `<div style="display:table-cell;vertical-align:middle;width:${logoH};padding-${logoLeft?'right':'left'}:10px;">
                <img src="${coLogo}" style="max-height:${logoH};max-width:90px;object-fit:contain;" onerror="this.style.display='none'">
            </div>` : '';

            const coInfoHtml = `<div style="display:table-cell;vertical-align:top;">
                ${cfg.header_show_company_name ? `<div style="font-size:${nameSizePt};font-weight:bold;color:${headerColor};line-height:1.2;">${coName}</div>` : ''}
                ${cfg.header_show_tagline && coTagline ? `<div style="font-size:${fontSm};color:${accentColor};font-style:italic;">${coTagline}</div>` : ''}
                ${cfg.header_show_address && coAddr ? `<div style="font-size:${fontSm};color:#6c757d;">${coAddr}${coCity?', '+coCity:''}</div>` : ''}
                ${cfg.header_show_phone && coPhone ? `<div style="font-size:${fontSm};color:#6c757d;">Tel: ${coPhone}</div>` : ''}
                ${cfg.header_show_email && coEmail ? `<div style="font-size:${fontSm};color:#6c757d;">Email: ${coEmail}</div>` : ''}
                ${cfg.header_show_ntn && coNtn ? `<div style="font-size:${fontSm};color:#6c757d;">NTN: ${coNtn}</div>` : ''}
            </div>`;

            const titleAlign = cfg.header_title_align || 'left';
            const titleHtml = `<div style="display:table-cell;vertical-align:bottom;text-align:${titleAlign};min-width:120px;">
                ${cfg.header_show_report_title ? `<div style="font-size:${titleSizePt};font-weight:bold;color:${accentColor};">Sample Report Title</div>` : ''}
                ${cfg.header_show_category ? `<div style="font-size:${fontSm};color:#6c757d;">Finance & GL</div>` : ''}
                ${cfg.header_show_print_date ? `<div style="font-size:${fontSm};color:#6c757d;">13 Mar 2026</div>` : ''}
                ${cfg.header_show_generated_by ? `<div style="font-size:${fontSm};color:#6c757d;">By: Admin</div>` : ''}
            </div>`;

            html += `<div style="display:table;width:100%;padding-bottom:8px;margin-bottom:8px;${dividerStyle}">
                <div style="display:table-row;">
                    ${logoLeft ? logoHtml : ''}
                    ${coInfoHtml}
                    ${titleHtml}
                    ${logoRight ? logoHtml : ''}
                </div>
            </div>`;
        }
    }

    // ── PARAMS BAR ────────────────────────────────────────────────────
    if (cfg.params_bar_style !== 'none') {
        if (cfg.params_bar_style === 'table') {
            html += `<table style="margin-bottom:7px;border-collapse:collapse;font-size:${fontSm};">
                <tr>
                    <td style="background:#e8f0fb;font-weight:bold;padding:2px 8px;border:1px solid #c5d5ea;">Date From</td>
                    <td style="padding:2px 8px;border:1px solid #c5d5ea;">01/01/2025</td>
                    <td style="background:#e8f0fb;font-weight:bold;padding:2px 8px;border:1px solid #c5d5ea;padding-left:12px;">Date To</td>
                    <td style="padding:2px 8px;border:1px solid #c5d5ea;">31/01/2025</td>
                </tr>
            </table>`;
        } else {
            html += `<div style="background:#f0f4fa;border-left:4px solid ${accentColor};padding:3px 8px;margin-bottom:7px;font-size:${fontSm};">
                <strong style="color:${headerColor};">Parameters: </strong>
                <span style="color:#6c757d;">Date From:</span> <strong>01/01/2025</strong>
                &nbsp;<span style="color:#6c757d;">Date To:</span> <strong>31/01/2025</strong>
            </div>`;
        }
    }

    // ── DATA TABLE ────────────────────────────────────────────────────
    const cols = layout === 'aged'
        ? ['Customer','Total Dues','Current','1-30 Days','31-60 Days','61-90 Days','> 90 Days']
        : ['Date','Reference','Description','Debit','Credit','Balance'];
    const numCols = layout === 'aged'
        ? ['Total Dues','Current','1-30 Days','31-60 Days','61-90 Days','> 90 Days']
        : ['Debit','Credit','Balance'];
    const rows = [
        layout==='aged'
            ? ['ABC Traders','1,500','500','','1,000','','']
            : ['15/01/2025','INV-0042','Sales Invoice','1,500.00','','1,500.00'],
        layout==='aged'
            ? ['XYZ Co.','800','','800','','','']
            : ['20/01/2025','RCP-0011','Receipt','','1,500.00','0.00'],
        layout==='aged'
            ? ['Apex Ltd.','3,200','','','','200','3,000']
            : ['25/01/2025','INV-0043','Sales Invoice','800.00','','800.00'],
    ];

    const colHdrStyle = cfg.col_header_style || 'filled';
    let thStyle = `padding:4px 6px;font-size:${fontSm};font-weight:bold;`;
    if (colHdrStyle === 'filled')    thStyle += `background:${cfg.color_header_bg};color:${cfg.color_header_text};`;
    else if (colHdrStyle === 'outline') thStyle += `background:#fff;color:${cfg.color_header_bg};border-bottom:2px solid ${cfg.color_header_bg};`;
    else if (colHdrStyle === 'minimal') thStyle += `background:#fff;color:#6c757d;border-bottom:1px solid ${cfg.color_border};`;
    else if (colHdrStyle === 'gradient') thStyle += `background:linear-gradient(135deg,${cfg.color_header_bg},${accentColor});color:${cfg.color_header_text};`;

    html += `<table style="width:100%;border-collapse:collapse;font-size:${fontPt};">
        <thead><tr>
            ${cfg.show_row_numbers ? `<th style="${thStyle}width:20px;">#</th>` : ''}
            ${cols.map(h=>`<th style="${thStyle}${numCols.includes(h)?'text-align:right;':''}">${h}</th>`).join('')}
        </tr></thead><tbody>`;

    rows.forEach((r,i) => {
        const bg = cfg.zebra_rows ? (i%2===0?cfg.color_row_odd:cfg.color_row_even) : '#fff';
        html += `<tr style="background:${bg};">
            ${cfg.show_row_numbers ? `<td style="padding:3px 5px;color:#adb5bd;font-size:${fontSm};text-align:right;border-bottom:1px solid ${cfg.color_border};">${i+1}</td>` : ''}
            ${r.map((v,j)=>`<td style="padding:3px 5px;border-bottom:1px solid ${cfg.color_border};${numCols.includes(cols[j])?'text-align:right;font-family:monospace;':''}">${v}</td>`).join('')}
        </tr>`;
    });

    if (cfg.show_totals_row) {
        html += `<tr style="background:${cfg.color_total_bg};color:${cfg.color_total_text};font-weight:bold;">
            ${cfg.show_row_numbers ? `<td style="padding:4px 5px;"></td>` : ''}
            <td style="padding:4px 5px;font-size:${fontSm};font-weight:normal;opacity:.7;" colspan="3">TOTAL</td>
            ${numCols.slice(0,3).map(()=>`<td style="padding:4px 5px;text-align:right;font-family:monospace;">2,300.00</td>`).join('')}
        </tr>`;
    }

    html += `</tbody></table>`;

    if (cfg.show_record_count) {
        html += `<div style="background:#f0f4fa;color:${headerColor};border-radius:2px;padding:2px 8px;font-size:${fontSm};display:inline-block;margin-top:5px;font-weight:bold;">3 records returned</div>`;
    }

    // ── FOOTER ────────────────────────────────────────────────────────
    if (cfg.show_page_footer !== false) {
        const fzMap = (zone, custom) => {
            if (zone === 'company')      return coName + (cfg.footer_confidential ? ' — CONFIDENTIAL' : '');
            if (zone === 'report_name')  return 'Sample Report';
            if (zone === 'datetime')     return '13 Mar 2026 14:30';
            if (zone === 'page')         return 'Page 1 of 1';
            if (zone === 'custom')       return custom || '';
            return '';
        };
        const fl = fzMap(cfg.footer_left,   cfg.footer_custom_left);
        const fc = fzMap(cfg.footer_center, cfg.footer_custom_center);
        const fr = fzMap(cfg.footer_right,  cfg.footer_custom_right);
        const fDiv = cfg.footer_show_divider !== false ? `border-top:1px solid ${cfg.color_border};` : '';
        html += `<div style="${fDiv}margin-top:10px;padding-top:5px;display:flex;justify-content:space-between;font-size:${fontSm};color:#adb5bd;">
            <span>${fl}</span><span>${fc}</span><span>${fr}</span>
        </div>`;
    }

    html += `</div>`;
    document.getElementById('previewPaper').innerHTML = html;
}

document.getElementById('templateForm').addEventListener('input', updatePreview);
document.getElementById('templateForm').addEventListener('change', updatePreview);
updatePreview();
</script>
@endpush

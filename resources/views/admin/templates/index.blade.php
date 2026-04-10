@extends('layouts.app')
@section('title', 'Report Templates')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item active">Templates</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h4><i class="bi bi-layout-wtf me-2"></i>Report Templates</h4>
        <small class="text-muted">Create reusable PDF templates with custom colours, layout options, and features</small>
    </div>
    <a href="{{ route('admin.templates.create') }}" class="btn btn-erp">
        <i class="bi bi-plus-lg me-2"></i>New Template
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">
    @forelse($templates as $tpl)
    @php
        $cfg = $tpl->getEffectiveConfig();
        $layoutInfo = \App\Models\ReportTemplate::layoutOptions()[$tpl->layout] ?? ['icon'=>'bi-file','label'=>$tpl->layout];
    @endphp
    <div class="col-md-6 col-xl-4">
        <div class="card h-100" style="border-top:4px solid {{ $cfg['color_accent'] }};">
            <div class="card-body">
                {{-- Template name + layout badge --}}
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div>
                        <div class="fw-bold" style="font-size:.95rem;color:{{ $cfg['color_page_header'] }};">
                            <i class="bi {{ $layoutInfo['icon'] }} me-1"></i>{{ $tpl->name }}
                        </div>
                        @if($tpl->description)
                            <div class="text-muted" style="font-size:.78rem;margin-top:2px;">{{ $tpl->description }}</div>
                        @endif
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <span class="badge" style="background:{{ $cfg['color_page_header'] }};color:#fff;font-size:.68rem;">
                            {{ $layoutInfo['label'] }}
                        </span>
                        @if($tpl->is_system)
                            <span class="badge bg-secondary" style="font-size:.65rem;">SYSTEM</span>
                        @endif
                    </div>
                </div>

                {{-- Colour swatches --}}
                <div class="d-flex gap-1 mb-2 align-items-center">
                    <small class="text-muted me-1" style="font-size:.68rem;">Colours:</small>
                    @foreach(['color_page_header','color_accent','color_header_bg','color_subtotal_bg','color_total_bg'] as $ck)
                        <div title="{{ $ck }}: {{ $cfg[$ck] }}"
                             style="width:16px;height:16px;border-radius:3px;background:{{ $cfg[$ck] }};border:1px solid rgba(0,0,0,.1);"></div>
                    @endforeach
                </div>

                {{-- Feature pills --}}
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @php
                        $feats = [
                            'show_totals_row'   => ['Totals',    'bg-success'],
                            'show_subtotals'    => ['Subtotals', 'bg-info text-dark'],
                            'show_row_numbers'  => ['Row #',     'bg-secondary'],
                            'show_params_bar'   => ['Params',    'bg-secondary'],
                            'show_column_borders'=> ['Borders',  'bg-secondary'],
                            'zebra_rows'        => ['Zebra',     'bg-secondary'],
                        ];
                    @endphp
                    @foreach($feats as $key => [$label, $cls])
                        @if($cfg[$key])
                            <span class="badge {{ $cls }}" style="font-size:.65rem;">{{ $label }}</span>
                        @endif
                    @endforeach
                    <span class="badge bg-light text-dark border" style="font-size:.65rem;">
                        {{ strtoupper($cfg['font_size']) }} font
                    </span>
                    <span class="badge bg-light text-dark border" style="font-size:.65rem;">
                        {{ $cfg['paper_size'] }} {{ ucfirst($cfg['orientation'][0]) }}
                    </span>
                </div>

                {{-- Usage count --}}
                <div class="text-muted" style="font-size:.75rem;">
                    <i class="bi bi-files me-1"></i>
                    Used by <strong>{{ $tpl->reports_count }}</strong> report{{ $tpl->reports_count != 1 ? 's' : '' }}
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center py-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge {{ $tpl->is_active ? 'bg-success' : 'bg-danger' }}" style="font-size:.65rem;">
                        {{ $tpl->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ route('admin.templates.edit', $tpl) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @if(!$tpl->is_system)
                    <form method="POST" action="{{ route('admin.templates.destroy', $tpl) }}"
                          onsubmit="return confirm('Delete template \'{{ addslashes($tpl->name) }}\'?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-layout-wtf fs-1 d-block mb-2 opacity-25"></i>
                <h6>No templates yet</h6>
                <a href="{{ route('admin.templates.create') }}" class="btn btn-erp btn-sm mt-2">
                    <i class="bi bi-plus-lg me-1"></i>Create First Template
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection

@extends('layouts.app')

@section('title', 'Preview: ' . $report->name)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('reports.index', ['category' => $report->category->slug]) }}">
            {{ $report->category->name }}
        </a>
    </li>
    <li class="breadcrumb-item"><a href="{{ route('reports.show', $report->slug) }}">{{ $report->name }}</a></li>
    <li class="breadcrumb-item active">Preview</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h4><i class="bi bi-eye me-2"></i>{{ $report->name }}</h4>
        <small class="text-muted">
            {{ count($rows) }} records &nbsp;·&nbsp;
            Template: <code>{{ $report->blade_view ?: 'reports.pdf.tabular' }}</code>
        </small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.show', $report->slug) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <form method="POST" action="{{ route('reports.generate', $report->slug) }}">
            @csrf
            @foreach($params as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach
            <button type="submit" class="btn btn-sm btn-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
            </button>
        </form>
    </div>
</div>

{{-- Active parameters --}}
<div class="card mb-3">
    <div class="card-body py-2 d-flex flex-wrap gap-2 align-items-center">
        <small class="text-muted fw-semibold me-1">Params:</small>
        @foreach($params as $key => $val)
            @if($val)
                <span class="badge bg-light text-dark border">
                    {{ ucwords(str_replace('_', ' ', $key)) }}: <strong>{{ $val }}</strong>
                </span>
            @endif
        @endforeach
    </div>
</div>

@if(empty($rows))
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
            <h6>No data found</h6>
            <p class="small mb-0">Try adjusting your parameters.</p>
        </div>
    </div>
@else

@php
    $allCols    = array_keys($rows[0]);
    $masterCols = array_filter($allCols, fn($c) => str_starts_with(strtolower($c), 'header_'));
    $detailCols = array_filter($allCols, fn($c) => str_starts_with(strtolower($c), 'detail_'));
    $isMasterDetail = count($masterCols) > 0 && count($detailCols) > 0;

    $numericCols = [];
    foreach ($rows as $row) {
        foreach ($allCols as $c) {
            if (is_numeric(str_replace([',',' '],'', $row[$c] ?? ''))) {
                $numericCols[$c] = true;
            }
        }
    }
@endphp

@if($isMasterDetail)
    {{-- ── Master-Detail Preview ──────────────────────────────────────── --}}
    @php
        $grouped = collect($rows)->groupBy(fn($r) => $r[array_values($masterCols)[0]] ?? '');
    @endphp

    @foreach($grouped as $groupKey => $groupRows)
        <div class="card mb-3">
            {{-- Master header --}}
            <div class="card-header light">
                <div class="row g-2">
                    @foreach($masterCols as $mc)
                        <div class="col-auto">
                            <small class="text-muted d-block" style="font-size:.7rem;text-transform:uppercase;">
                                {{ ucwords(str_replace(['header_','_'],' ', $mc)) }}
                            </small>
                            <span class="fw-semibold">{{ $groupRows->first()[$mc] ?? '—' }}</span>
                        </div>
                    @endforeach
                    <div class="col-auto ms-auto">
                        <span class="badge bg-secondary">{{ $groupRows->count() }} lines</span>
                    </div>
                </div>
            </div>
            {{-- Detail lines --}}
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:.8rem;">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            @foreach($detailCols as $dc)
                                <th class="{{ ($numericCols[$dc] ?? false) ? 'text-end' : '' }}">
                                    {{ ucwords(str_replace(['detail_','_'],' ', $dc)) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupRows as $i => $row)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                @foreach($detailCols as $dc)
                                    <td class="{{ ($numericCols[$dc] ?? false) ? 'text-end' : '' }}">
                                        @if(($numericCols[$dc] ?? false) && ($row[$dc] ?? '') !== '')
                                            {{ number_format(floatval(str_replace(',','',$row[$dc])), 2) }}
                                        @else
                                            {{ $row[$dc] ?? '' }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

@else
    {{-- ── Standard Flat Table Preview ────────────────────────────────── --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-hover table-striped mb-0" style="font-size:.8rem;">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        @foreach($allCols as $col)
                            <th class="{{ ($numericCols[$col] ?? false) ? 'text-end' : '' }}">
                                {{ ucwords(str_replace('_',' ', $col)) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                        <tr>
                            <td class="text-muted" style="font-size:.7rem;">{{ $i + 1 }}</td>
                            @foreach($allCols as $col)
                                @php
                                    $val   = $row[$col] ?? '';
                                    $isNum = $numericCols[$col] ?? false;
                                    $fval  = $isNum ? floatval(str_replace(',','',$val)) : null;
                                @endphp
                                <td class="{{ $isNum ? 'text-end' : '' }} {{ ($fval !== null && $fval < 0) ? 'text-danger' : '' }}">
                                    @if($isNum && $val !== '')
                                        {{ number_format($fval, 2) }}
                                    @else
                                        {{ $val }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between text-muted" style="font-size:.78rem;">
            <span>{{ count($rows) }} records</span>
            <span>
                {{ $report->pdf_paper_size }} {{ ucfirst($report->pdf_orientation) }} ·
                Template: {{ basename(str_replace('.', '/', $report->blade_view ?? 'tabular')) }}
            </span>
        </div>
    </div>
@endif

@endif
@endsection

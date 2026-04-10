@extends('layouts.app')

@section('title', $activeCategory ? $activeCategory->name : 'All Reports')

@section('breadcrumb')
    <li class="breadcrumb-item active">
        @if($activeCategory) {{ $activeCategory->name }} @else All Reports @endif
    </li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h4>
            @if($activeCategory)
                <i class="bi {{ $activeCategory->icon }} me-2"></i>{{ $activeCategory->name }}
            @else
                <i class="bi bi-files me-2"></i>All Reports
            @endif
        </h4>
        <small class="text-muted">{{ $reports->count() }} report(s) available</small>
    </div>
</div>

{{-- Category filter pills --}}
<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="{{ route('reports.index') }}"
       class="btn btn-sm {{ !$activeCategory ? 'btn-primary' : 'btn-outline-secondary' }}">
        All
    </a>
    @foreach($categories as $cat)
        <a href="{{ route('reports.index', ['category' => $cat->slug]) }}"
           class="btn btn-sm {{ $activeCategory?->id === $cat->id ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="bi {{ $cat->icon }} me-1"></i>{{ $cat->name }}
        </a>
    @endforeach
</div>

@if($reports->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-bar-chart fs-1 d-block mb-3"></i>
            <h6>No reports available</h6>
            <p class="mb-0 small">No reports are accessible for your role in this category.</p>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($reports->groupBy('category.name') as $catName => $catReports)
            <div class="col-12">
                <h6 class="text-muted mb-2" style="font-size:.75rem;letter-spacing:.5px;text-transform:uppercase;font-weight:700;">
                    {{ $catName }}
                </h6>
                <div class="row g-3">
                    @foreach($catReports as $report)
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <div class="rounded p-2" style="background:#e8f0fb;flex-shrink:0;">
                                            <i class="bi bi-file-earmark-bar-graph" style="color:#1a3a5c;font-size:1.1rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $report->name }}</h6>
                                            <small class="text-muted">{{ $report->category->name }}</small>
                                        </div>
                                    </div>
                                    @if($report->description)
                                        <p class="text-muted small flex-grow-1" style="font-size:.78rem;">
                                            {{ Str::limit($report->description, 100) }}
                                        </p>
                                    @endif
                                    <a href="{{ route('reports.show', $report->slug) }}"
                                       class="btn btn-sm btn-erp mt-2 align-self-start">
                                        <i class="bi bi-play-fill me-1"></i>Run Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

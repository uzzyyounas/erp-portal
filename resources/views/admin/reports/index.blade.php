@extends('layouts.app')
@section('title','Manage Reports')
@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Reports</li>
@endsection

@section('content')
    <div class="page-header">
        <h4><i class="bi bi-gear-fill me-2"></i>Manage Reports</h4>
        <a href="{{ route('admin.reports.create') }}" class="btn btn-erp btn-sm">
            <i class="bi bi-file-earmark-plus me-1"></i>New Report
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead>
                <tr>
                    <th width="30">#</th>
                    <th>Category</th>
                    <th>Report Name</th>
                    <th width="70" class="text-center">Status</th>
                    <th width="200">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td class="text-muted" style="font-size:.78rem">{{ $report->id }}</td>
                        <td><span class="badge bg-secondary" style="font-size:.68rem">{{ $report->category->name }}</span></td>
                        <td>
                            <div class="fw-semibold" style="font-size:.85rem">{{ $report->name }}</div>
                            <div style="font-size:.7rem;color:#64748b">
                                <code style="font-size:.68rem">{{ $report->slug }}</code>
                                @if($report->hasDesignerConfig())
                                    &nbsp;<span style="color:#16a34a;font-weight:600"><i class="bi bi-palette-fill"></i> Designed</span>
                                @elseif($report->template_id)
                                    &nbsp;<span style="color:#0369a1"><i class="bi bi-layout-wtf"></i> Template</span>
                                @else
                                    &nbsp;<span style="color:#94a3b8"><i class="bi bi-file-text"></i> Default</span>
                                @endif
                            </div>
                        </td>

                        <td class="text-center">
                        <span class="badge {{ $report->is_active ? 'bg-success' : 'bg-danger' }}" style="font-size:.68rem">
                            {{ $report->is_active ? 'Active' : 'Off' }}
                        </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                {{-- Edit report (name, SQL, params) --}}
                                <a href="{{ route('admin.reports.edit', $report) }}"
                                   class="btn btn-xs btn-outline-primary" title="Edit report">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                {{-- Run / Preview --}}
                                <a href="{{ route('reports.show', $report->slug) }}"
                                   class="btn btn-xs btn-outline-secondary" target="_blank" title="Run report">
                                    <i class="bi bi-play-fill"></i>
                                </a>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('admin.reports.destroy', $report) }}"
                                      class="d-inline" onsubmit="return confirm('Delete this report?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">
                            No reports yet. <a href="{{ route('admin.reports.create') }}">Create your first report →</a>
                        </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@extends('layouts.app')

@section('title', isset($report) ? 'Edit: '.$report->name : 'Add Report')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.reports.index') }}">Reports</a>
    </li>
    <li class="breadcrumb-item active">
        {{ isset($report) ? 'Edit' : 'Add' }}
    </li>
@endsection

@section('content')

    <div class="page-header d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-file-earmark-plus me-2"></i>
            {{ isset($report) ? 'Edit Report: '.$report->name : 'Add New Report' }}
        </h4>

        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <form method="POST"
          action="{{ isset($report) ? route('admin.reports.update', $report) : route('admin.reports.store') }}">

        @csrf
        @if(isset($report)) @method('PUT') @endif

        <div class="row g-3">

            {{-- LEFT SIDE --}}
            <div class="col-lg-8">

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-info-circle me-2"></i>Report Details
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Name --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Report Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="reportName"
                                       class="form-control form-control-sm"
                                       required
                                       value="{{ old('name', $report->name ?? '') }}">
                            </div>

                            {{-- Slug --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Slug <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="slug" id="reportSlug"
                                       class="form-control form-control-sm"
                                       required
                                       placeholder="aged-customer-analysis"
                                       value="{{ old('slug', $report->slug ?? '') }}">
                            </div>

                            {{-- Category --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Category <span class="text-danger">*</span>
                                </label>
                                <select name="category_id"
                                        class="form-select form-select-sm"
                                        required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('category_id', $report->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Route --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Route Name
                                </label>
                                <input type="text" name="route"
                                       class="form-control form-control-sm"
                                       placeholder="custreports.aged-customer-analysis"
                                       value="{{ old('route', $report->route ?? '') }}">
                            </div>

                            {{-- Icon --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Icon
                                </label>
                                <input type="text" name="icon"
                                       class="form-control form-control-sm"
                                       placeholder="bi-bar-chart-fill"
                                       value="{{ old('icon', $report->icon ?? 'bi-bar-chart-fill') }}">
                            </div>

                            {{-- Sort Order --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Sort Order
                                </label>
                                <input type="number" name="sort_order"
                                       class="form-control form-control-sm"
                                       value="{{ old('sort_order', $report->sort_order ?? 0) }}">
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Description
                                </label>
                                <textarea name="description"
                                          class="form-control form-control-sm"
                                          rows="2">{{ old('description', $report->description ?? '') }}</textarea>
                            </div>

                            {{-- Active --}}
                            <div class="col-12">
                                <div class="form-check mt-2">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="is_active"
                                           value="1"
                                        {{ old('is_active', $report->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        Active
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT SIDE (ROLES) --}}
            <div class="col-lg-4">

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-shield-check me-2"></i>Role Access
                    </div>

                    <div class="card-body">
                        <p class="text-muted small mb-2">
                            Select which roles can run this report:
                        </p>

                        @php
                            $reportRoleIds = isset($report)
                                ? $report->roles->pluck('id')->toArray()
                                : [];
                        @endphp

                        @foreach($roles as $role)
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="roles[]"
                                       value="{{ $role->id }}"
                                       id="role_{{ $role->id }}"
                                    {{ in_array($role->id, $reportRoleIds) ? 'checked' : '' }}>

                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @endforeach

                    </div>
                </div>

            </div>

        </div>

        {{-- BUTTONS --}}
        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-erp">
                <i class="bi bi-save me-2"></i>
                {{ isset($report) ? 'Update Report' : 'Create Report' }}
            </button>

            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                Cancel
            </a>
        </div>

    </form>

@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const nameInput = document.getElementById('reportName');
            const slugInput = document.getElementById('reportSlug');

            let manualEdit = false;

            // Detect manual slug edit
            slugInput.addEventListener('input', function () {
                manualEdit = true;
            });

            // Generate slug from name
            nameInput.addEventListener('input', function () {

                if (manualEdit) return;

                let slug = nameInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')   // remove special chars
                    .replace(/\s+/g, '-')           // spaces to dash
                    .replace(/-+/g, '-');           // remove duplicate dashes

                slugInput.value = slug;
            });

        });
    </script>
@endpush

@extends('layouts.app')

@section('title', $report->name)

@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5>{{ $report->name }}</h5>
            </div>

            <div class="card-body">

                <form method="POST" action="{{ route('reports.execute', $report->slug) }}">
                    @csrf

                    {{-- Customer --}}
                    <div class="mb-3">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->debtor_no }}">
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- From Date --}}
                    <div class="mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control" required>
                    </div>

                    {{-- To Date --}}
                    <div class="mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control" required>
                    </div>

                    <button class="btn btn-primary">
                        <i class="bi bi-file-earmark-pdf"></i> Generate Report
                    </button>

                </form>

            </div>
        </div>
    </div>

@endsection

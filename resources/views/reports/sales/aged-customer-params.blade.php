@extends('layouts.app')

@section('title', 'Aged Customer Analysis')

@push('styles')
    <style>
        .param-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(26,58,92,.09);
            overflow: hidden;
            max-width: 780px;
            margin: 0 auto;
        }
        .param-card-header {
            background: linear-gradient(135deg, #1a3a5c 0%, #2d6a9f 100%);
            padding: 20px 28px 18px;
            display: flex; align-items: center; gap: 14px;
        }
        .param-card-header .hicon {
            width: 44px; height: 44px; border-radius: 10px;
            background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; color: #fff; flex-shrink: 0;
        }
        .param-card-header h5 { margin:0; color:#fff; font-size:1rem; font-weight:700; }
        .param-card-header p  { margin:2px 0 0; color:rgba(255,255,255,.6); font-size:.74rem; }

        .param-body { padding: 26px 28px; }

        .group-label {
            font-size: .62rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .7px; color: #94a3b8;
            margin-bottom: 10px; padding-bottom: 6px;
            border-bottom: 1px solid #f0f4f8;
        }
        .field-label {
            font-size: .78rem; font-weight: 600; color: #374151;
            margin-bottom: 5px; display: flex; align-items: center; gap: 6px;
        }
        .field-label i { color: #2d6a9f; }

        .form-control, .form-select {
            font-size: .82rem; border-radius: 8px;
            border: 1px solid #dde3ed; color: #1e293b;
            padding: 8px 12px;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2d6a9f;
            box-shadow: 0 0 0 3px rgba(45,106,159,.12);
            outline: none;
        }

        .date-presets { display:flex; gap:5px; flex-wrap:wrap; margin-top:6px; }
        .date-preset {
            font-size:.67rem; padding:2px 9px;
            border:1px solid #dde3ed; border-radius:20px;
            background:#f8fafc; color:#475569;
            cursor:pointer; transition:all .15s; user-select:none;
        }
        .date-preset:hover { background:#1a3a5c; color:#fff; border-color:#1a3a5c; }

        .sel-wrap { position:relative; }
        .sel-spin {
            position:absolute; right:34px; top:50%;
            transform:translateY(-50%); display:none;
        }
        .sel-spin.on { display:block; }

        .count-badge {
            font-size:.63rem; padding:1px 7px;
            background:rgba(45,106,159,.13); color:#2d6a9f;
            border-radius:4px; margin-left:5px;
        }

        .info-tip {
            background:#eff6ff; border:1px solid #bfdbfe;
            border-radius:8px; padding:10px 14px;
            font-size:.75rem; color:#1d4ed8;
            display:flex; align-items:flex-start; gap:8px; margin-top:20px;
        }
        .info-tip i { margin-top:1px; flex-shrink:0; }

        .param-footer {
            background:#f8fafc; border-top:1px solid #eef2f7;
            padding:16px 28px;
            display:flex; align-items:center; justify-content:flex-end; gap:10px;
        }
        .btn-generate {
            background: linear-gradient(135deg,#1a3a5c,#2d6a9f);
            color:#fff; border:none; padding:9px 24px;
            border-radius:8px; font-size:.82rem; font-weight:600;
            display:inline-flex; align-items:center; gap:7px;
            transition:opacity .2s, transform .15s; cursor:pointer;
        }
        .btn-generate:hover  { opacity:.88; color:#fff; transform:translateY(-1px); }
        .btn-generate:active { transform:translateY(0); }
        .btn-generate:disabled { opacity:.55; pointer-events:none; }

        .btn-reset {
            background:transparent; border:1px solid #dde3ed;
            color:#64748b; padding:9px 18px; border-radius:8px;
            font-size:.82rem; font-weight:500; cursor:pointer;
            transition:background .15s;
        }
        .btn-reset:hover { background:#f1f5f9; }
    </style>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Reports</li>
    <li class="breadcrumb-item active">Aged Customer Analysis</li>
@endsection

@section('content')

    <div class="page-header">
        <h4><i class="bi bi-bar-chart-steps me-2" style="color:#1a3a5c;"></i>Aged Customer Analysis</h4>
        <small class="text-muted">Aging Date: {{ now()->format('d M Y') }}</small>
    </div>

    <div class="param-card">

        {{-- Header --}}
        <div class="param-card-header">
            <div class="hicon"><i class="bi bi-sliders2"></i></div>
            <div>
                <h5>Report Parameters</h5>
                <p>Set date range, salesman, and customer then click Generate to open the PDF.</p>
            </div>
        </div>

        {{-- Form opens in new tab --}}
        <form method="GET" action="{{ route('reports.aged-customer-analysis.generate') }}" target="_blank" id="paramForm">

            <div class="param-body">

                {{-- Date Range --}}
                <div class="group-label"><i class="bi bi-calendar3 me-1"></i>Date Range</div>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label class="field-label"><i class="bi bi-calendar-event"></i> From Date</label>
                        <input type="date" name="from" id="from" class="form-control"
                               value="{{ now()->startOfMonth()->toDateString() }}" required>
                        <div class="date-presets">
                            <span class="date-preset" data-target="from" data-val="month_start">This Month</span>
                            <span class="date-preset" data-target="from" data-val="qtr_start">This Quarter</span>
                            <span class="date-preset" data-target="from" data-val="year_start">This Year</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="field-label"><i class="bi bi-calendar-check"></i> End Date</label>
                        <input type="date" name="to" id="to" class="form-control"
                               value="{{ now()->toDateString() }}" required>
                        <div class="date-presets">
                            <span class="date-preset" data-target="to" data-val="today">Today</span>
                            <span class="date-preset" data-target="to" data-val="month_end">Month End</span>
                            <span class="date-preset" data-target="to" data-val="qtr_end">Qtr End</span>
                        </div>
                    </div>
                </div>

                {{-- Salesman --}}
                <div class="group-label"><i class="bi bi-person-badge me-1"></i>Group / Salesman</div>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="field-label"><i class="bi bi-person-lines-fill"></i> Salesman</label>
                        <select name="salesman_code" id="salesmanSel" class="form-select">
                            <option value="">— All Salesmen —</option>
                            @foreach($salesmen as $s)
                                <option value="{{ $s->salesman_code }}">{{ $s->salesman_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Customer --}}
                <div class="group-label"><i class="bi bi-building me-1"></i>Customer</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="field-label">
                            <i class="bi bi-shop"></i> Customer
                            <span class="count-badge" id="custCount">{{ count($customers) }} records</span>
                        </label>
                        <div class="sel-wrap">
                            <select name="debtor_no" id="customerSel" class="form-select">
                                <option value="">— All Customers —</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->debtor_no }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            <div class="sel-spin" id="custSpinner">
                                <div class="spinner-border spinner-border-sm text-secondary"
                                     style="width:14px;height:14px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

{{--                <div class="info-tip">--}}
{{--                    <i class="bi bi-info-circle-fill"></i>--}}
{{--                    <span>Leave <strong>Salesman</strong> and <strong>Customer</strong> blank to include all records.--}}
{{--                    Selecting a salesman filters the customer list automatically via AJAX.</span>--}}
{{--                </div>--}}

            </div>

            {{-- Footer --}}
            <div class="param-footer">
                <button type="button" class="btn-reset" id="resetBtn">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
                <button type="submit" class="btn-generate" id="genBtn">
                    <i class="bi bi-file-earmark-pdf"></i> Generate PDF Report
                </button>
            </div>

        </form>
    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            const now    = new Date();
            const pad    = n => String(n).padStart(2, '0');
            const ymd    = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
            const q      = m => Math.floor(m / 3);

            const presets = {
                today:       () => ymd(now),
                month_start: () => ymd(new Date(now.getFullYear(), now.getMonth(), 1)),
                month_end:   () => ymd(new Date(now.getFullYear(), now.getMonth()+1, 0)),
                qtr_start:   () => ymd(new Date(now.getFullYear(), q(now.getMonth())*3, 1)),
                qtr_end:     () => ymd(new Date(now.getFullYear(), q(now.getMonth())*3+3, 0)),
                year_start:  () => ymd(new Date(now.getFullYear(), 0, 1)),
            };

            // Date preset buttons
            document.querySelectorAll('.date-preset').forEach(btn => {
                btn.addEventListener('click', () => {
                    const el = document.getElementById(btn.dataset.target);
                    const fn = presets[btn.dataset.val];
                    if (el && fn) el.value = fn();
                });
            });

            // Salesman → Customer AJAX cascade
            const salesmanSel  = document.getElementById('salesmanSel');
            const customerSel  = document.getElementById('customerSel');
            const spinner      = document.getElementById('custSpinner');
            const countBadge   = document.getElementById('custCount');
            const allOptions   = Array.from(customerSel.options).map(o => ({ v: o.value, t: o.text }));

            function rebuildCustomers(list) {
                customerSel.innerHTML = '';
                list.forEach(item => {
                    const o = document.createElement('option');
                    o.value = item.v ?? item.debtor_no ?? '';
                    o.textContent = item.t ?? item.name ?? '';
                    customerSel.appendChild(o);
                });
                const count = list.filter(i => (i.v ?? i.debtor_no) !== '').length;
                countBadge.textContent = count + ' records';
            }

            salesmanSel.addEventListener('change', function () {
                const code = this.value;
                if (!code) { rebuildCustomers(allOptions); return; }

                spinner.classList.add('on');
                customerSel.disabled = true;

                fetch(`{{ route('reports.aged-customer-analysis.customers') }}?salesman_code=${encodeURIComponent(code)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(data => {
                        const list = [{ v: '', t: '— All Customers —' }]
                            .concat(data.map(c => ({ v: c.debtor_no, t: c.name })));
                        rebuildCustomers(list);
                    })
                    .catch(() => rebuildCustomers(allOptions))
                    .finally(() => { spinner.classList.remove('on'); customerSel.disabled = false; });
            });

            // Reset
            document.getElementById('resetBtn').addEventListener('click', () => {
                document.getElementById('from').value = '{{ now()->startOfMonth()->toDateString() }}';
                document.getElementById('to').value   = '{{ now()->toDateString() }}';
                salesmanSel.value = '';
                rebuildCustomers(allOptions);
            });

            // Loading state on submit
            document.getElementById('paramForm').addEventListener('submit', () => {
                const btn = document.getElementById('genBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:13px;height:13px;"></span> Generating…';
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Generate PDF Report';
                }, 4000);
            });
        })();
    </script>
@endpush

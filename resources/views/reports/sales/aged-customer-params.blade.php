@extends('layouts.app')

@section('title', 'Aged Customer Analysis')

@push('styles')
    <style>
        .param-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(26,58,92,.09);
            overflow: hidden;
            max-width: 860px;
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
            margin-bottom: 12px; padding-bottom: 6px;
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

        /* ── Date presets ── */
        .date-presets { display:flex; gap:5px; flex-wrap:wrap; margin-top:6px; }
        .date-preset {
            font-size:.67rem; padding:2px 9px;
            border:1px solid #dde3ed; border-radius:20px;
            background:#f8fafc; color:#475569;
            cursor:pointer; transition:all .15s; user-select:none;
        }
        .date-preset:hover { background:#1a3a5c; color:#fff; border-color:#1a3a5c; }

        /* ── Customer spinner ── */
        .sel-wrap { position:relative; }
        .sel-spin { position:absolute; right:34px; top:50%; transform:translateY(-50%); display:none; }
        .sel-spin.on { display:block; }
        .count-badge {
            font-size:.63rem; padding:1px 7px;
            background:rgba(45,106,159,.13); color:#2d6a9f;
            border-radius:4px; margin-left:5px;
        }

        /* ── Aging period inputs ── */
        .aging-inputs-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .aging-field {
            display: flex;
            flex-direction: column;
            min-width: 80px;
            flex: 1;
        }
        .aging-field label {
            font-size: .7rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }
        .aging-sep {
            font-size: .82rem;
            color: #94a3b8;
            font-weight: 600;
            padding-bottom: 10px;
            flex-shrink: 0;
        }
        .aging-field input[type=number] {
            width: 100%;
            text-align: center;
            font-weight: 700;
            font-size: .88rem;
            color: #1a3a5c;
            border: 1.5px solid #dde3ed;
            border-radius: 8px;
            padding: 7px 6px;
            transition: border-color .2s, box-shadow .2s;
        }
        .aging-field input[type=number]:focus {
            border-color: #2d6a9f;
            box-shadow: 0 0 0 3px rgba(45,106,159,.12);
            outline: none;
        }
        .aging-field input.input-error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,.12);
        }
        .aging-err {
            font-size: .65rem;
            color: #ef4444;
            margin-top: 2px;
            display: none;
        }

        /* ── Live column preview ── */
        .aging-preview {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 4px;
        }
        .aging-preview-label {
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #94a3b8;
            margin-bottom: 7px;
        }
        .aging-preview-cols {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            align-items: center;
        }
        .aging-col-pill {
            background: #1a3a5c;
            color: #fff;
            font-size: .68rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }
        .aging-col-pill.current-pill { background: #475569; }
        .aging-col-pill.total-pill   { background: #008C00; }
        .aging-arrow {
            color: #cbd5e1;
            font-size: .7rem;
        }

        /* ── Toggle option cards ── */
        .toggle-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .toggle-card {
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            padding: 13px 14px; cursor: pointer;
            transition: all .18s; position: relative; user-select: none;
        }
        .toggle-card:hover { border-color: #2d6a9f; background: #f8fbff; }
        .toggle-card.active { border-color: #1a3a5c; background: #eff6ff; }
        .toggle-card input[type="checkbox"] { position: absolute; opacity: 0; pointer-events: none; }
        .toggle-card .tc-header {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;
        }
        .toggle-card .tc-icon {
            width: 28px; height: 28px; border-radius: 7px; background: #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            font-size: .82rem; color: #64748b; transition: all .18s;
        }
        .toggle-card.active .tc-icon { background: #1a3a5c; color: #fff; }
        .toggle-card .tc-check {
            width: 18px; height: 18px; border-radius: 50%; border: 2px solid #cbd5e1;
            display: flex; align-items: center; justify-content: center;
            font-size: .65rem; color: transparent; transition: all .18s;
        }
        .toggle-card.active .tc-check { background: #1a3a5c; border-color: #1a3a5c; color: #fff; }
        .toggle-card .tc-title { font-size: .78rem; font-weight: 600; color: #1e293b; }
        .toggle-card.active .tc-title { color: #1a3a5c; }
        .toggle-card .tc-desc { font-size: .69rem; color: #64748b; line-height: 1.45; margin-top: 2px; }

        /* ── Footer ── */
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
        .btn-generate:disabled { opacity:.55; pointer-events:none; }
        .btn-reset {
            background:transparent; border:1px solid #dde3ed;
            color:#64748b; padding:9px 18px; border-radius:8px;
            font-size:.82rem; cursor:pointer; transition:background .15s;
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
        <small class="text-muted">Today: {{ now()->format('d M Y') }}</small>
    </div>

    <div class="param-card">

        <div class="param-card-header">
            <div class="hicon"><i class="bi bi-sliders2"></i></div>
            <div>
                <h5>Report Parameters</h5>
                <p>Set date range, aging periods, filters and display options, then click Generate.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('reports.aged-customer-analysis.generate') }}" target="_blank" id="paramForm">

            <div class="param-body">

                {{-- ── Date Range ── --}}
                <div class="group-label"><i class="bi bi-calendar3 me-1"></i>Date Range</div>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label class="field-label"><i class="bi bi-calendar-event"></i> Start Date</label>
                        <input type="date" name="from" id="from" class="form-control"
                               value="{{ now()->startOfMonth()->toDateString() }}" required>
                        <div class="date-presets">
                            <span class="date-preset" data-target="from" data-val="month_start">This Month</span>
                            <span class="date-preset" data-target="from" data-val="qtr_start">This Quarter</span>
                            <span class="date-preset" data-target="from" data-val="year_start">This Year</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="field-label"><i class="bi bi-calendar-check"></i> End Date (Aging Date)</label>
                        <input type="date" name="to" id="to" class="form-control"
                               value="{{ now()->toDateString() }}" required>
                        <div class="date-presets">
                            <span class="date-preset" data-target="to" data-val="today">Today</span>
                            <span class="date-preset" data-target="to" data-val="month_end">Month End</span>
                            <span class="date-preset" data-target="to" data-val="qtr_end">Quarter End</span>
                            <span class="date-preset" data-target="to" data-val="year_end">Year End</span>
                        </div>
                    </div>
                </div>

                {{-- ── Custom Aging Periods ── --}}
                <div class="group-label"><i class="bi bi-calendar-range me-1"></i>Aging Periods (Days)</div>

                <div class="aging-inputs-row">
                    {{-- Period 1 --}}
                    <div class="aging-field">
                        <label>Period 1 (days)</label>
                        <input type="number" name="aging_d1" id="aging_d1"
                               value="30" min="1" max="9999"
                               oninput="updateAgingPreview()">
                        <span class="aging-err" id="err_d1">Must be &gt; 0</span>
                    </div>

                    <div class="aging-sep">–</div>

                    {{-- Period 2 --}}
                    <div class="aging-field">
                        <label>Period 2 (days)</label>
                        <input type="number" name="aging_d2" id="aging_d2"
                               value="60" min="1" max="9999"
                               oninput="updateAgingPreview()">
                        <span class="aging-err" id="err_d2">Must be &gt; Period 1</span>
                    </div>

                    <div class="aging-sep">–</div>

                    {{-- Period 3 --}}
                    <div class="aging-field">
                        <label>Period 3 (days)</label>
                        <input type="number" name="aging_d3" id="aging_d3"
                               value="90" min="1" max="9999"
                               oninput="updateAgingPreview()">
                        <span class="aging-err" id="err_d3">Must be &gt; Period 2</span>
                    </div>
                </div>

                {{-- Live column preview ── --}}
                <div class="aging-preview mb-4 d-none">
                    <div class="aging-preview-label"><i class="bi bi-table me-1"></i>Report columns preview</div>
                    <div class="aging-preview-cols" id="agingPreview">
                        {{-- Populated by JS --}}
                    </div>
                </div>

                {{-- ── Salesman ── --}}
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

                {{-- ── Customer ── --}}
                <div class="group-label"><i class="bi bi-building me-1"></i>Customer</div>
                <div class="row g-3 mb-4">
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

                {{-- ── Display Options ── --}}
                <div class="group-label"><i class="bi bi-toggles me-1"></i>Display Options</div>
                <div class="toggle-grid">

                    <label class="toggle-card" id="card_show_allocated">
                        <input type="checkbox" name="show_allocated" id="show_allocated" value="1">
                        <div class="tc-header">
                            <div class="tc-icon"><i class="bi bi-check2-all"></i></div>
                            <div class="tc-check"><i class="bi bi-check-lg"></i></div>
                        </div>
                        <div class="tc-title">Show Also Allocated</div>
                        <div class="tc-desc">Include fully paid / allocated transactions.</div>
                    </label>

                    <label class="toggle-card" id="card_summary_only">
                        <input type="checkbox" name="summary_only" id="summary_only" value="1">
                        <div class="tc-header">
                            <div class="tc-icon"><i class="bi bi-list-columns-reverse"></i></div>
                            <div class="tc-check"><i class="bi bi-check-lg"></i></div>
                        </div>
                        <div class="tc-title">Summary Only</div>
                        <div class="tc-desc">Show customer totals only.</div>
                    </label>

                    <label class="toggle-card" id="card_suppress_zeros">
                        <input type="checkbox" name="suppress_zeros" id="suppress_zeros" value="1">
                        <div class="tc-header">
                            <div class="tc-icon"><i class="bi bi-slash-circle"></i></div>
                            <div class="tc-check"><i class="bi bi-check-lg"></i></div>
                        </div>
                        <div class="tc-title">Suppress Zeros</div>
                        <div class="tc-desc">Hide customers with zero balance.</div>
                    </label>

                </div>

            </div>

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
            /* ── Date presets ─────────────────────────────────────────────── */
            const now = new Date();
            const pad = n => String(n).padStart(2, '0');
            const ymd = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
            const q   = m => Math.floor(m / 3);

            const presets = {
                today:       () => ymd(now),
                month_start: () => ymd(new Date(now.getFullYear(), now.getMonth(), 1)),
                month_end:   () => ymd(new Date(now.getFullYear(), now.getMonth()+1, 0)),
                qtr_start:   () => ymd(new Date(now.getFullYear(), q(now.getMonth())*3, 1)),
                qtr_end:     () => ymd(new Date(now.getFullYear(), q(now.getMonth())*3+3, 0)),
                year_start:  () => ymd(new Date(now.getFullYear(), 0, 1)),
                year_end:    () => ymd(new Date(now.getFullYear(), 11, 31)),
            };

            document.querySelectorAll('.date-preset').forEach(btn => {
                btn.addEventListener('click', () => {
                    const fn     = presets[btn.dataset.val];
                    const target = btn.dataset.target || 'to';
                    if (fn) document.getElementById(target).value = fn();
                });
            });

            /* ── Aging period preview ────────────────────────────────────── */
            function updateAgingPreview() {
                const d1 = parseInt(document.getElementById('aging_d1').value) || 0;
                const d2 = parseInt(document.getElementById('aging_d2').value) || 0;
                const d3 = parseInt(document.getElementById('aging_d3').value) || 0;

                // Validate order
                let valid = true;

                const e1 = document.getElementById('err_d1');
                const e2 = document.getElementById('err_d2');
                const e3 = document.getElementById('err_d3');
                const i1 = document.getElementById('aging_d1');
                const i2 = document.getElementById('aging_d2');
                const i3 = document.getElementById('aging_d3');

                // d1 must be > 0
                if (d1 < 1) {
                    e1.style.display = 'block'; i1.classList.add('input-error'); valid = false;
                } else {
                    e1.style.display = 'none'; i1.classList.remove('input-error');
                }

                // d2 must be > d1
                if (d2 <= d1) {
                    e2.style.display = 'block'; i2.classList.add('input-error'); valid = false;
                } else {
                    e2.style.display = 'none'; i2.classList.remove('input-error');
                }

                // d3 must be > d2
                if (d3 <= d2) {
                    e3.style.display = 'block'; i3.classList.add('input-error'); valid = false;
                } else {
                    e3.style.display = 'none'; i3.classList.remove('input-error');
                }

                document.getElementById('genBtn').disabled = !valid;

                // Build preview pills
                const preview = document.getElementById('agingPreview');

                if (!valid) {
                    preview.innerHTML = '<span style="font-size:.72rem;color:#ef4444;"><i class="bi bi-exclamation-triangle me-1"></i>Fix period values above</span>';
                    return;
                }

                const cols = [
                    { label: 'Customer',            cls: '' },
                    { label: 'Days',                cls: '' },
                    { label: 'Current',             cls: 'current-pill' },
                    { label: `1–${d1} Days`,        cls: '' },
                    { label: `${d1+1}–${d2} Days`,  cls: '' },
                    { label: `${d2+1}–${d3} Days`,  cls: '' },
                    { label: `Over ${d3} Days`,     cls: '' },
                    { label: 'Total Balance',       cls: 'total-pill' },
                ];

                preview.innerHTML = cols.map((c, i) =>
                    (i > 0 && i < cols.length ? '<span class="aging-arrow">›</span>' : '') +
                    `<span class="aging-col-pill ${c.cls}">${c.label}</span>`
                ).join('');
            }

            // Run on load to show default preview
            updateAgingPreview();

            /* ── Toggle cards ────────────────────────────────────────────── */
            document.querySelectorAll('.toggle-card').forEach(card => {
                const cb = card.querySelector('input[type="checkbox"]');
                if (cb.checked) card.classList.add('active');
                card.addEventListener('click', () => {
                    requestAnimationFrame(() => card.classList.toggle('active', cb.checked));
                });
            });

            /* ── Salesman → Customer AJAX cascade ───────────────────────── */
            const salesmanSel = document.getElementById('salesmanSel');
            const customerSel = document.getElementById('customerSel');
            const spinner     = document.getElementById('custSpinner');
            const countBadge  = document.getElementById('custCount');
            const allOptions  = Array.from(customerSel.options).map(o => ({ v: o.value, t: o.text }));

            function rebuildCustomers(list) {
                customerSel.innerHTML = '';
                list.forEach(item => {
                    const o = document.createElement('option');
                    o.value = item.v ?? item.debtor_no ?? '';
                    o.textContent = item.t ?? item.name ?? '';
                    customerSel.appendChild(o);
                });
                countBadge.textContent = list.filter(i => (i.v ?? i.debtor_no) !== '').length + ' records';
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
                        rebuildCustomers([{ v: '', t: '— All Customers —' }].concat(
                            data.map(c => ({ v: c.debtor_no, t: c.name }))
                        ));
                    })
                    .catch(() => rebuildCustomers(allOptions))
                    .finally(() => { spinner.classList.remove('on'); customerSel.disabled = false; });
            });

            /* ── Reset ───────────────────────────────────────────────────── */
            document.getElementById('resetBtn').addEventListener('click', () => {
                document.getElementById('from').value     = '{{ now()->startOfMonth()->toDateString() }}';
                document.getElementById('to').value       = '{{ now()->toDateString() }}';
                document.getElementById('aging_d1').value = '30';
                document.getElementById('aging_d2').value = '60';
                document.getElementById('aging_d3').value = '90';
                updateAgingPreview();
                salesmanSel.value = '';
                rebuildCustomers(allOptions);
                document.querySelectorAll('.toggle-card').forEach(card => {
                    const cb = card.querySelector('input[type="checkbox"]');
                    cb.checked = false;
                    card.classList.remove('active');
                });
            });

            /* ── Submit loading state ────────────────────────────────────── */
            document.getElementById('paramForm').addEventListener('submit', function (e) {
                // Final validation before submit
                const d1 = parseInt(document.getElementById('aging_d1').value);
                const d2 = parseInt(document.getElementById('aging_d2').value);
                const d3 = parseInt(document.getElementById('aging_d3').value);
                if (d1 < 1 || d2 <= d1 || d3 <= d2) {
                    e.preventDefault();
                    alert('Please fix the Aging Period values before generating.');
                    return;
                }

                const btn = document.getElementById('genBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:13px;height:13px;"></span> Generating…';
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Generate PDF Report';
                }, 5000);
            });
        })();
    </script>
@endpush

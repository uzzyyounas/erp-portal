@extends('layouts.app')

@section('title', 'Item Ledger')

@push('styles')
    <style>
        .param-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(26,58,92,.09);
            overflow: hidden;
            max-width: 900px;
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

        /* ── Spinner / count badge ── */
        .sel-wrap { position:relative; }
        .sel-spin { position:absolute; right:34px; top:50%; transform:translateY(-50%); display:none; }
        .sel-spin.on { display:block; }
        .count-badge {
            font-size:.63rem; padding:1px 7px;
            background:rgba(45,106,159,.13); color:#2d6a9f;
            border-radius:4px; margin-left:5px;
        }

        /* ── Item search ── */
        .item-search-wrap { display:flex; gap:6px; align-items:center; }
        .item-search-wrap .form-control { flex:0 0 160px; }
        .item-search-wrap .form-select  { flex:1; }
        .item-search-btn {
            flex-shrink:0; width:32px; height:32px; border-radius:8px;
            background:#1a3a5c; color:#fff; border:none;
            display:flex; align-items:center; justify-content:center;
            font-size:.8rem; cursor:pointer; transition:background .15s;
        }
        .item-search-btn:hover { background:#2d6a9f; }

        /* ── Toggle cards ── */
        .toggle-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        @media (max-width:600px) { .toggle-grid { grid-template-columns: repeat(2,1fr); } }
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
        .toggle-card.active .tc-check {
            background: #1a3a5c; border-color: #1a3a5c; color: #fff;
        }
        .toggle-card .tc-label {
            font-size: .75rem; font-weight: 600; color: #374151;
        }
        .toggle-card .tc-desc {
            font-size: .66rem; color: #94a3b8; margin-top: 1px;
        }

        /* ── Radio group (group-by) ── */
        .radio-group { display:flex; gap:10px; flex-wrap:wrap; }
        .radio-card {
            flex:1; min-width:120px;
            border:1.5px solid #e2e8f0; border-radius:10px;
            padding:12px 14px; cursor:pointer;
            transition:all .18s; user-select:none;
            display:flex; align-items:center; gap:10px;
        }
        .radio-card input[type="radio"] { position:absolute; opacity:0; pointer-events:none; }
        .radio-card.active { border-color:#1a3a5c; background:#eff6ff; }
        .radio-card .rc-dot {
            width:16px; height:16px; border-radius:50%;
            border:2px solid #cbd5e1; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            transition:all .18s;
        }
        .radio-card.active .rc-dot { border-color:#1a3a5c; background:#1a3a5c; }
        .radio-card.active .rc-dot::after {
            content:''; width:6px; height:6px; border-radius:50%; background:#fff;
        }
        .radio-card .rc-label { font-size:.76rem; font-weight:600; color:#374151; }
        .radio-card .rc-sub   { font-size:.66rem; color:#94a3b8; }

        /* ── Footer bar ── */
        .footer-bar {
            padding: 18px 28px;
            background: #f8fafc;
            border-top: 1px solid #e8edf3;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            flex-wrap: wrap;
        }
        .btn-reset {
            font-size:.8rem; padding:8px 18px; border-radius:8px;
            border:1.5px solid #dde3ed; background:#fff; color:#475569;
            cursor:pointer; transition:all .15s; display:flex; align-items:center; gap:6px;
        }
        .btn-reset:hover { border-color:#94a3b8; background:#f1f5f9; }
        .btn-generate {
            font-size:.82rem; padding:10px 24px; border-radius:8px;
            background:linear-gradient(135deg,#1a3a5c,#2d6a9f);
            color:#fff; border:none; cursor:pointer;
            transition:all .18s; display:flex; align-items:center; gap:7px;
            font-weight:600;
        }
        .btn-generate:hover:not(:disabled) { opacity:.9; transform:translateY(-1px); }
        .btn-generate:disabled { opacity:.55; cursor:not-allowed; transform:none; }
    </style>
@endpush

{{--@section('breadcrumb')--}}
{{--    <li class="breadcrumb-item text-muted">Reports</li>--}}
{{--    <li class="breadcrumb-item active">Product Sale Report</li>--}}
{{--@endsection--}}

@section('content')
    <div class="container-fluid">

        <div class="page-header">
            <h4><i class="bi bi-bar-chart-steps me-2" style="color:#1a3a5c;"></i>Item Ledger Report</h4>
            <small class="text-muted">Today: {{ now()->format('d M Y') }}</small>
        </div>

        <div class="param-card">

            {{-- ── Header ── --}}
            <div class="param-card-header">
                <div class="hicon"><i class="bi bi-bar-chart-line"></i></div>
                <div>
                    <h5>Report Parameters</h5>
                    <p>Filter by date, salesman, area, customer, category and item</p>
                </div>
            </div>

            <form id="paramForm"
                  action="{{ route('reports.item-ledger.generate') }}"
                  method="GET"
                  target="_blank">

                <div class="param-body">

                    {{-- ── 1. Date Range ── --}}
                    <div class="group-label">Date Range</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="field-label"><i class="bi bi-calendar-event"></i> Start Date</label>
                            <input type="date" id="from" name="from" class="form-control"
                                   value="{{ now()->startOfMonth()->toDateString() }}">
                            <div class="date-presets">
                                <span class="date-preset" data-target="from" data-val="month_start">Month Start</span>
                                <span class="date-preset" data-target="from" data-val="qtr_start">Qtr Start</span>
                                <span class="date-preset" data-target="from" data-val="year_start">Year Start</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label"><i class="bi bi-calendar-check"></i> End Date</label>
                            <input type="date" id="to" name="to" class="form-control"
                                   value="{{ now()->toDateString() }}">
                            <div class="date-presets">
                                <span class="date-preset" data-target="to" data-val="today">Today</span>
                                <span class="date-preset" data-target="to" data-val="month_end">Month End</span>
                                <span class="date-preset" data-target="to" data-val="qtr_end">Qtr End</span>
                                <span class="date-preset" data-target="to" data-val="year_end">Year End</span>
                            </div>
                        </div>
                    </div>


                    {{-- ── 4. Inventory Category & Item ── --}}
                    <div class="group-label">Product / Inventory</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-5">
                            <label class="field-label" for="categorySel">
                                <i class="bi bi-tag"></i> Inventory Category
                            </label>
                            <select id="categorySel" name="category_id" class="form-select">
                                <option value="">— No Category Filter —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->category_id }}">{{ $cat->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="field-label"><i class="bi bi-box-seam"></i> Item</label>
                            <div class="item-search-wrap">
                                <input type="text" id="itemSearch" placeholder="Search…" class="form-control"
                                       autocomplete="off">
                                <div class="sel-wrap" style="flex:1; position:relative;">
                                    <select id="itemSel" name="stock_id" class="form-select">
                                        <option value="">— All Items —</option>
                                    </select>
                                    <span class="spinner-border spinner-border-sm sel-spin text-primary" id="itemSpinner"></span>
                                </div>
                                <button type="button" class="item-search-btn" id="itemSearchBtn" title="Search">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>{{-- /param-body --}}

                {{-- ── Footer ── --}}
                <div class="footer-bar">
                    <button type="button" class="btn-reset" id="resetBtn">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                    <button type="submit" class="btn-generate" id="genBtn">
                        <i class="bi bi-file-earmark-pdf disable"></i> Generate PDF Report
                    </button>
                </div>

            </form>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            /* ── Date presets ─────────────────────────────────────────────────── */
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

            /* ── Radio cards (group-by) ──────────────────────────────────────── */
            document.querySelectorAll('#groupByGroup .radio-card').forEach(card => {
                const rb = card.querySelector('input[type="radio"]');
                card.addEventListener('click', () => {
                    document.querySelectorAll('#groupByGroup .radio-card').forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                });
                if (rb.checked) card.classList.add('active');
            });

            /* ── Toggle cards ────────────────────────────────────────────────── */
            document.querySelectorAll('.toggle-card').forEach(card => {
                const cb = card.querySelector('input[type="checkbox"]');
                if (cb && cb.checked) card.classList.add('active');
                card.addEventListener('click', () => {
                    requestAnimationFrame(() => {
                        if (cb) card.classList.toggle('active', cb.checked);
                    });
                });
            });

            /* ── Category → Item AJAX cascade ───────────────────────────────── */
            const categorySel  = document.getElementById('categorySel');
            const itemSel      = document.getElementById('itemSel');
            const itemSpinner  = document.getElementById('itemSpinner');
            const itemSearchEl = document.getElementById('itemSearch');
            let   allItems     = [];   // populated on first load

            function buildItemOptions(items, filterText) {
                const q   = (filterText || '').toLowerCase();
                const out = items.filter(i => !q || i.description.toLowerCase().includes(q));
                itemSel.innerHTML = '<option value="">— All Items —</option>';
                out.forEach(i => {
                    const o = document.createElement('option');
                    o.value = i.stock_id;
                    o.textContent = `[${i.stock_id}] ${i.description}`;
                    itemSel.appendChild(o);
                });
            }

            function fetchItems() {
                const catId = categorySel.value;
                itemSpinner.classList.add('on');
                itemSel.disabled = true;

                const url = `{{ route('reports.product-sale.items') }}` + (catId ? `?category_id=${encodeURIComponent(catId)}` : '');

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(data => {
                        allItems = data;
                        buildItemOptions(data, itemSearchEl.value);
                    })
                    .catch(() => { itemSel.innerHTML = '<option value="">— All Items —</option>'; })
                    .finally(() => { itemSpinner.classList.remove('on'); itemSel.disabled = false; });
            }

            categorySel.addEventListener('change', fetchItems);

            // Live text filter within already-loaded items
            itemSearchEl.addEventListener('input', () => buildItemOptions(allItems, itemSearchEl.value));
            document.getElementById('itemSearchBtn').addEventListener('click', () => buildItemOptions(allItems, itemSearchEl.value));

            // Load all items on page ready
            fetchItems();

            /* ── Reset ───────────────────────────────────────────────────────── */
            document.getElementById('resetBtn').addEventListener('click', () => {
                document.getElementById('from').value = '{{ now()->startOfMonth()->toDateString() }}';
                document.getElementById('to').value   = '{{ now()->toDateString() }}';
                categorySel.value  = '';
                itemSearchEl.value = '';
                fetchItems();

                // Reset radio to 'item'
                document.querySelectorAll('#groupByGroup .radio-card').forEach(c => c.classList.remove('active'));
                document.querySelector('#groupByGroup .radio-card').classList.add('active');
                document.querySelector('#groupByGroup input[value="item"]').checked = true;

                // Uncheck all toggles
                document.querySelectorAll('.toggle-card').forEach(card => {
                    const cb = card.querySelector('input[type="checkbox"]');
                    if (cb) { cb.checked = false; card.classList.remove('active'); }
                });
            });

            /* ── Submit loading state ────────────────────────────────────────── */
            document.getElementById('paramForm').addEventListener('submit', function (e) {
                e.preventDefault();

                alert('PDF Report feature is coming soon!');
            });
            // document.getElementById('paramForm').addEventListener('submit', function () {
            //     const btn = document.getElementById('genBtn');
            //     btn.disabled = true;
            //     btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:13px;height:13px;"></span> Generating…';
            //     setTimeout(() => {
            //         btn.disabled = false;
            //         btn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> Generate PDF Report';
            //     }, 6000);
            // });
        })();
    </script>
@endpush

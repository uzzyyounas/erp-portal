@extends('layouts.app')
@section('title', 'Home')

@push('styles')
    <style>
        /* ══════════════════════════════════ DASHBOARD LAYOUT */
        .dash-3col {
            display: grid;
            grid-template-columns: 220px 1fr 220px;
            gap: 12px;
            align-items: start;
        }
        @media(max-width:1100px) {
            .dash-3col { grid-template-columns: 200px 1fr; }
            .dash-right { display: none; }
        }
        @media(max-width:768px) {
            .dash-3col { grid-template-columns: 1fr; }
            .dash-left  { display: none; }
        }

        /* ══════════════════════════════════ PORTLETS */
        .portlet { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: var(--shadow-card); margin-bottom: 12px; overflow: hidden; }
        .portlet-hd {
            background: linear-gradient(180deg,#f5f8fe 0%,#edf1f9 100%);
            border-bottom: 1px solid var(--border);
            padding: 6px 11px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .portlet-hd-title {
            font-family: var(--font-hd); font-size: .72rem; font-weight: 700;
            color: var(--brand); display: flex; align-items: center; gap: 5px;
        }
        .portlet-hd-title i { font-size: .78rem; }
        .portlet-hd-actions { display: flex; gap: 3px; }
        .phd-btn {
            width: 18px; height: 18px; border-radius: 3px; border: 1px solid transparent;
            display: flex; align-items: center; justify-content: center;
            font-size: .6rem; color: var(--text-sm); cursor: pointer; transition: all .12s;
        }
        .phd-btn:hover { background: var(--border-lt); border-color: var(--border); }
        .portlet-bd { padding: 10px 12px; }
        .portlet-bd.p0 { padding: 0; }

        /* ══════════════════════════════════ PAGE TITLE */
        .dash-page-title {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 14px;
        }
        .dash-page-title h2 {
            font-family: var(--font-hd); font-size: 1.15rem; font-weight: 800;
            color: var(--text); margin: 0;
        }
        .dash-page-title-right { display: flex; align-items: center; gap: 8px; font-size: .72rem; color: var(--text-sm); }
        .dash-page-title-right a { color: var(--brand); text-decoration: none; font-weight: 600; }
        .dash-page-title-right a:hover { text-decoration: underline; }

        /* ══════════════════════════════════ REMINDER WIDGET */
        .reminder-item {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: start;
            gap: 10px;
            padding: 7px 0;
            border-bottom: 1px dashed var(--border-lt);
        }
        .reminder-item:last-child { border-bottom: none; padding-bottom: 0; }
        .reminder-count {
            font-family: var(--font-mono);
            font-size: 1.05rem; font-weight: 800; line-height: 1.2;
            white-space: nowrap; text-align: right;
            padding: 1px 6px;
            border-radius: 4px;
            background: rgba(0,0,0,.04);
        }
        .reminder-count.c-amber { color: var(--amber); background: var(--amber-lt); }
        .reminder-count.c-blue  { color: var(--blue);  background: var(--blue-lt); }
        .reminder-count.c-red   { color: var(--red);   background: var(--red-lt); }
        .reminder-count.c-green { color: var(--green); background: var(--green-lt); }
        .reminder-link {
            font-size: .74rem; font-weight: 600; color: var(--brand);
            text-decoration: none; line-height: 1.4; word-break: break-word;
            padding-top: 2px;
        }
        .reminder-link:hover { text-decoration: underline; color: var(--accent); }

        /* ══════════════════════════════════ TILE CARDS */
        .tile-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 8px; margin-bottom: 12px; }
        @media(max-width:900px) { .tile-grid { grid-template-columns: repeat(2,1fr); } }

        .tile-card {
            border-radius: var(--radius-md); padding: 14px 14px 12px;
            border: 1px solid var(--border); position: relative;
            overflow: hidden; cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            text-decoration: none; display: block;
        }
        .tile-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,.13); text-decoration: none; }
        .tile-card.tc-orange { background: linear-gradient(135deg,#fff8f0,#fff3e3); border-color: #f5c88a; }
        .tile-card.tc-pink   { background: linear-gradient(135deg,#fff0f5,#ffe4ef); border-color: #f5a0c0; }
        .tile-card.tc-teal   { background: linear-gradient(135deg,#f0fbf9,#dff5f0); border-color: #80cfc3; }
        .tile-card.tc-blue   { background: linear-gradient(135deg,#f0f6ff,#e0eeff); border-color: #90b8f0; }
        .tile-card .tc-label { font-family: var(--font-hd); font-size: .7rem; font-weight: 700; color: var(--text-md); margin-bottom: 6px; }
        .tile-card .tc-val   { font-family: var(--font-mono); font-size: 1.5rem; font-weight: 700; color: var(--text); line-height: 1; }
        .tile-card .tc-sub   { font-size: .65rem; color: var(--text-sm); margin-top: 3px; }
        .tile-card .tc-icon  {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            font-size: 2.2rem; opacity: .12;
        }
        .tile-card.tc-orange .tc-icon { color: #e65100; }
        .tile-card.tc-pink   .tc-icon { color: #c2185b; }
        .tile-card.tc-teal   .tc-icon { color: #00796b; }
        .tile-card.tc-blue   .tc-icon { color: #1565c0; }

        /* ══════════════════════════════════ SHORTCUT GROUP */
        .shortcut-section { margin-bottom: 4px; }
        .shortcut-section-title {
            font-family: var(--font-hd); font-size: .72rem; font-weight: 700;
            color: var(--brand); display: flex; align-items: center; gap: 6px; margin-bottom: 8px;
        }
        .shortcut-cols { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; }
        @media(max-width:700px) { .shortcut-cols { grid-template-columns: repeat(2,1fr); } }

        .shortcut-col-hd {
            font-family: var(--font-hd); font-size: .68rem; font-weight: 700;
            color: var(--text-md); margin-bottom: 6px; padding-bottom: 4px;
            border-bottom: 1px solid var(--border-lt);
        }
        .shortcut-link {
            display: flex; align-items: center; gap: 6px;
            font-size: .75rem; color: var(--brand);
            text-decoration: none; padding: 2px 0; transition: color .12s;
        }
        .shortcut-link:hover { color: var(--accent); text-decoration: underline; }
        .shortcut-link i { font-size: .72rem; color: var(--text-sm); }

        /* ══════════════════════════════════ KPI TABLE */
        .kpi-table-wrap { overflow-x: auto; }
        .kpi-table {
            width: 100%; border-collapse: collapse;
            font-size: .77rem;
        }
        .kpi-table thead th {
            font-family: var(--font-hd); font-size: .63rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .5px;
            background: #f6f8fc; color: var(--text-sm);
            padding: 7px 12px; border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }
        .kpi-table tbody td {
            padding: 7px 12px; border-bottom: 1px solid var(--border-lt);
            color: var(--text-md);
        }
        .kpi-table tbody tr:hover td { background: #f6f9fe; }
        .kpi-table .indicator-name { font-weight: 600; color: var(--text); }
        .kpi-table .period-link { color: var(--brand); text-decoration: none; font-size: .73rem; }
        .kpi-table .period-link:hover { text-decoration: underline; }
        .kpi-table .val-cell { font-family: var(--font-mono); font-size: .8rem; font-weight: 600; color: var(--text); }
        .kpi-table .change-cell { font-weight: 700; font-size: .8rem; white-space: nowrap; }
        .change-up   { color: var(--green); }
        .change-down { color: var(--red); }
        .sparkline-cell { width: 90px; }
        .sparkline-cell canvas { display: block; }

        /* ══════════════════════════════════ KPI BIG CARDS (top row) */
        .kpi-big-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 8px; margin-bottom: 10px; }
        @media(max-width:900px) { .kpi-big-grid { grid-template-columns: repeat(2,1fr); } }

        .kpi-big {
            background: var(--card); border: 1px solid var(--border);
            border-radius: var(--radius-md); padding: 14px;
            box-shadow: var(--shadow-card); position: relative; overflow: hidden;
        }
        .kpi-big-label {
            font-family: var(--font-hd); font-size: .6rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .7px; color: var(--text-sm);
            margin-bottom: 6px;
        }
        .kpi-big-change {
            font-family: var(--font-hd); font-size: 1.7rem; font-weight: 800;
            line-height: 1; letter-spacing: -.5px;
        }
        .kpi-big canvas { margin-top: 8px; display: block; }
        .kpi-big::before {
            content: '';
            position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
        }
        .kb-green::before { background: var(--green); }
        .kb-blue::before  { background: var(--blue); }
        .kb-amber::before { background: var(--accent); }
        .kb-red::before   { background: var(--red); }

        /* ══════════════════════════════════ RIGHT PANEL - GAUGE */
        .gauge-wrap { text-align: center; padding: 8px 4px 4px; }
        .gauge-wrap canvas { max-width: 160px; margin: 0 auto; display: block; }
        .gauge-val {
            font-family: var(--font-mono); font-size: 2rem; font-weight: 700;
            color: var(--text); line-height: 1; margin-top: -10px;
        }
        .gauge-lbl { font-size: .62rem; text-transform: uppercase; letter-spacing: .5px; color: var(--text-sm); margin-top: 2px; font-family: var(--font-hd); font-weight: 700; }
        .gauge-range { display: flex; justify-content: space-between; font-family: var(--font-mono); font-size: .62rem; color: var(--text-sm); margin-top: 4px; padding: 0 10px; }

        /* ══════════════════════════════════ RIGHT PANEL - TOP CUSTOMERS */
        .cust-row {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 0; border-bottom: 1px solid var(--border-lt);
            font-size: .73rem;
        }
        .cust-row:last-child { border: none; }
        .cust-rank { font-family: var(--font-mono); font-size: .65rem; font-weight: 700; color: var(--text-sm); width: 16px; }
        .cust-name { flex:1; font-weight: 600; color: var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .cust-val  { font-family: var(--font-mono); font-size: .72rem; font-weight: 700; color: var(--brand); }

        /* ══════════════════════════════════ SALES REP ROWS */
        .rep-row {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 0; border-bottom: 1px solid var(--border-lt);
            font-size: .73rem;
        }
        .rep-row:last-child { border: none; }
        .rep-num { font-family: var(--font-mono); font-size: .65rem; color: var(--text-sm); width: 14px; }
        .rep-name { flex:1; font-weight: 600; color: var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .rep-bar  { width: 60px; height: 4px; background: var(--border); border-radius: 2px; overflow: hidden; }
        .rep-fill { height: 100%; background: var(--brand); border-radius: 2px; }
        .rep-val  { font-family: var(--font-mono); font-size: .7rem; font-weight: 700; color: var(--brand); min-width: 52px; text-align: right; }

        /* ══════════════════════════════════ COMPARE CHART MINI */
        .compare-select {
            font-size: .7rem; padding: 2px 6px; border-radius: var(--radius);
            border: 1px solid var(--border); color: var(--text-md);
            font-family: var(--font); background: var(--card);
        }

        /* ══════════════════════════════════ MONTHLY TREND */
        .trend-legend { display: flex; gap: 12px; margin-bottom: 10px; }
        .tl-item { display: flex; align-items: center; gap: 5px; font-size: .7rem; color: var(--text-sm); font-weight: 600; }
        .tl-dot  { width: 10px; height: 4px; border-radius: 2px; }

        /* ══════════════════════════════════ ANIM */
        @keyframes fadeUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .anim { opacity:0; animation: fadeUp .4s ease forwards; }
        .d1{animation-delay:.04s}.d2{animation-delay:.09s}.d3{animation-delay:.14s}
        .d4{animation-delay:.19s}.d5{animation-delay:.24s}.d6{animation-delay:.29s}

        /* ══════════════════════════════════════ EXPAND MODAL */
        .exp-overlay {
            position: fixed; inset: 0; z-index: 9900;
            background: rgba(10,20,40,.55);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity .22s ease;
        }
        .exp-overlay.open { opacity: 1; pointer-events: all; }

        .exp-modal {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 24px 80px rgba(0,0,0,.28), 0 0 0 1px rgba(0,0,0,.06);
            width: min(92vw, 960px);
            max-height: 88vh;
            display: flex; flex-direction: column;
            transform: translateY(22px) scale(.97);
            transition: transform .24s cubic-bezier(.34,1.2,.64,1);
            overflow: hidden;
        }
        .exp-overlay.open .exp-modal {
            transform: translateY(0) scale(1);
        }

        .exp-modal-hd {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 20px;
            background: linear-gradient(135deg, var(--brand) 0%, #2d6a9f 100%);
            flex-shrink: 0;
        }
        .exp-modal-hd-icon {
            width: 34px; height: 34px; border-radius: 8px;
            background: rgba(255,255,255,.18);
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem; color: #fff; flex-shrink: 0;
        }
        .exp-modal-hd h5 {
            font-family: var(--font-hd); font-size: .88rem; font-weight: 800;
            color: #fff; margin: 0; flex: 1; letter-spacing: .2px;
        }
        .exp-modal-hd small {
            font-size: .65rem; color: rgba(255,255,255,.55); margin-left: 2px;
        }
        .exp-close {
            width: 28px; height: 28px; border-radius: 6px;
            border: 1px solid rgba(255,255,255,.25); background: rgba(255,255,255,.12);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-size: .82rem; cursor: pointer; flex-shrink: 0;
            transition: background .15s; margin-left: auto;
        }
        .exp-close:hover { background: rgba(255,255,255,.25); }

        /* Tab bar inside modal */
        .exp-tabs {
            display: flex; border-bottom: 2px solid var(--border);
            background: #f8fafd; flex-shrink: 0; padding: 0 20px;
        }
        .exp-tab {
            padding: 9px 16px; font-size: .75rem; font-weight: 700;
            color: var(--text-sm); cursor: pointer; border: none; background: none;
            border-bottom: 2px solid transparent; margin-bottom: -2px;
            font-family: var(--font-hd); transition: color .15s;
            display: flex; align-items: center; gap: 5px;
        }
        .exp-tab:hover { color: var(--brand); }
        .exp-tab.active { color: var(--brand); border-bottom-color: var(--brand); }

        /* Body */
        .exp-modal-body {
            flex: 1; overflow-y: auto; padding: 18px 22px;
        }
        .exp-modal-body::-webkit-scrollbar { width: 5px; }
        .exp-modal-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        /* Summary stat pills inside modal */
        .exp-stats {
            display: grid; grid-template-columns: repeat(4,1fr); gap: 10px;
            margin-bottom: 18px;
        }
        @media(max-width:600px) { .exp-stats { grid-template-columns: repeat(2,1fr); } }
        .exp-stat {
            background: var(--bg); border: 1px solid var(--border);
            border-radius: 8px; padding: 11px 14px;
            border-left: 3px solid var(--brand);
        }
        .exp-stat-val {
            font-family: var(--font-mono); font-size: 1.15rem; font-weight: 700;
            color: var(--brand); line-height: 1; font-variant-numeric: tabular-nums;
        }
        .exp-stat-lbl { font-size: .65rem; font-weight: 700; color: var(--text-sm); margin-top: 3px; text-transform: uppercase; letter-spacing: .4px; }

        /* Big chart canvas */
        .exp-chart-wrap { position: relative; width: 100%; margin-bottom: 18px; }
        .exp-chart-wrap canvas { width: 100% !important; }

        /* Full data table inside modal */
        .exp-table {
            width: 100%; border-collapse: collapse;
            font-size: .78rem;
        }
        .exp-table thead th {
            background: #f2f5fb; color: var(--text-sm);
            font-family: var(--font-hd); font-size: .63rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: .5px;
            padding: 8px 12px; border-bottom: 2px solid var(--border);
            position: sticky; top: 0; z-index: 1; white-space: nowrap;
        }
        .exp-table tbody td {
            padding: 8px 12px; border-bottom: 1px solid var(--border-lt);
            color: var(--text-md); vertical-align: middle;
        }
        .exp-table tbody tr:hover td { background: #f5f8fe; }
        .exp-table .rank-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 20px; height: 20px; border-radius: 5px;
            font-family: var(--font-mono); font-size: .62rem; font-weight: 800;
            background: var(--bg); border: 1px solid var(--border); color: var(--text-sm);
        }
        .exp-table .rank-badge.g1 { background:#fef3c7;border-color:#fbbf24;color:#92400e; }
        .exp-table .rank-badge.g2 { background:#f1f5f9;border-color:#94a3b8;color:#475569; }
        .exp-table .rank-badge.g3 { background:#fff7ed;border-color:#fb923c;color:#9a3412; }
        .exp-table .bar-cell { width: 120px; }
        .exp-table .bar-bg {
            height: 6px; border-radius: 4px;
            background: var(--border); overflow: hidden;
        }
        .exp-table .bar-fill { height: 100%; border-radius: 4px; transition: width .6s ease; }
        .exp-table .val-mono {
            font-family: var(--font-mono); font-weight: 700; color: var(--brand);
            font-variant-numeric: tabular-nums; white-space: nowrap;
        }

        /* Two-col layout inside modal (chart left, pie right) */
        .exp-grid-2 { display: grid; grid-template-columns: 1fr 280px; gap: 18px; }
        @media(max-width:700px) { .exp-grid-2 { grid-template-columns: 1fr; } }
        .exp-pie-legend { margin-top: 12px; }
        .exp-pie-row {
            display: flex; align-items: center; gap: 8px;
            padding: 4px 0; border-bottom: 1px solid var(--border-lt); font-size: .73rem;
        }
        .exp-pie-row:last-child { border: none; }
        .exp-pie-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
        .exp-pie-name { flex:1; color: var(--text-md); font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .exp-pie-pct  { font-family: var(--font-mono); font-size:.7rem; font-weight:700; color:var(--brand); }

        /* Comparative chart tab */
        .exp-compare-row {
            display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px;
        }
        .exp-period-btn {
            font-size: .72rem; font-weight: 700; padding: 4px 12px;
            border-radius: 20px; border: 1px solid var(--border);
            background: #fff; color: var(--text-md); cursor: pointer;
            transition: all .14s; font-family: var(--font);
        }
        .exp-period-btn:hover { border-color: var(--brand); color: var(--brand); }
        .exp-period-btn.active { background: var(--brand); color: #fff; border-color: var(--brand); }

    </style>


@endpush

@section('content')

    {{-- Page title --}}
    <div class="dash-page-title anim d1">
        <h2>Home</h2>
        <div class="dash-page-title-right">
            Viewing: <a href="#">Portlet date settings ▾</a>
            &nbsp;|&nbsp; <a href="#">Personalise ▾</a>
            &nbsp;|&nbsp; <a href="#">Layout ▾</a>
        </div>
    </div>

    {{-- Three-column dashboard --}}
    <div class="dash-3col">

        {{-- ════════════════ LEFT COLUMN ════════════════ --}}
        <div class="dash-left">

            {{-- Reminders --}}
            <div class="portlet anim d2">
                <div class="portlet-hd">
                    <div class="portlet-hd-title"><i class="bi bi-bell-fill"></i> Reminders</div>
                    <div class="portlet-hd-actions">
                        <div class="phd-btn" title="Refresh"><i class="bi bi-arrow-clockwise"></i></div>
                        <div class="phd-btn" title="Settings"><i class="bi bi-gear"></i></div>
                    </div>
                </div>
                <div class="portlet-bd">
                    @php
                        $overdueLabel = $overdueCount > 0 ? 'Overdue Invoices' : 'No Overdue Invoices';
                    @endphp
                    <div class="reminder-item">
                        <div class="reminder-count c-amber">{{ number_format($monthlySales/1000,0) }}K</div>
                        <a href="#" class="reminder-link">Sales this Month</a>
                    </div>
                    @if($overdueCount > 0)
                        <div class="reminder-item">
                            <div class="reminder-count c-red">{{ $overdueCount }}</div>
                            <a href="#" class="reminder-link">Overdue Invoices to collect</a>
                        </div>
                    @endif
                    <div class="reminder-item">
                        <div class="reminder-count c-blue">{{ number_format($totalReceivables/1000,0) }}K</div>
                        <a href="#" class="reminder-link">Outstanding Receivables</a>
                    </div>
                    <div class="reminder-item">
                        <div class="reminder-count c-amber">{{ number_format($totalPayables/1000,0) }}K</div>
                        <a href="#" class="reminder-link">Payables due to Suppliers</a>
                    </div>
                </div>
            </div>

            {{-- Comparative Sales mini chart --}}
            <div class="portlet anim d3">
                <div class="portlet-hd">
                    <div class="portlet-hd-title"><i class="bi bi-bar-chart-fill"></i> Comparative Sales</div>
                    <div class="portlet-hd-actions">
                        <div class="phd-btn exp-trigger" data-expand="comparative" title="Expand"><i class="bi bi-arrows-angle-expand"></i></div>
                    </div>
                </div>
                <div class="portlet-bd">
                    <select class="compare-select mb-2">
                        <option>This Year vs Last Year</option>
                        <option>This Month vs Last Month</option>
                    </select>
                    <canvas id="compareMini" height="130"></canvas>
                    <div style="display:flex;justify-content:space-between;margin-top:6px;">
                        <div style="display:flex;align-items:center;gap:4px;font-size:.65rem;color:var(--text-sm);">
                            <div style="width:10px;height:3px;background:var(--blue);border-radius:2px;"></div> This Year
                        </div>
                        <div style="display:flex;align-items:center;gap:4px;font-size:.65rem;color:var(--text-sm);">
                            <div style="width:10px;height:3px;background:var(--accent);border-radius:2px;"></div> Last Year
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Sales Reps --}}
            <div class="portlet anim d4">
                <div class="portlet-hd">
                    <div class="portlet-hd-title"><i class="bi bi-person-badge-fill"></i> Top Salesmen by Sales</div>
                    <div class="portlet-hd-actions">
                        <div class="phd-btn exp-trigger" data-expand="salesmen" title="Expand"><i class="bi bi-arrows-angle-expand"></i></div>
                    </div>
                </div>
                <div class="portlet-bd p0" style="padding:8px 12px;">
                    @php $maxCust = collect($topCustomers)->max('total') ?: 1; @endphp
                    @forelse($topCustomers as $i => $c)
                        <div class="rep-row">
                            <div class="rep-num">{{ $i+1 }}</div>
                            <div class="rep-name">{{ $c->name }}</div>
                            <div class="rep-bar"><div class="rep-fill" style="width:{{ number_format(($c->total/$maxCust)*100,1) }}%;"></div></div>
                            <div class="rep-val">{{ number_format($c->total/1000,1) }}K</div>
                        </div>
                    @empty
                        <div style="text-align:center;color:var(--text-sm);font-size:.75rem;padding:12px;">No data</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ════════════════ MAIN COLUMN ════════════════ --}}
        <div class="dash-main">

            {{-- Tile Cards --}}
            <div class="tile-grid anim d2">
                <a href="#" class="tile-card tc-orange">
                    <div class="tc-label">Total Sales YTD</div>
                    <div class="tc-val">{{ number_format($ytdNetSales/1000000,2) }}M</div>
                    <div class="tc-sub">Net invoiced amount</div>
                    <i class="bi bi-graph-up-arrow tc-icon"></i>
                </a>
                <a href="#" class="tile-card tc-pink">
                    <div class="tc-label">Open Receivables</div>
                    <div class="tc-val">{{ number_format($totalReceivables/1000,0) }}K</div>
                    <div class="tc-sub">{{ $overdueCount }} overdue invoices</div>
                    <i class="bi bi-cash-stack tc-icon"></i>
                </a>
                <a href="#" class="tile-card tc-teal">
                    <div class="tc-label">Gross Profit YTD</div>
                    <div class="tc-val">{{ $gpMarginPct }}%</div>
                    <div class="tc-sub">{{ number_format($ytdGrossProfit/1000,0) }}K earned</div>
                    <i class="bi bi-bar-chart-steps tc-icon"></i>
                </a>
                <a href="#" class="tile-card tc-blue">
                    <div class="tc-label">Total Payables</div>
                    <div class="tc-val">{{ number_format($totalPayables/1000,0) }}K</div>
                    <div class="tc-sub">Outstanding to suppliers</div>
                    <i class="bi bi-credit-card-2-back tc-icon"></i>
                </a>
            </div>

            {{-- Navigation Shortcuts --}}
            <div class="portlet anim d3">
                <div class="portlet-hd">
                    <div class="portlet-hd-title"><i class="bi bi-grid-3x3-gap-fill"></i> Navigation Shortcut Group</div>
                    <div class="portlet-hd-actions">
                        <div class="phd-btn"><i class="bi bi-pencil"></i></div>
                        <div class="phd-btn"><i class="bi bi-x"></i></div>
                    </div>
                </div>
                <div class="portlet-bd">
                    @forelse($sidebarModules as $module)
                        <div class="shortcut-section mb-3">
                            <div class="shortcut-section-title">
                                <i class="bi bi-list-ul" style="color:var(--brand);"></i>
                                <i class="bi {{ $module->icon }}" style="color:{{ $module->color }};"></i>
                                {{ $module->name }}
                            </div>
                            @php
                                $items = $module->activeMenuItems->where('type','!=','divider')->chunk(
                                    max(1, (int)ceil($module->activeMenuItems->where('type','!=','divider')->count() / 4))
                                );
                            @endphp
                            <div class="shortcut-cols">
                                @foreach($items as $chunk)
                                    <div>
                                        <div class="shortcut-col-hd">
                                            @if($loop->first) Reports @elseif($loop->index===1) Forms @else Links @endif
                                        </div>
                                        @foreach($chunk as $item)
                                            <a href="{{ $item->url }}" class="shortcut-link">
                                                <i class="bi {{ $item->icon ?: 'bi-file-text' }}"></i>
                                                {{ $item->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;color:var(--text-sm);font-size:.78rem;padding:14px;">No modules assigned.</div>
                    @endforelse
                </div>
            </div>

            {{-- Key Performance Indicators --}}
            <div class="portlet anim d4">
                <div class="portlet-hd">
                    <div class="portlet-hd-title"><i class="bi bi-speedometer2"></i> Key Performance Indicators</div>
                    <div class="portlet-hd-actions">
                        <div class="phd-btn"><i class="bi bi-arrows-angle-expand"></i></div>
                        <div class="phd-btn"><i class="bi bi-gear"></i></div>
                    </div>
                </div>
                <div class="portlet-bd p0">
                    {{-- Big change numbers --}}
                    @php
                        $sd = $prevMonthlySales>0?(($monthlySales-$prevMonthlySales)/$prevMonthlySales)*100:0;
                        $pd = $prevMonthlyPurchases>0?(($monthlyPurchases-$prevMonthlyPurchases)/$prevMonthlyPurchases)*100:0;
                    @endphp
                    <div class="kpi-big-grid" style="padding:12px 12px 8px;">
                        <div class="kpi-big kb-green">
                            <div class="kpi-big-label">Sales</div>
                            <div class="kpi-big-change change-{{ $sd>=0?'up':'down' }}">
                                {{ $sd>=0?'↑':'↓' }}{{ number_format(abs($sd),1) }}%
                            </div>
                            <canvas id="spark0" height="36"></canvas>
                        </div>
                        <div class="kpi-big kb-blue">
                            <div class="kpi-big-label">Purchases</div>
                            <div class="kpi-big-change {{ $pd<=0?'change-up':'change-down' }}">
                                {{ $pd>=0?'↑':'↓' }}{{ number_format(abs($pd),1) }}%
                            </div>
                            <canvas id="spark1" height="36"></canvas>
                        </div>
                        <div class="kpi-big kb-amber">
                            <div class="kpi-big-label">Gross Profit</div>
                            <div class="kpi-big-change change-up">{{ $gpMarginPct }}%</div>
                            <canvas id="spark2" height="36"></canvas>
                        </div>
                        <div class="kpi-big kb-red">
                            <div class="kpi-big-label">Overdue</div>
                            <div class="kpi-big-change {{ $overdueCount>0?'change-down':'change-up' }}">
                                {{ $overdueCount }}
                            </div>
                            <canvas id="spark3" height="36"></canvas>
                        </div>
                    </div>

                    {{-- Indicator table --}}
                    <div class="kpi-table-wrap" style="border-top:1px solid var(--border);">
                        <table class="kpi-table">
                            <thead>
                            <tr>
                                <th style="width:22px;"></th>
                                <th>Indicator</th>
                                <th>Period</th>
                                <th>Current</th>
                                <th>Previous</th>
                                <th>Change</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><i class="bi bi-caret-down-fill" style="color:var(--text-sm);font-size:.65rem;"></i></td>
                                <td class="indicator-name">Sales</td>
                                <td><a href="#" class="period-link">This Month vs. Last Month</a></td>
                                <td class="val-cell">{{ number_format($monthlySales,0) }}</td>
                                <td class="val-cell">{{ number_format($prevMonthlySales,0) }}</td>
                                <td class="change-cell {{ $sd>=0?'change-up':'change-down' }}">
                                    {{ $sd>=0?'↑':'↓' }} {{ number_format(abs($sd),1) }}%
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="indicator-name">Purchases</td>
                                <td><a href="#" class="period-link">This Month vs. Last Month</a></td>
                                <td class="val-cell">{{ number_format($monthlyPurchases,0) }}</td>
                                <td class="val-cell">{{ number_format($prevMonthlyPurchases,0) }}</td>
                                <td class="change-cell {{ $pd<=0?'change-up':'change-down' }}">
                                    {{ $pd>=0?'↑':'↓' }} {{ number_format(abs($pd),1) }}%
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="indicator-name">Gross Profit</td>
                                <td><a href="#" class="period-link">Year to Date</a></td>
                                <td class="val-cell">{{ number_format($ytdGrossProfit,0) }}</td>
                                <td class="val-cell">—</td>
                                <td class="change-cell change-up">{{ $gpMarginPct }}% margin</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="indicator-name">Receivables</td>
                                <td><a href="#" class="period-link">Outstanding Balance</a></td>
                                <td class="val-cell">{{ number_format($totalReceivables,0) }}</td>
                                <td class="val-cell">—</td>
                                <td class="change-cell {{ $overdueCount>0?'change-down':'change-up' }}">
                                    {{ $overdueCount }} overdue
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="indicator-name">Payables</td>
                                <td><a href="#" class="period-link">Outstanding Balance</a></td>
                                <td class="val-cell">{{ number_format($totalPayables,0) }}</td>
                                <td class="val-cell">—</td>
                                <td class="change-cell change-up">Suppliers</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Monthly Sales Trend --}}
            <div class="portlet anim d5">
                <div class="portlet-hd">
                    <div class="portlet-hd-title"><i class="bi bi-bar-chart-line-fill"></i> Monthly Sales Trend</div>
                    <div class="portlet-hd-actions">
                        <div class="phd-btn exp-trigger" data-expand="trend" title="Expand"><i class="bi bi-arrows-angle-expand"></i></div>
                    </div>
                </div>
                <div class="portlet-bd">
                    <div class="trend-legend">
                        <div class="tl-item"><div class="tl-dot" style="background:#1565c0;"></div>Sales</div>
                        <div class="tl-item"><div class="tl-dot" style="background:#e8a020;"></div>Purchases</div>
                        <div class="tl-item"><div class="tl-dot" style="background:#2e7d32;border-top:2px dashed #2e7d32;height:0;margin-top:2px;"></div>Gross Profit</div>
                    </div>
                    <canvas id="mainTrend" height="140"></canvas>
                </div>
            </div>

        </div>

        {{-- ════════════════ RIGHT COLUMN ════════════════ --}}
        <div class="dash-right">

            {{-- KPI Gauge 1: Receivables --}}
            <div class="portlet anim d3">
                <div class="portlet-hd">
                    <div class="portlet-hd-title"><i class="bi bi-speedometer"></i> KPI Meter</div>
                    <div class="portlet-hd-actions"><div class="phd-btn"><i class="bi bi-gear"></i></div></div>
                </div>
                <div class="portlet-bd">
                    <select class="compare-select w-100 mb-2">
                        <option>Open Receivables</option>
                    </select>
                    <div class="gauge-wrap">
                        <canvas id="gauge1" height="100" style="max-width:160px;margin:0 auto;display:block;"></canvas>
                        <div class="gauge-val">{{ number_format($totalReceivables/1000,0) }}K</div>
                        <div class="gauge-lbl">Open Receivables</div>
                        <div class="gauge-range">
                            <span>0</span>
                            <span>{{ number_format($totalReceivables/1000,0) }}K</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI Gauge 2: GP Margin --}}
            <div class="portlet anim d4">
                <div class="portlet-hd">
                    <div class="portlet-hd-title"><i class="bi bi-speedometer"></i> KPI Meter</div>
                    <div class="portlet-hd-actions"><div class="phd-btn"><i class="bi bi-gear"></i></div></div>
                </div>
                <div class="portlet-bd">
                    <select class="compare-select w-100 mb-2">
                        <option>GP Margin %</option>
                    </select>
                    <div class="gauge-wrap">
                        <canvas id="gauge2" height="100" style="max-width:160px;margin:0 auto;display:block;"></canvas>
                        <div class="gauge-val">{{ $gpMarginPct }}%</div>
                        <div class="gauge-lbl">Gross Profit Margin</div>
                        <div class="gauge-range"><span>0</span><span>100%</span></div>
                    </div>
                </div>
            </div>

            {{-- Top 5 Customers --}}
            <div class="portlet anim d5">
                <div class="portlet-hd">
                    <div class="portlet-hd-title"><i class="bi bi-people-fill"></i> Top Customers By Sales</div>
                    <div class="portlet-hd-actions"><div class="phd-btn exp-trigger" data-expand="customers" title="Expand"><i class="bi bi-arrows-angle-expand"></i></div></div>
                </div>
                <div class="portlet-bd">
                    <select class="compare-select w-100 mb-2">
                        <option>This Month</option>
                        <option>This Year</option>
                    </select>
                    <canvas id="custPie" height="130" style="max-width:160px;margin:0 auto;display:block;"></canvas>
                    <div style="margin-top:10px;">
                        @foreach($topCustomers as $i => $c)
                            <div class="cust-row">
                                <div class="cust-rank">{{ $i+1 }}</div>
                                <div class="cust-name">{{ $c->name }}</div>
                                <div class="cust-val">{{ number_format($c->total/1000,0) }}K</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sales by Category --}}
            <div class="portlet anim d6">
                <div class="portlet-hd">
                    <div class="portlet-hd-title"><i class="bi bi-pie-chart-fill"></i> Sales by Category</div>
                    <div class="portlet-hd-actions"><div class="phd-btn exp-trigger" data-expand="categories" title="Expand"><i class="bi bi-arrows-angle-expand"></i></div></div>
                </div>
                <div class="portlet-bd">
                    <canvas id="catPie" height="130" style="max-width:160px;margin:0 auto;display:block;"></canvas>
                    @foreach($salesByCategory as $i => $cat)
                        <div class="cust-row">
                            <div class="cust-rank">{{ $i+1 }}</div>
                            <div class="cust-name">{{ $cat->category ?: 'Other' }}</div>
                            <div class="cust-val">{{ number_format($cat->total/1000,0) }}K</div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    @php
        /* Pre-compute flat arrays for the expand modal JS — simple @json() only */
        $expCustomers = collect($topCustomers)->map(fn($c) => [
            'name'  => $c->name,
            'total' => (float) $c->total,
        ])->values()->toArray();

        $expSalesmen = collect($topCustomers)->map(fn($c) => [
            'name'  => $c->name,
            'total' => (float) $c->total,
        ])->values()->toArray();

        $expCategories = collect($salesByCategory)->map(fn($c) => [
            'name'  => $c->category ?: 'Other',
            'total' => (float) $c->total,
        ])->values()->toArray();

        $expTopSuppliers = collect($topSuppliers)->map(fn($s) => [
            'name'  => $s->supp_name,
            'total' => (float) $s->total,
        ])->values()->toArray();

        $expTopItems = collect($topItems)->map(fn($i) => [
            'name'  => $i->description ?: $i->stock_id,
            'total' => (float) $i->total,
            'qty'   => (float) $i->qty,
        ])->values()->toArray();
    @endphp


    {{-- ═══════════════════════════════════════ EXPAND MODALS ════════════════ --}}
    <div class="exp-overlay" id="expOverlay" role="dialog" aria-modal="true">
        <div class="exp-modal" id="expModal">

            {{-- Header --}}
            <div class="exp-modal-hd" id="expModalHd">
                <div class="exp-modal-hd-icon" id="expModalIcon"><i class="bi bi-bar-chart-line"></i></div>
                <div>
                    <h5 id="expModalTitle">Report</h5>
                    <small id="expModalSub">{{ now()->format('Y') }} — Expanded View</small>
                </div>
                <button class="exp-close" id="expClose" title="Close (Esc)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- Tab bar --}}
            <div class="exp-tabs" id="expTabs">
                <button class="exp-tab active" data-tab="chart">
                    <i class="bi bi-bar-chart-line"></i> Chart
                </button>
                <button class="exp-tab" data-tab="table">
                    <i class="bi bi-table"></i> Full Data
                </button>
            </div>

            {{-- Body --}}
            <div class="exp-modal-body" id="expModalBody">
                {{-- Content injected by JS --}}
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const overlay = document.getElementById('expOverlay');
            const modalBody = document.getElementById('expModalBody');
            const modalTitle = document.getElementById('expModalTitle');
            const closeBtn = document.getElementById('expClose');
            const tabs = document.querySelectorAll('.exp-tab');

            /* Sample data from blade */
            const modalData = {
                trend: {
                    title: 'Monthly Sales Trend',
                    content: `
                <div class="exp-stats">
                    <div class="exp-stat">
                        <div class="exp-stat-val">{{ number_format($monthlySales,0) }}</div>
                        <div class="exp-stat-lbl">Monthly Sales</div>
                    </div>

                    <div class="exp-stat">
                        <div class="exp-stat-val">{{ number_format($monthlyPurchases,0) }}</div>
                        <div class="exp-stat-lbl">Purchases</div>
                    </div>

                    <div class="exp-stat">
                        <div class="exp-stat-val">{{ $gpMarginPct }}%</div>
                        <div class="exp-stat-lbl">GP Margin</div>
                    </div>

                    <div class="exp-stat">
                        <div class="exp-stat-val">{{ $overdueCount }}</div>
                        <div class="exp-stat-lbl">Overdue</div>
                    </div>
                </div>

                <div class="exp-chart-wrap">
                    <canvas id="expandedTrendChart" height="120"></canvas>
                </div>
            `
                },

                customers: {
                    title: 'Top Customers',
                    content: `
                <table class="exp-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCustomers as $i => $c)
                    <tr>
                        <td>{{ $i+1 }}</td>
                            <td>{{ $c->name }}</td>
                            <td>{{ number_format($c->total,0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            `
                },

                salesmen: {
                    title: 'Top Salesmen',
                    content: `
                <table class="exp-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCustomers as $i => $c)
                    <tr>
                        <td>{{ $i+1 }}</td>
                            <td>{{ $c->name }}</td>
                            <td>{{ number_format($c->total,0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            `
                },

                comparative: {
                    title: 'Comparative Sales',
                    content: `
                <div class="exp-chart-wrap">
                    <canvas id="expandedCompareChart" height="120"></canvas>
                </div>
            `
                },

                categories: {
                    title: 'Sales By Category',
                    content: `
                <table class="exp-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByCategory as $i => $cat)
                    <tr>
                        <td>{{ $i+1 }}</td>
                            <td>{{ $cat->category ?: 'Other' }}</td>
                            <td>{{ number_format($cat->total,0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            `
                }
            };

            /* Open modal */
            document.querySelectorAll('.exp-trigger').forEach(btn => {
                btn.addEventListener('click', function () {

                    const type = this.dataset.expand;

                    if (!modalData[type]) return;

                    modalTitle.innerText = modalData[type].title;
                    modalBody.innerHTML = modalData[type].content;

                    overlay.classList.add('open');

                    /* Draw expanded charts after modal render */
                    setTimeout(() => {

                        if (type === 'trend') {
                            const ctx = document
                                .getElementById('expandedTrendChart')
                                ?.getContext('2d');

                            if (ctx) {
                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: @json($chartLabels),
                                        datasets: [
                                            {
                                                label: 'Sales',
                                                data: @json($chartSales)
                                            },
                                            {
                                                label: 'Purchases',
                                                data: @json($chartPurchases)
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true
                                    }
                                });
                            }
                        }

                        if (type === 'comparative') {
                            const ctx = document
                                .getElementById('expandedCompareChart')
                                ?.getContext('2d');

                            if (ctx) {
                                new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: @json($chartLabels),
                                        datasets: [
                                            {
                                                label: 'This Year',
                                                data: @json($chartSales)
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true
                                    }
                                });
                            }
                        }

                    }, 100);
                });
            });

            /* Close */
            closeBtn.addEventListener('click', () => {
                overlay.classList.remove('open');
            });

            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) {
                    overlay.classList.remove('open');
                }
            });

            /* ESC close */
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    overlay.classList.remove('open');
                }
            });

            /* Tabs */
            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

        });
    </script>
    <script>
        (function(){
            Chart.defaults.font.family = "'Nunito Sans', sans-serif";
            Chart.defaults.color = '#6b7c93';

            const labels    = @json($chartLabels);
            const sales     = @json($chartSales);
            const purchases = @json($chartPurchases);
            const gp        = @json($chartGP);
            const fmtC = v => new Intl.NumberFormat('en',{notation:'compact',maximumFractionDigits:1}).format(v);
            const fmtF = v => new Intl.NumberFormat().format(v);

            /* ── Shared tooltip ──────────────────────────────────────── */
            const tip = {
                backgroundColor:'#1a2332', padding:10, cornerRadius:6,
                titleFont:{family:"'Lexend',sans-serif",size:11,weight:'700'},
                bodyFont:{family:"'JetBrains Mono',monospace",size:10},
                callbacks:{ label:c=>'  '+c.dataset.label+': '+fmtF(c.raw) }
            };

            /* ── 1. Main trend (bars + line) ────────────────────────── */
            const mt = document.getElementById('mainTrend').getContext('2d');
            const sg = mt.createLinearGradient(0,0,0,180);
            sg.addColorStop(0,'rgba(21,101,192,.75)'); sg.addColorStop(1,'rgba(21,101,192,.35)');
            const pg = mt.createLinearGradient(0,0,0,180);
            pg.addColorStop(0,'rgba(232,160,32,.65)'); pg.addColorStop(1,'rgba(232,160,32,.25)');

            new Chart(mt,{
                data:{ labels, datasets:[
                        {type:'bar',  label:'Sales',        data:sales,     backgroundColor:sg, borderRadius:3, borderSkipped:false, order:2},
                        {type:'bar',  label:'Purchases',    data:purchases, backgroundColor:pg, borderRadius:3, borderSkipped:false, order:2},
                        {type:'line', label:'Gross Profit', data:gp, borderColor:'#2e7d32', backgroundColor:'rgba(46,125,50,.07)',
                            pointBackgroundColor:'#2e7d32', pointRadius:3, borderWidth:2, tension:.4, fill:true, order:1},
                    ]},
                options:{
                    responsive:true, maintainAspectRatio:true,
                    interaction:{mode:'index',intersect:false},
                    plugins:{legend:{display:false}, tooltip:tip},
                    scales:{
                        x:{grid:{display:false},border:{display:false},
                            ticks:{font:{size:9,weight:'600'},color:'#94a3b8'}},
                        y:{grid:{color:'#edf1f9'},border:{display:false},
                            ticks:{font:{family:"'JetBrains Mono'",size:9},color:'#94a3b8',callback:fmtC,maxTicksLimit:5}}
                    }
                }
            });

            /* ── 2. Comparative mini chart ──────────────────────────── */
            new Chart(document.getElementById('compareMini'),{
                type:'line',
                data:{ labels, datasets:[
                        {label:'This Year', data:sales, borderColor:'#1565c0', backgroundColor:'rgba(21,101,192,.1)',
                            pointRadius:2, borderWidth:1.8, tension:.4, fill:true},
                        {label:'Last Year', data:sales.map(v=>v*0.78+(Math.random()-.5)*v*.1),
                            borderColor:'#e8a020', backgroundColor:'transparent',
                            pointRadius:2, borderWidth:1.8, tension:.4, borderDash:[4,3]},
                    ]},
                options:{
                    responsive:true, maintainAspectRatio:true,
                    plugins:{legend:{display:false}, tooltip:tip},
                    scales:{
                        x:{grid:{display:false},border:{display:false},ticks:{font:{size:8},color:'#94a3b8'}},
                        y:{grid:{color:'#edf1f9'},border:{display:false},
                            ticks:{font:{family:"'JetBrains Mono'",size:8},color:'#94a3b8',callback:fmtC,maxTicksLimit:4}}
                    }
                }
            });

            /* ── 3. Sparklines ──────────────────────────────────────── */
            const sparkColors = ['#2e7d32','#1565c0','#e65100','#c62828'];
            [sales, purchases, gp, Array(labels.length).fill(0).map((_,i)=>i*2)].forEach((data,i)=>{
                const el = document.getElementById('spark'+i);
                if(!el) return;
                new Chart(el,{
                    type:'line',
                    data:{ labels, datasets:[{
                            data, borderColor:sparkColors[i], backgroundColor:'transparent',
                            pointRadius:0, borderWidth:1.5, tension:.4
                        }]},
                    options:{
                        responsive:true, animation:false,
                        plugins:{legend:{display:false},tooltip:{enabled:false}},
                        scales:{x:{display:false},y:{display:false}}
                    }
                });
            });

            /* ── 4. Gauges (half-doughnut) ──────────────────────────── */
            function makeGauge(id, value, max, color) {
                const el = document.getElementById(id);
                if(!el) return;
                const pct = Math.min(Math.max(value,0),max)/max;
                new Chart(el,{
                    type:'doughnut',
                    data:{ datasets:[{
                            data:[pct,1-pct],
                            backgroundColor:[color,'#e8ecf2'],
                            borderWidth:0, borderRadius:[6,0],
                            circumference:180, rotation:270
                        }]},
                    options:{
                        responsive:true, maintainAspectRatio:true, cutout:'72%',
                        plugins:{legend:{display:false},tooltip:{enabled:false}}
                    }
                });
            }

            makeGauge('gauge1', {{ $totalReceivables }}, Math.max({{ $totalReceivables * 1.2 }}, 1), '#1565c0');
            makeGauge('gauge2', {{ $gpMarginPct }}, 100, '#2e7d32');

            /* ── 5. Customer donut ──────────────────────────────────── */
            const custPalette = ['#1565c0','#2e7d32','#e65100','#6a1b9a','#c62828'];
            const custVals    = @json(collect($topCustomers)->pluck('total'));
            const custLabels  = @json(collect($topCustomers)->pluck('name'));

            function makePie(id, labels, data, colors) {
                const el = document.getElementById(id);
                if(!el) return;
                new Chart(el,{
                    type:'doughnut',
                    data:{ labels, datasets:[{
                            data, backgroundColor:colors,
                            borderColor:'#fff', borderWidth:2, hoverOffset:4
                        }]},
                    options:{
                        responsive:true, maintainAspectRatio:true, cutout:'55%',
                        plugins:{
                            legend:{display:false},
                            tooltip:{backgroundColor:'#1a2332',padding:8,cornerRadius:5,
                                callbacks:{label:c=>'  '+c.label+': '+fmtF(c.raw)}}
                        }
                    }
                });
            }

            makePie('custPie', custLabels, custVals, custPalette);

            const catVals   = @json(collect($salesByCategory)->pluck('total'));
            const catLabels = @json(collect($salesByCategory)->pluck('category'));
            makePie('catPie', catLabels, catVals,
                ['#1565c0','#0d9488','#e8a020','#8b5cf6','#ef4444','#65a30d','#f97316']);

        })();
    </script>
@endpush

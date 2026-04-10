{{--
    AGED RECEIVABLES / PAYABLES PDF TEMPLATE
    ──────────────────────────────────────────
    Special design for aging reports:
    - Entity list as rows
    - Aging buckets (Current, 1-30, 31-60, 60+) as bold columns
    - Heat-map colouring on overdue amounts
    - Summary pie-like totals bar at bottom
--}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size:7.5pt; color:#1e293b; }
@page { margin:12mm 10mm 15mm 10mm; size: {{ $report->pdf_paper_size }} landscape; }

/* ── Top bar ── */
.topbar {
    background:#1a3a5c;
    color:#fff;
    padding:8pt 10pt;
    margin-bottom:8pt;
    display:table; width:100%;
}
.tb-left  { display:table-cell; vertical-align:middle; }
.tb-right { display:table-cell; vertical-align:middle; text-align:right; width:200pt; }
.co-name   { font-size:13pt; font-weight:bold; }
.rpt-name  { font-size:8.5pt; color:rgba(255,255,255,.8); margin-top:2pt; }
.tb-meta   { font-size:6.5pt; color:rgba(255,255,255,.6); line-height:1.6; }

/* ── Info strip ── */
.info-strip {
    display:table; width:100%; margin-bottom:8pt;
}
.info-cell {
    display:table-cell;
    border:1px solid #dce6f0;
    padding:5pt 8pt;
    vertical-align:middle;
    background:#fafbfc;
}
.info-cell:not(:last-child) { border-right:none; }
.info-label { font-size:6pt; text-transform:uppercase; color:#94a3b8; letter-spacing:.3pt; }
.info-value { font-size:9pt; font-weight:bold; color:#1a3a5c; margin-top:1pt; }

/* ── Aging Table ── */
.aging-table {
    width:100%;
    border-collapse:collapse;
    font-size:7pt;
}

/* Header rows */
.aging-table .col-group-header td {
    padding:3pt 6pt;
    font-size:6.5pt;
    font-weight:bold;
    text-transform:uppercase;
    letter-spacing:.3pt;
    border-bottom:none;
}
.aging-table thead tr.main-header th {
    padding:5pt 6pt;
    font-weight:bold;
    font-size:7pt;
    border-right:1px solid rgba(255,255,255,.1);
    text-align:right;
}
.aging-table thead tr.main-header th.text-left { text-align:left; }
.aging-table thead tr.main-header th:last-child { border-right:none; }

/* Header colouring by bucket */
.th-entity   { background:#1a3a5c; color:#fff; }
.th-current  { background:#166534; color:#fff; }
.th-30       { background:#854d0e; color:#fff; }
.th-60       { background:#9a3412; color:#fff; }
.th-over60   { background:#7f1d1d; color:#fff; }
.th-total    { background:#1e1e2e; color:#fff; }
.th-currency { background:#374151; color:#fff; }

/* Data rows */
.aging-table tbody tr td {
    padding:3.5pt 6pt;
    border-bottom:1px solid #f1f5f9;
    text-align:right;
    font-family:DejaVu Sans Mono, monospace;
    vertical-align:middle;
}
.aging-table tbody tr td.name-cell {
    text-align:left;
    font-family:DejaVu Sans, sans-serif;
    font-weight:bold;
    color:#1a3a5c;
}
.aging-table tbody tr td.currency-cell {
    text-align:center;
    font-family:DejaVu Sans, sans-serif;
    font-size:6.5pt;
}
.aging-table tbody tr:nth-child(even) td { background:#f8fafc; }

/* Amount heat-map */
.amt-zero    { color:#cbd5e1; }
.amt-current { color:#15803d; font-weight:bold; }
.amt-30      { color:#b45309; font-weight:bold; }
.amt-60      { color:#c2410c; font-weight:bold; }
.amt-over60  { color:#b91c1c; font-weight:bold; }
.amt-total   { font-weight:bold; color:#1a3a5c; }

/* Total row */
.aging-table tfoot tr td {
    padding:5pt 6pt;
    font-weight:bold;
    text-align:right;
    font-family:DejaVu Sans Mono, monospace;
    font-size:8pt;
}
.aging-table tfoot .td-current { background:#d1fae5; color:#065f46; border-top:2px solid #065f46; }
.aging-table tfoot .td-30      { background:#fef3c7; color:#78350f; border-top:2px solid #78350f; }
.aging-table tfoot .td-60      { background:#ffedd5; color:#9a3412; border-top:2px solid #9a3412; }
.aging-table tfoot .td-over60  { background:#fee2e2; color:#991b1b; border-top:2px solid #991b1b; }
.aging-table tfoot .td-total   { background:#1a3a5c; color:#fff; border-top:2px solid #1a3a5c; }
.aging-table tfoot .td-label   { background:#f1f5f9; color:#475569; border-top:2px solid #e2e8f0; text-align:left; font-family:DejaVu Sans, sans-serif; }

/* ── Totals visualizer ── */
.totals-bar {
    margin-top:8pt;
    display:table;
    width:100%;
    border:1px solid #e2e8f0;
    border-radius:2pt;
    overflow:hidden;
}
.totals-cell {
    display:table-cell;
    padding:6pt 8pt;
    text-align:center;
    vertical-align:middle;
}
.tc-label  { font-size:6pt; text-transform:uppercase; letter-spacing:.3pt; margin-bottom:2pt; }
.tc-amount { font-size:9pt; font-weight:bold; font-family:DejaVu Sans Mono, monospace; }
.tc-pct    { font-size:6pt; margin-top:1pt; opacity:.7; }

/* ── Footer ── */
.page-footer {
    position:fixed;
    bottom:0; left:0; right:0;
    height:11mm;
    border-top:1px solid #e2e8f0;
    font-size:6pt;
    color:#94a3b8;
    display:table; width:100%;
}
.pfc { display:table-cell; vertical-align:middle; padding:0 10mm; }
</style>
</head>
<body>

<div class="page-footer">
    <div class="pfc">{{ config('app.name') }} — {{ $report->name }}</div>
    <div class="pfc" style="text-align:center;">Aging as at {{ $generatedAt->format('d M Y') }}</div>
    <div class="pfc" style="text-align:right;">Generated by {{ $generatedBy }}</div>
</div>

{{-- Top bar --}}
<div class="topbar">
    <div class="tb-left">
        <div class="co-name">{{ config('app.name', 'ERP System') }}</div>
        <div class="rpt-name">{{ $report->name }}</div>
    </div>
    <div class="tb-right">
        <div class="tb-meta">
            As at: {{ $generatedAt->format('d M Y') }}<br>
            Printed by: {{ $generatedBy }}<br>
            {{ $report->category->name }}
        </div>
    </div>
</div>

{{-- Info strip --}}
<div class="info-strip">
    @foreach(array_filter($params) as $key => $val)
    <div class="info-cell">
        <div class="info-label">{{ ucwords(str_replace('_',' ',$key)) }}</div>
        <div class="info-value">{{ $val }}</div>
    </div>
    @endforeach
    <div class="info-cell">
        <div class="info-label">Total Customers</div>
        <div class="info-value">{{ count($rows) }}</div>
    </div>
    <div class="info-cell">
        <div class="info-label">Report Date</div>
        <div class="info-value">{{ $generatedAt->format('d M Y') }}</div>
    </div>
</div>

@if(empty($rows))
    <div style="text-align:center;padding:20pt;color:#94a3b8;font-style:italic;">No outstanding balances found.</div>
@else
@php
    $cols        = array_keys($rows[0]);
    $numericCols = [];
    $colTotals   = [];

    foreach ($rows as $row) {
        foreach ($cols as $c) {
            $raw = str_replace([',',' '], '', $row[$c] ?? '');
            if (is_numeric($raw) && $raw !== '' && !in_array($c, ['Currency','Curr','curr_code'])) {
                $numericCols[$c]  = true;
                $colTotals[$c]    = ($colTotals[$c] ?? 0) + floatval($raw);
            }
        }
    }

    // Detect aging bucket columns by keyword
    $bucketClasses = [];
    foreach ($cols as $c) {
        $cl = strtolower($c);
        if (str_contains($cl, 'current'))              $bucketClasses[$c] = ['th'=>'th-current','td'=>'amt-current','ft'=>'td-current'];
        elseif (str_contains($cl, '1-30') || str_contains($cl, '30'))  $bucketClasses[$c] = ['th'=>'th-30','td'=>'amt-30','ft'=>'td-30'];
        elseif (str_contains($cl, '31-60') || str_contains($cl, '60')) $bucketClasses[$c] = ['th'=>'th-60','td'=>'amt-60','ft'=>'td-60'];
        elseif (str_contains($cl, '60+') || str_contains($cl, 'over')) $bucketClasses[$c] = ['th'=>'th-over60','td'=>'amt-over60','ft'=>'td-over60'];
        elseif (str_contains($cl, 'balance') || str_contains($cl, 'total')) $bucketClasses[$c] = ['th'=>'th-total','td'=>'amt-total','ft'=>'td-total'];
    }
@endphp

<table class="aging-table">
    <thead>
        <tr class="main-header">
            @foreach($cols as $col)
                @php
                    $bc = $bucketClasses[$col] ?? ['th' => 'th-entity'];
                    $isNum = $numericCols[$col] ?? false;
                @endphp
                <th class="{{ $bc['th'] }} {{ $isNum ? '' : 'text-left' }}">
                    {{ ucwords(str_replace(['_','-'], ' ', $col)) }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            @foreach($cols as $col)
                @php
                    $val    = $row[$col] ?? '';
                    $isNum  = $numericCols[$col] ?? false;
                    $fval   = $isNum ? floatval(str_replace(',', '', $val)) : null;
                    $bc     = $bucketClasses[$col] ?? [];
                    $tdCls  = $bc['td'] ?? '';
                    $isName = (strtolower($col) === 'customer' || strtolower($col) === 'supplier' || strtolower($col) === 'name');
                    $isCurr = (str_contains(strtolower($col), 'curr'));
                @endphp
                <td class="{{ $isName ? 'name-cell' : '' }}
                           {{ $isCurr ? 'currency-cell' : '' }}
                           {{ $isNum && !$isCurr ? $tdCls : '' }}
                           {{ $isNum && $fval == 0 ? 'amt-zero' : '' }}">
                    @if($isNum && $val !== '')
                        @if($fval == 0) — @else {{ number_format($fval, 2) }} @endif
                    @else
                        {{ $val }}
                    @endif
                </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>

    {{-- Grand Total Footer --}}
    <tfoot>
        <tr>
            @foreach($cols as $col)
                @php
                    $isNum = $numericCols[$col] ?? false;
                    $bc    = $bucketClasses[$col] ?? ['ft' => 'td-label'];
                    $ftCls = $bc['ft'] ?? 'td-label';
                @endphp
                <td class="{{ $ftCls }}">
                    @if($isNum)
                        {{ number_format($colTotals[$col] ?? 0, 2) }}
                    @else
                        TOTAL
                    @endif
                </td>
            @endforeach
        </tr>
    </tfoot>
</table>

{{-- Totals visualizer bar --}}
@php
    $grandTotal = collect($colTotals)->sum();
@endphp
<div class="totals-bar">
    @foreach($colTotals as $col => $total)
    @php
        $bc  = $bucketClasses[$col] ?? [];
        $pct = $grandTotal > 0 ? round(($total / $grandTotal) * 100, 1) : 0;
        $bg  = match($bc['ft'] ?? '') {
            'td-current' => '#d1fae5', 'td-30' => '#fef3c7',
            'td-60'      => '#ffedd5', 'td-over60' => '#fee2e2',
            'td-total'   => '#dbeafe', default => '#f1f5f9'
        };
        $fg  = match($bc['ft'] ?? '') {
            'td-current' => '#065f46', 'td-30' => '#78350f',
            'td-60'      => '#9a3412', 'td-over60' => '#991b1b',
            'td-total'   => '#1e40af', default => '#475569'
        };
    @endphp
    <div class="totals-cell" style="background:{{ $bg }};color:{{ $fg }};">
        <div class="tc-label">{{ ucwords(str_replace(['_','-'], ' ', $col)) }}</div>
        <div class="tc-amount">{{ number_format($total, 2) }}</div>
        @if($bc['ft'] ?? '' !== 'td-total')
            <div class="tc-pct">{{ $pct }}% of total</div>
        @endif
    </div>
    @endforeach
</div>
@endif

</body>
</html>

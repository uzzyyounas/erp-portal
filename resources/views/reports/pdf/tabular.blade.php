{{--
    CLEAN TABULAR PDF TEMPLATE
    ───────────────────────────
    Used for: GL Transactions, Stock On Hand, Purchase Orders list, Audit Log
    A simple professional table — no grouping, just clean rows with zebra stripes,
    column-level totals, and a minimalist header.
--}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 7.5pt; color:#2c2c2c; }
@page { margin:12mm 10mm 14mm 10mm; size: {{ $report->pdf_paper_size }} {{ $report->pdf_orientation }}; }

/* ── Header strip ── */
.top-strip {
    border-bottom: 2px solid #e8a020;
    padding-bottom: 7pt;
    margin-bottom: 8pt;
    display:table; width:100%;
}
.top-left  { display:table-cell; vertical-align:bottom; }
.top-right { display:table-cell; vertical-align:bottom; text-align:right; width:200pt; }

.co-name  { font-size:13pt; font-weight:bold; color:#1a3a5c; }
.rpt-name { font-size:9pt; color:#e8a020; font-weight:bold; margin-top:1pt; }
.rpt-desc { font-size:6.5pt; color:#888; margin-top:2pt; }
.meta     { font-size:6.5pt; color:#888; line-height:1.6; }

/* ── Params chips ── */
.chips { margin-bottom:7pt; }
.chip {
    display:inline-block;
    background:#f0f4fa;
    border:1px solid #cdd5e0;
    border-radius:10pt;
    padding:1.5pt 7pt;
    font-size:6.5pt;
    color:#334155;
    margin-right:4pt;
    margin-bottom:2pt;
}
.chip strong { color:#1a3a5c; }

/* ── Main table ── */
.main-table {
    width:100%;
    border-collapse:collapse;
    font-size:7pt;
}
.main-table thead tr {
    background:#1a3a5c;
    color:#fff;
}
.main-table thead th {
    padding:5pt 6pt;
    font-size:6.8pt;
    font-weight:bold;
    letter-spacing:.2pt;
    white-space:nowrap;
    border-right:1px solid rgba(255,255,255,.1);
}
.main-table thead th:last-child { border-right:none; }

/* Alternate rows */
.main-table tbody tr:nth-child(odd)  td { background:#ffffff; }
.main-table tbody tr:nth-child(even) td { background:#f5f8ff; }
.main-table tbody tr:last-child td   { border-bottom:2px solid #1a3a5c; }

.main-table tbody td {
    padding:3.5pt 6pt;
    border-bottom:1px solid #eaecf0;
    vertical-align:top;
}

/* Totals row */
.main-table tfoot tr td {
    padding:5pt 6pt;
    background:#1a3a5c;
    color:#fff;
    font-weight:bold;
    font-size:7.5pt;
    border-right:1px solid rgba(255,255,255,.1);
}
.main-table tfoot tr td.label-cell { font-weight:normal; color:rgba(255,255,255,.7); }

/* Utilities */
.text-right  { text-align:right; }
.text-center { text-align:center; }
.mono        { font-family: DejaVu Sans Mono, monospace; }
.text-muted  { color:#94a3b8; }
.negative    { color:#dc2626; }
.positive    { color:#16a34a; }
.rownum      { color:#cbd5e1; font-size:6pt; }

/* ── Footer ── */
.page-footer {
    position:fixed;
    bottom:0; left:0; right:0;
    height:10mm;
    font-size:6pt;
    color:#94a3b8;
    display:table;
    width:100%;
    border-top:1px solid #e2e8f0;
}
.fc { display:table-cell; vertical-align:middle; padding: 0 10mm; }

/* ── Record count badge ── */
.record-badge {
    display:inline-block;
    background:#e8f0fb;
    color:#1a3a5c;
    border-radius:2pt;
    padding:2pt 6pt;
    font-size:6.5pt;
    font-weight:bold;
    margin-top:5pt;
}
</style>
</head>
<body>

<div class="page-footer">
    <div class="fc">{{ config('app.name') }}</div>
    <div class="fc" style="text-align:center;">{{ $report->name }}</div>
    <div class="fc" style="text-align:right;">{{ $generatedAt->format('d/m/Y H:i') }} · {{ $generatedBy }}</div>
</div>

{{-- Header --}}
<div class="top-strip">
    <div class="top-left">
        <div class="co-name">{{ config('app.name', 'ERP System') }}</div>
        <div class="rpt-name">{{ $report->name }}</div>
        @if($report->description)
            <div class="rpt-desc">{{ $report->description }}</div>
        @endif
    </div>
    <div class="top-right">
        <div class="meta">
            {{ $report->category->name }}<br>
            {{ $generatedAt->format('d M Y, H:i') }}<br>
            By: {{ $generatedBy }}
        </div>
    </div>
</div>

{{-- Parameter Chips --}}
@if(count(array_filter($params)))
<div class="chips">
    @foreach($params as $key => $val)
        @if($val)
        <span class="chip">
            <strong>{{ ucwords(str_replace('_',' ',$key)) }}: </strong>{{ $val }}
        </span>
        @endif
    @endforeach
</div>
@endif

@if(empty($rows))
    <div style="text-align:center;padding:25pt;color:#94a3b8;font-style:italic;border:1px dashed #e2e8f0;border-radius:3pt;margin-top:10pt;">
        No records match the selected parameters.
    </div>
@else
@php
    $cols        = array_keys($rows[0]);
    $numericCols = [];
    $colTotals   = [];

    foreach ($rows as $row) {
        foreach ($cols as $c) {
            $raw = str_replace([',',' '], '', $row[$c] ?? '');
            if (is_numeric($raw) && $raw !== '') {
                $numericCols[$c] = true;
                $colTotals[$c]   = ($colTotals[$c] ?? 0) + floatval($raw);
            }
        }
    }
@endphp

<table class="main-table">
    <thead>
        <tr>
            <th class="text-center" style="width:20pt;">#</th>
            @foreach($cols as $col)
                <th class="{{ ($numericCols[$col] ?? false) ? 'text-right' : '' }}">
                    {{ ucwords(str_replace('_', ' ', $col)) }}
                </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($rows as $i => $row)
        <tr>
            <td class="text-right rownum">{{ $i + 1 }}</td>
            @foreach($cols as $col)
                @php
                    $val   = $row[$col] ?? '';
                    $isNum = $numericCols[$col] ?? false;
                    $fval  = $isNum ? floatval(str_replace(',', '', $val)) : null;
                @endphp
                <td class="{{ $isNum ? 'text-right mono' : '' }} {{ ($fval !== null && $fval < 0) ? 'negative' : '' }}">
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

    @if(count($colTotals) > 0)
    <tfoot>
        <tr>
            <td class="label-cell text-right">—</td>
            @foreach($cols as $col)
                <td class="{{ ($numericCols[$col] ?? false) ? 'text-right mono' : 'label-cell' }}">
                    @if($numericCols[$col] ?? false)
                        {{ number_format($colTotals[$col] ?? 0, 2) }}
                    @else
                        @if($loop->first) TOTALS @endif
                    @endif
                </td>
            @endforeach
        </tr>
    </tfoot>
    @endif
</table>

<div class="record-badge">
    {{ count($rows) }} records returned
</div>
@endif

</body>
</html>

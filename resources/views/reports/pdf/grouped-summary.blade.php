{{--
    GROUPED SUMMARY PDF TEMPLATE
    ─────────────────────────────
    Used for: Trial Balance, Aged Receivables, Sales by Category, Stock Summary
    Renders rows grouped by a category/type column with subtotals per group
    and a grand total at the bottom. Clean accounting-style layout.
--}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size:8pt; color:#212529; }
@page { margin:14mm 12mm 16mm 12mm; size: {{ $report->pdf_paper_size }} {{ $report->pdf_orientation }}; }

/* ── Header ── */
.letterhead {
    background: linear-gradient(to right, #1a3a5c, #2d6a9f);
    color:#fff;
    padding:10pt 12pt;
    margin-bottom:10pt;
}
.letterhead .co-name   { font-size:14pt; font-weight:bold; letter-spacing:.5pt; }
.letterhead .rpt-title { font-size:9pt; color:rgba(255,255,255,.8); margin-top:2pt; }
.letterhead .rpt-meta  { font-size:7pt; color:rgba(255,255,255,.6); }

/* ── Params row ── */
.params-row {
    border:1px dashed #c5d5ea;
    padding:4pt 8pt;
    font-size:7.5pt;
    color:#495057;
    margin-bottom:8pt;
}

/* ── Section header (group title) ── */
.section-header {
    background:#e8f0fb;
    border-left:4px solid #2d6a9f;
    padding:4pt 8pt;
    font-size:8.5pt;
    font-weight:bold;
    color:#1a3a5c;
    margin-top:8pt;
    margin-bottom:0;
    letter-spacing:.3pt;
}
.section-header:first-child { margin-top:0; }

/* ── Data table ── */
.data-table {
    width:100%;
    border-collapse:collapse;
    font-size:7.5pt;
}
.data-table tbody tr td {
    padding:3.5pt 7pt;
    border-bottom:1px solid #f0f0f0;
    vertical-align:middle;
}
.data-table tbody tr:hover td { background:#fafbfd; }

/* Subtotal row */
.data-table tr.subtotal td {
    background:#dce9f7;
    font-weight:bold;
    font-size:8pt;
    border-top:1px solid #a8c4e0;
    border-bottom:1px solid #a8c4e0;
    padding:4pt 7pt;
}

/* ── Grand Total ── */
.grand-total-table {
    width:100%;
    border-collapse:collapse;
    margin-top:10pt;
    font-size:8.5pt;
}
.grand-total-table td {
    padding:5pt 7pt;
    background:#1a3a5c;
    color:#fff;
    font-weight:bold;
}
.grand-total-table td.label { font-size:7.5pt; font-weight:normal; color:rgba(255,255,255,.7); }
.grand-total-table td.value { text-align:right; font-family:DejaVu Sans Mono, monospace; font-size:10pt; }

/* ── Balance indicator ── */
.debit  { color:#c0392b; }
.credit { color:#1a7a4a; }
.zero   { color:#6c757d; }

/* ── Columns ── */
.text-right  { text-align:right; }
.text-center { text-align:center; }
.mono        { font-family:DejaVu Sans Mono, monospace; }
.text-muted  { color:#6c757d; }
.bold        { font-weight:bold; }

/* ── Summary stats bar ── */
.stats-bar {
    display:table;
    width:100%;
    border-top:2px solid #dee2e6;
    padding-top:6pt;
    margin-top:8pt;
    font-size:7pt;
    color:#6c757d;
}
.stat-cell { display:table-cell; text-align:center; }
.stat-value { font-size:10pt; font-weight:bold; color:#1a3a5c; display:block; }
.stat-label { color:#adb5bd; font-size:6.5pt; text-transform:uppercase; letter-spacing:.3pt; }

/* ── Footer ── */
.page-footer {
    position:fixed;
    bottom:0; left:0; right:0;
    height:11mm;
    border-top:1px solid #dee2e6;
    font-size:6.5pt;
    color:#adb5bd;
    display:table;
    width:100%;
}
.footer-cell { display:table-cell; padding:3pt 12mm; vertical-align:middle; }
</style>
</head>
<body>

<div class="page-footer">
    <div class="footer-cell">{{ config('app.name') }} — {{ $report->name }}</div>
    <div class="footer-cell" style="text-align:center;">CONFIDENTIAL — For internal use only</div>
    <div class="footer-cell" style="text-align:right;">{{ $generatedAt->format('d M Y H:i') }} · {{ $generatedBy }}</div>
</div>

{{-- Letterhead --}}
<div class="letterhead">
    <table style="width:100%;border:none;"><tr>
        <td style="border:none;padding:0;vertical-align:top;">
            <div class="co-name">{{ config('app.name', 'ERP System') }}</div>
            <div class="rpt-title">{{ $report->name }}</div>
            @if($report->description)
                <div class="rpt-meta" style="margin-top:2pt;">{{ $report->description }}</div>
            @endif
        </td>
        <td style="border:none;padding:0;text-align:right;vertical-align:top;width:160pt;">
            <div class="rpt-meta">
                Category: {{ $report->category->name }}<br>
                Date: {{ $generatedAt->format('d M Y') }}<br>
                {{ $report->pdf_paper_size }} · {{ ucfirst($report->pdf_orientation) }}
            </div>
        </td>
    </tr></table>
</div>

{{-- Params --}}
@if(count(array_filter($params)))
<div class="params-row">
    @foreach($params as $key => $val)
        @if($val)
            <strong>{{ ucwords(str_replace('_',' ',$key)) }}:</strong> {{ $val }} &nbsp;&nbsp;
        @endif
    @endforeach
</div>
@endif

@if(empty($rows))
    <div style="text-align:center;padding:25pt;color:#6c757d;font-style:italic;">
        No records found for the selected criteria.
    </div>
@else
@php
    $allCols    = array_keys($rows[0]);
    $groupCol   = $allCols[0];   // First column is the group
    $dataCols   = array_slice($allCols, 1);

    // Detect numeric columns
    $numericCols = [];
    foreach ($rows as $row) {
        foreach ($dataCols as $c) {
            if (is_numeric(str_replace([',',' '], '', $row[$c] ?? ''))) {
                $numericCols[$c] = true;
            }
        }
    }

    $grouped    = collect($rows)->groupBy($groupCol);
    $grandTotals = array_fill_keys(array_keys($numericCols), 0);
@endphp

<table class="data-table">
    {{-- Column headers --}}
    <thead>
        <tr style="background:#1a3a5c;">
            @foreach($allCols as $col)
                <th style="color:#fff;padding:5pt 7pt;font-size:7.5pt;font-weight:bold;
                    text-align:{{ ($numericCols[$col] ?? false) ? 'right' : 'left' }};">
                    {{ ucwords(str_replace('_', ' ', $col)) }}
                </th>
            @endforeach
        </tr>
    </thead>

    @foreach($grouped as $group => $groupRows)
        @php
            $groupTotals = array_fill_keys(array_keys($numericCols), 0);
        @endphp

        {{-- Section Header --}}
        <tbody>
            <tr>
                <td colspan="{{ count($allCols) }}"
                    style="background:#e8f0fb;border-left:4px solid #2d6a9f;
                           padding:4pt 8pt;font-weight:bold;color:#1a3a5c;font-size:8.5pt;">
                    {{ $group }}
                </td>
            </tr>
        </tbody>

        {{-- Detail rows --}}
        <tbody>
            @foreach($groupRows as $row)
                <tr>
                    @foreach($allCols as $col)
                        @php
                            $val   = $row[$col] ?? '';
                            $isNum = $numericCols[$col] ?? false;
                            if ($isNum && $val !== '') {
                                $fval = floatval(str_replace(',', '', $val));
                                $groupTotals[$col] = ($groupTotals[$col] ?? 0) + $fval;
                                $grandTotals[$col] = ($grandTotals[$col] ?? 0) + $fval;
                            }
                        @endphp
                        <td class="{{ $isNum ? 'text-right mono' : '' }}
                                   {{ $isNum && floatval(str_replace(',','',$val??'')) < 0 ? 'debit' : '' }}">
                            @if($isNum && $val !== '')
                                {{ number_format(floatval(str_replace(',', '', $val)), 2) }}
                            @else
                                {{ $val }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>

        {{-- Subtotal row --}}
        <tbody>
            <tr class="subtotal">
                <td style="color:#1a3a5c;">
                    Subtotal — {{ $group }}
                    <span style="color:#888;font-size:6.5pt;font-weight:normal;">
                        ({{ $groupRows->count() }} records)
                    </span>
                </td>
                @foreach($dataCols as $col)
                    <td class="{{ ($numericCols[$col] ?? false) ? 'text-right mono' : '' }}">
                        @if($numericCols[$col] ?? false)
                            {{ number_format($groupTotals[$col] ?? 0, 2) }}
                        @endif
                    </td>
                @endforeach
            </tr>
        </tbody>
    @endforeach
</table>

{{-- Grand Total --}}
@if(count($grandTotals) > 0)
<table class="grand-total-table">
    <tr>
        <td class="label" style="width:50%;">GRAND TOTAL — {{ count($rows) }} records · {{ $grouped->count() }} groups</td>
        @foreach($dataCols as $col)
            <td class="{{ ($numericCols[$col] ?? false) ? 'value' : '' }}">
                @if($numericCols[$col] ?? false)
                    {{ number_format($grandTotals[$col] ?? 0, 2) }}
                @endif
            </td>
        @endforeach
    </tr>
</table>
@endif

{{-- Stats bar --}}
<div class="stats-bar">
    <div class="stat-cell">
        <span class="stat-value">{{ $grouped->count() }}</span>
        <span class="stat-label">Groups</span>
    </div>
    <div class="stat-cell">
        <span class="stat-value">{{ count($rows) }}</span>
        <span class="stat-label">Total Records</span>
    </div>
    @foreach(array_slice($grandTotals, 0, 3, true) as $col => $total)
    <div class="stat-cell">
        <span class="stat-value {{ $total < 0 ? 'debit' : '' }}">{{ number_format($total, 2) }}</span>
        <span class="stat-label">{{ ucwords(str_replace('_',' ',$col)) }}</span>
    </div>
    @endforeach
</div>

@endif
</body>
</html>

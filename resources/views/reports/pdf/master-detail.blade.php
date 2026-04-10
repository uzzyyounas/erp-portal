{{--
    MASTER-DETAIL PDF TEMPLATE
    ───────────────────────────
    Used for: Sales Orders, Purchase Orders, Customer Statements, GRN
    Expects in $rows each row to have:
        - Master fields  (header_*)   e.g. header_ref, header_date, header_customer
        - Detail fields  (detail_*)   e.g. detail_item, detail_qty, detail_price
    OR a flat $rows array where the report service groups by a master key.

    The template auto-detects columns starting with "header_" vs "detail_" and renders
    a header block once per group, then a table of detail lines below it.

    If no header_ columns exist, falls back to plain grouped rendering.
--}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size:8pt; color:#1a1a2e; }

/* ── Page ── */
@page { margin:14mm 12mm 16mm 12mm; size: {{ $report->pdf_paper_size }} {{ $report->pdf_orientation }}; }

/* ── Company Header ── */
.page-header {
    border-bottom: 3px solid #1a3a5c;
    padding-bottom: 8pt;
    margin-bottom: 10pt;
}
.company-block { }
.company-name  { font-size:15pt; font-weight:bold; color:#1a3a5c; letter-spacing:.5pt; }
.report-title  { font-size:10pt; color:#2d6a9f; font-weight:bold; margin-top:2pt; }
.report-meta   { font-size:7pt; color:#6c757d; margin-top:3pt; line-height:1.5; }

/* ── Params Banner ── */
.params-banner {
    background:#eef3fa;
    border:1px solid #c5d5ea;
    border-left:4px solid #2d6a9f;
    padding:5pt 8pt;
    margin-bottom:10pt;
    font-size:7.5pt;
}
.params-banner strong { color:#1a3a5c; }

/* ── Master Block (one per document/group) ── */
.master-block {
    background:#f8fafc;
    border:1px solid #dce6f0;
    border-top:3px solid #1a3a5c;
    border-radius:2pt;
    padding:8pt 10pt;
    margin-bottom:5pt;
    page-break-inside: avoid;
}
.master-grid { width:100%; border-collapse:collapse; }
.master-grid td { padding:2pt 6pt 2pt 0; vertical-align:top; width:25%; }
.master-label { font-size:6.5pt; text-transform:uppercase; color:#8096ac; font-weight:bold; letter-spacing:.3pt; display:block; }
.master-value { font-size:8.5pt; font-weight:bold; color:#1a3a5c; }
.master-value.highlight { color:#c0392b; }

/* ── Detail Table ── */
.detail-table {
    width:100%;
    border-collapse:collapse;
    margin-bottom:12pt;
    font-size:7.5pt;
}
.detail-table thead tr th {
    background:#1a3a5c;
    color:#fff;
    padding:4pt 6pt;
    text-align:left;
    font-size:7pt;
    font-weight:bold;
    letter-spacing:.2pt;
}
.detail-table thead tr th.text-right { text-align:right; }
.detail-table tbody tr td {
    padding:4pt 6pt;
    border-bottom:1px solid #eaeff5;
    vertical-align:top;
}
.detail-table tbody tr:nth-child(even) td { background:#f5f8fb; }
.detail-table tfoot tr td {
    padding:4pt 6pt;
    font-weight:bold;
    border-top:2px solid #1a3a5c;
    background:#eef3fa;
    font-size:8pt;
}
.text-right  { text-align:right; }
.text-center { text-align:center; }
.text-muted  { color:#6c757d; }
.amount      { font-family: DejaVu Sans Mono, monospace; }

/* ── Status Badge ── */
.badge {
    display:inline-block;
    padding:1pt 5pt;
    border-radius:2pt;
    font-size:6.5pt;
    font-weight:bold;
    letter-spacing:.3pt;
    text-transform:uppercase;
}
.badge-success  { background:#d4edda; color:#155724; }
.badge-warning  { background:#fff3cd; color:#856404; }
.badge-danger   { background:#f8d7da; color:#721c24; }
.badge-info     { background:#d1ecf1; color:#0c5460; }

/* ── Page Footer ── */
.page-footer {
    position:fixed;
    bottom:0; left:0; right:0;
    height:12mm;
    border-top:1px solid #dee2e6;
    font-size:6.5pt;
    color:#adb5bd;
    display:table;
    width:100%;
}
.footer-left  { display:table-cell; padding:3pt 12mm; vertical-align:middle; }
.footer-right { display:table-cell; text-align:right; padding:3pt 12mm; vertical-align:middle; }

/* ── Summary box ── */
.summary-box {
    background:#1a3a5c;
    color:#fff;
    padding:6pt 10pt;
    border-radius:2pt;
    font-size:8pt;
    width:200pt;
    float:right;
    margin-bottom:8pt;
}
.summary-row { display:table; width:100%; }
.summary-label { display:table-cell; }
.summary-amount { display:table-cell; text-align:right; font-weight:bold; font-family: DejaVu Sans Mono, monospace; }
.summary-total { border-top:1px solid rgba(255,255,255,.3); margin-top:3pt; padding-top:3pt; font-size:9.5pt; }
</style>
</head>
<body>

{{-- Fixed footer on every page --}}
<div class="page-footer">
    <div class="footer-left">{{ config('app.name') }} — {{ $report->name }} — CONFIDENTIAL</div>
    <div class="footer-right">Generated: {{ $generatedAt->format('d/m/Y H:i') }} by {{ $generatedBy }}</div>
</div>

{{-- Page Header --}}
<div class="page-header">
    <table style="width:100%;border:none;"><tr>
        <td style="border:none;padding:0;vertical-align:top;">
            <div class="company-name">{{ config('app.name', 'ERP System') }}</div>
            <div class="report-title">{{ $report->name }}</div>
        </td>
        <td style="border:none;padding:0;text-align:right;vertical-align:top;width:180pt;">
            <div class="report-meta">
                <strong>{{ $report->category->name }}</strong><br>
                Printed: {{ $generatedAt->format('d M Y H:i') }}<br>
                By: {{ $generatedBy }}
            </div>
        </td>
    </tr></table>
</div>

{{-- Parameters Banner --}}
@if(count(array_filter($params)))
<div class="params-banner">
    <strong>Filters applied: </strong>
    @foreach($params as $key => $val)
        @if($val)<span style="margin-right:14pt;"><span style="color:#6c757d;">{{ ucwords(str_replace('_',' ',$key)) }}:</span> <strong>{{ $val }}</strong></span>@endif
    @endforeach
</div>
@endif

@if(empty($rows))
    <div style="text-align:center;padding:30pt;color:#6c757d;font-style:italic;">
        No records found for the selected parameters.
    </div>
@else
    @php
        // Auto-detect master/detail columns
        $allCols     = array_keys($rows[0]);
        $masterCols  = array_filter($allCols, fn($c) => str_starts_with(strtolower($c), 'header_'));
        $detailCols  = array_filter($allCols, fn($c) => str_starts_with(strtolower($c), 'detail_'));
        $hasSections = count($masterCols) > 0 && count($detailCols) > 0;

        // If no header/detail split → group by first column
        $groupKey    = $hasSections ? null : $allCols[0];
        $grouped     = collect($rows)->groupBy(fn($r) => $hasSections ? ($r[array_values($masterCols)[0]] ?? '') : $r[$groupKey]);

        // Numeric columns detector
        $numericCols = [];
        foreach ($rows as $row) {
            foreach ($row as $k => $v) {
                if (is_numeric(str_replace([',',' '], '', $v ?? ''))) {
                    $numericCols[$k] = true;
                }
            }
        }

        $grandTotal = [];
    @endphp

    @foreach($grouped as $groupName => $groupRows)
        @php
            $firstRow   = $groupRows->first();
            $groupTotal = [];
        @endphp

        {{-- Master / Header Block --}}
        <div class="master-block">
            <table class="master-grid">
                <tr>
                @if($hasSections)
                    @foreach($masterCols as $mc)
                        <td>
                            <span class="master-label">{{ ucwords(str_replace(['header_','_'],' ', $mc)) }}</span>
                            <span class="master-value">{{ $firstRow[$mc] ?? '—' }}</span>
                        </td>
                    @endforeach
                @else
                    <td>
                        <span class="master-label">{{ ucwords(str_replace('_',' ', $groupKey)) }}</span>
                        <span class="master-value">{{ $groupName }}</span>
                    </td>
                    <td>
                        <span class="master-label">Records</span>
                        <span class="master-value">{{ $groupRows->count() }}</span>
                    </td>
                @endif
                </tr>
            </table>
        </div>

        {{-- Detail / Line Items Table --}}
        @php
            $tableCols = $hasSections
                ? array_values(array_filter($allCols, fn($c) => str_starts_with(strtolower($c), 'detail_')))
                : array_values(array_filter($allCols, fn($c) => $c !== $groupKey));
        @endphp

        <table class="detail-table">
            <thead><tr>
                <th style="width:25pt;">#</th>
                @foreach($tableCols as $col)
                    <th class="{{ ($numericCols[$col] ?? false) ? 'text-right' : '' }}">
                        {{ ucwords(str_replace(['detail_','_'],' ', $col)) }}
                    </th>
                @endforeach
            </tr></thead>
            <tbody>
                @foreach($groupRows as $i => $row)
                    <tr>
                        <td class="text-muted text-right">{{ $i + 1 }}</td>
                        @foreach($tableCols as $col)
                            @php
                                $val    = $row[$col] ?? '';
                                $isNum  = $numericCols[$col] ?? false;
                                if ($isNum && $val !== '' && $val !== null) {
                                    $groupTotal[$col] = ($groupTotal[$col] ?? 0) + floatval(str_replace(',', '', $val));
                                    $grandTotal[$col] = ($grandTotal[$col] ?? 0) + floatval(str_replace(',', '', $val));
                                }
                            @endphp
                            <td class="{{ $isNum ? 'text-right amount' : '' }}">
                                {{ $isNum && $val !== '' ? number_format(floatval(str_replace(',', '', $val)), 2) : $val }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            <tfoot><tr>
                <td class="text-right text-muted" style="font-size:6.5pt;">TOTAL</td>
                @foreach($tableCols as $col)
                    <td class="{{ ($numericCols[$col] ?? false) ? 'text-right amount' : '' }}">
                        @if($numericCols[$col] ?? false)
                            {{ number_format($groupTotal[$col] ?? 0, 2) }}
                        @endif
                    </td>
                @endforeach
            </tr></tfoot>
        </table>
    @endforeach

    {{-- Grand Total Summary --}}
    @if(count($grandTotal) > 0)
    <div style="clear:both;">
        <div class="summary-box">
            @foreach($grandTotal as $col => $total)
                <div class="summary-row {{ $loop->last ? 'summary-total' : '' }}">
                    <div class="summary-label">{{ ucwords(str_replace(['detail_','header_','_'],' ', $col)) }}</div>
                    <div class="summary-amount">{{ number_format($total, 2) }}</div>
                </div>
            @endforeach
        </div>
        <div style="clear:both;"></div>
    </div>
    @endif

    <div style="font-size:6.5pt;color:#adb5bd;margin-top:8pt;">
        Total groups: {{ count($grouped) }} &nbsp;|&nbsp; Total records: {{ count($rows) }}
    </div>
@endif

</body>
</html>

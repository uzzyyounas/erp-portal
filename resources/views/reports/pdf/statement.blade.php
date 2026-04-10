{{--
    STATEMENT PDF TEMPLATE
    ───────────────────────
    Used for: Customer Statement, Supplier Statement
    Shows a formal account statement with:
    - Entity details box (name, address, account code)
    - Transaction table with running balance
    - Opening balance / closing balance summary
    - Aging analysis footer
--}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size:8pt; color:#1e1e2e; }
@page { margin:15mm 12mm 18mm 12mm; size: {{ $report->pdf_paper_size }} portrait; }

/* ── Letterhead ── */
.letterhead {
    display:table;
    width:100%;
    border-bottom:3px double #1a3a5c;
    padding-bottom:8pt;
    margin-bottom:10pt;
}
.lh-left  { display:table-cell; vertical-align:top; }
.lh-right { display:table-cell; vertical-align:top; text-align:right; width:200pt; }

.co-name { font-size:16pt; font-weight:bold; color:#1a3a5c; }
.co-sub  { font-size:7.5pt; color:#888; margin-top:2pt; }
.stmt-title {
    font-size:12pt; font-weight:bold; color:#e8a020;
    letter-spacing:.5pt; text-transform:uppercase;
    margin-top:4pt;
}
.print-date { font-size:7pt; color:#888; margin-top:3pt; }

/* ── Account Info box ── */
.account-box {
    display:table;
    width:100%;
    margin-bottom:10pt;
}
.acct-left  { display:table-cell; vertical-align:top; width:60%; }
.acct-right { display:table-cell; vertical-align:top; text-align:right; }

.entity-card {
    border:1px solid #dce6f0;
    border-top:3px solid #e8a020;
    padding:7pt 10pt;
    background:#fefefe;
    border-radius:2pt;
    display:inline-block;
    width:100%;
}
.entity-name    { font-size:11pt; font-weight:bold; color:#1a3a5c; }
.entity-detail  { font-size:7pt; color:#6c757d; margin-top:1pt; }

.period-box {
    border:1px solid #dce6f0;
    padding:7pt 10pt;
    background:#f0f4fa;
    border-radius:2pt;
    text-align:center;
}
.period-label { font-size:6.5pt; color:#888; text-transform:uppercase; letter-spacing:.3pt; }
.period-value { font-size:9pt; font-weight:bold; color:#1a3a5c; margin-top:2pt; }

/* ── Statement table ── */
.stmt-table {
    width:100%;
    border-collapse:collapse;
    font-size:7.5pt;
}
.stmt-table thead th {
    background:#1a3a5c;
    color:#fff;
    padding:5pt 7pt;
    font-weight:bold;
    font-size:7pt;
    letter-spacing:.2pt;
}
.stmt-table thead th.right { text-align:right; }

.stmt-table tbody td {
    padding:4pt 7pt;
    border-bottom:1px solid #edf0f5;
    vertical-align:middle;
}
.stmt-table tbody tr:nth-child(even) td { background:#f8fafc; }

/* Type badges */
.type-badge {
    display:inline-block;
    padding:1.5pt 5pt;
    border-radius:2pt;
    font-size:6pt;
    font-weight:bold;
    text-transform:uppercase;
    letter-spacing:.3pt;
}
.type-inv    { background:#dbeafe; color:#1e40af; }
.type-cr     { background:#dcfce7; color:#166534; }
.type-pmt    { background:#fef9c3; color:#854d0e; }
.type-adj    { background:#f3e8ff; color:#6b21a8; }
.type-other  { background:#f1f5f9; color:#475569; }

/* Amounts */
.amount { font-family:DejaVu Sans Mono, monospace; text-align:right; }
.debit  { color:#dc2626; }
.credit { color:#16a34a; }
.balance-positive { color:#16a34a; font-weight:bold; }
.balance-negative { color:#dc2626; font-weight:bold; }
.balance-zero     { color:#94a3b8; }

/* Opening/Closing balance rows */
.balance-row td {
    background:#eef3fa !important;
    font-weight:bold;
    font-style:italic;
    border-top:1px solid #c5d5ea !important;
    border-bottom:1px solid #c5d5ea !important;
    color:#1a3a5c;
}

/* ── Summary footer ── */
.summary-section {
    display:table;
    width:100%;
    margin-top:10pt;
    border-spacing:6pt;
}
.summary-left  { display:table-cell; vertical-align:top; width:50%; }
.summary-right { display:table-cell; vertical-align:top; padding-left:6pt; }

.aging-box {
    border:1px solid #dce6f0;
    border-radius:2pt;
    overflow:hidden;
}
.aging-header {
    background:#1a3a5c;
    color:#fff;
    font-size:7pt;
    font-weight:bold;
    padding:4pt 8pt;
    letter-spacing:.3pt;
}
.aging-row {
    display:table;
    width:100%;
    border-bottom:1px solid #edf0f5;
    font-size:7.5pt;
}
.aging-row:last-child { border-bottom:none; }
.aging-label  { display:table-cell; padding:3.5pt 8pt; color:#475569; }
.aging-amount { display:table-cell; padding:3.5pt 8pt; text-align:right; font-family:DejaVu Sans Mono, monospace; font-weight:bold; color:#1a3a5c; }
.aging-total  { background:#e8f0fb; }

.closing-box {
    background:#1a3a5c;
    color:#fff;
    padding:8pt 10pt;
    border-radius:2pt;
    text-align:center;
}
.closing-label  { font-size:7pt; color:rgba(255,255,255,.7); text-transform:uppercase; letter-spacing:.3pt; }
.closing-amount { font-size:15pt; font-weight:bold; font-family:DejaVu Sans Mono, monospace; margin-top:2pt; }
.closing-note   { font-size:6pt; color:rgba(255,255,255,.5); margin-top:3pt; }

/* ── Page footer ── */
.page-footer {
    position:fixed;
    bottom:0; left:0; right:0;
    height:12mm;
    border-top:1px solid #dee2e6;
    font-size:6pt;
    color:#94a3b8;
    display:table; width:100%;
}
.pfc { display:table-cell; vertical-align:middle; padding:0 12mm; }
</style>
</head>
<body>

<div class="page-footer">
    <div class="pfc">{{ config('app.name') }}</div>
    <div class="pfc" style="text-align:center;">{{ $report->name }} — This is a computer-generated statement</div>
    <div class="pfc" style="text-align:right;">{{ $generatedAt->format('d M Y H:i') }}</div>
</div>

{{-- Letterhead --}}
<div class="letterhead">
    <div class="lh-left">
        <div class="co-name">{{ config('app.name', 'ERP System') }}</div>
        <div class="co-sub">Accounting & Finance Department</div>
        <div class="stmt-title">{{ $report->name }}</div>
    </div>
    <div class="lh-right">
        <div class="print-date">
            Printed: {{ $generatedAt->format('d M Y') }}<br>
            By: {{ $generatedBy }}<br>
            Ref: RPT-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}
        </div>
    </div>
</div>

{{-- Account + Period Info --}}
<div class="account-box">
    <div class="acct-left">
        <div class="entity-card">
            @php
                // Try to extract entity name from params or first row
                $entityName = $params['customer_id'] ?? $params['supplier_id'] ?? 'All Accounts';
                $dateFrom   = $params['date_from'] ?? '';
                $dateTo     = $params['date_to']   ?? '';
                if (!empty($rows)) {
                    $firstRow = $rows[0];
                    $possibleName = $firstRow['Customer'] ?? $firstRow['Supplier'] ?? $firstRow['Name'] ?? null;
                    if ($possibleName) $entityName = $possibleName;
                }
            @endphp
            <div class="entity-name">{{ $entityName }}</div>
            <div class="entity-detail">Account Statement</div>
            @if($dateFrom || $dateTo)
                <div class="entity-detail" style="margin-top:4pt;">
                    Period: {{ $dateFrom ? date('d M Y', strtotime($dateFrom)) : '—' }}
                    to {{ $dateTo ? date('d M Y', strtotime($dateTo)) : '—' }}
                </div>
            @endif
        </div>
    </div>
    <div class="acct-right">
        <div class="period-box">
            <div class="period-label">Statement Date</div>
            <div class="period-value">{{ $generatedAt->format('d M Y') }}</div>
            <div class="period-label" style="margin-top:5pt;">Total Records</div>
            <div class="period-value">{{ count($rows) }}</div>
        </div>
    </div>
</div>

@if(empty($rows))
    <div style="text-align:center;padding:25pt;color:#94a3b8;font-style:italic;border:1px dashed #e2e8f0;">
        No transactions found for the selected period.
    </div>
@else
@php
    $cols        = array_keys($rows[0]);
    $numericCols = [];
    $colTotals   = [];
    $runningBal  = 0;

    foreach ($rows as $row) {
        foreach ($cols as $c) {
            $raw = str_replace([',',' '], '', $row[$c] ?? '');
            if (is_numeric($raw) && $raw !== '') {
                $numericCols[$c] = true;
                $colTotals[$c]   = ($colTotals[$c] ?? 0) + floatval($raw);
            }
        }
    }

    // Try to detect a Balance column
    $balanceCol = null;
    foreach ($cols as $c) {
        if (stripos($c, 'balance') !== false || stripos($c, 'bal') !== false) {
            $balanceCol = $c;
            break;
        }
    }

    // Detect transaction type column
    $typeCol = null;
    foreach ($cols as $c) {
        if (stripos($c, 'type') !== false) {
            $typeCol = $c;
            break;
        }
    }
@endphp

<table class="stmt-table">
    <thead>
        <tr>
            @foreach($cols as $col)
                <th class="{{ ($numericCols[$col] ?? false) ? 'right' : '' }}">
                    {{ ucwords(str_replace('_', ' ', $col)) }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $row)
            @php
                $typeVal = $typeCol ? ($row[$typeCol] ?? '') : '';
                $typeClass = match(true) {
                    stripos($typeVal, 'invoice') !== false => 'type-inv',
                    stripos($typeVal, 'credit') !== false  => 'type-cr',
                    stripos($typeVal, 'receipt') !== false,
                    stripos($typeVal, 'payment') !== false => 'type-pmt',
                    stripos($typeVal, 'adjust') !== false  => 'type-adj',
                    default => 'type-other'
                };
            @endphp
            <tr>
                @foreach($cols as $col)
                    @php
                        $val   = $row[$col] ?? '';
                        $isNum = $numericCols[$col] ?? false;
                        $fval  = $isNum ? floatval(str_replace(',', '', $val)) : null;
                        $isBal = ($col === $balanceCol);
                        $isType = ($col === $typeCol);
                    @endphp
                    <td class="{{ $isNum && !$isBal ? 'amount' : '' }}
                               {{ $isBal ? 'amount ' . ($fval > 0 ? 'balance-positive' : ($fval < 0 ? 'balance-negative' : 'balance-zero')) : '' }}
                               {{ $isNum && !$isBal && !$isBal ? ($fval < 0 ? 'debit' : '') : '' }}">
                        @if($isType)
                            <span class="type-badge {{ $typeClass }}">{{ $val }}</span>
                        @elseif($isNum && $val !== '')
                            {{ number_format($fval, 2) }}
                        @else
                            {{ $val }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Summary + Aging --}}
<div class="summary-section">
    <div class="summary-left">
        {{-- Totals summary --}}
        @if(count($colTotals) > 0)
        <div class="aging-box">
            <div class="aging-header">Column Totals</div>
            @foreach($colTotals as $col => $total)
            <div class="aging-row">
                <div class="aging-label">{{ ucwords(str_replace('_',' ',$col)) }}</div>
                <div class="aging-amount {{ $total < 0 ? 'debit' : '' }}">
                    {{ number_format($total, 2) }}
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div class="summary-right">
        @if($balanceCol && isset($colTotals[$balanceCol]))
        <div class="closing-box">
            <div class="closing-label">Closing Balance</div>
            <div class="closing-amount">{{ number_format($colTotals[$balanceCol], 2) }}</div>
            <div class="closing-note">As at {{ $generatedAt->format('d M Y') }}</div>
        </div>
        @endif
    </div>
</div>
@endif

</body>
</html>

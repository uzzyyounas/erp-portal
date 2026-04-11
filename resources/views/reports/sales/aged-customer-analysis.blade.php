<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        /*
         * Dompdf-safe stylesheet
         * No flexbox | No grid | No CDN | No JS
         * Paper : A4 Landscape  |  Font : DejaVu Sans (Dompdf built-in)
         */

        @page {
            size: A4 landscape;
            margin: 14mm 14mm 14mm 14mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 8.5pt;
            color: #000000;
            background: #ffffff;
        }

        /* ─── Page break ─────────────────────────────────────────────── */
        .page-block {
            page-break-after: always;
            border: 0.5pt solid #cccccc;
            padding: 12pt 14pt 10pt 14pt;
        }
        .page-block:last-child { page-break-after: avoid; }

        /* ─── Company header layout table ───────────────────────────── */
        .co-table { width:100%; border-collapse:collapse; margin-bottom:10pt; }
        .co-table td { vertical-align:top; padding:0; border:none; }
        .co-left  { width:75%; }
        .co-right { width:25%; text-align:right; }

        .co-name {
            font-size: 13pt;
            font-weight: bold;
            color: black;
            line-height: 1.4;
            margin-bottom: 3pt;
        }
        .co-meta {
            font-size: 7.5pt;
            color: #000000;
            line-height: 1.7;
        }
        .logo-img { max-height:152pt; max-width:150pt; }

        /* ─── Salesman banner ────────────────────────────────────────── */
        .salesman-banner {
            background-color: #008C00;
            color: #ffffff;
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 0.3pt;
            padding: 4pt 6pt;
            margin-bottom: 1.5pt;
        }

        /* ─── Customer banner ────────────────────────────────────────── */
        .customer-banner {
            background-color: #008C00;
            color: #ffffff;
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 0.4pt;
            padding: 5pt 6pt;
            margin-bottom: 4pt;
        }

        /* ─── Aging date label ───────────────────────────────────────── */
        .aging-label {
            text-align: center;
            font-size: 9pt;
            font-weight: normal;
            color: #000000;
            margin-bottom: 5pt;
        }

        /* ─── Options meta bar ───────────────────────────────────────── */
        .opts-bar {
            text-align: center;
            font-size: 7pt;
            color: #555555;
            margin-bottom: 7pt;
        }
        .opt-pill {
            display: inline;
            border: 0.4pt solid #008C00;
            color: #008C00;
            padding: 0.5pt 4pt;
            border-radius: 3pt;
            font-size: 6.5pt;
            font-weight: bold;
            margin: 0 2pt;
        }
        .opt-pill-off {
            display: inline;
            border: 0.4pt solid #bbbbbb;
            color: #aaaaaa;
            padding: 0.5pt 4pt;
            border-radius: 3pt;
            font-size: 6.5pt;
            margin: 0 2pt;
        }

        /* ─── Summary-only notice ────────────────────────────────────── */
        .summary-note {
            text-align: center;
            font-size: 7.5pt;
            color: #555555;
            font-style: italic;
            margin-bottom: 6pt;
        }

        /* ─── Main aging table ───────────────────────────────────────── */
        .ag-table {
            width: 100%;
            border-collapse: collapse;
            border: 0.5pt solid #b0b0b0;
        }

        /* Header */
        .ag-table thead tr { background-color:#008C00; color:#ffffff; }
        .ag-table thead th {
            font-size: 8pt;
            font-weight: bold;
            padding: 5pt 4pt;
            text-align: right;
            white-space: nowrap;
            border-left: 0.3pt solid #009900;
        }
        .ag-table thead th:first-child { text-align:left; border-left:none; }
        .ag-table thead th.th-ref      { text-align:left; }
        .ag-table thead th.th-date     { text-align:left; }
        .ag-table thead th.th-days     { text-align:center; }

        /* Summary / totals row */
        .r-sum td {
            background-color: #e8f1e0;
            font-size: 8.5pt;
            font-weight: bold;
            color: #000000;
            padding: 5pt 4pt;
            border-top: 0.5pt solid #008C00;
            border-bottom: 1pt solid #008C00;
        }

        /* Detail rows */
        .r-det td {
            font-size: 7.8pt;
            color: #000000;
            padding: 2.5pt 4pt;
            border-bottom: 0.3pt solid #e0e0e0;
            vertical-align: middle;
        }

        /* Alignment */
        .num  { text-align:right; }
        .ctr  { text-align:center; }
        .zero { color:#bbbbbb; }

        /* Negative amounts shown in red (payments, credits, bank deposits) */
        .neg  { color:#c0392b; }

        /* Overdue invoice rows */
        .od td { color:#c0392b; font-weight:bold; }
        .od-badge {
            font-size: 6pt;
            color: #c0392b;
            background-color: #fde8e8;
            padding: 0.5pt 2pt;
            border-radius: 2pt;
            margin-left: 2pt;
        }

        /* Grand total row (dark navy) */
        .r-grand td {
            background-color: #1a3a5c;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 5pt 4pt;
        }

        /* ─── Report footer ──────────────────────────────────────────── */
        .rpt-footer {
            font-size: 7pt;
            color: #aaaaaa;
            text-align: right;
            margin-top: 5pt;
            padding-top: 3pt;
            border-top: 0.3pt solid #dddddd;
        }

        /* ─── Grand total section ────────────────────────────────────── */
        .grand-wrap {
            border: 0.5pt solid #cccccc;
            padding: 12pt 14pt 10pt 14pt;
        }
        .grand-title {
            font-size: 9pt;
            font-weight: bold;
            color: #1a3a5c;
            padding-bottom: 4pt;
            margin-bottom: 7pt;
            border-bottom: 1pt solid #1a3a5c;
        }

        /* ─── Empty state ────────────────────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 40pt;
            color: #888888;
            font-size: 10pt;
        }
    </style>
</head>
<body>

{{-- ══════════════════════════════════════════════════════════════
     FA type → label map (matches FA $systypes_array exactly)
     ══════════════════════════════════════════════════════════════ --}}
@php
    use Carbon\Carbon;

    $typeLabels = [
        0  => 'Journal Entry',
        1  => 'Bank Payment',
        2  => 'Bank Deposit',
        3  => 'Bank Charge',
        10 => 'Credit Note',
        11 => 'Delivery',
        12 => 'Customer Payment',
        13 => 'Sales Invoice',
        16 => 'Debit Note',
        17 => 'Purchase Invoice',
        30 => 'Sales Order',
        31 => 'Sales Quote',
    ];
@endphp

@forelse($customers as $i => $customer)
    <div class="page-block">

        {{-- ── Company Header ── --}}
        <table class="co-table">
            <tr>
                <td class="co-left">
                    <div class="co-name">Lucky Snacks (Pvt.) Ltd</div>
                    <div class="co-meta">
                        4-KM Satluj Toll Plaza Bahawalpur Road<br>
                        Lodhran, Punjab, Pakistan, Lodhran,59320<br>
                        +92 333 666 3171<br>
                        info@luckyfoods.net<br>
                        http://luckyfoods.net
                    </div>
                </td>
                <td class="co-right">
                    @if(!empty($logoSrc))
                        <img src="{{ $logoSrc }}" class="logo-img" alt="Lucky Snacks">
                    @else
                        <span style="font-size:8pt;color:#008C00;font-weight:bold;">Lucky Snacks<br>(Pvt.) Ltd</span>
                    @endif
                </td>
            </tr>
        </table>

        {{-- ── Salesman Banner ── --}}
        @if(!empty($salesmanLabel) && $salesmanLabel !== 'All Salesmen')
            <div class="salesman-banner">{{ strtoupper($salesmanLabel) }}</div>
        @endif

        {{-- ── Customer Banner ── --}}
        <div class="customer-banner">{{ strtoupper($customer['name']) }}</div>

        {{-- ── Aging Date ── --}}
        <div class="aging-label">Aging Date {{ $to->format('d-M-y') }}</div>

        @if($summaryOnly ?? false)
            <div class="summary-note">Summary view — individual transactions not shown</div>
        @endif

        {{-- ══════════════════════════════════════════════════════
             AGING TABLE
             Columns match FA exactly:
               Customer | Ref | Date | Days | Current | 1-30 | 31-60 | Over60 | Total
             ══════════════════════════════════════════════════════ --}}
        <table class="ag-table">
            <thead>
            <tr>
                <th style="width:18%">Customer</th>
                <th class="th-ref"  style="width:8%">Reference</th>
                <th class="th-date" style="width:9%">Date</th>
                <th class="th-days" style="width:5%">Days</th>
                <th style="width:12%">Current</th>
                <th style="width:12%">1-{{ $pastDueDays1 ?? 30 }} Days</th>
                <th style="width:12%">{{ ($pastDueDays1 ?? 30) + 1 }}-{{ $pastDueDays2 ?? 60 }} Days</th>
                <th style="width:12%">Over {{ $pastDueDays2 ?? 60 }} Days</th>
                <th style="width:12%">Total Balance</th>
            </tr>
            </thead>
            <tbody>

            {{-- Customer summary / totals row --}}
            <tr class="r-sum">
                <td colspan="4">{{ $customer['name'] }}</td>
                <td class="num">{{ number_format($customer['totals']['current']) }}</td>
                <td class="num">{{ number_format($customer['totals']['days_1_30']) }}</td>
                <td class="num">{{ number_format($customer['totals']['days_31_60']) }}</td>
                <td class="num">{{ number_format($customer['totals']['over_60']) }}</td>
                <td class="num">{{ number_format($customer['totals']['balance']) }}</td>
            </tr>

            {{-- Transaction detail rows --}}
            {{-- When summaryOnly=true, controller passes empty array → no output --}}
            @foreach($customer['transactions'] as $tx)
                @php
                    // FA: $systypes_array[$trans['type']] — type label
                    $label   = $typeLabels[(int)$tx['type']] ?? ('Trans #' . $tx['type']);
                    // FA: $trans['reference'] — the transaction reference number
                    $ref     = $tx['reference'] ?? '';
                    $txDate  = Carbon::parse($tx['tran_date'])->format('d/m/Y');
                    $balance = (float)$tx['balance'];
                    $overdue = (bool)($tx['OverDue'] ?? false);
                    $isNeg   = $balance < 0;
                @endphp
                <tr class="r-det {{ $overdue ? 'od' : '' }}">
                    <td>{{ $label }}</td>
                    <td>{{ $ref }}</td>
                    <td>{{ $txDate }}</td>
                    <td class="ctr">{{ $tx['days'] > 0 ? $tx['days'] : '' }}</td>
                    <td class="num {{ $tx['current'] == 0 ? 'zero' : ($tx['current'] < 0 ? 'neg' : '') }}">
                        {{ number_format($tx['current']) }}
                    </td>
                    <td class="num {{ $tx['b1_30'] == 0 ? 'zero' : ($tx['b1_30'] < 0 ? 'neg' : '') }}">
                        {{ number_format($tx['b1_30']) }}
                    </td>
                    <td class="num {{ $tx['b31_60'] == 0 ? 'zero' : ($tx['b31_60'] < 0 ? 'neg' : '') }}">
                        {{ number_format($tx['b31_60']) }}
                    </td>
                    <td class="num {{ $tx['bOver60'] == 0 ? 'zero' : ($tx['bOver60'] < 0 ? 'neg' : '') }}">
                        {{ number_format($tx['bOver60']) }}
                    </td>
                    <td class="num {{ $isNeg ? 'neg' : '' }}">
                        {{ number_format($balance) }}
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>

{{--        <div class="rpt-footer">--}}
{{--            Generated: {{ now()->format('d M Y, H:i') }}--}}
{{--            &nbsp;|&nbsp;--}}
{{--            @if(isset($from) && $from)Period: {{ $from->format('d M Y') }} &ndash; @endif--}}
{{--            Aging Date: {{ $to->format('d M Y') }}--}}
{{--            &nbsp;|&nbsp;--}}
{{--            Page {{ $i + 1 }} of {{ count($customers) }}--}}
{{--        </div>--}}

    </div>
@empty
    <div class="empty-state">
        No transactions found for the selected parameters.
        @if($suppressZeros ?? false)
            <br><span style="font-size:8pt;">(All customers may have zero balances — try unchecking Suppress Zeros)</span>
        @endif
    </div>
@endforelse

{{-- ══ Grand Total block ══ --}}
@if(count($customers) > 1)
    <div class="grand-wrap">
        <div class="grand-title">
            Grand Total &mdash; {{ count($customers) }} Customers
            &nbsp;|&nbsp; {{ $salesmanLabel ?? 'All Salesmen' }}
            &nbsp;|&nbsp; Aging Date: {{ $to->format('d M Y') }}
        </div>
        <table class="ag-table">
            <thead>
            <tr>
                <th style="width:18%">Summary</th>
                <th class="th-ref"  style="width:8%">&nbsp;</th>
                <th class="th-date" style="width:9%">&nbsp;</th>
                <th class="th-days" style="width:5%">&nbsp;</th>
                <th style="width:12%">Current</th>
                <th style="width:12%">1-{{ $pastDueDays1 ?? 30 }} Days</th>
                <th style="width:12%">{{ ($pastDueDays1 ?? 30) + 1 }}-{{ $pastDueDays2 ?? 60 }} Days</th>
                <th style="width:12%">Over {{ $pastDueDays2 ?? 60 }} Days</th>
                <th style="width:12%">Total Balance</th>
            </tr>
            </thead>
            <tbody>
            <tr class="r-grand">
                <td colspan="4">All Selected Customers ({{ count($customers) }})</td>
                <td class="num">{{ number_format($grand['current']) }}</td>
                <td class="num">{{ number_format($grand['days_1_30']) }}</td>
                <td class="num">{{ number_format($grand['days_31_60']) }}</td>
                <td class="num">{{ number_format($grand['over_60']) }}</td>
                <td class="num">{{ number_format($grand['balance']) }}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endif

</body>
</html>

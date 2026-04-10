<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        /*
         * Dompdf-safe stylesheet
         * No flexbox | No grid | No CDN | No JS
         * Paper : A4 Landscape
         * Font  : DejaVu Sans (built into Dompdf)
         */

        @page {
            size: A4 landscape;
            margin: 14mm 14mm 14mm 14mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 8.5pt;
            color: #000000;
            background: #ffffff;
        }

        /* ─── Page block: one customer per page ──────────────────────── */
        .page-block {
            page-break-after: always;
            border: 0.5pt solid #cccccc;
            padding: 12pt 14pt 10pt 14pt;
        }
        .page-block:last-child {
            page-break-after: avoid;
        }

        /* ─── Company header layout table ───────────────────────────── */
        .co-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10pt;
        }
        .co-table td {
            vertical-align: top;
            padding: 0;
            border: none;
        }
        .co-left  { width: 75%; }
        .co-right { width: 25%; text-align: center; }

        .co-name {
            font-size: 12pt;
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
        .logo-img {
            max-height: 500pt;
            max-width: 500pt;
        }

        /* ─── Salesman banner (sits above customer banner) ────────────── */
        .salesman-banner {
            background-color: #006800;
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
            margin-bottom: 9pt;
        }

        /* ─── Main aging table ───────────────────────────────────────── */
        .ag-table {
            width: 100%;
            border-collapse: collapse;
            border: 0.5pt solid #b0b0b0;
        }

        /* Header */
        .ag-table thead tr {
            background-color: #008C00;
            color: #ffffff;
        }
        .ag-table thead th {
            font-size: 8pt;
            font-weight: bold;
            padding: 5pt 5pt;
            text-align: right;
            white-space: nowrap;
            border-left: 0.3pt solid #009900;
        }
        .ag-table thead th:first-child {
            text-align: left;
            border-left: none;
        }
        .ag-table thead th.th-days {
            text-align: center;
        }

        /* Summary / totals row */
        .r-sum td {
            background-color: #e8f1e0;
            font-size: 8.5pt;
            font-weight: bold;
            color: #000000;
            padding: 5pt 5pt;
            border-top: 0.5pt solid #008C00;
            border-bottom: 1pt solid #008C00;
        }

        /* Detail rows */
        .r-det td {
            font-size: 8pt;
            font-weight: normal;
            color: #000000;
            padding: 3pt 5pt;
            border-bottom: 0.3pt solid #e0e0e0;
            vertical-align: middle;
        }

        /* Alignment helpers */
        .num { text-align: right; }
        .ctr { text-align: center; }
        .zero { color: #bbbbbb; }

        /* Ref/date in the Customer column */
        .ref-text {
            font-size: 7.5pt;
            color: #000000;
            margin-left: 6pt;
        }

        /* Overdue rows */
        .od td { color: #c0392b; }
        .od-badge {
            font-size: 6.5pt;
            color: #c0392b;
            background-color: #fde8e8;
            padding: 0.5pt 2.5pt;
            margin-left: 2pt;
        }

        /* Grand total row (dark navy) */
        .r-grand td {
            background-color: #1a3a5c;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 5pt 5pt;
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

        /* ─── Grand total section (last page) ───────────────────────── */
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

{{-- ═══════════════════════════════════════════════════════
     One page-block per customer
     ═══════════════════════════════════════════════════════ --}}
@forelse($customers as $i => $customer)
    <div class="page-block">

        {{-- Company Header --}}
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

        {{-- Salesman Banner (only when a specific salesman is selected) --}}
        @if(!empty($salesmanLabel) && $salesmanLabel !== 'All Salesmen')
            <div class="salesman-banner">{{ strtoupper($salesmanLabel) }}</div>
        @endif

        {{-- Customer Banner --}}
        <div class="customer-banner">{{ strtoupper($customer['name']) }}</div>

        {{-- Aging Date --}}
        <div class="aging-label">Aging Date {{ $to->format('d-M-y') }}</div>

        {{-- Aging Table --}}
        <table class="ag-table">
            <thead>
            <tr>
                <th style="width:32%">Customer</th>
                <th class="th-days" style="width:6%">Days</th>
                <th style="width:12%">Current</th>
                <th style="width:12%">1-30 Days</th>
                <th style="width:12%">31-60 Days</th>
                <th style="width:13%">Over 60 Days</th>
                <th style="width:13%">Total Balance</th>
            </tr>
            </thead>
            <tbody>

            {{-- Customer summary / totals row --}}
            <tr class="r-sum">
                <td colspan="2">{{ $customer['name'] }}</td>
                <td class="num">{{ number_format($customer['totals']['current']) }}</td>
                <td class="num">{{ number_format($customer['totals']['days_1_30']) }}</td>
                <td class="num">{{ number_format($customer['totals']['days_31_60']) }}</td>
                <td class="num">{{ number_format($customer['totals']['over_60']) }}</td>
                <td class="num">{{ number_format($customer['totals']['balance']) }}</td>
            </tr>

            {{-- Transaction detail rows --}}
            @foreach($customer['transactions'] as $ri => $tx)
                @php
                    $label = match((int)$tx['type']) {
                        13  => 'Sales Invoice',
                        10  => 'Credit Note',
                        11  => 'Delivery',
                        30  => 'Journal Entry',
                        16  => 'Sales Payment',
                        default => 'Trans #' . $tx['type'],
                    };
                    $overdue = (bool)($tx['OverDue'] ?? false);
                    $ref     = !empty($tx['memo_']) ? trim($tx['memo_']) : '';
                    $txDate  = \Carbon\Carbon::parse($tx['tran_date'])->format('d/m/Y');
                @endphp
                <tr class="r-det {{ $overdue ? 'od' : '' }}">
                    <td>
                        {{ $label }}
                        <span class="ref-text">
                        @if($ref){{ $ref }}&nbsp;&nbsp;@endif{{ $txDate }}
                    </span>
                        @if($overdue)
                            <span class="od-badge">OVERDUE</span>
                        @endif
                    </td>
                    <td class="ctr {{ $tx['days'] == 0 ? 'zero' : '' }}">
                        {{ $tx['days'] > 0 ? $tx['days'] : '' }}
                    </td>
                    <td class="num {{ $tx['current'] == 0 ? 'zero' : '' }}">
                        {{ number_format($tx['current']) }}
                    </td>
                    <td class="num {{ $tx['b1_30'] == 0 ? 'zero' : '' }}">
                        {{ number_format($tx['b1_30']) }}
                    </td>
                    <td class="num {{ $tx['b31_60'] == 0 ? 'zero' : '' }}">
                        {{ number_format($tx['b31_60']) }}
                    </td>
                    <td class="num {{ $tx['bOver60'] == 0 ? 'zero' : '' }}">
                        {{ number_format($tx['bOver60']) }}
                    </td>
                    <td class="num">
                        {{ number_format($tx['balance']) }}
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>

        <div class="rpt-footer">
            Generated: {{ now()->format('d M Y, H:i') }}
            &nbsp;|&nbsp;
            Period: {{ $from->format('d M Y') }} &ndash; {{ $to->format('d M Y') }}
            &nbsp;|&nbsp;
            Page {{ $i + 1 }} of {{ count($customers) }}
        </div>

    </div>
    {{-- end .page-block --}}

@empty

    <div class="empty-state">
        No transactions found for the selected parameters.
    </div>

@endforelse

{{-- Grand Total block — only shown when more than one customer --}}
@if(count($customers) > 1)
    <div class="grand-wrap">
        <div class="grand-title">
            Grand Total &mdash; {{ count($customers) }} Customers
            &nbsp;|&nbsp; {{ $salesmanLabel ?? 'All Salesmen' }}
            &nbsp;|&nbsp; {{ $from->format('d M Y') }} &ndash; {{ $to->format('d M Y') }}
        </div>
        <table class="ag-table">
            <thead>
            <tr>
                <th style="width:32%">Summary</th>
                <th class="th-days" style="width:6%">&nbsp;</th>
                <th style="width:12%">Current</th>
                <th style="width:12%">1-30 Days</th>
                <th style="width:12%">31-60 Days</th>
                <th style="width:13%">Over 60 Days</th>
                <th style="width:13%">Total Balance</th>
            </tr>
            </thead>
            <tbody>
            <tr class="r-grand">
                <td colspan="2">All Selected Customers ({{ count($customers) }})</td>
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        /*
         * Dompdf-safe stylesheet — No flexbox | No grid | No CDN | No JS
         * Paper: A4 Landscape  |  Font: DejaVu Sans (Dompdf built-in)
         */

        @page {
            size: A4 landscape;
            margin: 12mm 12mm 12mm 12mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 8pt;
            color: #000000;
            background: #ffffff;
        }

        /* ─── Page break ─────────────────────────────────────────────── */
        .page-block {
            page-break-after: always;
            border: 0.5pt solid #cccccc;
            padding: 10pt 12pt 8pt 12pt;
        }
        .page-block:last-child { page-break-after: avoid; }

        /* ─── Company header ─────────────────────────────────────────── */
        .co-table { width:100%; border-collapse:collapse; margin-bottom:8pt; }
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
        .logo-img { max-height:152pt; max-width:152pt; }

        /* ─── Banners ────────────────────────────────────────────────── */
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
        .customer-banner {
            background-color: #008C00; color: #ffffff;
            text-align: center; font-size: 9.5pt; font-weight: bold;
            letter-spacing: 0.3pt; padding: 4.5pt 6pt; margin-bottom: 3pt;
        }

        /* ─── Period label ───────────────────────────────────────────── */
        .aging-label {
            text-align: center; font-size: 8.5pt; color: #000000; margin-bottom: 4pt;
        }

        /* ─── Main table ─────────────────────────────────────────────── */
        .ag-table { width:100%; border-collapse:collapse; border: 0.5pt solid #b0b0b0; }

        .ag-table thead tr { background-color: #008C00; color: #ffffff; }
        .ag-table thead th {
            font-size: 7.5pt; font-weight: bold;
            padding: 4pt 3.5pt; text-align: right;
            white-space: nowrap;
            border-left: 0.3pt solid #009900;
        }
        .ag-table thead th:first-child { text-align: left; border-left: none; }
        .ag-table thead th.th-ref      { text-align: left; }
        .ag-table thead th.th-date     { text-align: left; }
        .ag-table thead th.th-days     { text-align: center; }

        /* Summary / totals row */
        .r-sum td {
            background-color: #e8f1e0; font-size: 8pt; font-weight: bold; color: #000000;
            padding: 4.5pt 3.5pt;
            border-top: 0.5pt solid #008C00; border-bottom: 1pt solid #008C00;
        }

        /* Detail rows */
        .r-det td {
            font-size: 7.5pt; color: #000000;
            padding: 2.5pt 3.5pt; border-bottom: 0.3pt solid #e0e0e0;
            vertical-align: middle;
        }

        /* Helpers */
        .num  { text-align: right; }
        .ctr  { text-align: center; }
        .zero { color: #bbbbbb; }
        .neg  { color: #c0392b; }

        /* Grand total */
        .r-grand td {
            background-color: #1a3a5c; color: #ffffff;
            font-weight: bold; font-size: 8pt; padding: 4.5pt 3.5pt;
        }

        /* ─── Footer ─────────────────────────────────────────────────── */
        .rpt-footer {
            font-size: 6.5pt; color: #aaaaaa;
            text-align: right; margin-top: 4pt; padding-top: 3pt;
            border-top: 0.3pt solid #dddddd;
        }

        /* ─── Grand total section ────────────────────────────────────── */
        .grand-wrap { border: 0.5pt solid #cccccc; padding: 10pt 12pt 8pt 12pt; }
        .grand-title {
            font-size: 8.5pt; font-weight: bold; color: #1a3a5c;
            padding-bottom: 3pt; margin-bottom: 6pt; border-bottom: 1pt solid #1a3a5c;
        }

        /* ─── Empty state ────────────────────────────────────────────── */
        .empty-state { text-align: center; padding: 40pt; color: #888888; font-size: 10pt; }
    </style>
</head>
<body>

{{-- ── Type labels (FA systypes_array) ── --}}
@php
    use Carbon\Carbon;

     $typeLabels = [
        0  => 'Journal Entry',
        1  => 'Bank Payment',
        2  => 'Bank Deposit',
        3  => 'Bank Charge',
        11 => 'Credit Note',
        13 => 'Delivery',
        12 => 'Customer Payment',
        10 => 'Sales Invoice',
        16 => 'Debit Note',
        17 => 'Purchase Invoice',
        30 => 'Sales Order',
        31 => 'Sales Quote',
    ];

    // agingLabels passed from controller:
    // ['b1' => '1-30 Days', 'b2' => '31-60 Days', 'b3' => '61-90 Days', 'b4' => 'Over 90 Days']
    $lB1 = $agingLabels['b1'] ?? '1-30 Days';
    $lB2 = $agingLabels['b2'] ?? '31-60 Days';
    $lB3 = $agingLabels['b3'] ?? '61-90 Days';
    $lB4 = $agingLabels['b4'] ?? 'Over 90 Days';
@endphp

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
                        <span style="font-size:7.5pt;color:#008C00;font-weight:bold;">Lucky Snacks<br>(Pvt.) Ltd</span>
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

        {{-- Period & Aging Date --}}
        <div class="aging-label">
            Period: {{ $from->format('d M Y') }} &ndash; {{ $to->format('d M Y') }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            Aging Date {{ $to->format('d-M-y') }}
        </div>

{{--        @if($summaryOnly ?? false)--}}
{{--            <div class="summary-note">Summary view — individual transactions not shown</div>--}}
{{--        @endif--}}

        {{-- ══ Aging Table ══ --}}
        <table class="ag-table">
            <thead>
            <tr>
                <th style="width:16%">Customer</th>
                <th class="th-ref"  style="width:8%">Reference</th>
                <th class="th-date" style="width:8%">Date</th>
                <th class="th-days" style="width:4%">Days</th>
                <th style="width:11%">Current</th>
                <th style="width:11%">{{ $lB1 }}</th>
                <th style="width:11%">{{ $lB2 }}</th>
                <th style="width:11%">{{ $lB3 }}</th>
                <th style="width:11%">{{ $lB4 }}</th>
                <th style="width:9%">Total Balance</th>
            </tr>
            </thead>
            <tbody>

            {{-- Summary row --}}
            <tr class="r-sum">
                <td colspan="4">{{ $customer['name'] }}</td>
                <td class="num">{{ number_format($customer['totals']['current']) }}</td>
                <td class="num">{{ number_format($customer['totals']['b1']) }}</td>
                <td class="num">{{ number_format($customer['totals']['b2']) }}</td>
                <td class="num">{{ number_format($customer['totals']['b3']) }}</td>
                <td class="num">{{ number_format($customer['totals']['b4']) }}</td>
                <td class="num">{{ number_format($customer['totals']['balance']) }}</td>
            </tr>

            {{-- Detail rows (empty when summaryOnly) --}}
            @foreach($customer['transactions'] as $tx)
                @php
                    $label   = $typeLabels[(int)$tx['type']] ?? ('Trans #' . $tx['type']);
                    $ref     = $tx['reference'] ?? '';
                    $txDate  = Carbon::parse($tx['tran_date'])->format('d/m/Y');
                    $bal     = (float) $tx['balance'];
                    $isNeg   = $bal < 0;
                @endphp
                <tr class="r-det">
                    <td>{{ $label }}</td>
                    <td>{{ $ref }}</td>
                    <td>{{ $txDate }}</td>
                    <td class="ctr {{ $tx['days'] == 0 ? 'zero' : '' }}">
                        {{ $tx['days'] > 0 ? $tx['days'] : '' }}
                    </td>
                    <td class="num {{ $tx['current'] == 0 ? 'zero' : ($tx['current'] < 0 ? 'neg' : '') }}">
                        {{ number_format($tx['current']) }}
                    </td>
                    <td class="num {{ $tx['b1'] == 0 ? 'zero' : ($tx['b1'] < 0 ? 'neg' : '') }}">
                        {{ number_format($tx['b1']) }}
                    </td>
                    <td class="num {{ $tx['b2'] == 0 ? 'zero' : ($tx['b2'] < 0 ? 'neg' : '') }}">
                        {{ number_format($tx['b2']) }}
                    </td>
                    <td class="num {{ $tx['b3'] == 0 ? 'zero' : ($tx['b3'] < 0 ? 'neg' : '') }}">
                        {{ number_format($tx['b3']) }}
                    </td>
                    <td class="num {{ $tx['b4'] == 0 ? 'zero' : ($tx['b4'] < 0 ? 'neg' : '') }}">
                        {{ number_format($tx['b4']) }}
                    </td>
                    <td class="num {{ $isNeg ? 'neg' : '' }}">
                        {{ number_format($bal) }}
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
            <br><span style="font-size:8pt;">(Suppress Zeros is on — try unchecking it)</span>
        @endif
    </div>
@endforelse

{{-- Grand Total (2+ customers) --}}
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
                <th style="width:16%">Summary</th>
                <th class="th-ref"  style="width:8%">&nbsp;</th>
                <th class="th-date" style="width:8%">&nbsp;</th>
                <th class="th-days" style="width:4%">&nbsp;</th>
                <th style="width:11%">Current</th>
                <th style="width:11%">{{ $lB1 }}</th>
                <th style="width:11%">{{ $lB2 }}</th>
                <th style="width:11%">{{ $lB3 }}</th>
                <th style="width:11%">{{ $lB4 }}</th>
                <th style="width:9%">Total Balance</th>
            </tr>
            </thead>
            <tbody>
            <tr class="r-grand">
                <td colspan="4">All Selected Customers ({{ count($customers) }})</td>
                <td class="num">{{ number_format($grand['current']) }}</td>
                <td class="num">{{ number_format($grand['b1']) }}</td>
                <td class="num">{{ number_format($grand['b2']) }}</td>
                <td class="num">{{ number_format($grand['b3']) }}</td>
                <td class="num">{{ number_format($grand['b4']) }}</td>
                <td class="num">{{ number_format($grand['balance']) }}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endif

</body>
</html>

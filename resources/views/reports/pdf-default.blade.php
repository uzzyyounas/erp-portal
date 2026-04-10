<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8pt; color: #212529; }

        /* Header */
        .report-header {
            border-bottom: 2px solid #1a3a5c;
            padding-bottom: 8pt;
            margin-bottom: 10pt;
        }
        .report-header .company-name {
            font-size: 14pt; font-weight: bold; color: #1a3a5c;
        }
        .report-header .report-title {
            font-size: 11pt; font-weight: bold; color: #2d6a9f; margin-top: 2pt;
        }
        .report-header .meta {
            font-size: 7pt; color: #6c757d; margin-top: 4pt;
        }

        /* Params bar */
        .params-bar {
            background: #f0f4f8;
            border: 1px solid #cdd8e3;
            padding: 5pt 8pt;
            margin-bottom: 8pt;
            font-size: 7.5pt;
            border-radius: 2pt;
        }
        .params-bar .param-item {
            display: inline-block;
            margin-right: 16pt;
        }
        .params-bar .param-label { color: #6c757d; font-weight: bold; }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4pt;
        }
        thead tr th {
            background: #1a3a5c;
            color: #fff;
            padding: 5pt 6pt;
            text-align: left;
            font-size: 7.5pt;
            font-weight: bold;
            white-space: nowrap;
        }
        tbody tr td {
            padding: 4pt 6pt;
            border-bottom: 1px solid #e9ecef;
            font-size: 7.5pt;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td { background: #f8f9fa; }
        tbody tr:last-child td { border-bottom: 2px solid #1a3a5c; }

        /* Numeric columns right-aligned */
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Footer */
        .report-footer {
            margin-top: 10pt;
            border-top: 1px solid #dee2e6;
            padding-top: 5pt;
            font-size: 7pt;
            color: #6c757d;
            display: flex;
            justify-content: space-between;
        }

        /* No data */
        .no-data {
            text-align: center;
            padding: 20pt;
            color: #6c757d;
            font-style: italic;
        }

        /* Page counter */
        @page { margin: 15mm 12mm 15mm 12mm; }
    </style>
</head>
<body>

{{-- Report Header --}}
<div class="report-header">
    <table style="width:100%;border:none;">
        <tr>
            <td style="border:none;padding:0;">
                <div class="company-name">{{ config('app.name', 'ERP System') }}</div>
                <div class="report-title">{{ $report->name }}</div>
                @if($report->description)
                    <div class="meta" style="margin-top:2pt;">{{ $report->description }}</div>
                @endif
            </td>
            <td style="border:none;padding:0;text-align:right;vertical-align:top;">
                <div class="meta">
                    Generated: {{ $generatedAt->format('d/m/Y H:i:s') }}<br>
                    By: {{ $generatedBy }}<br>
                    Category: {{ $report->category->name }}
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- Parameters Bar --}}
@if(count($params) > 0)
<div class="params-bar">
    <strong>Parameters: </strong>
    @foreach($params as $key => $value)
        @if($value)
            <span class="param-item">
                <span class="param-label">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                {{ $value }}
            </span>
        @endif
    @endforeach
</div>
@endif

{{-- Data Table --}}
@if(empty($rows))
    <div class="no-data">No data found for the selected parameters.</div>
@else
    <table>
        <thead>
            <tr>
                @foreach($columns as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($columns as $col)
                        <td @if(is_numeric(str_replace([',', ' '], '', $row[$col] ?? ''))) class="text-right" @endif>
                            {{ $row[$col] ?? '' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Summary row count --}}
    <div style="font-size:7pt;color:#6c757d;margin-top:5pt;text-align:right;">
        Total records: {{ count($rows) }}
    </div>
@endif

{{-- Footer --}}
<div class="report-footer">
    <span>{{ config('app.name') }} — Confidential</span>
    <span>{{ $report->name }} — {{ $generatedAt->format('d/m/Y') }}</span>
</div>

</body>
</html>

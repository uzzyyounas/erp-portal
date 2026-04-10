{{--
    UNIVERSAL PDF TEMPLATE — reads all settings from $tplConfig + CompanySetting
--}}
@php
    use App\Models\CompanySetting;
    $c   = $tplConfig;
    $co  = CompanySetting::all_settings();

    $fontPt = match($c['font_size']??'medium') { 'small'=>'7pt','large'=>'9.5pt',default=>'8pt' };
    $fontSm = match($c['font_size']??'medium') { 'small'=>'6pt','large'=>'8pt',  default=>'7pt' };

    $headerColor  = $c['color_page_header'] ?? '#1a3a5c';
    $accentColor  = $c['color_accent']      ?? '#e8a020';
    $coName    = $co['company_name']    ?? config('app.name','ERP System');
    $coTagline = $co['company_tagline'] ?? '';
    $coAddr    = $co['company_address'] ?? '';
    $coAddr2   = $co['company_address2']?? '';
    $coCity    = $co['company_city']    ?? '';
    $coPhone   = $co['company_phone']   ?? '';
    $coEmail   = $co['company_email']   ?? '';
    $coNtn     = $co['company_ntn']     ?? '';
    $coLogo    = !empty($c['logo_url']) ? $c['logo_url'] : ($co['company_logo_url'] ?? '');
    $logoH     = ($c['logo_height'] ?? 55).'pt';
    $headerStyle = $c['header_style'] ?? 'full';
    $logoPos     = $c['logo_position'] ?? 'left';

    // Title sizes
    $titleSizePt = match($c['header_title_size']??'large') { 'small'=>'8pt','large'=>'13pt',default=>'10pt' };
    $nameSizePt  = match($c['header_company_name_size']??'large') { 'small'=>'9pt','large'=>'14pt',default=>'11pt' };

    // Divider
    $divColor = !empty($c['header_divider_color']) ? $c['header_divider_color'] : $accentColor;
    $dividerCss = match($c['header_divider']??'thick') {
        'none'    => '',
        'thin'    => "border-bottom:1pt solid {$divColor};",
        'double'  => "border-bottom:3pt double {$divColor};",
        'colored' => "border-bottom:6pt solid {$divColor};",
        default   => "border-bottom:3pt solid {$divColor};",
    };

    // Column detection
    $allCols    = !empty($rows) ? array_keys($rows[0]) : [];
    $masterCols = array_filter($allCols, fn($k) => str_starts_with(strtolower($k), 'header_'));
    $detailCols = array_filter($allCols, fn($k) => str_starts_with(strtolower($k), 'detail_'));
    $isMD       = ($layout === 'master-detail') && count($masterCols) && count($detailCols);
    $numericCols = [];
    foreach ($rows as $row) {
        foreach ($allCols as $col) {
            $raw = str_replace([',',' '], '', $row[$col] ?? '');
            if (is_numeric($raw) && $raw !== '') $numericCols[$col] = true;
        }
    }
    $balanceCol = null;
    if ($layout === 'statement') {
        foreach ($allCols as $col) {
            if (stripos($col, 'balance') !== false || stripos($col, 'bal') !== false) {
                $balanceCol = $col; break;
            }
        }
    }

    // Column header CSS
    $colHdrStyle = $c['col_header_style'] ?? 'filled';
    $thBg   = $c['color_header_bg']   ?? '#1a3a5c';
    $thText = $c['color_header_text'] ?? '#ffffff';
    $thStyle = match($colHdrStyle) {
        'outline'  => "background:#fff;color:{$thBg};border-bottom:2pt solid {$thBg};",
        'minimal'  => "background:#fff;color:#6c757d;border-bottom:1pt solid {$c['color_border']};",
        'gradient' => "background:linear-gradient(135deg,{$thBg},{$accentColor});color:{$thText};",
        default    => "background:{$thBg};color:{$thText};",
    };

    // Footer zone resolver
    $fzResolve = function($zone, $custom) use ($coName, $c, $report, $generatedAt, $generatedBy) {
        return match($zone) {
            'company'     => $coName . ($c['footer_confidential'] ? ' — CONFIDENTIAL' : ''),
            'report_name' => $report->name,
            'datetime'    => $generatedAt->format('d M Y H:i'),
            'page'        => 'Page {PAGE_NUM} of {PAGE_COUNT}',
            'custom'      => $custom ?? '',
            default       => '',
        };
    };
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:DejaVu Sans,sans-serif; font-size:{{ $fontPt }}; color:#1e1e2e; }
@page { margin:13mm 11mm 18mm 11mm; size:{{ $c['paper_size']??'A4' }} {{ $c['orientation']??'landscape' }}; }

/* Watermark */
@if(!empty($c['watermark_text']))
.watermark {
    position:fixed; top:40%; left:0; right:0; text-align:center;
    font-size:60pt; font-weight:bold; opacity:{{ ($c['watermark_opacity']??8)/100 }};
    color:#000; transform:rotate(-30deg); z-index:-1;
}
@endif

/* Footer */
.page-footer {
    position:fixed; bottom:0; left:0; right:0; height:14mm;
    @if($c['footer_show_divider']??true) border-top:1pt solid {{ $c['color_border'] }}; @endif
    font-size:{{ $fontSm }}; color:#adb5bd;
    display:table; width:100%;
}
.pfc { display:table-cell; vertical-align:middle; padding:0 11mm; }

/* Data table */
.data-table { width:100%; border-collapse:collapse; font-size:{{ $fontPt }}; }
.data-table thead th { {{ $thStyle }} padding:5pt 6pt; font-size:{{ $fontSm }}; font-weight:bold; letter-spacing:.2pt; @if($c['show_column_borders']??false) border-right:1pt solid rgba(255,255,255,.15); @endif }
.data-table thead th.r { text-align:right; }
.data-table tbody td { padding:3.5pt 6pt; vertical-align:top; border-bottom:1pt solid {{ $c['color_border'] }}; @if($c['show_column_borders']??false) border-right:1pt solid {{ $c['color_border'] }}; @endif }
.data-table tbody td.r  { text-align:right; }
.data-table tbody td.mn { font-family:DejaVu Sans Mono,monospace; }
.data-table tbody td.rn { color:#adb5bd; font-size:{{ $fontSm }}; text-align:right; width:22pt; }
.data-table tbody td.ng { color:#dc2626; }
.data-table tr.row-odd td  { background:{{ $c['color_row_odd'] }}; }
.data-table tr.row-even td { background:{{ $c['color_row_even'] }}; }
.data-table tr.group-hdr td { background:#e8f0fb; border-left:4pt solid {{ $accentColor }}; padding:4pt 8pt; font-weight:bold; color:{{ $headerColor }}; }
.data-table tr.subtot td { background:{{ $c['color_subtotal_bg'] }}; font-weight:bold; border-top:1pt solid #b0c8e0; padding:4pt 6pt; text-align:right; }
.data-table tr.subtot td.lbl { text-align:left; font-size:{{ $fontSm }}; }
.data-table tfoot tr td { background:{{ $c['color_total_bg'] }}; color:{{ $c['color_total_text'] }}; padding:5pt 6pt; font-weight:bold; font-size:calc({{ $fontPt }} + .5pt); text-align:right; }
.data-table tfoot tr td.lbl { text-align:left; font-size:{{ $fontSm }}; font-weight:normal; opacity:.75; }

.master-block { background:#f8fafc; border:1pt solid #dce6f0; border-top:3pt solid {{ $headerColor }}; padding:7pt 10pt; margin-bottom:4pt; page-break-inside:avoid; }
.tot-panel { background:{{ $c['color_total_bg'] }}; color:{{ $c['color_total_text'] }}; padding:6pt 10pt; border-radius:2pt; width:210pt; float:right; margin-top:6pt; font-size:{{ $fontPt }}; }
.rec-badge { display:inline-block; background:#f0f4fa; color:{{ $headerColor }}; border-radius:2pt; padding:2pt 7pt; font-size:{{ $fontSm }}; font-weight:bold; margin-top:5pt; clear:both; }
.params-bar { background:#f0f4fa; border:1pt solid #c5d5ea; border-left:4pt solid {{ $accentColor }}; padding:4pt 8pt; margin-bottom:8pt; font-size:{{ $fontSm }}; }

/* Aged buckets */
.th-curr { background:#166534!important; } .th-30 { background:#854d0e!important; }
.th-60   { background:#9a3412!important; } .th-90 { background:#7f1d1d!important; }
.amt-curr{ color:#166534;font-weight:bold; } .amt-30{ color:#854d0e;font-weight:bold; }
.amt-60  { color:#9a3412;font-weight:bold; } .amt-90{ color:#b91c1c;font-weight:bold; }
.ft-curr { background:#d1fae5!important;color:#065f46!important; } .ft-30 { background:#fef3c7!important;color:#78350f!important; }
.ft-60   { background:#ffedd5!important;color:#9a3412!important; } .ft-90 { background:#fee2e2!important;color:#991b1b!important; }

/* Statement */
.type-badge { display:inline-block; padding:1.5pt 5pt; border-radius:2pt; font-size:{{ $fontSm }}; font-weight:bold; text-transform:uppercase; }
.type-inv { background:#dbeafe;color:#1e40af; } .type-cr { background:#dcfce7;color:#166534; } .type-pmt { background:#fef9c3;color:#854d0e; }
</style>
</head>
<body>

{{-- Watermark --}}
@if(!empty($c['watermark_text']))
<div class="watermark">{{ strtoupper($c['watermark_text']) }}</div>
@endif

{{-- Fixed footer --}}
@if($c['show_page_footer']??true)
<div class="page-footer">
    <div class="pfc">{{ $fzResolve($c['footer_left']??'company', $c['footer_custom_left']??'') }}</div>
    <div class="pfc" style="text-align:center;">{{ $fzResolve($c['footer_center']??'report_name', $c['footer_custom_center']??'') }}</div>
    <div class="pfc" style="text-align:right;">{{ $fzResolve($c['footer_right']??'page', $c['footer_custom_right']??'') }}</div>
</div>
@endif

{{-- ══════════ PAGE HEADER ══════════ --}}
@if($headerStyle !== 'none')
@php
    $showLogo = $logoPos !== 'none' && !empty($coLogo) && !in_array($headerStyle, ['simple']);
@endphp

@if($headerStyle === 'centered')
<div style="text-align:center;padding-bottom:8pt;margin-bottom:8pt;{{ $dividerCss }}">
    @if($showLogo) <img src="{{ $coLogo }}" style="max-height:{{ $logoH }};max-width:140pt;object-fit:contain;display:block;margin:0 auto 4pt;"> @endif
    @if($c['header_show_company_name']??true) <div style="font-size:{{ $nameSizePt }};font-weight:bold;color:{{ $headerColor }};">{{ $coName }}</div> @endif
    @if(($c['header_show_tagline']??false) && $coTagline) <div style="color:{{ $accentColor }};font-style:italic;font-size:{{ $fontSm }};">{{ $coTagline }}</div> @endif
    @if($c['header_show_report_title']??true) <div style="font-size:{{ $titleSizePt }};font-weight:bold;color:{{ $accentColor }};margin-top:4pt;">{{ $report->name }}</div> @endif
    <div style="font-size:{{ $fontSm }};color:#6c757d;">
        @if($c['header_show_category']??true) {{ $report->category->name }} · @endif
        @if($c['header_show_print_date']??true) {{ $generatedAt->format($c['header_date_format']??'d M Y') }} · @endif
        @if($c['header_show_generated_by']??true) By: {{ $generatedBy }} @endif
    </div>
</div>

@elseif($headerStyle === 'logo-only')
<div style="padding-bottom:8pt;margin-bottom:8pt;{{ $dividerCss }};text-align:{{ $logoPos }};">
    @if(!empty($coLogo)) <img src="{{ $coLogo }}" style="max-height:{{ $logoH }};max-width:200pt;object-fit:contain;">
    @else <span style="font-size:{{ $nameSizePt }};font-weight:bold;color:{{ $headerColor }};">{{ $coName }}</span>
    @endif
</div>

@elseif($headerStyle === 'simple')
<div style="display:table;width:100%;padding-bottom:8pt;margin-bottom:8pt;{{ $dividerCss }}">
    <div style="display:table-cell;vertical-align:bottom;">
        @if($c['header_show_report_title']??true) <div style="font-size:{{ $titleSizePt }};font-weight:bold;color:{{ $accentColor }};">{{ $report->name }}</div> @endif
        @if($c['header_show_category']??true) <div style="font-size:{{ $fontSm }};color:#6c757d;">{{ $report->category->name }}</div> @endif
    </div>
    <div style="display:table-cell;vertical-align:bottom;text-align:right;">
        <div style="font-size:{{ $fontSm }};color:#6c757d;">
            @if($c['header_show_print_date']??true) {{ $generatedAt->format($c['header_date_format']??'d M Y') }}<br> @endif
            @if($c['header_show_generated_by']??true) By: {{ $generatedBy }} @endif
        </div>
    </div>
</div>

@else {{-- FULL header --}}
<div style="display:table;width:100%;padding-bottom:8pt;margin-bottom:8pt;{{ $dividerCss }}">
    <div style="display:table-row;">
        @if($showLogo && $logoPos === 'left')
        <div style="display:table-cell;vertical-align:middle;width:{{ $logoH }};padding-right:10pt;">
            <img src="{{ $coLogo }}" style="max-height:{{ $logoH }};max-width:90pt;object-fit:contain;">
        </div>
        @endif
        <div style="display:table-cell;vertical-align:top;">
            @if($c['header_show_company_name']??true) <div style="font-size:{{ $nameSizePt }};font-weight:bold;color:{{ $headerColor }};line-height:1.2;">{{ $coName }}</div> @endif
            @if(($c['header_show_tagline']??false) && $coTagline) <div style="font-size:{{ $fontSm }};color:{{ $accentColor }};font-style:italic;">{{ $coTagline }}</div> @endif
            @if(($c['header_show_address']??false) && $coAddr) <div style="font-size:{{ $fontSm }};color:#6c757d;">{{ $coAddr }}@if($coAddr2), {{ $coAddr2 }}@endif@if($coCity), {{ $coCity }}@endif</div> @endif
            @if(($c['header_show_phone']??false) && $coPhone) <div style="font-size:{{ $fontSm }};color:#6c757d;">Tel: {{ $coPhone }}</div> @endif
            @if(($c['header_show_email']??false) && $coEmail) <div style="font-size:{{ $fontSm }};color:#6c757d;">{{ $coEmail }}</div> @endif
            @if(($c['header_show_ntn']??false) && $coNtn) <div style="font-size:{{ $fontSm }};color:#6c757d;">NTN: {{ $coNtn }}</div> @endif
        </div>
        <div style="display:table-cell;vertical-align:bottom;text-align:{{ $c['header_title_align']??'left' }};min-width:120pt;">
            @if($c['header_show_report_title']??true) <div style="font-size:{{ $titleSizePt }};font-weight:bold;color:{{ $accentColor }};">{{ $report->name }}</div> @endif
            @if($c['header_show_category']??true) <div style="font-size:{{ $fontSm }};color:#6c757d;">{{ $report->category->name }}</div> @endif
            @if($c['header_show_print_date']??true) <div style="font-size:{{ $fontSm }};color:#6c757d;">{{ $generatedAt->format($c['header_date_format']??'d M Y') }}</div> @endif
            @if($c['header_show_generated_by']??true) <div style="font-size:{{ $fontSm }};color:#6c757d;">By: {{ $generatedBy }}</div> @endif
        </div>
        @if($showLogo && $logoPos === 'right')
        <div style="display:table-cell;vertical-align:middle;width:{{ $logoH }};padding-left:10pt;">
            <img src="{{ $coLogo }}" style="max-height:{{ $logoH }};max-width:90pt;object-fit:contain;">
        </div>
        @endif
    </div>
</div>
@endif
@endif {{-- end header --}}

{{-- Params bar --}}
@if(($c['params_bar_style']??'banner') !== 'none' && count(array_filter($params)))
@if(($c['params_bar_style']??'banner') === 'table')
<table style="margin-bottom:7pt;border-collapse:collapse;font-size:{{ $fontSm }};">
    <tr>
    @foreach($params as $key => $val)
        @if($val)
        <td style="background:#e8f0fb;font-weight:bold;padding:2pt 8pt;border:1pt solid #c5d5ea;">{{ ucwords(str_replace('_',' ',$key)) }}</td>
        <td style="padding:2pt 8pt;border:1pt solid #c5d5ea;">{{ is_array($val) ? implode(', ',$val) : $val }}</td>
        @endif
    @endforeach
    </tr>
</table>
@else
<div class="params-bar">
    <strong style="color:{{ $headerColor }};">Parameters: </strong>
    @foreach($params as $key => $val)
        @if($val)
        <span style="margin-right:12pt;">
            <span style="color:#6c757d;">{{ ucwords(str_replace('_',' ',$key)) }}:</span>
            <strong>{{ is_array($val) ? implode(', ',$val) : $val }}</strong>
        </span>
        @endif
    @endforeach
</div>
@endif
@endif

{{-- ══════════ DATA AREA ══════════ --}}
@if(empty($rows))
<div style="text-align:center;padding:30pt;color:#6c757d;font-style:italic;border:1pt dashed #dee2e6;">
    No records found for the selected parameters.
</div>
@else
{{-- MASTER-DETAIL --}}
@if($isMD)
@php $grouped = collect($rows)->groupBy(fn($r) => $r[array_values($masterCols)[0]] ?? ''); $grandTotals = []; @endphp
@foreach($grouped as $gKey => $gRows)
@php $firstRow = $gRows->first(); $groupTotals = []; @endphp
<div class="master-block">
    <table style="width:100%;border:none;">
        <tr>
            @foreach($masterCols as $mc)
            <td style="padding:2pt 8pt 2pt 0;vertical-align:top;width:25%;">
                <span style="font-size:{{ $fontSm }};text-transform:uppercase;color:#8096ac;font-weight:bold;display:block;">{{ ucwords(str_replace(['header_','_'],' ',$mc)) }}</span>
                <span style="font-size:calc({{ $fontPt }} + .5pt);font-weight:bold;color:{{ $headerColor }};">{{ $firstRow[$mc] ?? '—' }}</span>
            </td>
            @endforeach
        </tr>
    </table>
</div>
<table class="data-table" style="margin-bottom:12pt;">
    <thead><tr>
        @if($c['show_row_numbers']??true)<th style="{{ $thStyle }}width:22pt;">#</th>@endif
        @foreach($detailCols as $dc)<th style="{{ $thStyle }}{{ ($numericCols[$dc]??false)?'text-align:right;':'' }}">{{ ucwords(str_replace(['detail_','_'],' ',$dc)) }}</th>@endforeach
    </tr></thead>
    <tbody>
    @foreach($gRows as $i => $row)
    <tr class="{{ ($c['zebra_rows']??true) ? ($i%2==0?'row-odd':'row-even') : '' }}">
        @if($c['show_row_numbers']??true)<td class="rn">{{ $i+1 }}</td>@endif
        @foreach($detailCols as $dc)
        @php $val=$row[$dc]??''; $isN=$numericCols[$dc]??false; $fval=$isN?floatval(str_replace(',','',$val)):null;
        if($isN&&$val!==''){$groupTotals[$dc]=($groupTotals[$dc]??0)+$fval;$grandTotals[$dc]=($grandTotals[$dc]??0)+$fval;} @endphp
        <td class="{{ $isN?'r mn':'' }} {{ $fval<0?'ng':'' }}">{{ $isN&&$val!==''?number_format($fval,2):$val }}</td>
        @endforeach
    </tr>
    @endforeach
    </tbody>
    @if($c['show_subtotals']??true)
    <tfoot><tr>
        @if($c['show_row_numbers']??true)<td class="lbl"></td>@endif
        @foreach($detailCols as $dc)
        <td style="background:{{ $c['color_subtotal_bg'] }};color:{{ $headerColor }};padding:4pt 6pt;font-weight:bold;text-align:right;font-family:DejaVu Sans Mono,monospace;">
            {{ ($numericCols[$dc]??false)?number_format($groupTotals[$dc]??0,2):'' }}
        </td>
        @endforeach
    </tr></tfoot>
    @endif
</table>
@endforeach
@if(($c['show_totals_row']??true) && count($grandTotals))
<div class="tot-panel">
    @foreach($grandTotals as $col => $total)
    <div style="display:table;width:100%;margin-bottom:2pt;{{ $loop->last?'border-top:1pt solid rgba(255,255,255,.3);padding-top:3pt;margin-top:3pt;':''; }}">
        <div style="display:table-cell;opacity:.75;font-size:{{ $fontSm }};">{{ ucwords(str_replace(['detail_','header_','_'],' ',$col)) }}</div>
        <div style="display:table-cell;text-align:right;font-weight:bold;font-family:DejaVu Sans Mono,monospace;">{{ number_format($total,2) }}</div>
    </div>
    @endforeach
</div><div style="clear:both;"></div>
@endif

{{-- GROUPED --}}
@elseif($layout === 'grouped')
@php $groupCol=$allCols[$c['group_col_index']??0]??$allCols[0]; $grouped=collect($rows)->groupBy($groupCol); $grandTotals=array_fill_keys(array_filter($allCols,fn($col)=>$numericCols[$col]??false),0); @endphp
<table class="data-table">
    <thead><tr>
        @if($c['show_row_numbers']??true)<th style="{{ $thStyle }}width:22pt;">#</th>@endif
        @foreach($allCols as $col)<th style="{{ $thStyle }}{{ ($numericCols[$col]??false)?'text-align:right;':'' }}">{{ ucwords(str_replace('_',' ',$col)) }}</th>@endforeach
    </tr></thead>
    @foreach($grouped as $group => $gRows)
    @php $groupTotals=array_fill_keys(array_keys($grandTotals),0); @endphp
    <tbody>
        <tr class="group-hdr"><td colspan="{{ count($allCols)+($c['show_row_numbers']??true?1:0) }}">{{ $group }} <span style="color:#888;font-size:{{ $fontSm }};font-weight:normal;"> — {{ $gRows->count() }} records</span></td></tr>
        @foreach($gRows as $i => $row)
        <tr class="{{ ($c['zebra_rows']??true)?($i%2==0?'row-odd':'row-even'):'' }}">
            @if($c['show_row_numbers']??true)<td class="rn">{{ $i+1 }}</td>@endif
            @foreach($allCols as $col)
            @php $val=$row[$col]??''; $isN=$numericCols[$col]??false; $fval=$isN?floatval(str_replace(',','',$val)):null;
            if($isN&&$val!==''&&$col!==$groupCol){$groupTotals[$col]=($groupTotals[$col]??0)+$fval;$grandTotals[$col]=($grandTotals[$col]??0)+$fval;} @endphp
            <td class="{{ $isN?'r mn':'' }} {{ $fval<0?'ng':'' }}">{{ $isN&&$val!==''?number_format($fval,2):$val }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
    @if($c['show_subtotals']??true)
    <tbody><tr class="subtot">
        <td class="lbl" colspan="{{ ($c['show_row_numbers']??true?1:0)+1 }}">Subtotal — {{ $group }}</td>
        @foreach(array_slice($allCols,1) as $col)<td class="{{ ($numericCols[$col]??false)?'mn':'' }}">{{ ($numericCols[$col]??false)?number_format($groupTotals[$col]??0,2):'' }}</td>@endforeach
    </tr></tbody>
    @endif
    @endforeach
    @if($c['show_totals_row']??true)
    <tfoot><tr>
        <td class="lbl" colspan="{{ ($c['show_row_numbers']??true?1:0)+1 }}">GRAND TOTAL — {{ count($rows) }} records</td>
        @foreach(array_slice($allCols,1) as $col)<td class="{{ ($numericCols[$col]??false)?'mn':'' }}">{{ ($numericCols[$col]??false)?number_format($grandTotals[$col]??0,2):'' }}</td>@endforeach
    </tr></tfoot>
    @endif
</table>

{{-- AGED --}}
@elseif($layout === 'aged')
@php
    $colTotals=[];$bucketClass=[];
    foreach($allCols as $col){$cl=strtolower($col);
        if(str_contains($cl,'current')) $bucketClass[$col]=['th'=>'th-curr','td'=>'amt-curr','ft'=>'ft-curr'];
        elseif(str_contains($cl,'1-30')||str_contains($cl,'20-30')||preg_match('/\b30\b/',$cl)) $bucketClass[$col]=['th'=>'th-30','td'=>'amt-30','ft'=>'ft-30'];
        elseif(str_contains($cl,'31-60')||str_contains($cl,'46-60')||preg_match('/\b60\b/',$cl)) $bucketClass[$col]=['th'=>'th-60','td'=>'amt-60','ft'=>'ft-60'];
        elseif(str_contains($cl,'61')||str_contains($cl,'76')||str_contains($cl,'90')||str_contains($cl,'over')) $bucketClass[$col]=['th'=>'th-90','td'=>'amt-90','ft'=>'ft-90'];
    }
@endphp
<table class="data-table">
    <thead><tr>
        @if($c['show_row_numbers']??true)<th style="{{ $thStyle }}width:22pt;">#</th>@endif
        @foreach($allCols as $col)
        @php $bc=$bucketClass[$col]??[]; @endphp
        <th class="{{ $bc['th']??'' }}" style="{{ $thStyle }}{{ ($numericCols[$col]??false)?'text-align:right;':'' }}">{{ ucwords(str_replace(['_','-'],' ',$col)) }}</th>
        @endforeach
    </tr></thead>
    <tbody>
    @foreach($rows as $i => $row)
    <tr class="{{ ($c['zebra_rows']??true)?($i%2==0?'row-odd':'row-even'):'' }}">
        @if($c['show_row_numbers']??true)<td class="rn">{{ $i+1 }}</td>@endif
        @foreach($allCols as $col)
        @php $val=$row[$col]??''; $isN=$numericCols[$col]??false; $fval=$isN?floatval(str_replace(',','',$val)):null;
        $bc=$bucketClass[$col]??[]; if($isN&&$val!=='') $colTotals[$col]=($colTotals[$col]??0)+$fval; @endphp
        <td class="{{ $isN?'r mn '.($bc['td']??''):'' }}">
            @if($isN&&$val!==''){{ $fval==0?'—':number_format($fval,2) }}@else{{ $val }}@endif
        </td>
        @endforeach
    </tr>
    @endforeach
    </tbody>
    @if($c['show_totals_row']??true)
    <tfoot><tr>
        @if($c['show_row_numbers']??true)<td class="lbl">—</td>@endif
        @foreach($allCols as $col)
        @php $bc=$bucketClass[$col]??[]; @endphp
        <td class="{{ $bc['ft']??'' }} {{ ($numericCols[$col]??false)?'mn':'lbl' }}">
            {{ ($numericCols[$col]??false)?number_format($colTotals[$col]??0,2):'TOTAL' }}
        </td>
        @endforeach
    </tr></tfoot>
    @endif
</table>

{{-- STATEMENT --}}
@elseif($layout === 'statement')
@php $typeCol=null; foreach($allCols as $col){if(stripos($col,'type')!==false){$typeCol=$col;break;}} $colTotals=[]; @endphp
<table class="data-table">
    <thead><tr>
        @if($c['show_row_numbers']??true)<th style="{{ $thStyle }}width:22pt;">#</th>@endif
        @foreach($allCols as $col)<th style="{{ $thStyle }}{{ ($numericCols[$col]??false)?'text-align:right;':'' }}">{{ ucwords(str_replace('_',' ',$col)) }}</th>@endforeach
    </tr></thead>
    <tbody>
    @foreach($rows as $i => $row)
    <tr class="{{ ($c['zebra_rows']??true)?($i%2==0?'row-odd':'row-even'):'' }}">
        @if($c['show_row_numbers']??true)<td class="rn">{{ $i+1 }}</td>@endif
        @foreach($allCols as $col)
        @php $val=$row[$col]??''; $isN=$numericCols[$col]??false; $fval=$isN?floatval(str_replace(',','',$val)):null; $isBal=($col===$balanceCol);
        if($isN) $colTotals[$col]=($colTotals[$col]??0)+($fval??0);
        $typeCls=''; if($col===$typeCol){$typeCls=stripos($val,'invoice')!==false?'type-inv':(stripos($val,'credit')!==false?'type-cr':(str_contains(strtolower($val),'payment')||str_contains(strtolower($val),'receipt')?'type-pmt':''));} @endphp
        <td class="{{ $isN?'r mn':'' }} {{ $isBal&&$fval<0?'ng':'' }}">
            @if($typeCls)<span class="type-badge {{ $typeCls }}">{{ $val }}</span>
            @elseif($isN&&$val!==''){{ number_format($fval,2) }}
            @else{{ $val }}@endif
        </td>
        @endforeach
    </tr>
    @endforeach
    </tbody>
    @if($c['show_totals_row']??true)
    <tfoot><tr>
        @if($c['show_row_numbers']??true)<td class="lbl">—</td>@endif
        @foreach($allCols as $col)<td class="{{ ($numericCols[$col]??false)?'mn':'lbl' }}">{{ ($numericCols[$col]??false)?number_format($colTotals[$col]??0,2):'TOTAL' }}</td>@endforeach
    </tr></tfoot>
    @endif
</table>

{{-- TABULAR (default) --}}
@else
@php $colTotals=[]; @endphp
<table class="data-table">
    <thead><tr>
        @if($c['show_row_numbers']??true)<th style="{{ $thStyle }}width:22pt;">#</th>@endif
        @foreach($allCols as $col)<th style="{{ $thStyle }}{{ ($numericCols[$col]??false)?'text-align:right;':'' }}">{{ ucwords(str_replace('_',' ',$col)) }}</th>@endforeach
    </tr></thead>
    <tbody>
    @foreach($rows as $i => $row)
    <tr class="{{ ($c['zebra_rows']??true)?($i%2==0?'row-odd':'row-even'):'' }}">
        @if($c['show_row_numbers']??true)<td class="rn">{{ $i+1 }}</td>@endif
        @foreach($allCols as $col)
        @php $val=$row[$col]??''; $isN=$numericCols[$col]??false; $fval=$isN?floatval(str_replace(',','',$val)):null;
        if($isN&&$val!=='') $colTotals[$col]=($colTotals[$col]??0)+$fval; @endphp
        <td class="{{ $isN?'r mn':'' }} {{ $fval<0?'ng':'' }}">{{ $isN&&$val!==''?number_format($fval,2):$val }}</td>
        @endforeach
    </tr>
    @endforeach
    </tbody>
    @if(($c['show_totals_row']??true) && count($colTotals))
    <tfoot><tr>
        @if($c['show_row_numbers']??true)<td class="lbl">—</td>@endif
        @foreach($allCols as $col)<td class="{{ ($numericCols[$col]??false)?'mn':'lbl' }}">{{ ($numericCols[$col]??false)?number_format($colTotals[$col]??0,2):'TOTALS' }}</td>@endforeach
    </tr></tfoot>
    @endif
</table>
@endif

@if($c['show_record_count']??true)
<div class="rec-badge">{{ count($rows) }} records returned</div>
@endif
@endif

</body>
</html>

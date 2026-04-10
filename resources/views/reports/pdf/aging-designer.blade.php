{{--
    AGING/DESIGNER PDF TEMPLATE
    Variables:
      $designerConfig  — full config from template (header_elements, style, canvas_height, paper_size, orientation)
      $datasetsData    — array of datasets [{title, show_title, date_label, date_value, cols, groups}]
      $report, $params, $generatedAt, $generatedBy
--}}
@php
    use App\Models\CompanySetting;
    $co  = CompanySetting::all_settings();
    $dc  = $designerConfig;
    $st  = $dc['style'] ?? [];
    $els = $dc['header_elements'] ?? [];

    $thBg     = $st['th_bg']    ?? '#008000';
    $thText   = $st['th_text']  ?? '#ffffff';
    $smBg     = $st['sm_bg']    ?? '#008000';
    $smText   = $st['sm_text']  ?? '#ffffff';
    $totBg    = $st['tot_bg']   ?? '#008000';
    $totText  = $st['tot_text'] ?? '#ffffff';
    $evenBg   = $st['even_bg']  ?? '#ffffff';
    $oddBg    = $st['odd_bg']   ?? '#f5f5f5';
    $fsPt     = ($st['font_size'] ?? 8).'pt';
    $fsSm     = (($st['font_size'] ?? 8) - 1).'pt';
    $showTot  = $st['show_totals']        ?? true;
    $indent   = $st['indent_customers']   ?? true;
    $showConf = $st['show_confidential']  ?? true;
    $canvasH  = ($dc['canvas_height'] ?? 95);

    // Resolve logo url
    $logoUrl = $co['company_logo_url'] ?? '';
    foreach ($els as $el) {
        if (($el['type'] ?? '') === 'logo' && !empty($el['logoUrl'])
            && !str_starts_with($el['logoUrl'], '[')) {
            $logoUrl = $el['logoUrl'];
        }
    }
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:DejaVu Sans,Arial,sans-serif; font-size:{{ $fsPt }}; color:#000; }
@page { margin:10mm 8mm 16mm; size:{{ $dc['paper_size'] ?? 'A4' }} {{ $dc['orientation'] ?? 'landscape' }}; }

.page-footer {
    position:fixed; bottom:0; left:0; right:0; height:13mm;
    border-top:0.5pt solid #bbb;
    font-size:{{ $fsSm }}; color:#555;
    display:table; width:100%;
}
.pfc { display:table-cell; vertical-align:middle; }
.pfc-l { padding-left:2mm; }
.pfc-c { text-align:center; }
.pfc-r { text-align:right; padding-right:2mm; }

.hdr-wrap { position:relative; height:{{ $canvasH }}pt; overflow:hidden; margin-bottom:2pt; }

/* TABLE STYLES */
.rpt-table { width:100%; border-collapse:collapse; font-size:{{ $fsPt }}; table-layout:fixed; }
.th-cell { background:{{ $thBg }}; color:{{ $thText }}; font-weight:bold; text-align:center;
           padding:3pt 5pt; border:0.5pt solid {{ $thBg }}; }
.sm-row td { background:{{ $smBg }}; color:{{ $smText }}; font-weight:bold;
             padding:2.5pt 5pt; border:0.5pt solid #444; }
.tot-row td { background:{{ $totBg }}; color:{{ $totText }}; font-weight:bold;
              text-align:right; font-family:DejaVu Sans Mono,monospace;
              padding:3pt 5pt; border:0.5pt solid #444; }
.tot-row td.tot-lbl { text-align:right; }
td.cust-name { padding:2.5pt 5pt; border:0.5pt solid #ccc; overflow:hidden; text-overflow:ellipsis; }
td.cust-num  { padding:2.5pt 5pt; border:0.5pt solid #ccc; text-align:right; font-family:DejaVu Sans Mono,monospace; }
td.even { background:{{ $evenBg }}; }
td.odd  { background:{{ $oddBg }}; }

.ds-title-row { font-size:10pt; font-weight:bold; text-align:center;
                text-decoration:underline; text-transform:uppercase;
                padding:7pt 0 3pt; }
.date-box { display:inline-block; border:1pt solid #000; padding:2pt 10pt;
            font-size:8pt; font-weight:bold; margin-left:10pt; }
.conf-line { font-size:{{ $fsSm }}; color:#666; text-align:center;
             margin-top:6pt; padding-top:4pt; border-top:0.5pt solid #ccc; }
</style>
</head>
<body>

{{-- ── FIXED FOOTER ─────────────────────────────────────────────────────── --}}
<div class="page-footer">
    <div class="pfc pfc-l">{{ $co['company_name'] ?? '' }}{{ $showConf ? ' — Confidential' : '' }}</div>
    <div class="pfc pfc-c">{{ $report->name }}</div>
    <div class="pfc pfc-r">{{ $generatedAt->format('d M Y') }} &nbsp;|&nbsp; Page {PAGE_NUM} of {PAGE_COUNT}</div>
</div>

{{-- ── HEADER CANVAS (designer elements, absolutely positioned) ──────────── --}}
<div class="hdr-wrap">
@foreach($els as $el)
@php
    $ex = ($el['x'] ?? 0);
    $ey = ($el['y'] ?? 0);
    $ew = ($el['w'] ?? 100);
    $eh = ($el['h'] ?? 20);
    $fs = ($el['fontSize'] ?? 8);
    $fw = ($el['bold']   ?? false) ? 'bold'   : 'normal';
    $fi = ($el['italic'] ?? false) ? 'italic' : 'normal';
    $fc = $el['color'] ?? '#000000';
    $fa = $el['align'] ?? 'left';
    $et = $el['type']  ?? 'text';
    $txt= $el['text']  ?? '';
    $baseStyle = "position:absolute;left:{$ex}pt;top:{$ey}pt;width:{$ew}pt;height:{$eh}pt;overflow:hidden;";
@endphp

@if($et === 'logo')
    @if(!empty($logoUrl))
    <div style="{{ $baseStyle }}">
        <img src="{{ $logoUrl }}" style="max-height:{{ $eh }}pt;max-width:{{ $ew }}pt;object-fit:contain;">
    </div>
    @endif

@elseif($et === 'banner')
    <div style="{{ $baseStyle }}border:1pt solid #000;background:#fff;padding:2pt 6pt;
                 font-size:{{ $fs }}pt;font-weight:{{ $fw }};font-style:{{ $fi }};
                 color:{{ $fc }};text-align:center;line-height:1.3;">{{ $txt }}</div>

@elseif($et === 'datebox')
    <div style="{{ $baseStyle }}border:1pt solid #000;background:#fff;padding:2pt 8pt;
                 font-size:{{ $fs }}pt;font-weight:bold;color:{{ $fc }};text-align:center;line-height:1.3;">{{ $txt }}</div>

@elseif($et === 'title')
    <div style="{{ $baseStyle }}font-size:{{ $fs }}pt;font-weight:bold;color:{{ $fc }};
                 text-align:center;text-decoration:underline;text-transform:uppercase;line-height:1.3;">{{ $txt }}</div>

@elseif($et === 'subtitle')
    <div style="{{ $baseStyle }}font-size:{{ $fs }}pt;font-weight:{{ $fw }};font-style:{{ $fi }};
                 color:{{ $fc }};text-align:center;line-height:1.3;">{{ $txt }}</div>

@elseif($et === 'pagebox')
    <div style="{{ $baseStyle }}font-size:{{ $fs }}pt;font-weight:{{ $fw }};font-style:{{ $fi }};
                 color:{{ $fc }};text-align:right;line-height:1.3;">Page {PAGE_NUM} of {PAGE_COUNT}</div>

@else{{-- text / company --}}
    <div style="{{ $baseStyle }}font-size:{{ $fs }}pt;font-weight:{{ $fw }};font-style:{{ $fi }};
                 color:{{ $fc }};text-align:{{ $fa }};white-space:pre-line;line-height:1.4;">{{ $txt }}</div>
@endif

@endforeach
</div>{{-- end hdr-wrap --}}

{{-- ── DATASETS ─────────────────────────────────────────────────────────── --}}
<div style="padding:0 0 6pt;">
@foreach($datasetsData as $ds)

@php
    $cols    = $ds['cols']   ?? [];
    $groups  = $ds['groups'] ?? [];
    // Split cols: first = name col, second = total col, rest = bucket cols
    $nameCol   = $cols[0]  ?? ['name'=>'Name',  'width'=>200,'align'=>'left'];
    $totalCol  = $cols[1]  ?? ['name'=>'Total', 'width'=>75, 'align'=>'right'];
    $bucketCols= array_slice($cols, 2);

    // Compute grand totals across all groups
    $grandTotal = 0;
    $grandBuckets = array_fill(0, count($bucketCols), 0);
    foreach ($groups as $grp) {
        $grandTotal += (float) str_replace(',', '', $grp['total'] ?? '0');
        foreach (($grp['buckets'] ?? []) as $bi => $bv) {
            $grandBuckets[$bi] += (float) str_replace(',', '', $bv ?? '0');
        }
    }
@endphp

{{-- Dataset title row --}}
@if(!empty($ds['show_title']) && !empty($ds['title']))
<div class="ds-title-row">
    {{ $ds['title'] }}
    @if(!empty($ds['date_label']) || !empty($ds['date_value']))
    <span class="date-box">{{ $ds['date_label'] }}&nbsp;&nbsp;{{ $ds['date_value'] }}</span>
    @endif
</div>
@endif

<table class="rpt-table">
<thead>
@if(count($bucketCols) > 0)
<tr>
    <th class="th-cell" rowspan="2" style="width:{{ $nameCol['width'] }}pt;text-align:left;">
        {{ $nameCol['name'] }}
    </th>
    <th class="th-cell" rowspan="2" style="width:{{ $totalCol['width'] }}pt;">
        {{ $totalCol['name'] }}
    </th>
    <th class="th-cell" colspan="{{ count($bucketCols) }}">Dues Days</th>
</tr>
<tr>
    @foreach($bucketCols as $bc)
    <th class="th-cell" style="width:{{ $bc['width'] }}pt;">{{ $bc['name'] }}</th>
    @endforeach
</tr>
@else
<tr>
    <th class="th-cell" style="width:{{ $nameCol['width'] }}pt;text-align:left;">{{ $nameCol['name'] }}</th>
    <th class="th-cell" style="width:{{ $totalCol['width'] }}pt;">{{ $totalCol['name'] }}</th>
</tr>
@endif
</thead>

<tbody>
@foreach($groups as $grp)
{{-- Salesman / group header row --}}
@if(!empty($grp['salesman']))
<tr class="sm-row">
    <td colspan="{{ count($cols) }}">{{ $grp['salesman'] }}</td>
</tr>
@endif

{{-- Customer rows --}}
@foreach($grp['customers'] ?? [] as $ri => $cust)
@php $isOdd = $ri % 2 !== 0; $rc = $isOdd ? 'odd' : 'even'; @endphp
<tr>
    <td class="cust-name {{ $rc }}" style="{{ $indent ? 'padding-left:14pt;' : '' }}">{{ $cust['name'] ?? '' }}</td>
    <td class="cust-num {{ $rc }}">{{ $cust['total'] ?? '' }}</td>
    @foreach($cust['buckets'] ?? [] as $bv)
    <td class="cust-num {{ $rc }}">{{ $bv }}</td>
    @endforeach
</tr>
@endforeach

@endforeach{{-- end groups --}}
</tbody>

{{-- Totals row --}}
@if($showTot)
<tfoot>
<tr class="tot-row">
    <td class="tot-lbl">Total :</td>
    <td>{{ $grandTotal > 0 ? number_format($grandTotal, 0) : '' }}</td>
    @foreach($grandBuckets as $bTotal)
    <td>{{ $bTotal > 0 ? number_format($bTotal, 0) : '' }}</td>
    @endforeach
</tr>
</tfoot>
@endif

</table>
@endforeach{{-- end datasets --}}

@if($showConf)
<div class="conf-line">
    Company Confidential - Internal Distribution ONLY &nbsp;&nbsp;&nbsp; Computer Generated Document
</div>
@endif

</div>

</body>
</html>

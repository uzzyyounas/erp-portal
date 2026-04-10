{{-- DESIGNER PDF — pixel-matches the canvas preview --}}
@php
    use App\Models\CompanySetting;
    $co  = $companyData;
    $pg  = $dc['page']   ?? [];
    $hdr = $dc['header'] ?? [];
    $st  = $dc['style']  ?? [];
    $ftr = $dc['footer'] ?? [];

    $paperSize = $pg['paper_size']   ?? 'A4';
    $orient    = $pg['orientation']  ?? 'landscape';
    $fsPt      = ($pg['font_size']   ?? 8).'pt';
    $fsSm      = (($pg['font_size']  ?? 8)-1).'pt';
    $hdrH      = ($hdr['height']     ?? 90).'pt';
    $hdrEls    = $hdr['elements']    ?? [];

    // Logo
    $logoUrl = $co['company_logo_url'] ?? '';
    foreach ($hdrEls as $el) {
        if (($el['type']??'')  === 'logo' && !empty($el['logoUrl']) && !str_starts_with($el['logoUrl'],'[')) {
            $logoUrl = $el['logoUrl']; break;
        }
    }

    // Style globals
    $thBg    = $st['th_bg']    ?? '#008000';
    $thText  = $st['th_text']  ?? '#ffffff';
    $smBg    = $st['sm_bg']    ?? '#006600';
    $smText  = $st['sm_text']  ?? '#ffffff';
    $totBg   = $st['tot_bg']   ?? '#004400';
    $totText = $st['tot_text'] ?? '#ffffff';
    $evenBg  = $st['even_bg']  ?? '#ffffff';
    $oddBg   = $st['odd_bg']   ?? '#f0fff5';
    $bdColor = $st['border_color'] ?? '#cccccc';
    $BD      = ($st['show_borders']??true) ? "border:0.5pt solid {$bdColor}" : "border-bottom:0.5pt solid {$bdColor}";
    $showTot = $st['show_totals']       ?? true;
    $indent  = $st['indent_customers']  ?? true;
    $showConf= $st['show_confidential'] ?? true;
    $showRn  = $st['show_row_numbers']  ?? false;
    $zebra   = $st['zebra_rows']        ?? true;

    // Margins
    $mtop    = ($pg['margin_top']    ?? 10).'mm';
    $mright  = ($pg['margin_right']  ?? 8).'mm';
    $mbottom = ($pg['margin_bottom'] ?? 16).'mm';
    $mleft   = ($pg['margin_left']   ?? 8).'mm';

    // Footer zone resolver
    $fzText = function($zone, $custom='') use ($co, $report, $generatedAt) {
        return match($zone) {
            'company'    => $co['company_name'] ?? '',
            'report_name'=> $report->name,
            'datetime'   => $generatedAt->format('d M Y H:i'),
            'page'       => 'Page {PAGE_NUM} of {PAGE_COUNT}',
            'custom'     => $custom,
            default      => '',
        };
    };

    // Token resolver for header element text
    $resolveText = function($text) use ($report, $generatedAt, $params, $co) {
        return strtr($text ?? '', [
            '{report_name}'    => $report->name,
            '{print_date}'     => $generatedAt->format('d M Y'),
            '{print_datetime}' => $generatedAt->format('d M Y H:i'),
            '{company_name}'   => $co['company_name'] ?? '',
            '{company_ntn}'    => $co['company_ntn']  ?? '',
            '{page}'           => 'Page {PAGE_NUM}',
            '{pages}'          => '{PAGE_COUNT}',
        ] + array_map(fn($v) => is_string($v)?$v:'', $params));
    };

    $layout = $dc['layout'] ?? 'tabular';
@endphp
    <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:DejaVu Sans,Arial,sans-serif;font-size:{{ $fsPt }};color:#000}
        @page{margin:{{ $mtop }} {{ $mright }} {{ $mbottom }} {{ $mleft }};size:{{ $paperSize }} {{ $orient }}}
        .pf{position:fixed;bottom:0;left:0;right:0;height:13mm;font-size:{{ $fsSm }};color:#666;display:table;width:100%;
        {{ ($ftr['show_divider']??true)?'border-top:0.5pt solid #ccc;':'' }} }
        .pfc{display:table-cell;vertical-align:middle}
        .hdr-wrap{position:relative;height:{{ $hdrH }};overflow:hidden}
        .dt{width:100%;border-collapse:collapse;font-size:{{ $fsPt }};table-layout:fixed}
        .dt th{font-weight:bold;padding:3pt 5pt}
        .dt td{padding:2.5pt 5pt;vertical-align:middle}
        td.n{text-align:right;font-family:DejaVu Sans Mono,monospace}
        td.rn{text-align:right;color:#aaa;font-size:{{ $fsSm }};width:18pt}
        .sm-row td{font-weight:bold;padding:2.5pt 5pt}
        .tot-row td{font-weight:bold;padding:3pt 5pt;text-align:right;font-family:DejaVu Sans Mono,monospace}
        .tot-row td.lbl{text-align:right}
        .ds-title{font-size:10pt;font-weight:bold;text-align:center;text-decoration:underline;text-transform:uppercase;padding:6pt 0 2pt}
        .date-box{display:inline-block;border:0.5pt solid #000;padding:2pt 8pt;font-size:8pt;margin-left:8pt}
        .conf-line{font-size:{{ $fsSm }};color:#666;text-align:center;margin-top:5pt;border-top:0.5pt solid #ccc;padding-top:3pt}
    </style>
</head>
<body>

{{-- FOOTER (fixed) --}}
<div class="pf">
    <div class="pfc" style="padding-left:3mm">{{ $fzText($ftr['left']??'company', $ftr['custom_left']??'') }}</div>
    <div class="pfc" style="text-align:center">{{ $fzText($ftr['center']??'report_name', $ftr['custom_center']??'') }}</div>
    <div class="pfc" style="text-align:right;padding-right:3mm">{{ $fzText($ftr['right']??'page', $ftr['custom_right']??'') }}</div>
</div>

{{-- HEADER ELEMENTS (absolutely positioned) --}}
<div class="hdr-wrap">
    @foreach($hdrEls as $el)
        @php
            $ex  = ($el['x']??0); $ey=($el['y']??0);
            $ew  = ($el['w']??100); $eh=($el['h']??20);
            $efs = ($el['fontSize']??8);
            $efw = ($el['bold']??false)?'bold':'normal';
            $efi = ($el['italic']??false)?'italic':'normal';
            $efc = $el['color']??'#000000';
            $efa = $el['align']??'left';
            $ety = $el['type']??'text';
            $etx = $resolveText($el['text']??'');
            $bg  = $el['bg']??'';
            $bd  = $el['borderColor']??'';
            $base= "position:absolute;left:{$ex}pt;top:{$ey}pt;width:{$ew}pt;min-height:{$eh}pt;overflow:hidden;";
            $contentStyle="font-size:{$efs}pt;font-weight:{$efw};font-style:{$efi};color:{$efc};text-align:{$efa};width:100%;white-space:pre-line;line-height:1.3;".
                ($bg?"background:{$bg};":'').
                ($bd?"border:1pt solid {$bd};padding:2pt 5pt;":'');
        @endphp
        @if($ety==='logo' && !empty($logoUrl))
            <div style="{{ $base }}"><img src="{{ $logoUrl }}" style="max-height:{{ $eh }}pt;max-width:{{ $ew }}pt;object-fit:contain"></div>
        @elseif($ety==='divider')
            <div style="{{ $base }}border-top:{{ ($el['thickness']??1) }}pt solid {{ $efc }};"></div>
        @elseif($ety==='title')
            <div style="{{ $base }}{{ $contentStyle }}text-decoration:underline;text-transform:uppercase;">{{ $etx }}</div>
        @elseif($ety==='banner')
            <div style="{{ $base }}{{ $contentStyle }}text-align:center;">{{ $etx }}</div>
        @elseif($ety==='datebox')
            <div style="{{ $base }}{{ $contentStyle }}text-align:center;">{{ $etx }}</div>
        @elseif($ety==='pagebox')
            <div style="{{ $base }}{{ $contentStyle }}text-align:right;">Page {PAGE_NUM} of {PAGE_COUNT}</div>
        @else
            <div style="{{ $base }}{{ $contentStyle }}">{{ $etx }}</div>
        @endif
    @endforeach
</div>

@if(($dc['params']['show'] ?? true) && !empty($dc['params']['fields'] ?? []))
    @php $paramsFields = $dc['params']['fields'] ?? []; @endphp
    <div style="margin:6pt 0 12pt 0;border-bottom:1px solid #e0e7ef;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:flex-start;padding:8pt 0 6pt 0;background:linear-gradient(to bottom, #f9fbff, #f2f6fc);border-radius:4pt 4pt 0 0;">
            @foreach($paramsFields as $field)
                @php
                    $label = $field['label'] ?? 'Parameter';
                    $paramKey = ltrim($field['value'] ?? '', ':');
                    $value = $params[$paramKey] ?? ($field['default'] ?? '');
                    $width = $field['width'] ?? 120;
                    $align = $field['align'] ?? 'left';

                    // Style variations based on field type
                    $isDate = str_contains(strtolower($label), 'date');
                    $isAmount = str_contains(strtolower($label), 'amount') || str_contains(strtolower($label), 'total');

                    // Format value based on type
                    if ($isDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                        $value = \Carbon\Carbon::parse($value)->format('d M Y');
                    } elseif ($isAmount && is_numeric($value)) {
                        $value = number_format($value, 0);
                    }
                @endphp
                <div style="display:flex;flex-direction:column;min-width:{{ $width }}pt;border-right:1px solid #d8e0ea;padding-right:12px;{{ $loop->last ? 'border-right:none;' : '' }}">
                    <span style="font-size:6.5pt;font-weight:600;text-transform:uppercase;letter-spacing:0.3pt;color:#5a6b7c;margin-bottom:2pt">{{ $label }}</span>
                    <div style="display:flex;align-items:center;gap:4px;">
                        <span style="font-size:8.5pt;font-weight:500;color:#1a3a5c;background:#ffffff;padding:3pt 8pt;border:1px solid #cbd5e1;border-radius:12pt;box-shadow:0 1px 2px rgba(0,0,0,0.03);text-align:{{ $align }};flex:1;{{ $isDate ? 'font-family:monospace;' : '' }}{{ $isAmount ? 'font-weight:600;color:#0f3b5e;' : '' }}">
                            {{ $value }}
                        </span>
                        @if($paramKey && isset($params[$paramKey]))
                            <span style="font-size:5.5pt;color:#8899aa;font-style:italic;opacity:0.7">:{{ $paramKey }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- DATA AREA --}}
<div style="padding:0 0 6pt">
    @forelse($datasetsResult as $dsIdx => $ds)
        @php
            $dsCfg   = $dc['datasets'][$dsIdx] ?? [];
            $cols    = $ds['cols'] ?? [];     // [{sql_key,label,type,width,align,thBg,thText,tdBg,tdText,altBg,fontWeight,showTotal,visible,zone}]
            $groups  = $ds['groups'] ?? [];
            $layout  = $dc['layout'] ?? 'tabular';

            // Split by zone
            $allCols    = array_filter($cols, fn($c)=>($c['visible']??true)!==false);
            $mCols      = array_values(array_filter($allCols, fn($c)=>($c['zone']??'data')==='master'));
            $dCols      = array_values(array_filter($allCols, fn($c)=>($c['zone']??'data')==='detail'));
            $gCol       = array_values(array_filter($allCols, fn($c)=>($c['zone']??'data')==='group'))[0]??null;
            $dataCols   = array_values(array_filter($allCols, fn($c)=>!in_array($c['zone']??'data',['master','group'])));

            // For grouped: detect name, total, buckets among data cols
            $nameCol    = null; $totalCol = null; $bucketCols = [];
            if ($layout==='grouped') {
                foreach ($dataCols as $c) { if ($c['type']==='text'&&!$nameCol) { $nameCol=$c; continue; } if ($c['type']==='number'&&!$totalCol) { $totalCol=$c; continue; } if ($c['type']==='number') $bucketCols[]=$c; }
            }

            // Grand totals
            $grandTotal = 0; $grandBuckets=array_fill(0,count($bucketCols),0);
            foreach ($groups as $grp) {
                $grandTotal += (float)str_replace(',','',$grp['total']??'0');
                foreach(($grp['buckets']??[]) as $bi=>$bv) $grandBuckets[$bi]+=(float)str_replace(',','',$bv??'0');
            }
        @endphp

        @if(!empty($dsCfg['show_title'])&&!empty($dsCfg['title']))
            <div class="ds-title">{{ $dsCfg['title'] }}
                @if(!empty($dsCfg['date_label'])||!empty($ds['date_value']))
                    <span class="date-box">{{ $dsCfg['date_label']??'' }}&nbsp;&nbsp;{{ $ds['date_value']??'' }}</span>
                @endif
            </div>
        @endif

        @if(!count($groups))
            <div style="text-align:center;padding:12pt;color:#888;font-style:italic;font-size:{{ $fsSm }};border:0.5pt dashed #ccc">No records found.</div>
        @elseif($layout==='master-detail')
            {{-- MASTER-DETAIL --}}
            @foreach($groups as $grp)
                @foreach($grp['customers']??[] as $custIdx => $cust)
                    @php $masterRow = $cust; @endphp
                    <div style="border:1pt solid #e2e8f0;border-top:3pt solid {{ $thBg }};padding:7pt 10pt;margin-bottom:4pt;background:#fafafa;page-break-inside:avoid">
                        <table style="width:100%;border:none;font-size:{{ $fsPt }}">
                            <tr>
                                @foreach($mCols as $mc)
                                    @php $mVal = $masterRow['master_vals'][$mc['sql_key']] ?? ($masterRow['name']??'') @endphp
                                    <td style="padding:2pt 8pt 2pt 0;vertical-align:top;width:{{ ($mc['width']??120) }}pt">
                                        <span style="font-size:5.5pt;text-transform:uppercase;color:#8096ac;font-weight:bold;display:block">{{ $mc['label'] }}</span>
                                        <span style="font-size:8pt;font-weight:bold;color:{{ $thBg }}">{{ $mVal }}</span>
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    </div>
                    <table class="dt" style="margin-bottom:10pt">
                        <thead><tr>
                            @if($showRn)<th style="background:{{ $thBg }};color:{{ $thText }};{{ $BD }};width:18pt;text-align:center">#</th>@endif
                            @foreach($dCols as $dc2)
                                <th style="background:{{ $dc2['thBg']??$thBg }};color:{{ $dc2['thText']??$thText }};{{ $BD }};width:{{ ($dc2['width']??65) }}pt;text-align:{{ $dc2['align']??'left' }};font-size:{{ ($dc2['fontSize']??7) }}pt">{{ $dc2['label'] }}</th>
                            @endforeach
                        </tr></thead>
                        <tbody>
                        {{-- For master-detail we show detail lines from this customer's buckets --}}
                        @foreach($cust['buckets']??[] as $bi=>$bv)
                            @php $bg = $zebra?($bi%2===0?$evenBg:$oddBg):$evenBg @endphp
                            <tr>
                                @if($showRn)<td class="rn" style="background:{{ $bg }};{{ $BD }}">{{ $bi+1 }}</td>@endif
                                @foreach($dCols as $dc2)
                                    @php $isNum=($dc2['type']??'text')==='number'; $tdBg=$dc2['tdBg']??$bg; $tdText=$dc2['tdText']??''; @endphp
                                    <td class="{{ $isNum?'n':'' }}" style="background:{{ $tdBg }};{{ $tdText?'color:'.$tdText.';':'' }}{{ $BD }};font-weight:{{ $dc2['fontWeight']??'normal' }}">{{ $bv }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endforeach
            @endforeach

        @elseif($layout==='grouped')
            {{-- GROUPED --}}
            <table class="dt">
                <thead>
                @if(count($bucketCols))
                    <tr>
                        @if($showRn)<th rowspan="2" style="background:{{ $thBg }};color:{{ $thText }};{{ $BD }};width:18pt"></th>@endif
                        @if($nameCol)<th rowspan="2" style="background:{{ $nameCol['thBg']??$thBg }};color:{{ $nameCol['thText']??$thText }};{{ $BD }};width:{{ ($nameCol['width']??180) }}pt;text-align:left">{{ $nameCol['label'] }}</th>@endif
                        @if($totalCol)<th rowspan="2" style="background:{{ $totalCol['thBg']??$thBg }};color:{{ $totalCol['thText']??$thText }};{{ $BD }};width:{{ ($totalCol['width']??72) }}pt">{{ $totalCol['label'] }}</th>@endif
                        <th colspan="{{ count($bucketCols) }}" style="background:{{ $thBg }};color:{{ $thText }};{{ $BD }};text-align:center">Dues Days</th>
                    </tr>
                    <tr>
                        @foreach($bucketCols as $bc)<th style="background:{{ $bc['thBg']??$thBg }};color:{{ $bc['thText']??$thText }};{{ $BD }};width:{{ ($bc['width']??62) }}pt">{{ $bc['label'] }}</th>@endforeach
                    </tr>
                @else
                    <tr>
                        @if($showRn)<th style="background:{{ $thBg }};color:{{ $thText }};{{ $BD }};width:18pt">#</th>@endif
                        @foreach($dataCols as $dc2)<th style="background:{{ $dc2['thBg']??$thBg }};color:{{ $dc2['thText']??$thText }};{{ $BD }};width:{{ ($dc2['width']??65) }}pt;text-align:{{ $dc2['align']??'center' }}">{{ $dc2['label'] }}</th>@endforeach
                    </tr>
                @endif
                </thead>
                <tbody>
                @foreach($groups as $grp)
                    @if($grp['salesman'])
                        <tr class="sm-row"><td colspan="{{ count($dataCols)+($showRn?1:0) }}" style="background:{{ $smBg }};color:{{ $smText }};{{ $BD }}">{{ $grp['salesman'] }}</td></tr>
                    @endif
                    @foreach($grp['customers']??[] as $ri=>$cust)
                        @php $bg=$zebra?($ri%2===0?$evenBg:$oddBg):$evenBg @endphp
                        <tr>
                            @if($showRn)<td class="rn" style="background:{{ $bg }};{{ $BD }}">{{ $ri+1 }}</td>@endif
                            @if($nameCol)
                                <td style="background:{{ $nameCol['tdBg']??$bg }};{{ $nameCol['tdText']??''?'color:'.$nameCol['tdText'].';':'' }}{{ $BD }};font-weight:{{ $nameCol['fontWeight']??'normal' }};{{ $indent?'padding-left:14pt;':'' }}">{{ $cust['name']??'' }}</td>
                            @endif
                            @if($totalCol)
                                <td class="n" style="background:{{ $totalCol['tdBg']??$bg }};{{ $totalCol['tdText']??''?'color:'.$totalCol['tdText'].';':'' }}{{ $BD }}">{{ $cust['total']??'' }}</td>
                            @endif
                            @foreach($bucketCols as $bi=>$bc)
                                <td class="n" style="background:{{ $bc['tdBg']??$bg }};{{ $bc['tdText']??''?'color:'.$bc['tdText'].';':'' }}{{ $BD }}">{{ $cust['buckets'][$bi]??'' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
                @if($showTot)
                    <tfoot><tr class="tot-row">
                        @if($showRn)<td style="background:{{ $totBg }};{{ $BD }}"></td>@endif
                        @if($nameCol)<td class="lbl" style="background:{{ $totBg }};color:{{ $totText }};{{ $BD }}">Total :</td>@endif
                        @if($totalCol)<td style="background:{{ $totBg }};color:{{ $totText }};{{ $BD }}">{{ $grandTotal>0?number_format($grandTotal,0):'' }}</td>@endif
                        @foreach($grandBuckets as $bT)<td style="background:{{ $totBg }};color:{{ $totText }};{{ $BD }}">{{ $bT>0?number_format($bT,0):'' }}</td>@endforeach
                    </tr></tfoot>
                @endif
            </table>

        @else
            {{-- TABULAR / STATEMENT --}}
            @php
                $visCols = array_values(array_filter($dataCols?:array_values($allCols), fn($c)=>($c['visible']??true)!==false));
                $colTotals = array_fill(0,count($visCols),0);
            @endphp
            <table class="dt">
                <thead><tr>
                    @if($showRn)<th style="background:{{ $thBg }};color:{{ $thText }};{{ $BD }};width:18pt;text-align:center">#</th>@endif
                    @foreach($visCols as $col)
                        <th style="background:{{ $col['thBg']??$thBg }};color:{{ $col['thText']??$thText }};{{ $BD }};width:{{ ($col['width']??65) }}pt;text-align:{{ $col['align']??'center' }};font-size:{{ ($col['fontSize']??7) }}pt">{{ $col['label'] }}</th>
                    @endforeach
                </tr></thead>
                <tbody>
                @php $rowNum=0; @endphp
                @foreach($groups as $grp)
                    @if($grp['salesman'])
                        <tr><td colspan="{{ count($visCols)+($showRn?1:0) }}" style="background:{{ $smBg }};color:{{ $smText }};font-weight:bold;padding:2.5pt 5pt;{{ $BD }}">{{ $grp['salesman'] }}</td></tr>
                    @endif
                    @foreach($grp['customers']??[] as $ri=>$cust)
                        @php $rowNum++; $bg=$zebra?($ri%2===0?$evenBg:$oddBg):$evenBg @endphp
                        <tr>
                            @if($showRn)<td class="rn" style="background:{{ $bg }};{{ $BD }}">{{ $rowNum }}</td>@endif
                            @foreach($visCols as $ci=>$col)
                                @php
                                    $isNum=($col['type']??'text')==='number';
                                    $raw = $visCols[$ci]['sql_key']??'';
                                    // Get value from customer data by position in columns
                                    $colIdx = array_search($raw, array_column($ds['cols'],'sql_key'));
                                    if ($colIdx===false) $colIdx=$ci;
                                    if ($colIdx===0) $val=$cust['name']??'';
                                    elseif($colIdx===1) $val=$cust['total']??'';
                                    else $val=$cust['buckets'][$colIdx-2]??'';
                                    if ($isNum&&$val!=='') { $nv=(float)str_replace(',','',$val); $colTotals[$ci]+=$nv; }
                                    $tdBg=$col['tdBg']??$bg; $tdText=$col['tdText']??'';
                                @endphp
                                <td class="{{ $isNum?'n':'' }}" style="background:{{ $tdBg }};{{ $tdText?'color:'.$tdText.';':'' }}{{ $BD }};font-weight:{{ $col['fontWeight']??'normal' }}">{{ $val }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
                @if($showTot && array_sum($colTotals)>0)
                    <tfoot><tr class="tot-row">
                        @if($showRn)<td style="background:{{ $totBg }};{{ $BD }}"></td>@endif
                        @foreach($visCols as $ci=>$col)
                            @php $isNum=($col['type']??'text')==='number'; @endphp
                            <td class="{{ $isNum?'':'lbl' }}" style="background:{{ $totBg }};color:{{ $totText }};{{ $BD }}">{{ $ci===0?'Total :':($isNum&&$col['showTotal']!==false&&$colTotals[$ci]>0?number_format($colTotals[$ci],0):'') }}</td>
                        @endforeach
                    </tr></tfoot>
                @endif
            </table>
        @endif

    @empty
        <div style="text-align:center;padding:14pt;color:#888;font-style:italic">No data returned.</div>
    @endforelse

    @if($showConf)
        <div class="conf-line">Company Confidential - Internal Distribution ONLY &nbsp;·&nbsp; Computer Generated Document</div>
    @endif
</div>

</body>
</html>

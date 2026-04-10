<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportLog;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportService
{
    // ── SQL EXECUTION ────────────────────────────────────────────────────────

    public function executeSql(string $sql, array $params): array
    {
        $prefix = $params['company'] ?? '0';
        $sql    = str_replace('{company_prefix}', $prefix, $sql);
        $binds  = [];

        foreach ($params as $key => $value) {
            if ($key === 'company' || !str_contains($sql, ":$key")) continue;
            if (is_array($value) && count($value)) {
                $ph = [];
                foreach ($value as $i => $v) { $bk="{$key}_{$i}"; $ph[]=":{$bk}"; $binds[$bk]=$v; }
                $sql = preg_replace('/\s*:'.preg_quote($key,'/').'(?!\w)/', ' '.implode(', ',$ph), $sql);
            } elseif (is_array($value)) {
                $sql = preg_replace('/\s*:'.preg_quote($key,'/').'(?!\w)/', ' NULL', $sql);
            } else {
                $binds[$key] = $value;
            }
        }
        return array_map(fn($r) => (array)$r, DB::select($sql, $binds));
    }

    public function executeQuery(Report $report, array $params): array
    {
        return $this->executeSql($report->sql_query ?? '', $params);
    }

    // ── COLUMN CONFIG (legacy) ───────────────────────────────────────────────

    public function applyColumnConfig(Report $report, array $rows): array
    {
        $config = $report->column_config;
        if (empty($rows) || empty($config)) return $rows;
        return $this->resolveLayout($report) === 'master-detail'
            ? $this->applyMdConfig($config, $rows)
            : $this->applyFlatConfig($config, $rows);
    }

    private function applyFlatConfig(array $c, array $rows): array
    {
        $visible=$c['visible']??null; $labels=$c['labels']??[]; $hidden=$c['hidden']??[];
        return array_map(function($row) use($visible,$labels,$hidden){
            foreach($hidden as $h) unset($row[$h]);
            if(!empty($visible)){$r=[];foreach($visible as $col){if(array_key_exists($col,$row))$r[$labels[$col]??$col]=$row[$col];}
                foreach($row as $k=>$v)if(!in_array($k,$visible)&&!in_array($k,$hidden))$r[$labels[$k]??$k]=$v;return $r;}
            if(!empty($labels)){$r=[];foreach($row as $k=>$v)$r[$labels[$k]??$k]=$v;return $r;}
            return $row;
        }, $rows);
    }

    private function applyMdConfig(array $c, array $rows): array
    {
        $mc=$c['master']??[]; $dc=$c['detail']??[]; $lb=$c['labels']??[];
        if(empty($mc)&&empty($dc)) return $rows;
        return array_map(function($row) use($mc,$dc,$lb){
            $s=[];foreach($row as $k=>$v)$s[preg_replace('/^(header_|detail_)/i','',$k)]=$v;
            $n=[];foreach($mc as $col)if(array_key_exists($col,$s))$n['header_'.($lb[$col]??$col)]=$s[$col];
            foreach($dc as $col)if(array_key_exists($col,$s))$n['detail_'.($lb[$col]??$col)]=$s[$col];
            foreach($s as $col=>$val)if(!in_array($col,$mc)&&!in_array($col,$dc))$n['detail_'.$col]=$val;
            return $n;
        }, $rows);
    }

    // ── LAYOUT RESOLUTION ────────────────────────────────────────────────────

    public function resolveLayout(Report $report): string
    {
        if ($report->hasDesignerConfig()) return 'designer';
        if ($report->template_id && $report->template) return $report->template->layout;
        if ($report->blade_view) {
            if (str_contains($report->blade_view,'master-detail')) return 'master-detail';
            if (str_contains($report->blade_view,'grouped'))       return 'grouped';
            if (str_contains($report->blade_view,'statement'))     return 'statement';
            if (str_contains($report->blade_view,'aged'))          return 'aged';
        }
        return 'tabular';
    }

    public function resolveTemplateConfig(Report $report): ?array
    {
        if ($report->template_id && $report->template)
            return $report->template->getEffectiveConfig();
        return null;
    }

    public function getColumns(array $rows): array
    {
        return empty($rows) ? [] : array_keys($rows[0]);
    }

    // ── DESIGNER: EXECUTE DATASETS ───────────────────────────────────────────

    /**
     * Execute all datasets. Accepts pre-executed $mainRows so we never
     * re-run the main query unnecessarily.
     */
    public function executeDesignerDatasets(Report $report, array $params, array $mainRows = []): array
    {
        $dc      = $report->designer_config ?? [];
        $dsCfgs  = $dc['datasets'] ?? [];
        $result  = [];

        foreach ($dsCfgs as $dsCfg) {
            // FIX: use !empty() so empty-string falls back to main SQL
            $sql      = !empty($dsCfg['sql_query']) ? $dsCfg['sql_query'] : ($report->sql_query ?? '');
            $cols     = $dsCfg['columns'] ?? [];
            $groupKey = !empty($dsCfg['group_column']) ? $dsCfg['group_column'] : null;

            // If this dataset has its own SQL use it; otherwise reuse already-executed mainRows
            if (!empty($dsCfg['sql_query'])) {
                try {
                    $rows = $this->executeSql($dsCfg['sql_query'], $params);
                } catch (\Throwable $e) {
                    $result[] = $this->emptyDataset($dsCfg, $cols);
                    continue;
                }
            } elseif (!empty($mainRows)) {
                $rows = $mainRows;
            } elseif (!empty($report->sql_query)) {
                try {
                    $rows = $this->executeSql($report->sql_query, $params);
                } catch (\Throwable $e) {
                    $result[] = $this->emptyDataset($dsCfg, $cols);
                    continue;
                }
            } else {
                $result[] = $this->emptyDataset($dsCfg, $cols);
                continue;
            }

            if (empty($rows)) {
                $result[] = $this->emptyDataset($dsCfg, $cols);
                continue;
            }

            $sqlCols = array_keys($rows[0]);
            $colDefs = $this->mergeColDefs($sqlCols, $cols);
            $groups  = $this->groupRows($rows, $sqlCols, $groupKey);

            $result[] = [
                'cols'       => $colDefs,
                'groups'     => $groups,
                'date_value' => $this->resolveToken($dsCfg['date_value'] ?? '', $params),
                'row_count'  => count($rows),
            ];
        }

        // Fallback: no datasets in config → use main rows
        if (empty($result)) {
            $rows = !empty($mainRows) ? $mainRows : [];
            if (empty($rows) && !empty($report->sql_query)) {
                try { $rows = $this->executeSql($report->sql_query, $params); } catch (\Throwable $e) {}
                $rows = $this->applyColumnConfig($report, $rows);
            }
            if (!empty($rows)) {
                $sqlCols = array_keys($rows[0]);
                $groupKey = !empty($dc['datasets'][0]['group_column']) ? $dc['datasets'][0]['group_column'] : null;
                $colDefs = $this->mergeColDefs($sqlCols, []);
                $groups  = $this->groupRows($rows, $sqlCols, $groupKey);
                $result[] = [
                    'cols'       => $colDefs,
                    'groups'     => $groups,
                    'date_value' => '',
                    'row_count'  => count($rows),
                ];
            }
        }

        return $result;
    }

    private function emptyDataset(array $cfg, array $cols): array
    {
        return ['cols'=>$cols,'groups'=>[],'date_value'=>$cfg['date_value']??'','row_count'=>0];
    }

    /**
     * Build column definitions from SQL column names, overlaying any
     * designer-specified labels/widths/types.
     */
    private function mergeColDefs(array $sqlCols, array $cfgCols): array
    {
        $cfgByKey = [];
        foreach ($cfgCols as $i => $c) {
            $k = $c['sql_key'] ?? $c['name'] ?? $sqlCols[$i] ?? null;
            if ($k) $cfgByKey[$k] = $c;
        }

        $defs = [];
        foreach ($sqlCols as $col) {
            $cfg   = $cfgByKey[$col] ?? [];
            $isNum = !empty($cfg['type']) ? in_array($cfg['type'],['number','currency'])
                : (bool) preg_match('/amount|dues|total|days|balance|debit|credit|qty|price|cost|tax|\d{1,2}\s*[-–]\s*\d{1,2}/i', $col);
            $defs[] = [
                'sql_key' => $col,
                'label'   => $cfg['label'] ?? ucwords(str_replace(['_','-'], ' ', $col)),
                'type'    => $isNum ? 'number' : 'text',
                'width'   => (int)($cfg['width'] ?? ($isNum ? 65 : 180)),
                'align'   => $cfg['align'] ?? ($isNum ? 'right' : 'left'),
            ];
        }
        return $defs;
    }

    /**
     * Group rows by groupCol.
     *
     * Column convention (robust detection):
     *   - groupCol  = salesman/group name (skip from customer display)
     *   - text cols = customer name candidates (first text col ≠ groupCol)
     *   - num cols  = value cols (first = total, rest = aging buckets)
     *
     * Works for:
     *   SQL: salesman_name, customer_name, total_dues, 20-30, 31-45 ...
     *   SQL: salesman_name, total_dues, 20-30, 31-45 ... (no customer sub-level)
     *   SQL: customer_name, total_dues, 20-30 ... (no grouping)
     */
    private function groupRows(array $rows, array $sqlCols, ?string $groupCol): array
    {
        if (empty($rows)) return [];

        $fmt = function($v) {
            $n = is_numeric(str_replace([',',' '], '', (string)$v)) ? (float)str_replace(',','',(string)$v) : null;
            return $n !== null ? number_format($n, 0) : ($v ?? '');
        };
        $raw = fn($v) => (float) str_replace([',',' '], '', (string)($v ?? 0));

        // Detect which columns are numeric by sampling first 3 rows
        $numCols  = [];
        $textCols = [];
        $sample   = array_slice($rows, 0, 3);
        foreach ($sqlCols as $col) {
            if ($col === $groupCol) continue;
            $vals = array_filter(array_map(fn($r) => str_replace([',',' '], '', (string)($r[$col] ?? '')), $sample));
            $numericCount = count(array_filter($vals, fn($v) => is_numeric($v) && $v !== ''));
            if ($numericCount > 0 && $numericCount >= count($vals) / 2) {
                $numCols[] = $col;
            } else {
                $textCols[] = $col;
            }
        }

        // Name column: first text col that's not the group col
        $nameKey  = $textCols[0] ?? ($groupCol ? null : $sqlCols[0]);
        // Total col: first numeric
        $totalKey = $numCols[0] ?? null;
        // Bucket cols: remaining numerics
        $bKeys    = array_slice($numCols, 1);

        // No grouping
        if (!$groupCol || !in_array($groupCol, $sqlCols)) {
            $customers = [];
            foreach ($rows as $ri => $row) {
                $br = array_map(fn($k) => $raw($row[$k] ?? 0), $bKeys);
                $customers[] = [
                    'name'        => $nameKey ? ($row[$nameKey] ?? '') : '',
                    'total'       => $totalKey ? (($raw($row[$totalKey]??0)!=0)?$fmt($row[$totalKey]):'') : '',
                    'total_raw'   => $totalKey ? $raw($row[$totalKey]??0) : 0,
                    'buckets'     => array_map(fn($k) => ($raw($row[$k]??0)!=0)?$fmt($row[$k]):'', $bKeys),
                    'buckets_raw' => $br,
                ];
            }
            return [['salesman'=>'','total'=>'','total_raw'=>0,'buckets'=>[],'buckets_raw'=>[],'customers'=>$customers]];
        }

        // Group by groupCol
        $grouped = [];
        foreach ($rows as $row) {
            $gk = $row[$groupCol] ?? '';
            $grouped[$gk][] = $row;
        }

        $result = [];
        foreach ($grouped as $gk => $gRows) {
            $grpTotal = $totalKey ? array_sum(array_map(fn($r)=>$raw($r[$totalKey]??0), $gRows)) : 0;
            $grpBkts  = array_map(fn($bk)=>array_sum(array_map(fn($r)=>$raw($r[$bk]??0), $gRows)), $bKeys);

            $customers = [];
            foreach ($gRows as $ri => $row) {
                $br = array_map(fn($k)=>$raw($row[$k]??0), $bKeys);
                $customers[] = [
                    'name'        => $nameKey ? ($row[$nameKey] ?? $gk) : $gk,
                    'total'       => $totalKey ? (($raw($row[$totalKey]??0)!=0)?$fmt($row[$totalKey]):'') : '',
                    'total_raw'   => $totalKey ? $raw($row[$totalKey]??0) : 0,
                    'buckets'     => array_map(fn($k)=>($raw($row[$k]??0)!=0)?$fmt($row[$k]):'', $bKeys),
                    'buckets_raw' => $br,
                ];
            }

            $result[] = [
                'salesman'    => $gk,
                'total'       => $grpTotal!=0?$fmt($grpTotal):'',
                'total_raw'   => $grpTotal,
                'buckets'     => array_map(fn($v)=>$v!=0?$fmt($v):'', $grpBkts),
                'buckets_raw' => $grpBkts,
                'customers'   => $customers,
            ];
        }
        return $result;
    }

    private function resolveToken(string $val, array $params): string
    {
        if (str_starts_with($val, ':')) {
            $k = ltrim($val, ':');
            return $params[$k] ?? $val;
        }
        foreach ($params as $k => $v) {
            if (is_string($v)) $val = str_replace('{'.$k.'}', $v, $val);
        }
        return $val;
    }

    // ── PDF GENERATION ───────────────────────────────────────────────────────

    public function generatePdf(Report $report, array $rows, array $columns, array $params): \Barryvdh\DomPDF\PDF
    {
        $start = microtime(true);
        $at    = now();
        $by    = Auth::user()->name;

        // ── Path 1: Designer config ──────────────────────────────────────────
        if ($report->hasDesignerConfig()) {
            $dc     = $report->designer_config;
            // Pass already-executed $rows so we don't re-query unnecessarily
            $dsData = $this->executeDesignerDatasets($report, $params, $rows);

//            dd($report, $dc, $dsData,$params,$at,$by);

            $pdf = Pdf::loadView('reports.pdf.designer', [
                'report'         => $report,
                'dc'             => $dc,
                'datasetsResult' => $dsData,
                'params'         => $params,
                'generatedAt'    => $at,
                'generatedBy'    => $by,
                'companyData'    => CompanySetting::all_settings(),
            ]);
            $pdf->setPaper(
                $dc['page']['paper_size']  ?? 'A4',
                $dc['page']['orientation'] ?? 'landscape'
            );

            // ── Path 2: Legacy template ──────────────────────────────────────────
        } elseif ($tplConfig = $this->resolveTemplateConfig($report)) {
            $layout = $this->resolveLayout($report);
            $pdf = Pdf::loadView('reports.pdf.universal', [
                'report'=>$report,'rows'=>$rows,'columns'=>$columns,'params'=>$params,
                'tplConfig'=>$tplConfig,'layout'=>$layout,'generatedAt'=>$at,'generatedBy'=>$by,
            ]);
            $pdf->setPaper($tplConfig['paper_size']??$report->pdf_paper_size, $tplConfig['orientation']??$report->pdf_orientation);

            // ── Path 3: Static blade_view ────────────────────────────────────────
        } else {
            $view = $report->blade_view ?: 'reports.pdf.tabular';
            $pdf  = Pdf::loadView($view, [
                'report'=>$report,'rows'=>$rows,'columns'=>$columns,'params'=>$params,'generatedAt'=>$at,'generatedBy'=>$by,
            ]);
            $pdf->setPaper($report->pdf_paper_size, $report->pdf_orientation);
        }

        ReportLog::create([
            'user_id'            => Auth::id(),
            'report_id'          => $report->id,
            'parameters'         => $params,
            'ip_address'         => request()->ip(),
            'generation_time_ms' => (int) round((microtime(true)-$start)*1000),
        ]);

        return $pdf;
    }

    public function run(Report $report, array $params): array
    {
        $rows    = $this->executeQuery($report, $params);
        $rows    = $this->applyColumnConfig($report, $rows);
        $columns = $this->getColumns($rows);
        return compact('rows', 'columns');
    }
}

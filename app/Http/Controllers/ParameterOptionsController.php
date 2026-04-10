<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ParameterOptionsController extends Controller
{
    /**
     * Given a report, a child parameter name, and the parent's current value,
     * return the child's options as [{value, label}] JSON.
     *
     * Route: GET /reports/{report}/parameter-options/{paramName}?parent_value=XYZ
     */
    public function __invoke(Request $request, Report $report, string $paramName): JsonResponse
    {
        // Auth check — user must be able to run this report
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $param = $report->parameters()->where('name', $paramName)->first();

        if (!$param) {
            return response()->json(['error' => 'Parameter not found'], 404);
        }

        if (!in_array($param->options_source, ['sql', 'dependent_sql'])) {
            return response()->json(['error' => 'Parameter does not use SQL options'], 400);
        }

        $sql = trim($param->options ?? '');
        if (!$sql) {
            return response()->json(['options' => []]);
        }

        // Replace company prefix placeholder
        $companyPrefix = $request->input('company', '0');
        $sql = str_replace('{company_prefix}', $companyPrefix, $sql);

        // Replace the parent parameter placeholder if present
        $parentValue = $request->input('parent_value', '');
        $bindings    = [];

        // The child SQL may reference :parent_value OR the actual parent param name
        // e.g.  WHERE salesman_id = :parent_value
        // or    WHERE salesman_id = :salesman_id
        if (str_contains($sql, ':parent_value')) {
            $bindings['parent_value'] = $parentValue;
        }

        // Also replace any other named params the SQL might reference
        // (e.g. :salesman_id if the dev used the actual name)
        if ($param->depends_on && str_contains($sql, ':' . $param->depends_on)) {
            $bindings[$param->depends_on] = $parentValue;
        }

        try {
            // Safety: only allow SELECT
            $upperSql = strtoupper(preg_replace('/\s+/', ' ', trim($sql)));
            if (!str_starts_with($upperSql, 'SELECT')) {
                return response()->json(['error' => 'Only SELECT queries are allowed'], 400);
            }

            $rows = DB::select($sql, $bindings);

            $options = array_map(function ($row) {
                $row = (array) $row;
                // Expect columns named 'value' and 'label'
                // Fall back to first/second column if not present
                $keys = array_keys($row);
                return [
                    'value' => $row['value'] ?? $row[$keys[0]] ?? '',
                    'label' => $row['label'] ?? $row[$keys[1] ?? $keys[0]] ?? '',
                ];
            }, $rows);

            return response()->json(['options' => $options]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

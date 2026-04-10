<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SqlValidatorController extends Controller
{
    /**
     * Validate a SQL query and return its columns (or an error).
     * Called via AJAX from the report form.
     */
    public function validate(Request $request)
    {
        $request->validate([
            'sql'     => 'required|string',
            'company' => 'nullable|string',
        ]);

        $sql     = trim($request->input('sql'));
        $company = $request->input('company', '0');

        // ── Security checks ───────────────────────────────────────────────
        $forbidden = ['INSERT', 'UPDATE', 'DELETE', 'DROP', 'TRUNCATE',
                      'ALTER', 'CREATE', 'REPLACE', 'GRANT', 'REVOKE',
                      'EXEC', 'EXECUTE', 'CALL', 'LOAD', 'OUTFILE'];

        $upperSql = strtoupper(preg_replace('/\s+/', ' ', $sql));

        foreach ($forbidden as $keyword) {
            // Check it's a standalone keyword (not part of a column/alias name)
            if (preg_match('/\b' . $keyword . '\b/', $upperSql)) {
                return response()->json([
                    'success' => false,
                    'error'   => "Forbidden keyword detected: {$keyword}. Only SELECT statements are allowed.",
                ], 422);
            }
        }

        if (!preg_match('/^\s*SELECT\b/i', $sql)) {
            return response()->json([
                'success' => false,
                'error'   => 'Query must start with SELECT.',
            ], 422);
        }

        // ── Replace placeholders ──────────────────────────────────────────
        // Replace {company_prefix}
        $sql = str_replace('{company_prefix}', $company, $sql);

        // Replace all :param_name with NULL so the query can run without values
        $sql = preg_replace('/:\w+/', 'NULL', $sql);

        // Wrap in LIMIT 1 to avoid fetching all rows
        $wrappedSql = "SELECT * FROM ({$sql}) AS __validator_subquery__ LIMIT 1";

        // ── Run the query ─────────────────────────────────────────────────
        try {
            $results = DB::select($wrappedSql);

            // Get column names from result (or from the raw query if empty)
            if (!empty($results)) {
                $columns = array_keys((array) $results[0]);
            } else {
                // Try to get columns from an empty result set
                $stmt = DB::getPdo()->query($wrappedSql);
                $columns = [];
                for ($i = 0; $i < $stmt->columnCount(); $i++) {
                    $meta      = $stmt->getColumnMeta($i);
                    $columns[] = $meta['name'];
                }
            }

            return response()->json([
                'success' => true,
                'columns' => $columns,
                'message' => 'Query is valid. ' . count($columns) . ' column(s) detected.',
            ]);

        } catch (\Exception $e) {
            // Clean up DB error message for display
            $message = $e->getMessage();

            // Remove internal path info from message
            $message = preg_replace('/\(SQL:.*\)$/s', '', $message);
            $message = trim($message);

            return response()->json([
                'success' => false,
                'error'   => $message,
            ], 422);
        }
    }
}

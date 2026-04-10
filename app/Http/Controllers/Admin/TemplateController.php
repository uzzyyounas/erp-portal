<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReportTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = ReportTemplate::withCount('reports')
            ->orderBy('sort_order')->orderBy('name')
            ->get();

        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        $template = null;
        $defaults = ReportTemplate::defaultConfig();
        $layouts  = ReportTemplate::layoutOptions();
        return view('admin.templates.form', compact('template', 'defaults', 'layouts'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        ReportTemplate::create($data);
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(ReportTemplate $template)
    {
        $defaults = ReportTemplate::defaultConfig();
        $layouts  = ReportTemplate::layoutOptions();
        return view('admin.templates.form', compact('template', 'defaults', 'layouts'));
    }

    public function update(Request $request, ReportTemplate $template)
    {
        $data = $this->validated($request, $template->id);
        $template->update($data);
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template updated.');
    }

    public function destroy(ReportTemplate $template)
    {
        if ($template->is_system) {
            return back()->with('error', 'System templates cannot be deleted.');
        }
        if ($template->reports()->count()) {
            return back()->with('error', 'Cannot delete: template is used by ' . $template->reports()->count() . ' report(s). Reassign first.');
        }
        $template->delete();
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template deleted.');
    }

    // ── Ajax: return template config as JSON for live preview ──────────────
    public function configJson(ReportTemplate $template)
    {
        return response()->json([
            'config' => $template->getEffectiveConfig(),
            'layout' => $template->layout,
        ]);
    }

    // ── Private ────────────────────────────────────────────────────────────

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'layout'      => 'required|in:tabular,grouped,master-detail,statement,aged,custom',
            'sort_order'  => 'integer|min:0',
        ]);

        // If designer JSON was pasted directly, use it
        if ($request->filled('raw_config_json')) {
            $decoded = json_decode($request->input('raw_config_json'), true);
            if (is_array($decoded)) {
                $layout = $decoded['layout'] ?? $request->input('layout');
                $config = $decoded; // full designer config including columns
                return [
                    'name'        => $request->input('name'),
                    'slug'        => Str::slug($request->input('name')),
                    'description' => $request->input('description'),
                    'layout'      => $layout,
                    'config'      => $config,
                    'sort_order'  => (int) $request->input('sort_order', 0),
                    'is_active'   => $request->boolean('is_active', true),
                ];
            }
        }

        // Build config array from individual form fields
        $config = [];
        $defaults = ReportTemplate::defaultConfig();

        foreach ($defaults as $key => $default) {
            $posted = $request->input("config_{$key}");

            if (is_bool($default)) {
                $config[$key] = $request->boolean("config_{$key}");
            } elseif (is_int($default)) {
                $config[$key] = (int) ($posted ?? $default);
            } else {
                $config[$key] = $posted ?? $default;
            }
        }

        return [
            'name'        => $request->input('name'),
            'slug'        => Str::slug($request->input('name')),
            'description' => $request->input('description'),
            'layout'      => $request->input('layout'),
            'config'      => $config,
            'sort_order'  => (int) $request->input('sort_order', 0),
            'is_active'   => $request->boolean('is_active', true),
        ];
    }
}

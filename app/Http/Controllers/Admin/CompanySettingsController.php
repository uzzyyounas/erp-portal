<?php

namespace App\Http\Controllers\Admin;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class CompanySettingsController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::orderBy('group')->orderBy('id')->get()->keyBy('key');
        return view('admin.company-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            CompanySetting::where('key', $key)->update(['value' => $value]);
        }

        // Clear all caches
        Cache::forget('company_settings_all');
        foreach (array_keys($data) as $key) {
            Cache::forget("company_setting_{$key}");
        }

        return back()->with('success', 'Company settings saved successfully.');
    }
}

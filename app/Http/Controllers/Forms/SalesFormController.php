<?php

namespace App\Http\Controllers\Forms;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SalesFormController extends Controller
{
    /**
     * Sales Order Entry form.
     * Route: forms.sales.order-entry
     */
    public function orderEntry()
    {
        // Replace with real DB lookups:
        $customers = collect([
            ['id' => 1, 'name' => 'Lucky Foods Ltd.'],
            ['id' => 2, 'name' => 'Star Traders'],
            ['id' => 3, 'name' => 'Metro Distributors'],
        ]);

        $products = collect([
            ['id' => 1, 'name' => 'Product A — 1kg',  'price' => 250.00],
            ['id' => 2, 'name' => 'Product B — 500g', 'price' => 145.00],
            ['id' => 3, 'name' => 'Product C — 250g', 'price' =>  88.00],
        ]);

        $salesmen = collect([
            ['id' => 1, 'name' => 'Ahmed Raza'],
            ['id' => 2, 'name' => 'Bilal Tariq'],
            ['id' => 3, 'name' => 'Fatima Malik'],
        ]);

        return view('forms.sales.order-entry', compact('customers', 'products', 'salesmen'));
    }

    /**
     * Handle form submission.
     * Replace with actual save logic (DB insert / API call).
     */
    public function orderEntryStore(Request $request)
    {
        $request->validate([
            'customer_id'       => 'required|integer',
            'salesman_id'       => 'required|integer',
            'order_date'        => 'required|date',
            'delivery_date'     => 'nullable|date|after_or_equal:order_date',
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|integer',
            'items.*.qty'       => 'required|numeric|min:0.01',
            'items.*.price'     => 'required|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
        ]);

        // TODO: persist to database
        // SalesOrder::create([...]);

        return redirect()->route('forms.sales.order-entry')
            ->with('success', 'Sales order saved successfully! (Ref: SO-' . rand(10000, 99999) . ')');
    }
}

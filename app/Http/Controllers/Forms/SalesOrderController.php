<?php

namespace App\Http\Controllers\Forms;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    /**
     * Show the Sales Order entry form.
     * Route: forms.sales.order  [GET]
     */
    public function index()
    {
        // Load any dynamic dropdowns your form needs
        // Replace with real DB queries as needed
        $customers = [
            ['id' => 1, 'name' => 'Al-Fatah General Store'],
            ['id' => 2, 'name' => 'City Traders'],
            ['id' => 3, 'name' => 'Prime Distributors'],
            ['id' => 4, 'name' => 'National Wholesalers'],
        ];

        $products = [
            ['id' => 1, 'code' => 'PRD-001', 'name' => 'Product A', 'price' => 150.00],
            ['id' => 2, 'code' => 'PRD-002', 'name' => 'Product B', 'price' => 280.00],
            ['id' => 3, 'code' => 'PRD-003', 'name' => 'Product C', 'price' => 95.00],
            ['id' => 4, 'code' => 'PRD-004', 'name' => 'Product D', 'price' => 420.00],
        ];

        $salesmen = [
            ['id' => 1, 'name' => 'Ahmed Raza'],
            ['id' => 2, 'name' => 'Bilal Hassan'],
            ['id' => 3, 'name' => 'Fatima Malik'],
        ];

        return view('forms.sales.order', compact('customers', 'products', 'salesmen'));
    }

    /**
     * Save the Sales Order.
     * Route: forms.sales.order.store  [POST]
     *
     * Wire up validation and DB inserts for your real schema.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'      => 'required|integer',
            'salesman_id'      => 'required|integer',
            'order_date'       => 'required|date',
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount'   => 'nullable|numeric|min:0|max:100',
        ]);

        // ── Replace with your real DB logic ───────────────────────────
        //
        // DB::transaction(function () use ($request) {
        //     $orderId = DB::table('sales_orders')->insertGetId([
        //         'customer_id' => $request->customer_id,
        //         'salesman_id' => $request->salesman_id,
        //         'order_date'  => $request->order_date,
        //         'notes'       => $request->notes,
        //         'created_by'  => auth()->id(),
        //         'created_at'  => now(),
        //         'updated_at'  => now(),
        //     ]);
        //
        //     foreach ($request->items as $item) {
        //         DB::table('sales_order_items')->insert([...]);
        //     }
        // });

        return redirect()->route('forms.sales.order')
            ->with('success', 'Sales order saved successfully! (Dummy — wire up your real DB logic.)');
    }
}

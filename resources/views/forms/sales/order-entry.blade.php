@extends('layouts.app')
@section('title', 'Sales Order Entry')
@section('breadcrumb')
    <li class="breadcrumb-item">Sales</li>
    <li class="breadcrumb-item active">Sales Order Entry</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h4><i class="bi bi-pencil-square me-2"></i>Sales Order Entry</h4>
        <small class="text-muted">Create a new sales order</small>
    </div>
</div>

<form method="POST" action="{{ route('forms.sales.order-entry.store') }}" id="orderForm">
@csrf

<div class="row g-3">

    {{-- LEFT: Order Header --}}
    <div class="col-lg-8">

        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Order Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-select form-select-sm" required>
                            <option value="">— Select Customer —</option>
                            @foreach($customers as $c)
                                <option value="{{ $c['id'] }}"
                                    {{ old('customer_id') == $c['id'] ? 'selected' : '' }}>
                                    {{ $c['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Salesman <span class="text-danger">*</span></label>
                        <select name="salesman_id" class="form-select form-select-sm" required>
                            <option value="">— Select Salesman —</option>
                            @foreach($salesmen as $s)
                                <option value="{{ $s['id'] }}"
                                    {{ old('salesman_id') == $s['id'] ? 'selected' : '' }}>
                                    {{ $s['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold">Order Date <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control form-control-sm"
                               value="{{ old('order_date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold">Delivery Date</label>
                        <input type="date" name="delivery_date" class="form-control form-control-sm"
                               value="{{ old('delivery_date') }}">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fw-semibold">Reference #</label>
                        <input type="text" name="reference" class="form-control form-control-sm"
                               value="{{ old('reference') }}" placeholder="Optional">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2"
                                  placeholder="Any special instructions…">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Lines --}}
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-cart me-2"></i>Order Lines</span>
                <button type="button" class="btn btn-xs btn-outline-success" id="addLineBtn">
                    <i class="bi bi-plus-lg me-1"></i>Add Line
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0" id="linesTable">
                    <thead>
                        <tr>
                            <th style="width:40%;">Product</th>
                            <th style="width:15%;" class="text-end">Qty</th>
                            <th style="width:20%;" class="text-end">Unit Price</th>
                            <th style="width:18%;" class="text-end">Line Total</th>
                            <th style="width:7%;"></th>
                        </tr>
                    </thead>
                    <tbody id="linesBody">
                        {{-- Initial empty row --}}
                        @include('forms.sales._order-line', ['i' => 0, 'products' => $products, 'line' => null])
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8fafc;font-weight:700;">
                            <td colspan="3" class="text-end" style="font-size:.82rem;">ORDER TOTAL</td>
                            <td class="text-end" id="grandTotal">0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    {{-- RIGHT: Summary + Submit --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-receipt me-2"></i>Order Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2" style="font-size:.85rem;">
                    <span class="text-muted">Subtotal</span>
                    <span id="summarySubtotal" class="fw-semibold">0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:.85rem;">
                    <span class="text-muted">Lines</span>
                    <span id="summaryLines" class="fw-semibold">0</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between" style="font-size:1rem;">
                    <span class="fw-bold">Total</span>
                    <span id="summaryTotal" class="fw-bold" style="color:#1a3a5c;font-size:1.1rem;">0.00</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body d-flex flex-column gap-2">
                <button type="submit" class="btn btn-erp">
                    <i class="bi bi-save me-2"></i>Save Order
                </button>
                <button type="reset" class="btn btn-outline-secondary" onclick="resetForm()">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                </button>
            </div>
        </div>
    </div>

</div>
</form>
@endsection

@push('scripts')
<script>
let lineIndex = 1;

// Add new line
document.getElementById('addLineBtn').addEventListener('click', function() {
    fetch(`/forms/sales/order-line-template?i=${lineIndex}`)
        .catch(() => {
            // Fallback: clone existing row
            const tbody  = document.getElementById('linesBody');
            const newRow = tbody.querySelector('tr').cloneNode(true);
            const i      = lineIndex;

            // Update name indices
            newRow.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, `[${i}]`);
                if (el.tagName === 'SELECT') el.value = '';
                if (el.type !== 'hidden') el.value = el.type === 'number' ? '1' : '';
            });
            newRow.querySelector('.line-total').textContent = '0.00';
            newRow.querySelectorAll('input').forEach(el => el.value = el.type === 'number' ? '1' : '');
            tbody.appendChild(newRow);
            bindLineEvents(newRow);
            lineIndex++;
            recalculate();
        });

    // Inline template (no extra route needed)
    const tbody = document.getElementById('linesBody');
    const i     = lineIndex++;
    const products = @json($products->map(fn($p) => ['id' => $p['id'], 'name' => $p['name'], 'price' => $p['price']]));

    let opts = '<option value="">— Select Product —</option>';
    products.forEach(p => {
        opts += `<option value="${p.id}" data-price="${p.price}">${p.name}</option>`;
    });

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="items[${i}][product_id]" class="form-select form-select-sm product-select" required>
                ${opts}
            </select>
        </td>
        <td><input type="number" name="items[${i}][qty]" class="form-control form-control-sm text-end qty-input" value="1" min="0.01" step="0.01" required></td>
        <td><input type="number" name="items[${i}][price]" class="form-control form-control-sm text-end price-input" value="0.00" min="0" step="0.01" required></td>
        <td class="text-end fw-semibold line-total" style="font-size:.82rem;">0.00</td>
        <td class="text-center">
            <button type="button" class="btn btn-xs btn-outline-danger remove-line">
                <i class="bi bi-x"></i>
            </button>
        </td>`;
    tbody.appendChild(tr);
    bindLineEvents(tr);
    recalculate();
});

function bindLineEvents(row) {
    row.querySelector('.product-select')?.addEventListener('change', function() {
        const price = this.options[this.selectedIndex]?.dataset?.price ?? 0;
        row.querySelector('.price-input').value = parseFloat(price).toFixed(2);
        calcLine(row);
    });
    row.querySelector('.qty-input')?.addEventListener('input',   () => calcLine(row));
    row.querySelector('.price-input')?.addEventListener('input', () => calcLine(row));
    row.querySelector('.remove-line')?.addEventListener('click', function() {
        if (document.querySelectorAll('#linesBody tr').length > 1) {
            row.remove(); recalculate();
        }
    });
}

function calcLine(row) {
    const qty   = parseFloat(row.querySelector('.qty-input')?.value)   || 0;
    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
    const total = qty * price;
    row.querySelector('.line-total').textContent = total.toFixed(2);
    recalculate();
}

function recalculate() {
    let grand = 0, lines = 0;
    document.querySelectorAll('.line-total').forEach(el => {
        grand += parseFloat(el.textContent) || 0;
        lines++;
    });
    document.getElementById('grandTotal').textContent    = grand.toFixed(2);
    document.getElementById('summarySubtotal').textContent = grand.toFixed(2);
    document.getElementById('summaryTotal').textContent  = grand.toFixed(2);
    document.getElementById('summaryLines').textContent  = lines;
}

function resetForm() {
    const tbody = document.getElementById('linesBody');
    while (tbody.rows.length > 1) tbody.deleteRow(1);
    const firstRow = tbody.rows[0];
    firstRow.querySelectorAll('input').forEach(el => el.value = el.type === 'number' ? '1' : '');
    firstRow.querySelector('.product-select').value = '';
    firstRow.querySelector('.line-total').textContent = '0.00';
    recalculate();
}

// Bind existing rows on load
document.querySelectorAll('#linesBody tr').forEach(bindLineEvents);
recalculate();
</script>
@endpush

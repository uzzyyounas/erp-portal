<tr>
    <td>
        <select name="items[{{ $i }}][product_id]"
                class="form-select form-select-sm product-select" required>
            <option value="">— Select Product —</option>
            @foreach($products as $p)
                <option value="{{ $p['id'] }}"
                        data-price="{{ $p['price'] }}"
                        {{ old("items.$i.product_id") == $p['id'] ? 'selected' : '' }}>
                    {{ $p['name'] }}
                </option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="number" name="items[{{ $i }}][qty]"
               class="form-control form-control-sm text-end qty-input"
               value="{{ old("items.$i.qty", 1) }}"
               min="0.01" step="0.01" required>
    </td>
    <td>
        <input type="number" name="items[{{ $i }}][price]"
               class="form-control form-control-sm text-end price-input"
               value="{{ old("items.$i.price", '0.00') }}"
               min="0" step="0.01" required>
    </td>
    <td class="text-end fw-semibold line-total" style="font-size:.82rem;">0.00</td>
    <td class="text-center">
        <button type="button" class="btn btn-xs btn-outline-danger remove-line">
            <i class="bi bi-x"></i>
        </button>
    </td>
</tr>

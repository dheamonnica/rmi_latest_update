<div class="row">
    {!! Form::hidden('order_id', $order->id) !!}
    {!! Form::hidden('manufacture_id', '1') !!}
    {!! Form::hidden('po_number_ref', $order->po_number_ref) !!}

    <div class="col-md-12">
        <p>Date: {{ $order->created_at->toDayDateTimeString() }} - {{ $order->id }}</p>
        <p>No. Purchase Order: {{ $order->po_number_ref }}</p>
        <p>Manufacture: Manufacture</p>
    </div>

    <div class="col-md-12" style="overflow: auto">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ trans('app.form.sku') }}</th>
                    <th>{{ trans('app.form.kode_reg_alkes') }}</th>
                    <th>{{ trans('app.form.hs_code') }}</th>
                    <th>{{ trans('app.form.product_name') }}</th>
                    <th>{{ trans('app.form.order_qty') }}</th>
                    <th>{{ trans('app.form.price_pcs') }}</th>
                    <th>{{ trans('app.form.subtotal') }}</th>
                    <th>{{ trans('app.form.shipping_fee') }}</th>
                    <th>{{ trans('app.form.grand_total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderData as $index => $data)
                    <tr>
                        <td>
                            <input type="text" class="editable-input" name="sku[]"
                                value="{{ $data->getProduct->selling_skuid }}" disabled>
                            <input type="hidden" class="editable-input" name="sku[]"
                                value="{{ $data->getProduct->selling_skuid }}">
                        </td>
                        <td>
                            <input type="hidden" class="editable-input" name="product_id[]"
                                value="{{ $data->getProduct->id }}">
                            <input type="hidden" class="editable-input" name="inventory_id[]"
                                value="{{ $data->inventory_id }}">
                            <input type="text" class="editable-input" name="kode_reg_alkes[]"
                                value="{{ $data->kode_reg_alkes }}">
                        </td>
                        <td>
                            <input type="text" class="editable-input" name="hs_code[]" value="{{ $data->hs_code }}">
                        </td>
                        <td>
                            <input type="text" class="editable-input" name="product_name[]"
                                value="{{ $data->getProduct->name }}" disabled>
                            <input type="hidden" class="editable-input" name="product_name[]"
                                value="{{ $data->getProduct->name }}">
                        </td>
                        <td>
                            <input type="number" class="editable-input" name="order_qty[]"
                                value="{{ $data->quantity }}" disabled>
                            <input type="hidden" class="editable-input" name="order_qty[]"
                                value="{{ $data->quantity }}">
                        </td>
                        <td>
                            <input type="text" class="editable-input" name="price_pcs[]"
                                value="{{ number_format($data->unit_price) }}" disabled>
                            <input type="hidden" class="editable-input" name="price_pcs[]"
                                value="{{ $data->unit_price }}">
                        </td>
                        <td>
                            <input type="text" class="editable-input" name="subtotal[]" id="subtotal"
                                value="{{ number_format($data->quantity * $data->unit_price) }}" disabled>
                            <input type="hidden" class="editable-input" name="subtotal[]"
                                value="{{ $data->quantity * $data->unit_price }}">
                        </td>
                        <td>
                            <input type="number" class="editable-input" name="shipping_fee[]" id="shipping_fee"
                                value="{{ $data->shipping_fee }}">
                        </td>
                        <td>
                            <input type="text" class="editable-input" name="total[]" id="total"
                                value="{{ number_format($data->shipping_fee + $data->quantity * $data->unit_price) }}"
                                disabled>
                            <input type="hidden" class="editable-input" name="total[]" id="grand_total"
                                value="{{ $data->shipping_fee + $data->quantity * $data->unit_price }}">
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.table tbody').on('input',
            'input[name="order_qty"], input[name="price_pcs"], input[name="shipping_fee"]',
            function() {
                const row = $(this).closest('tr'); // Get the parent row

                const qty = parseFloat(row.find('input[name="order_qty"]').val()) || 0;
                const price = parseFloat(row.find('input[name="price_pcs"]').val().replace(/,/g, '')) || 0;
                const shippingFee = parseFloat(row.find('input[name="shipping_fee"]').val().replace(/,/g,
                    '')) || 0;

                // Calculate subtotal and grand total
                const subtotal = qty * price;
                const grandTotal = subtotal + shippingFee;

                // Update the fields
                row.find('input[name="subtotal"]').val(subtotal);
                row.find('#subtotal').val(subtotal.toLocaleString()); // Format with thousand separators

                row.find('input[name="total"]').val(grandTotal);
                row.find('#total').val(grandTotal.toLocaleString());

                row.find('input[name="grand_total"]').val(grandTotal);
            });

        // Function to recalculate totals for all rows
        function recalculateAllTotals() {
            let totalGrandTotal = 0;
            $('.table tbody tr').each(function() {
                const row = $(this);
                const subtotal = parseFloat(row.find('input[name="subtotal"]').val().replace(/,/g,
                    '')) || 0;
                const shippingFee = parseFloat(row.find('input[name="shipping_fee"]').val().replace(
                    /,/g, '')) || 0;
                const grandTotal = subtotal + shippingFee;

                totalGrandTotal += grandTotal;

                // Update grand total for each row
                row.find('input[name="total"]').val(grandTotal);
                row.find('#total').val(grandTotal.toLocaleString());
            });

            // Update the final total for all rows
            $('#finalGrandTotal').text(totalGrandTotal.toLocaleString());
        }

        // Recalculate totals when any input changes
        $('.table tbody').on('input',
            'input[name="order_qty"], input[name="price_pcs"], input[name="shipping_fee"]',
            function() {
                recalculateAllTotals();
            });

        // Initial calculation in case of pre-filled values
        recalculateAllTotals();
    });
</script>

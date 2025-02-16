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
                            <input type="hidden" name="sku[]" value="{{ $data->getProduct->selling_skuid }}">
                        </td>
                        <td>
                            <input type="hidden" name="product_id[]" value="{{ $data->getProduct->id }}">
                            <input type="hidden" name="inventory_id[]" value="{{ $data->inventory_id }}">
                            <input type="text" class="editable-input" name="kode_reg_alkes[]"
                                value="{{ $data->kode_reg_alkes }}" required>
                        </td>
                        <td>
                            <input type="text" class="editable-input" name="hs_code[]" value="{{ $data->hs_code }}"
                                required>
                        </td>
                        <td>
                            <input type="text" class="editable-input" name="product_name[]"
                                value="{{ $data->getProduct->name }}" disabled>
                            <input type="hidden" name="product_name[]" value="{{ $data->getProduct->name }}">
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
                            <input type="hidden" name="price_pcs_hidden[]" value="{{ $data->unit_price }}">
                        </td>
                        <td>
                            <input type="text" class="editable-input" name="subtotal[]"
                                value="{{ number_format($data->quantity * $data->unit_price) }}" disabled>
                            <input type="hidden" name="subtotal_hidden[]"
                                value="{{ $data->quantity * $data->unit_price }}">
                        </td>
                        <td>
                            <input type="number" class="editable-input" name="shipping_fee[]"
                                value="{{ $data->shipping_fee }}" required>
                        </td>
                        <td>
                            <input type="text" class="editable-input" name="total[]"
                                value="{{ number_format($data->shipping_fee + $data->quantity * $data->unit_price) }}"
                                disabled>
                            <input type="hidden" name="total_hidden[]"
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
            'input[name="order_qty[]"], input[name="price_pcs[]"], input[name="shipping_fee[]"]',
            function() {
                const row = $(this).closest('tr');

                const qty = parseFloat(row.find('input[name="order_qty[]"]').val()) || 0;
                const price = parseFloat(row.find('input[name="price_pcs[]"]').first().val()?.replace(/,/g,
                    '')) || 0;
                const shippingFee = parseFloat(row.find('input[name="shipping_fee[]"]').val()?.replace(/,/g,
                    '')) || 0;

                console.log("Price:", price);

                // Calculate subtotal and grand total
                const subtotal = qty * price;
                const grandTotal = subtotal + shippingFee;

                // Update the fields
                row.find('input[name="subtotal_hidden[]"]').val(subtotal);
                row.find('input[name="subtotal[]"]').val(subtotal.toLocaleString());

                row.find('input[name="total_hidden[]"]').val(grandTotal);
                row.find('input[name="total[]"]').val(grandTotal.toLocaleString());

                recalculateAllTotals();
            });

        // Function to recalculate totals for all rows
        function recalculateAllTotals() {
            let totalGrandTotal = 0;
            $('.table tbody tr').each(function() {
                const row = $(this);
                const subtotal = parseFloat(row.find('input[name="subtotal_hidden[]"]').val()?.replace(
                    /,/g, '')) || 0;
                const shippingFee = parseFloat(row.find('input[name="shipping_fee[]"]').val()?.replace(
                    /,/g, '')) || 0;
                const grandTotal = subtotal + shippingFee;

                totalGrandTotal += grandTotal;

                // Update grand total for each row
                row.find('input[name="total_hidden[]"]').val(grandTotal);
                row.find('input[name="total[]"]').val(grandTotal.toLocaleString());
            });

            // Update the final total for all rows
            $('#finalGrandTotal').text(totalGrandTotal.toLocaleString());
        }

        // Initial calculation in case of pre-filled values
        recalculateAllTotals();
    });

    $(document).ready(function () {
        $('.table tbody').on('input', 'input', function () {
            if ($(this).val().trim() !== '') {
                $(this).removeClass('error-border'); // Remove error class when field is filled
            }
        });

        $('form').on('submit', function (e) {
            let isValid = true;

            $('input[required]').each(function () {
                if ($(this).val().trim() === '') {
                    $(this).addClass('error-border'); // Add red border for empty fields
                    isValid = false;
                } else {
                    $(this).removeClass('error-border');
                }
            });

            if (!isValid) {
                e.preventDefault(); // Stop form submission if there are empty required fields
                alert('Please fill all required fields.');
            }
        });
    });
</script>

<style>
    /* Add red border for empty required fields */
    .error-border {
        border: 2px solid rgb(251, 93, 93) !important;
    }
</style>
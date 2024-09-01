<div class="row">
    {!! Form::hidden('id', null, [
        'class' => 'form-control datepicker',
        'placeholder' => trans('app.form.date'),
        'required',
        'id' => 'id-budget',
    ]) !!}
    <div class="col-md-2 nopadding-right">
        <div class="form-group">
            {!! Form::label('date', trans('app.form.date'), ['class' => 'with-help']) !!}
            {!! Form::text('date', null, [
                'class' => 'form-control datepicker',
                'placeholder' => trans('app.form.date'),
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>

    <div class="col-md-2 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('requirement', trans('app.form.requirement'), ['class' => 'with-help']) !!}
            {!! Form::text('requirement', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.requirement'),
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-md-2 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('category', trans('app.form.category'), ['class' => 'with-help']) !!}
            {!! Form::select('category_id', ['' => 'Select'] + $budgetCategories, null, [
                'class' => 'form-control',
                'required',
                'id' => 'category-select',
            ]) !!}
        </div>
    </div>

    <div class="col-md-2 nopadding-left nopadding-right" id="quantity-field" style="display: none;">
        <div class="form-group">
            {!! Form::label('qty', trans('app.form.quantity'), ['class' => 'with-help']) !!}
            {!! Form::number('qty', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.quantity'),
                'required',
                'id' => 'qty-input',
                'min' => '0',
                'step' => 'any', // Allows for decimal values
            ]) !!}
        </div>
    </div>

    <div class="col-md-2 nopadding-left nopadding-right" id="total-field">
        <div class="form-group">
            {!! Form::label('total', trans('app.form.total'), ['class' => 'with-help']) !!}
            {!! Form::number('total', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.total'),
                'required',
                'id' => 'total-input',
                'readonly' => true, // Initially readonly
            ]) !!}
        </div>
    </div>

    <div class="col-md-2 nopadding-left" id="grand-total-field">
        <div class="form-group">
            {!! Form::label('grand_total', trans('app.form.grand_total'), ['class' => 'with-help']) !!}
            {!! Form::number('grand_total', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.grand_total'),
                'required',
                'id' => 'grand-total-input',
                'readonly' => true, // Initially readonly
            ]) !!}
        </div>
    </div>

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-9 nopadding-right">
                <input id="uploadFile" placeholder="{{ trans('app.placeholder.image') }}" class="form-control"
                    disabled="disabled" style="height: 28px;" />
                <div class="help-block with-errors"></div>
            </div>
            <div class="col-md-3 nopadding-left">
                <div class="fileUpload btn btn-primary btn-block btn-flat">
                    <span>{{ trans('app.form.upload') }}</span>
                    <input type="file" name="images[picture]" id="uploadBtn" class="upload"
                        {{ isset($budget) ? '' : 'required' }} />
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const categorySelect = $('#category-select');
        const quantityField = $('#quantity-field');
        const totalField = $('#total-field');
        const grandtotalField = $('#grand-total-field');
        const totalInput = $('#total-input');
        const qtyInput = $('#qty-input');
        const grandtotalInput = $('#grand-total-input');

        function fetchBudgetCategoryValue(id) {
            $.ajax({
                url: "{{ route('admin.admin.budget.getBudgetCategoryValue') }}", // Ensure this route is correct
                type: 'GET', // Use the appropriate HTTP method (GET, POST, etc.)
                data: {
                    id: id
                }, // Send the ID as query parameter
                success: function(response) {
                    // console.log('Response:', response.data[0].value);
                    valueBudget = response.data[0].value;
                    console.log(valueBudget, 'valueBudget');
                    // Process the response data as needed

                    if (valueBudget === 0) {
                        quantityField.show(); // Show quantity field
                        qtyInput.prop('readonly', false); // Enable quantity input
                        totalInput.prop('readonly', false); // Enable total input
                        grandtotalField.show(); // Show grandtotal field
                        totalInput.val(''); // Clear total input when category is 0
                        grandtotalInput.val(''); // Clear grandtotal field
                    } else {
                        quantityField.show(); // Hide quantity field
                        totalInput.prop('readonly', true); // Disable total input
                        grandtotalField.show(); // Hide grandtotal field

                        // Set the total field value based on selected category
                        totalInput.val(valueBudget); // Replace with actual logic if available
                        grandtotalInput.val(''); // Clear grandtotal field
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                }
            });
        }

        function updateGrandtotal() {
            const qty = parseFloat(qtyInput.val()) || 0;
            const total = parseFloat(totalInput.val()) || 0;
            const grandtotal = qty * total;
            grandtotalInput.val(grandtotal); // Set grandtotal with two decimal places
        }

        function fetchBudgetData(id) {
            $.ajax({
                url: "{{ route('admin.admin.budget.getBudgetData') }}", // Ensure this route is correct
                type: 'GET', // Use the appropriate HTTP method (GET, POST, etc.)
                data: {
                    id: id
                }, // Send the ID as query parameter
                success: function(response) {
                    valueBudget = response.data[0];
                    console.log(valueBudget, 'valueBudget');
                    if (valueBudget.qty) {
                        quantityField.show(); // Show quantity field
                    }
                    if (valueBudget.total) {
                        totalField.show(); // Show quantity field
                        totalInput.prop('readonly', false); // Enable quantity input
                    }
                    if (valueBudget.grand_total) {
                        grandtotalField.show(); // Show quantity field
                        grandtotalInput.prop('readonly', false); // Enable quantity input
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                }
            });
        }

        console.log($('#id-budget').val(), 'id-budget')
        if ($('#id-budget').val()) {
            fetchBudgetData($('#id-budget').val());
        }

        // Add event listener for changes in the category dropdown
        categorySelect.on('change', async function() {
            const selectedId = $(this).val();
            console.log(selectedId, 'selectedId')
            await fetchBudgetCategoryValue(selectedId);
        });

        // Add event listeners for qty and total inputs to update grandtotal
        qtyInput.on('input', updateGrandtotal);
        totalInput.on('input', updateGrandtotal);
    });
</script>

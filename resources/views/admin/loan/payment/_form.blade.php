<div class="row">
    <div class="col-md-3 nopadding-right">
        <div class="form-group p-1">
            {!! Form::label('user_id', trans('app.form.select_user') . '*') !!}
            {!! Form::select('user_id', ['' => 'Select'] + $users, null, [
                'class' => 'form-control select2-normal',
                'required',
                'id' => 'user-select',
            ]) !!}
        </div>
    </div>
    <div class="col-md-3 nopadding-left">
        <div class="form-group">
            {!! Form::label('total_loan', trans('app.form.total_loan'), ['class' => 'with-help']) !!}
            {!! Form::text('total_loan', null, [
                'placeholder' => trans('app.form.total_loan'),
                'class' => 'form-control',
                'required',
                'id' => 'total-loan',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-3 nopadding-left">
        <div class="form-group">
            {!! Form::label('amount', trans('app.form.amount'), ['class' => 'with-help']) !!}
            {!! Form::number('amount', null, [
                'placeholder' => trans('app.form.amount'),
                'class' => 'form-control',
                'required',
                'id' => 'amount-payment',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-3 nopadding-left">
        <div class="form-group">
            {!! Form::label('outstanding_balance', trans('app.form.outstanding_balance'), ['class' => 'with-help']) !!}
            {!! Form::number('outstanding_balance', null, [
                'placeholder' => trans('app.form.outstanding_balance'),
                'class' => 'form-control',
                'required',
                'id' => 'outstanding-balance',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const userSelect = $('#user-select');
        const totalLoan = $('#total-loan');
        const amountPayment = $('#amount-payment');
        const outstandingBalance = $('#outstanding-balance');

        function fetchDataLoan(id) {
            $.ajax({
                url: "{{ route('admin.admin.loan.getLoanAndPaymentData') }}",
                type: 'GET',
                data: {
                    id: id
                },
                success: function(response) {
                    totalLoan.val(response.data[0].sum_amount_loan - response.data[0].sum_amount_loan_payment);
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                }
            });
        }

        function updateOutstandingBalance() {
            const loan = parseFloat(totalLoan.val()) || 0;
            const payment = parseFloat(amountPayment.val()) || 0;
            const grandtotal = loan - payment;
            outstandingBalance.val(grandtotal);
        }

        userSelect.on('change', async function() {
            const selectedId = $(this).val();
            console.log(selectedId, 'selectedId')
            await fetchDataLoan(selectedId);
        });

        amountPayment.on('input', updateOutstandingBalance);

        totalLoan.prop('readonly', true);
        outstandingBalance.prop('readonly', true);
    });
</script>

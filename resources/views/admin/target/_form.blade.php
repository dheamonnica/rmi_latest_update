<div class="row">
    <div class="col-md-4 nopadding-right">
        <div class="form-group">
            {!! Form::label('month', trans('app.form.month'), ['class' => 'with-help']) !!}
            {!! Form::text('month', null, [
                'class' => 'form-control monthpicker',
                'placeholder' => trans('app.form.month'),
                'required',
                isset($target) ? 'disabled' : '' => 'disabled'
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>

    <div class="col-md-4 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('hospital_name', trans('app.form.select_hospital_name') . '*', ['class' => 'with-help']) !!}
            {!! Form::select('hospital_id', ['' => 'Select Client'] + $hospital_name, null, [
                'class' => 'form-control select2-normal',
                'required',
                isset($target) ? 'disabled' : '' => 'disabled'
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>

    <div class="col-md-4 nopadding-left">
        <div class="form-group">
            {!! Form::label('grand_total', trans('app.form.grand_total'), ['class' => 'with-help']) !!}
            {!! Form::text('grand_total', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.grand_total'),
                'required',
            ]) !!}
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.monthpicker').datepicker({
            format: "yyyy-mm",
            startView: "months", 
            minViewMode: "months",
            autoclose: true
        });
    });
</script>
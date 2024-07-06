<div class="row">
    <div class="col-md-4 nopadding-right">
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

    <div class="col-md-4 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('hospital_name', trans('app.form.select_hospital_name') . '*', ['class' => 'with-help']) !!}
            {!! Form::select('hospital_name', ['' => 'Select Client'] + $hospital_name, null, [
                'class' => 'form-control select2-normal',
                'required',
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

<div class="row">
    <div class="col-md-6 nopadding-right">
        <div class="form-group">
            {!! Form::label('start_time', trans('app.form.start_time'), ['class' => 'with-help']) !!}
            {!! Form::text('start_time', null, [
                'placeholder' => trans('app.form.start_time'),
                'class' => 'form-control datetimepicker',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-6 nopadding-left">
        <div class="form-group">
            {!! Form::label('end_time', trans('app.form.end_time'), ['class' => 'with-help']) !!}
            {!! Form::text('end_time', null, [
                'placeholder' => trans('app.form.end_time'),
                'class' => 'form-control datetimepicker',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>
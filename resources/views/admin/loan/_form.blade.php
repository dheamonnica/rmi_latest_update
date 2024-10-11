<div class="row">
    <div class="col-md-6 nopadding-right">
        <div class="form-group">
            {!! Form::label('amount', trans('app.form.amount'), ['class' => 'with-help']) !!}
            {!! Form::number('amount', null, [
                'placeholder' => trans('app.form.amount'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-6 nopadding-left">
        <div class="form-group">
            {!! Form::label('reason', trans('app.form.reason'), ['class' => 'with-help']) !!}
            {!! Form::textarea('reason', null, [
                'placeholder' => trans('app.form.reason'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('name', trans('app.form.name'), ['class' => 'with-help']) !!}
            {!! Form::text('name', null, [
                'placeholder' => trans('app.form.name'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>
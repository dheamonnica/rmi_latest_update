<div class="row">
    <div class="col-md-3">
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

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('hospital_group', trans('app.form.select_hospital_group') . '*', ['class' => 'with-help']) !!}
            {!! Form::select('hospital_group', $hospital_group, null, [
                'class' => 'form-control select2-normal',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('actual_sales', trans('app.form.actual_sales'), ['class' => 'with-help']) !!}
            {!! Form::number('actual_sales', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.actual_sales'),
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-md-3">
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

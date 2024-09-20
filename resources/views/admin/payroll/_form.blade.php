<div class="row">
    <div class="col-md-3 nopadding-right">
        <div class="form-group">
            {!! Form::label('position', trans('app.form.position_'), ['class' => 'with-help']) !!}
            {!! Form::text('position', null, [
                'placeholder' => trans('app.form.position'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-3 nopadding-right nopadding-left">
        <div class="form-group">
            {!! Form::label('grade', trans('app.form.grade'), ['class' => 'with-help']) !!}
            {!! Form::text('grade', null, [
                'placeholder' => trans('app.form.grade'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-3 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('sub_grade', trans('app.form.sub_grade'), ['class' => 'with-help']) !!}
            {!! Form::text('sub_grade', null, [
                'placeholder' => trans('app.form.sub_grade'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-3 nopadding-left">
        <div class="form-group">
            {!! Form::label('level', trans('app.form.level'), ['class' => 'with-help']) !!}
            {!! Form::text('level', null, [
                'placeholder' => trans('app.form.level'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 nopadding-right">
        <div class="form-group">
            {!! Form::label('take_home_pay', trans('app.form.take_home_pay'), ['class' => 'with-help']) !!}
            {!! Form::number('take_home_pay', null, [
                'placeholder' => trans('app.form.take_home_pay'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-3 nopadding-right nopadding-left">
        <div class="form-group">
            {!! Form::label('basic_salary', trans('app.form.basic_salary'), ['class' => 'with-help']) !!}
            {!! Form::number('basic_salary', null, [
                'placeholder' => trans('app.form.basic_salary'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-3 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('operational_allowance', trans('app.form.operational_allowance'), ['class' => 'with-help']) !!}
            {!! Form::number('operational_allowance', null, [
                'placeholder' => trans('app.form.operational_allowance'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-3 nopadding-left">
        <div class="form-group">
            {!! Form::label('position_allowance', trans('app.form.position_allowance'), ['class' => 'with-help']) !!}
            {!! Form::number('position_allowance', null, [
                'placeholder' => trans('app.form.position_allowance'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 nopadding-right">
        <div class="form-group">
            {!! Form::label('child_education_allowance', trans('app.form.child_education_allowance'), ['class' => 'with-help']) !!}
            {!! Form::number('child_education_allowance', null, [
                'placeholder' => trans('app.form.child_education_allowance'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-4 nopadding-right nopadding-left">
        <div class="form-group">
            {!! Form::label('transportation', trans('app.form.transportation'), ['class' => 'with-help']) !!}
            {!! Form::number('transportation', null, [
                'placeholder' => trans('app.form.transportation'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-4 nopadding-left">
        <div class="form-group">
            {!! Form::label('quota', trans('app.form.quota'), ['class' => 'with-help']) !!}
            {!! Form::number('quota', null, [
                'placeholder' => trans('app.form.quota'),
                'class' => 'form-control',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>
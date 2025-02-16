<div class="row">
  <div class="col-md-9 nopadding-right">
    <div class="form-group">
      {!! Form::label('name', trans('app.form.name') . '*', ['class' => 'with-help']) !!}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('app.form.logistic_name'), 'required']) !!}
      {!! Form::hidden('shop_id', Auth::user()->shop_id, ['class' => 'form-control', 'placeholder' => trans('app.form.shop'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>  
  </div>

  <div class="col-md-3 nopadding-left">
    <div class="form-group">
      {!! Form::label('active', trans('app.form.status') . '*', ['class' => 'with-help']) !!}
      {!! Form::select('active', ['1' => trans('app.active'), '0' => trans('app.inactive')], null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.status'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4 nopadding-right">
    <div class="form-group">
      {!! Form::label('email', trans('app.form.email_address')) !!}
      <div class="form-group">
        {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.valid_email')]) !!}
      </div>
      <div class="help-block with-errors"></div>
    </div>
  </div>
  <div class="col-md-4 nopadding-left">
    <div class="form-group">
      {!! Form::label('phone', trans('app.form.phone')) !!}
      <div class="form-group">
        {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.phone_number')]) !!}
      </div>
    </div>
  </div>
  <div class="col-md-4 nopadding-left">
    <div class="form-group">
      {!! Form::label('country_id', trans('app.form.country')) !!}
      {!! Form::select('country_id', $countries, 360, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.country')]) !!}
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4 nopadding-right">
    <div class="form-group">
      {!! Form::label('logistic_pic_name', trans('app.form.logistic_pic_name') . '*', ['class' => 'with-help']) !!}
      {!! Form::text('logistic_pic_name', null, ['class' => 'form-control', 'placeholder' => trans('app.form.logistic_pic_name'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div> 
  </div>
  <div class="col-md-4 nopadding-left nopadding-right">
    <div class="form-group">
      {!! Form::label('logistic_pic_email', trans('app.form.logistic_pic_email') . '*', ['class' => 'with-help']) !!}
      {!! Form::email('logistic_pic_email', null, ['class' => 'form-control', 'placeholder' => trans('app.form.logistic_pic_email'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div> 
  </div>
  <div class="col-md-4 nopadding-left">
    <div class="form-group">
      {!! Form::label('logistic_pic_phone', trans('app.form.logistic_pic_phone') . '*', ['class' => 'with-help']) !!}
      {!! Form::text('logistic_pic_phone', null, ['class' => 'form-control', 'placeholder' => trans('app.form.logistic_pic_phone'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div> 
  </div>
</div>

<p class="help-block">* {{ trans('app.form.required_fields') }}</p>

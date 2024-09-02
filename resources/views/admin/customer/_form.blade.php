<div class="row">
  <div class="col-md-4 nopadding-right">
    <div class="form-group">
      {!! Form::label('title', trans('app.form.title') . '*') !!}
      {!! Form::select('title', ['rsup' => 'RSUP', 'rsud' => 'RSUD', 'rsu' => 'RSU', 'clinic' => 'Clinic'], null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.title'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
  <div class="col-md-4 nopadding-right nopadding-left">
    <div class="form-group">
      {!! Form::label('name', trans('app.form.hospital_name') . '*') !!}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('app.form.hospital_name'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
  <div class="col-md-4 nopadding-left">
    <div class="form-group">
      {!! Form::label('active', trans('app.form.status') . '*') !!}
      {!! Form::select('active', ['1' => trans('app.active'), '0' => trans('app.inactive')], null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.status'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 nopadding-right">
    <div class="form-group">
      {!! Form::label('hospital_group', trans('app.form.hospital_group') . '*') !!}
      {!! Form::text('hospital_group', null, ['class' => 'form-control', 'placeholder' => trans('app.form.hospital_group'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
  <div class="col-md-6 nopadding-left">
    <div class="form-group">
      {!! Form::label('email', trans('app.form.hospital_email') . '*') !!}
      {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('app.form.hospital_email'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
</div>

@if (!isset($customer))
  <div class="form-group">
    {!! Form::label('password', trans('app.form.password') . '*') !!}
    <div class="row">
      <div class="col-md-6 nopadding-right">
        {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => trans('app.placeholder.password'), 'data-minlength' => '6', 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>
      <div class="col-md-6 nopadding-left">
        {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => trans('app.placeholder.confirm_password'), 'data-match' => '#password', 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>
    </div>
  </div>
@endif

<div class="form-group">
  {!! Form::label('description', trans('app.form.description')) !!}
  {!! Form::textarea('description', null, ['class' => 'form-control summernote', 'rows' => '2', 'placeholder' => trans('app.placeholder.description')]) !!}
</div>

<div class="row">
  <div class="col-md-4 nopadding-right">
    <div class="form-group">
      {!! Form::label('hospital_pic_name', trans('app.form.hospital_pic_name') . '*') !!}
      {!! Form::text('hospital_pic_name', null, ['class' => 'form-control', 'placeholder' => trans('app.form.hospital_pic_name'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
  <div class="col-md-4 nopadding-left">
    <div class="form-group">
      {!! Form::label('hospital_pic_phone', trans('app.form.hospital_pic_phone') . '*') !!}
      {!! Form::text('hospital_pic_phone', null, ['class' => 'form-control', 'placeholder' => trans('app.form.hospital_pic_phone'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
  <div class="col-md-4 nopadding-left">
    <div class="form-group">
      {!! Form::label('hospital_pic_email', trans('app.form.hospital_pic_email') . '*') !!}
      {!! Form::email('hospital_pic_email', null, ['class' => 'form-control', 'placeholder' => trans('app.form.hospital_pic_email'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 nopadding-right">
    <div class="form-group">
      {!! Form::label('hospital_tax_name', trans('app.form.hospital_tax_name') . '*') !!}
      {!! Form::text('hospital_tax_name', null, ['class' => 'form-control', 'placeholder' => trans('app.form.hospital_tax_name'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>

  <div class="col-md-6 nopadding-left">
    <div class="form-group">
      {!! Form::label('shop_id', trans('app.form.coverage_area') . '*') !!}
      {!! Form::select('shop_id', $shops, null, ['id' => 'coverage_area', 'class' => 'form-control flat', 'placeholder' => trans('app.form.coverage_area') . '*', 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
</div>

@unless(isset($customer))
  @include('address._form')
@endunless

<div class="form-group">
  <label for="exampleInputFile">{{ trans('app.form.avatar') }}</label>
  @if (isset($customer) && $customer->avatarImage)
    <label>
      <img src="{{ get_avatar_src($customer, 'small') }}" width="" alt="{{ trans('app.avatar') }}">
      <span style="margin-left: 10px;">
        {!! Form::checkbox('delete_image[avatar]', 1, null, ['class' => 'icheck']) !!} {{ trans('app.form.delete_avatar') }}
      </span>
    </label>
  @endif

  <div class="row">
    <div class="col-md-9 nopadding-right">
      <input id="uploadFile" placeholder="{{ trans('app.placeholder.avatar') }}" class="form-control" disabled="disabled" style="height: 28px;" />
    </div>
    <div class="col-md-3 nopadding-left">
      <div class="fileUpload btn btn-primary btn-block btn-flat">
        <span>{{ trans('app.form.upload') }}</span>
        <input type="file" name="images[avatar]" id="uploadBtn" class="upload" />
      </div>
    </div>
  </div>
</div>
<p class="help-block">* {{ trans('app.form.required_fields') }}</p>

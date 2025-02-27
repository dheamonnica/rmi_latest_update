<div class="row">
  <div class="col-md-8 nopadding-right">
    <div class="form-group">
      {!! Form::label('name', trans('app.form.business_name') . '*') !!}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('app.form.business_name'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
  <div class="col-md-4 nopadding-left">
    <div class="form-group">
      {!! Form::label('active', trans('app.form.status') . '*') !!}
      {!! Form::select('active', ['1' => trans('app.active'), '0' => trans('app.inactive')], isset($merchant) ? null : 1, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.status'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 nopadding-right">
    <div class="form-group">
      {!! Form::label('email', trans('app.form.email_address') . '*') !!}
      {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.valid_email'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
  <div class="col-md-6 nopadding-left">
    <div class="form-group">
      {!! Form::label('nice_name', trans('app.form.nice_name') . '*') !!}
      {!! Form::text('nice_name', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.valid_email'), 'required']) !!}
      <div class="help-block with-errors"></div>
    </div>
  </div>
  <div class="col-md-6 nopadding-right">
    <div class="form-group">
      @if (!isset($merchant))
        {!! Form::label('password', trans('app.form.temporary_password') . '*') !!}
        {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => trans('app.placeholder.temporary_password'), 'data-minlength' => '6', 'required']) !!}
        <div class="help-block with-errors"></div>
      @else
        {{-- {!! Form::label('nice_name', trans('app.form.nice_name')) !!}
        {!! Form::text('nice_name', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.nice_name')]) !!} --}}
      @endif
    </div>
  </div>
</div>

@if (is_incevio_package_loaded('otp-login'))
  <div class="row">
    <div class="col-md-12">
      @include('otp-login::phone_field')
    </div>
  </div>
@endif

{{-- @unless (isset($merchant)) --}}
  <div class="row">
    <div class="col-md-6 nopadding-right">
      <div class="form-group">
        {!! Form::label('warehouse_name', trans('app.form.warehouse_name') . '*', ['class' => 'with-help']) !!}
        {{-- <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.warehouse') }}"></i> --}}
        {!! Form::text('warehouse_name', null, ['class' => 'form-control makeSlug', 'placeholder' => trans('app.placeholder.warehouse_name'), 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>
    </div>
    <div class="col-md-6 nopadding-left">
      <div class="form-group">
        {!! Form::label('pic_name', trans('app.form.pic_name') . '*', ['class' => 'with-help']) !!}
        {{-- <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.shop_legal_name') }}"></i> --}}
        {!! Form::text('pic_name', null, ['class' => 'form-control', 'placeholder' => trans('app.form.pic_name'), 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 nopadding-right">
      <div class="form-group">
        {!! Form::label('longitude', trans('app.form.longitude') . '*', ['class' => 'with-help']) !!}
        {!! Form::text('longitude', null, ['class' => 'form-control', 'placeholder' => trans('app.form.longitude'), 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>
    </div>
    <div class="col-md-6 nopadding-left">
      <div class="form-group">
        {!! Form::label('latitude', trans('app.form.latitude') . '*', ['class' => 'with-help']) !!}
        {!! Form::text('latitude', null, ['class' => 'form-control', 'placeholder' => trans('app.form.latitude'), 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>
    </div>
  </div>

  <div class="row" style="display: none">
    <div class="col-md-6 nopadding-right">
      <div class="form-group">
        {!! Form::label('slug', trans('app.form.slug') . '*', ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.shop_slug') }}"></i>
        {!! Form::text('slug', null, ['class' => 'form-control slug', 'placeholder' => trans('app.slug'), 'required']) !!}
        <div class="help-block with-errors"></div>
      </div>
    </div>

    <div class="col-md-6 nopadding-left">
      <div class="form-group">
        {!! Form::label('external_url', trans('app.form.external_url'), ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.shop_external_url') }}"></i>
        {!! Form::text('external_url', null, ['class' => 'form-control', 'placeholder' => trans('app.placeholder.external_url')]) !!}
      </div>
    </div>
  </div>
{{-- @endunless --}}

{{-- @if (isset($merchant))
  <div class="row">
    <div class="col-md-6 nopadding-right">
      <div class="form-group">
        {!! Form::label('dob', trans('app.form.dob')) !!}
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          {!! Form::text('dob', null, ['class' => 'form-control datepicker', 'placeholder' => trans('app.placeholder.dob')]) !!}
        </div>
      </div>
    </div>
    <div class="col-md-6 nopadding-left">
      <div class="form-group">
        {!! Form::label('sex', trans('app.form.sex')) !!}
        {!! Form::select('sex', ['app.male' => trans('app.male'), 'app.female' => trans('app.female'), 'app.other' => trans('app.other')], null, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.sex')]) !!}
      </div>
    </div>
  </div>
@endif --}}

<div class="form-group">
  {!! Form::label('description', trans('app.form.description')) !!}
  {!! Form::textarea('description', null, ['class' => 'form-control summernote', 'rows' => '2', 'placeholder' => trans('app.placeholder.description')]) !!}
</div>

<div class="form-group">
  <label for="exampleInputFile">{{ trans('app.form.avatar') }}</label>
  @if (isset($merchant) && $merchant->avatarImage)
    <label>
      <img src="{{ get_avatar_src($merchant, 'small') }}" alt="{{ trans('app.avatar') }}">
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

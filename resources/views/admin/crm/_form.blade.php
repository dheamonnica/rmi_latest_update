<div class="row">
    <div class="col-md-3 nopadding-right">
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
            {!! Form::label('client_id', trans('app.form.client') . '*', ['class' => 'with-help']) !!}
            {!! Form::select('client_id', ['' => 'Select Client'] + $customers, null, [
                'class' => 'form-control select2-normal',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>

    <div class="col-md-5 nopadding-left">
        {!! Form::label('photo', trans('app.form.photo') . '*', ['class' => 'with-help']) !!}

        <div class="row">
            <div class="col-md-9 nopadding-right">
                <input id="uploadFile" placeholder="{{ trans('app.placeholder.image') }}" class="form-control"
                    disabled="disabled" style="height: 28px;" />
            </div>
            <div class="col-md-3 nopadding-left">
                <div class="fileUpload btn btn-primary btn-block btn-flat">
                    <span>{{ trans('app.form.upload') }}</span>
                    <input type="file" name="images[picture]" id="uploadBtn" class="upload" />
                </div>
            </div>
        </div>
    </div>
</div>

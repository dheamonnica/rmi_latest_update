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
            {!! Form::label('requirement', trans('app.form.requirement'), ['class' => 'with-help']) !!}
            {!! Form::text('requirement', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.requirement'),
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('qty', trans('app.form.quantity'), ['class' => 'with-help']) !!}
            {!! Form::number('qty', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.quantity'),
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('total', trans('app.form.total'), ['class' => 'with-help']) !!}
            {!! Form::text('total', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.total'),
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-md-12">
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

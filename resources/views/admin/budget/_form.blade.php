<div class="row">
    <div class="col-md-2 nopadding-right">
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
            {!! Form::label('requirement', trans('app.form.requirement'), ['class' => 'with-help']) !!}
            {!! Form::text('requirement', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.requirement'),
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-md-2 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('category', trans('app.form.category'), ['class' => 'with-help']) !!}
            {!! Form::select('category', ['1' => 'Sallary', '2' => 'Additional'], null, [
                'class' => 'form-control select2-normal',
                'placeholder' => trans('app.placeholder.status'),
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-md-2 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('qty', trans('app.form.quantity'), ['class' => 'with-help']) !!}
            {!! Form::number('qty', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.quantity'),
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-md-2 nopadding-left">
        <div class="form-group">
            {!! Form::label('total', trans('app.form.total'), ['class' => 'with-help']) !!}
            {!! Form::number('total', null, [
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
                <div class="help-block with-errors"></div>
            </div>
            <div class="col-md-3 nopadding-left">
                <div class="fileUpload btn btn-primary btn-block btn-flat">
                    <span>{{ trans('app.form.upload') }}</span>
                    <input type="file" name="images[picture]" id="uploadBtn" class="upload"
                        {{ isset($budget) ? '' : 'required' }} />
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 nopadding-right">
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

    <div class="col-md-4 nopadding-left">
        <div class="form-group">
            {!! Form::label('assignee_user_id', trans('app.form.assignee') . '*', ['class' => 'with-help']) !!}
            {!! Form::select('assignee_user_id', ['' => 'Select Assignee'] + $users, null, [
                'class' => 'form-control select2-normal',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>

    @if (isset($visit))
    <div class="col-md-5 nopadding-right">
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

    <div class="col-md-4 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('note', trans('app.form.note'), ['class' => 'with-help']) !!}
            {!! Form::text('note', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.note'),
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-md-3 nopadding-left">
        <div class="form-group">
            {!! Form::label('next_visit_date', trans('app.form.next_visit_date'), ['class' => 'with-help']) !!}
            {!! Form::text('next_visit_date', null, [
                'class' => 'form-control datepicker',
                'placeholder' => trans('app.form.next_visit_date'),
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    @endif
</div>

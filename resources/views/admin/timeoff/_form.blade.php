<div class="row">
    <div class="col-md-3 nopadding-right">
        <div class="form-group">
            {!! Form::label('type', trans('app.form.leave_type')) !!}
            {!! Form::select(
                'type',
                [
                    'cuti_menikah' => 'Cuti Menikah (special leave)',
                    'cuti_menikahkan_anak' => 'Cuti Menikahkan Anak (special leave)',
                    'cuti_khitanan_anak' => 'Cuti Khitanan Anak (special leave)',
                    'cuti_baptis_anak' => 'Cuti Baptis Anak (special leave)',
                    'cuti_istri_melahirkan' => 'Cuti Istri Melahirkan/Keguguran (special leave)',
                    'cuti_keluarga_meninggal' => 'Cuti Keluarga Meninggal (special leave)',
                    'cuti_anggota_keluarga_meninggal' => 'Cuti Anggota Keluarga Dalam Satu Rumah Meninggal (special leave)',
                    'cuti_melahirkan' => 'Cuti Melahirkan (special leave)',
                    'cuti_haid' => 'Cuti Haid (special leave)',
                    'cuti_keguguran' => 'Cuti Keguguran (special leave)',
                    'cuti_ibadah_haji' => 'Cuti Ibadah Haji (special leave)',
                ],
                null,
                ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.leave_type')],
            ) !!}
        </div>
    </div>
    <div class="col-md-3 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('start_date', trans('app.form.start_date'), ['class' => 'with-help']) !!}
            {!! Form::text('start_date', null, [
                'placeholder' => trans('app.form.start_date'),
                'class' => 'form-control datepicker',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-3 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('end_date', trans('app.form.end_date'), ['class' => 'with-help']) !!}
            {!! Form::text('end_date', null, [
                'placeholder' => trans('app.form.end_date'),
                'class' => 'form-control datepicker',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-3 nopadding-left">
        <div class="form-group">
            {!! Form::label('notes', trans('app.form.notes'), ['class' => 'with-help']) !!}
            {!! Form::textarea('notes', null, [
                'placeholder' => trans('app.form.notes'),
                'class' => 'form-control',
                'required',
            ]) !!}
        </div>
    </div>
</div>

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

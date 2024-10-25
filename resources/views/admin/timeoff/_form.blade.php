<div class="row">
    <div class="col-md-2 nopadding-right">
        <div class="form-group">
            {!! Form::label('category', trans('app.form.category')) !!}
            {!! Form::select(
                'category',
                [
                    'annual_leave' => 'Cuti Tahunan',
                    'special_leave' => 'Cuti Spesial',
                    'sick_leave' => 'Cuti Sakit',
                ],
                null,
                ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.category'), 'id' => 'category'],
            ) !!}
        </div>
    </div>
    <div class="col-md-3 nopadding-right nopadding-left" id="type">
        <div class="form-group">
            {!! Form::label('type', trans('app.form.leave_type')) !!}
            {!! Form::select(
                'type',
                [
                    'cuti_menikah' => 'Cuti Menikah',
                    'cuti_menikahkan_anak' => 'Cuti Menikahkan Anak',
                    'cuti_khitanan_anak' => 'Cuti Khitanan Anak',
                    'cuti_baptis_anak' => 'Cuti Baptis Anak',
                    'cuti_istri_melahirkan' => 'Cuti Istri Melahirkan/Keguguran',
                    'cuti_keluarga_meninggal' => 'Cuti Keluarga Meninggal',
                    'cuti_anggota_keluarga_meninggal' => 'Cuti Anggota Keluarga Dalam Satu Rumah Meninggal',
                    'cuti_melahirkan' => 'Cuti Melahirkan',
                    'cuti_haid' => 'Cuti Haid',
                    'cuti_keguguran' => 'Cuti Keguguran',
                    'cuti_ibadah_haji' => 'Cuti Ibadah Haji',
                ],
                null,
                ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.leave_type')],
            ) !!}
        </div>
    </div>
    <div class="col-md-2 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('start_date', trans('app.form.start_date'), ['class' => 'with-help']) !!}
            {!! Form::text('start_date', null, [
                'placeholder' => trans('app.form.start_date'),
                'class' => 'form-control datepicker',
                'id' => 'start-date',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-2 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('end_date', trans('app.form.end_date'), ['class' => 'with-help']) !!}
            {!! Form::text('end_date', null, [
                'placeholder' => trans('app.form.end_date'),
                'class' => 'form-control datepicker',
                'id' => 'end-date',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-2 nopadding-left nopadding-right">
        <div class="form-group">
            {!! Form::label('total_days', trans('app.form.total_days'), ['class' => 'with-help']) !!}
            {!! Form::text('total_days', null, [
                'placeholder' => trans('app.form.total_days'),
                'class' => 'form-control datepicker',
                'id' => 'total-days',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-9">
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
            <input type="file" name="images[picture]" id="uploadBtn" class="upload" />
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const category = $('#category');
        const type = $('#type');
        const start_date = $('#start-date');
        const end_date = $('#end-date');
        const total_days = $('#total-days');

        async function calculateDays() {
            const startDate = new Date(document.getElementById('start-date').value);
            const endDate = new Date(document.getElementById('end-date').value);

            if (!isNaN(startDate) && !isNaN(endDate) && endDate >= startDate) {
                let weekdays = 0;
                const currentDate = new Date(startDate);

                while (currentDate <= endDate) {
                    const dayOfWeek = currentDate.getUTCDay();
                    // Skip Saturdays (6) and Sundays (0)
                    if (dayOfWeek !== 6 && dayOfWeek !== 0) {
                        weekdays++;
                    }
                    currentDate.setUTCDate(currentDate.getUTCDate() + 1); // Move to next day
                }

                document.getElementById('total-days').value = weekdays;
            } else {
                document.getElementById('total-days').value = ''; // Reset if dates are invalid
            }
        }

        // Add event listener for changes in the end_date dropdown
        end_date.on('change', async function() {
            await calculateDays();
        });

        category.on('change', function() {
            const selectedId = $(this).val();
            if (selectedId === "special_leave") {
                type.show();
                type.style.display = "block";
            } else {
                type.hide();
            }
        })
    });
</script>

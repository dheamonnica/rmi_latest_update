<div class="row">
    {!! Form::hidden('warehouse_id', Auth::user()->merchantId()) !!}
    {!! Form::hidden('sum_total_days', $timeoff_user_annual_leave->sum_total_days, ['id' => 'sumTotalDays']) !!}
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
            <div class="help-block with-errors end-date"></div>
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
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd', // Set the format you need
        startDate: '0d', // Disable past dates
        todayHighlight: true // Highlight today
    });

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
                    // Skip only Sundays (0)
                    if (dayOfWeek !== 0) {
                        weekdays++;
                    }
                    currentDate.setUTCDate(currentDate.getUTCDate() + 1); // Move to next day
                }

                document.getElementById('total-days').value = weekdays;
            } else {
                document.getElementById('total-days').value = ''; // Reset if dates are invalid
            }
        }

        const sumTotalDays = parseInt(document.getElementById('sumTotalDays').value, 10);
        const sumTotalDaysUsed = sumTotalDays - 12;
        const sumTotalDaysRemaining = 12 - sumTotalDays;

        end_date.on('change', async function() {
            await calculateDays();

            if (total_days.val() > sumTotalDaysRemaining) {
                console.log('gabisa');
                $('#submit-button').prop('disabled', true); // Disable submit button
                $('.help-block.with-errors.end-date').css('color', 'red').text(
                    `Warning: Remaining days exceed allowed limit of ${sumTotalDaysRemaining} days.`
                ); // Add warning message for end_date only with red color
            } else {
                $('#submit-button').prop('disabled', false); // Enable submit button
                $('.help-block.with-errors.end-date').text(
                    ''); // Clear warning message if condition is not met
            }
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

<style>
    .datepicker table tr td.disabled,
    .datepicker table tr td.disabled:hover {
        background: #eae5e5;
        color: #444;
        cursor: default;
    }

    .datepicker table tr td.today,
    .datepicker table tr td.today:hover,
    .datepicker table tr td.today.disabled,
    .datepicker table tr td.today.disabled:hover {
        color: green;
        background: #bdd3bd;
        /* border-color: #ffb733; */
    }
</style>

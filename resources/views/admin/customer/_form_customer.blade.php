@php
    $indices = range(0, count($positions) - 1); // Array of indices based on the number of positions
@endphp

@foreach ($indices as $index)
    <div class="row">
        {!! Form::hidden("pic[{$index}][customer_id]", $hospital->id) !!}
        {!! Form::hidden("pic[{$index}][created_by]", Auth::user()->id) !!}
        {!! Form::hidden("pic[{$index}][created_at]", now()) !!}
        {!! Form::hidden("pic[{$index}][updated_at]", null) !!}

        <div class="col-md-3 nopadding-right">
            <div class="form-group">
                {!! Form::label("pic[{$index}][name]", trans('app.form.position_')) !!}
                {!! Form::text("pic[{$index}][name]", $positions[$index], [
                    'class' => 'form-control',
                    'readonly' => false, // Set as readonly if you don't want users to change this
                ]) !!}
                <div class="help-block with-errors"></div>
            </div>
        </div>

        <div class="col-md-3 nopadding-left nopadding-right">
            <div class="form-group">
                {!! Form::label("pic[{$index}][value]", trans('app.form.full_name')) !!}
                {!! Form::text("pic[{$index}][value]", '', [
                    'class' => 'form-control',
                ]) !!}
                <div class="help-block with-errors"></div>
            </div>
        </div>

        <div class="col-md-3 nopadding-left nopadding-right">
            <div class="form-group">
                {!! Form::label("pic[{$index}][phone]", trans('app.form.phone')) !!}
                {!! Form::text("pic[{$index}][phone]", '', [
                    'class' => 'form-control',
                ]) !!}
                <div class="help-block with-errors"></div>
            </div>
        </div>

        <div class="col-md-3 nopadding-left">
            <div class="form-group">
                {!! Form::label("pic[{$index}][email]", trans('app.form.email')) !!}
                {!! Form::email("pic[{$index}][email]", '', [
                    'class' => 'form-control',
                ]) !!}
                <div class="help-block with-errors"></div>
            </div>
        </div>
    </div>
@endforeach

@foreach ($data as $index => $item)
<div class="row">
    {!! Form::hidden("pic[$index][id]", $item->id) !!}
    {!! Form::hidden("pic[$index][customer_id]", $item->customer_id) !!}
    {!! Form::hidden("pic[$index][updated_by]", Auth::user()->id) !!}
    {!! Form::hidden("pic[$index][updated_at]", now()) !!}
    
    <div class="col-md-3 nopadding-right">
        <div class="form-group">
            {!! Form::label("pic[$index][name]", trans('app.form.position_')) !!}
            {!! Form::text("pic[$index][name]", $item->name, [
                'class' => 'form-control',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>

    <div class="col-md-3 nopadding-left">
        <div class="form-group">
            {!! Form::label("pic[$index][value]", trans('app.form.full_name')) !!}
            {!! Form::text("pic[$index][value]", $item->value, [
                'class' => 'form-control',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>

    <div class="col-md-3 nopadding-left">
        <div class="form-group">
            {!! Form::label("pic[$index][phone]", trans('app.form.phone')) !!}
            {!! Form::text("pic[$index][phone]", $item->phone, [
                'class' => 'form-control',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>

    <div class="col-md-3 nopadding-left">
        <div class="form-group">
            {!! Form::label("pic[$index][email]", trans('app.form.email')) !!}
            {!! Form::email("pic[$index][email]", $item->email, [
                'class' => 'form-control',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>
@endforeach

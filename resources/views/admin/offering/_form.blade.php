<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('product_id', trans('app.form.select_product') . '*', ['class' => 'with-help']) !!}
            {!! Form::select('product_id', $product, null, [
                'class' => 'form-control select2-normal',
                'required',
            ]) !!}
            <div class="help-block with-errors"></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('small_quantity', trans('app.form.small_quantity'), ['class' => 'with-help']) !!}
            {!! Form::text('small_quantity_price', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.small_quantity_price'),
            ]) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('medium_quantity', trans('app.form.medium_quantity'), ['class' => 'with-help']) !!}
            {!! Form::text('medium_quantity_price', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.medium_quantity_price'),
            ]) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('large_quantity', trans('app.form.large_quantity'), ['class' => 'with-help']) !!}
            {!! Form::text('large_quantity_price', null, [
                'class' => 'form-control',
                'placeholder' => trans('app.form.large_quantity_price'),
            ]) !!}
        </div>
    </div>
</div>

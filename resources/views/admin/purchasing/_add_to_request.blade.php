<div class="row add-to-request-option">
  <div class="col-md-9 nopadding input-lg">
    {!! Form::select('product', $products, null, ['id' => 'product-to-request', 'class' => 'form-control select2', 'placeholder' => trans('app.placeholder.choose_product')]) !!}
  </div>
  <div class="col-md-3 nopadding">
    <button class="btn btn-lg bg-purple btn-block" id="add-to-request-btn">{{ trans('app.add_to_request') }}</button>
  </div>
</div>
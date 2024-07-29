<div class="modal-dialog modal-sm">
  <div class="modal-content">
    {!! Form::open(['route' => ['admin.order.deliveryboy.assign', $order], 'method' => 'post', 'id' => 'form', 'data-toggle' => 'validator']) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      {{ trans('app.assign_deliveryboy') }}
    </div>
    <div class="modal-body">
      {!! Form::select('delivery_boy_id', $deliveryBoysUser, $order->delivery_boy_id, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.select'), 'required']) !!}

      <div class="form-group">
        {!! Form::label('images', trans('app.form.image_uploads') . '*', ['class' => 'with-help']) !!}
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.order_fulfillment_carrier') }}"></i>
        <div class="file-loading">
            <input id="dropzone-input" name="images[]" type="file" accept="image/*" multiple>
        </div>
          <span class="small"><i class="fa fa-info-circle"></i> {{ trans('help.multi_img_upload_instruction', ['size' => getAllowedMaxImgSize(), 'number' => 2, 'dimension' => '800 x 800']) }}</span>
        <div class="help-block with-errors"></div>
      </div>
    </div>
    <div class="modal-footer">
      {!! Form::submit(trans('app.form.proceed'), ['class' => 'btn btn-flat btn-new']) !!}
    </div>
    {!! Form::close() !!}
  </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->

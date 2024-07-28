<div class="modal-dialog modal-sm">
	<div class="modal-content">
	  {!! Form::model($order, ['method' => 'PUT', 'route' => ['admin.order.order.deliveredConfirmed', $order->id], 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		{{ trans('app.fulfill_order') }}
	  </div>
	  <div class="modal-body">
  
		<div class="form-group">
		  {!! Form::label('images', trans('app.form.image_uploads') . '*', ['class' => 'with-help']) !!}
		  <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.order_fulfillment_carrier') }}"></i>
		  <div class="file-loading">
			  {{-- <input id="dropzone-input" name="images[]" type="file" accept="image/*" multiple> --}}
			  <input type="file" name="images" id="uploadBtn" class="upload" />
		  </div>
			<span class="small"><i class="fa fa-info-circle"></i> {{ trans('help.multi_img_upload_instruction', ['size' => getAllowedMaxImgSize(), 'number' => 1, 'dimension' => '800 x 800']) }}</span>
		  <div class="help-block with-errors"></div>
		</div>
		<div class="form-group">
		  {!! Form::label('images', trans('app.form.receiver_name') . '*', ['class' => 'with-help']) !!}
		  <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.order_fulfillment_carrier') }}"></i>
		  {!! Form::text('receiver_name', "", ['class' => 'form-control', 'placeholder' => trans('app.form.receiver_name'), 'required']) !!}
		</div>
		<div class="form-group">
		  {!! Form::label('digitalSign', trans('app.form.digital_sign') . '*', ['class' => 'with-help']) !!}
		  <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.order_fulfillment_carrier') }}"></i>
		  <br>
		  <div class="text-right">
			<button type="button" class="btn btn-danger btn-xs" id="clear"><i class="fa fa-eraser"></i> Clear</button>
		  </div>
		  <div class="border text-center" style="border: 1px"><canvas id="signature-pad" class="signature-pad" width="auto" height="200"></canvas></div>
		  <div class="sign">
			{{-- <button type="button" class="btn btn-primary btn-sm" id="sign-as-delivered">Sign as Delivered</button> --}}
		  </div>
		</div>
  
		<p class="help-block">* {{ trans('app.form.required_fields') }}</p>
	  </div>
	  <div class="modal-footer">
		{!! Form::submit(trans('app.form.update'), ['class' => 'btn btn-flat btn-new submit-delivered', 'id' => 'submit-delivered']) !!}
	  </div>
	  {!! Form::close() !!}
	</div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->
  <script>
	var canvas = document.getElementById('signature-pad');
  var signaturePad = new SignaturePad(canvas, {
	backgroundColor: 'rgb(255, 255, 245)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
  });
  document.getElementById('submit-delivered').addEventListener('click', function () {
	if (signaturePad.isEmpty()) {
	  alert("Tanda Tangan Anda Kosong! Silahkan tanda tangan terlebih dahulu.");
	}else{
	  var data = signaturePad.toDataURL('image/png');
	  console.log(data);
	  $('.sign').html('<textarea id="signature64" name="signed" style="display:none">'+data+'</textarea>');
	}
  });
  document.getElementById('clear').addEventListener('click', function () {
	signaturePad.clear();
  });
  </script>
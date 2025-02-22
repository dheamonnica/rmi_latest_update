@php
  $title_classes = isset($purchasing) ? 'form-control' : 'form-control makeSlug';
@endphp
@csrf
<div class="row">
    <div class="col-md-8">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ isset($purchasing) ? trans('app.update_request_purchasing') : trans('app.create_new_purchasing') }}</h3>
          <div class="box-tools pull-right">
            @if (!isset($purchasing))
              {{-- <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.upload') }}" class="ajax-modal-btn btn btn-default btn-flat">{{ trans('app.bulk_import') }}</a> --}}
            @endif
          </div>
        </div> <!-- /.box-header -->
        <div class="box-body">
			@include('admin.purchasing._add_to_request')

			{{-- @include('admin.order._cart') --}}
			<div class="row">
				<div class="col-md-12">
					  <table class="table table-sripe">
						<tbody id="items">
						</tbody>
					</table>
				  </div>
			</div>

			<hr>

			<div class="row">
				<div class="col-md-6">
					<div class="form-group pt-5">
						{!! Form::label('admin_note', trans('app.form.admin_note'), ['class' => 'with-help']) !!}
						<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.admin_note') }}"></i>
						{!! Form::textarea('admin_note', isset($cart->admin_note) ? $cart->admin_note : null, ['class' => 'form-control summernote-without-toolbar', 'rows' => '2', 'placeholder' => trans('app.placeholder.admin_note')]) !!}
					</div>
				</div>
				<div class="col-md-6" id="summary-block">
					<table class="table">
						<tr>
							<td class="text-right">{{ trans('app.quantity_total') }}</td>
							<td class="text-right" width="40%">
							  <span id="summary-qty-total">{{ get_formated_decimal(0, true, 0) }}&nbsp;Pc</span>
							</td>
						  </tr>

						  <tr>
							<td class="text-right">{{ trans('app.product_total') }}</td>
							<td class="text-right" width="40%">
							  <span id="summary-product-total">{{ get_formated_decimal(0, true, 0) }}&nbsp;Item</span>
							</td>
						  </tr>
					</table>
				</div>
			</div>
			<hr>

			{{-- {{ Form::hidden('shop_depature_id', $shop_depature_id, ['id' => 'shipping_rate_id']) }}
			{{ Form::hidden('shop_arrival_id', $shop_arrival_id, ['id' => 'shipping_rate_id']) }}
			{{ Form::hidden('movement_number', $movement_number, ['id' => 'movement_number']) }} --}}

			<hr>

			<div class="box-tools pull-right">
				<button name='action' type="submit" class='btn btn-flat btn-lg btn-new action-submit'>
					{{ trans('app.request_purchasing') }}
				</button>
			</div>
		</div>
	  </div>
	</div>

	@if (auth()->user()->merchantId())
		<div class="col-md-3 nopadding-left">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title"> {{ trans('app.alamat_penerimaan') }}</h3>
				</div> <!-- /.box-header -->
				<div class="box-body">
				<div class="form-group">
					{{-- {{ auth()->user()->merchantId() }} --}}
				</div>
				</div>	
			</div>
		</div>
	@endif
</div>
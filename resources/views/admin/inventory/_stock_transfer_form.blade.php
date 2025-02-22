@extends('admin.layouts.master')

@section('content')


<div class="row">

	{!! Form::open(['route' => 'admin.stock.inventory.storeStockTransfer', 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}
	@php
		$movement_number = get_formated_movement_number($shop_depature_id, $shop_arrival_id);
	@endphp

	<div class="col-md-9">
		<div class="box">
		  <div class="box-header with-border">
			<h3 class="box-title"><i class="fa fa-cart-plus"></i> {{ trans('app.stock_transfer') }} {{ $movement_number }}</h3>
		  </div> <!-- /.box-header -->
		  <div class="box-body">
				@include('admin.inventory._add_to_transfer')

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
						<div class="col-md-6">
							Warehouse From {{ $shop_depature->name }}
		
						</div>
		
						<div class="col-md-6">
							Warehouse Destination {{ $shop_arrival->name }}
		
						</div>
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

				{{ Form::hidden('shop_depature_id', $shop_depature_id, ['id' => 'shipping_rate_id']) }}
				{{ Form::hidden('shop_arrival_id', $shop_arrival_id, ['id' => 'shipping_rate_id']) }}
				{{ Form::hidden('movement_number', $movement_number, ['id' => 'movement_number']) }}

				<hr>

				<div class="box-tools pull-right">
					<button name='action' type="submit" class='btn btn-flat btn-lg btn-new action-submit'>
						{{ trans('app.transfer_stock') }}
					</button>
				</div>
			</div>
		</div>	
	</div>
	<div class="col-md-3 nopadding-left">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title"> {{ trans('app.transfer_type') }}</h3>
			</div> <!-- /.box-header -->
			<div class="box-body">
				<div class="form-group">
					{!! Form::label('transfer_type', trans('app.form.transfer_type') . '*') !!}
					{!! Form::select('transfer_type', [
						'delivery' => 'Delivery'
					], isset($cart->transfer_type) ? $cart->transfer_type : config('shop_settings.default_transfer_type'), ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.payment'), 'required']) !!}
					<div class="help-block with-errors"></div>
				</div>
			</div>	
		</div>
	</div>
	{!! Form::close() !!}
</div>
@endsection

@section('page-script')
<script language="javascript" type="text/javascript">

var productObj = <?= json_encode($inventories) ?>;

$('body').on('change', '.itemQtt', function() {
	var itemId = $(this).closest('tr').attr('id');
	calculateTransferTotal();
	// calculateItemTotal(itemId);
});

$('body').on('click', '.deleteThisRow', function() {
	var itemId = $(this).closest('tr').attr('id');
	deleteThisRow(itemId);
});

$('#add-to-cart-btn').click(
	function() {
		if($("#product-to-add").select2('data')[0].id){
			var ID = $("#product-to-add").select2('data')[0].id;
			if(productObj[ID].stockQtt === 0) {
				$("#global-alert-msg").html('{{ trans('messages.notice.out_of_stock') }}');
				$("#global-alert-box").removeClass('hidden');
			} else {
				var itemDescription = $("#product-to-add").select2('data')[0].text;

				var isPartial = "0";

				if (ID == '' || itemDescription == '') {
					return false;
				} else {
					$("#empty-cart").hide(); // Hide the empty cart message
				}

				$("#product-to-add").select2("val", ""); // Reset the product search dropdown

				// Check if the product is already on the cart, Is so then just increase the qtt
				if ($("tr#" + ID).length) {
					increaseQttByOne(ID);
					return;
				}

				//Pick the string after the - to get the item description
				// itemDescription = itemDescription.substring(itemDescription.indexOf(" - ") - 1);
				// Find the first occurrence of " - "
				var indexOfSeparator = itemDescription.indexOf(") - ");
				// Check if the separator exists
				if (indexOfSeparator != -1) {
					// Extract the substring from the beginning to before the separator
					itemDescription = itemDescription.substring(0, indexOfSeparator + 2);
				}

				var imgSrc = getFromPHPHelper('get_product_img_src', ID, 'tiny');

				var numOfRows = $("tbody#items tr").length;

				var dateOfferAvailable = productObj[ID].dateNow > productObj[ID].offerStart && productObj[ID].dateNow < productObj[ID].offerEnd;
				// var isOfferAvailable = dateOfferAvailable ? 'Offer Available' : 'Offer Unavailable';
				var price = productObj[ID].offerPrice > 0 ? productObj[ID].offerPrice : productObj[ID].salePrice;
				//product item added
				var node = '<tr id="' + ID + '">' +
					'<td><img src="' + imgSrc + '" class="img-circle img-md" alt="{{ trans('app.image') }}"></td>' +
					'<td class="nopadding-right" width="55%">' + itemDescription +
					'<input type="hidden" name="product[' + numOfRows + '][inventory_id]" value="' + ID + '"></input>' +
					'<input type="hidden" name="product[' + numOfRows + '][product_id]" value="' + productObj[ID].product_id + '"></input>' +
					'<input type="hidden" name="product[' + numOfRows + '][stock_quantity]" value="' + productObj[ID].stockQtt + '" id="stock-' + ID + '" class="itemStock"></input>' +
					'</td>' +
					'<td class="nopadding-right text-center" width="10%">' +
					'<span>transfer qty</span>' +
					'<input name="product[' + numOfRows + '][transfer_quantity]" value="1" type="number" max="' + productObj[ID].stockQtt + '" id="qtt-' + ID + '" class="form-control itemQtt no-border" placeholder="{{ trans('app.quantity') }}" required>' +
					'</td>' +
					'<td class="small"><i class="fa fa-trash text-muted deleteThisRow" data-toggle="tooltip" data-placement="left" title="{{ trans('help.remove_this_product_item') }}"></i></td>' +
					'</tr>';

					/**
					 * '<input name="product[' + numOfRows + '][transfer_quantity]" value="1" max="' + productObj[ID].stockQtt + '" type="number" id="qtt-' + ID + '" class="form-control itemQtt no-border" placeholder="{{ trans('app.quantity') }}" required>' +
					 */

				$('tbody#items').append(node);

				calculateTransferTotal();

				return false; //Return false to prevent unspected form submition
			}
		}
		$("#global-alert-msg").html('{{ trans('messages.notice.please_choose_at_least_one_product') }}');
		$("#global-alert-box").removeClass('hidden');

		return false;
		//
	}
		
	);

	 /**
       * This function will need in front end
       */

	   function calculateTransferTotal() {
        var sumQty = 0;
        var sumItem = 0;

        $(".itemQtt").each(
          function() {
            sumQty += ($(this).val()) * 1;
			sumItem ++;
          }
        );

        $("#summary-qty-total").text(sumQty + ' ' + (sumItem > 1 ? 'Pcs' : 'Pc'));
        $("#summary-product-total").text(sumItem + ' ' + (sumItem > 1 ? 'Items' : 'Item'));

        return;
      };

	function increaseQttByOne(ID) {
        var qtt = $("#qtt-" + ID).val();
        $("#qtt-" + ID).val(++qtt);

		calculateTransferTotal();
        return true;
    };

	function getFormatedValue(value = 0) {
        value = value ? value : 0;
        return parseFloat(value).toFixed(2);
    }

	      // Remove table rows
	function deleteThisRow(ID) {
        $("tr#" + ID).remove();
        if ($("tbody#items tr").length <= 1) {
          $("#empty-cart").show(); // Show the empty cart message
        }

		calculateTransferTotal();
        return;
      };
</script>
@endsection
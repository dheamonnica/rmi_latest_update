@extends('admin.layouts.master')

@section('content')
<div class="row">
    <div class="col-md-8">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">
					<i class="fa fa-shopping-cart"></i> {{ trans('app.form.manufacture_numbers') . ': ' . $purchasing->items[0]->manufacture_number }}
				  </h3>
			  <div class="box-tools pull-right">
				{{ get_purchasing_status_name($purchasing->transfer_status) }}
			  </div>
			</div> <!-- /.box-header -->
	
			<div class="box-body">
				{!! Form::open(['route' => ['admin.purchasing.purchasing.setShippingStatus', $purchasing->id], 'method' => 'POST', 'class' => 'inline']) !!}
				<div class="row">
				  <div class="col-sm-12">
					<div class="well well-lg">
					  <span class="lead">
						{{ trans('app.form.purchasing_date') . ': '. date('d-m-Y G:i:s')}}
					  </span>
  
					</div>
				  </div>
				</div><!-- /.row -->
			  
			  <div class="row">
				<div class="col-md-12">
				  <h4>{{ trans('app.purchasing_order_details') }}
				  </h4>
				  <span class="spacer10"></span>
				  <table class="table table-sripe">
					<tbody id="items">
						<thead>
							<tr>
							  <th>{{ trans('app.product_name') }}</th>
							  <th>{{ trans('app.manufacture') }}</th>
							  <th>{{ trans('app.request_quantity') }}</th>
							  <th>{{ trans('app.price') }} (IDR)<small>( {{ trans('app.excl_tax') }} )</small> </th>
							  <th>{{ trans('app.action') }}</th>
							</tr>
						  </thead>
						  <tbody>
							{{-- grouping the product --}}
							@foreach ($purchasing->itemGroups as $items)
							<tr>
								<td>{{ $items->product->name}}</td>
								<td>{{ $items->manufacture->name ?? '-'}}</td>
								<td>{{ $items->request_quantity ?? '0'}}</td>
								{{-- <td>{{ $items->price ?? '0'}}</td> --}}
								<td class="nopadding-right text-center" width="20%">
									{{ Form::hidden('product['.$items->product_id.'][product_id]', $items->product_id) }}
									{{ Form::hidden('product['.$items->product_id.'][purchasing_order_id]', $items->purchasing_order_id) }}
									<input name="product[{{$items->product_id}}][price]" value="{{ $items->price ?? 0 }}" type="number" class="form-control itemQtt no-border" placeholder="{{ trans('app.price') }}" required>
								</td>

								{{-- can change price --}}
								<td>{{ get_purchasing_status_name($items->shipment_status)}}
								</td>
								<tr>
							@endforeach
					</tbody>
				  </table>
			    </div>
			  </div><!-- /.row -->
			  <div class="row">
				<div class="col-md-6">
					<dir class="spacer30"></dir>
					@if ($purchasing->admin_note)
					  {{ trans('app.admin_note') }}:
	  
					  @can('fulfill', $purchasing)
						<a href="javascript:void(0)" data-link="{{ route('admin.purchasing.purchasing.adminNote', $purchasing) }}" class='ajax-modal-btn btn btn-link'>
						  {{ trans('app.edit') }}
						</a>
					  @endcan
	  
					  <blockquote>
						{!! $purchasing->admin_note !!}
					  </blockquote>
					@endif
				</div>
				<div class="col-md-6" id="summary-block">
					<table class="table">
						 <tr>
							<td class="text-right">{{ trans('app.price_total') }}</td>
							<td class="text-right" width="40%">
							  <span id="summary-price-total">{{ number_format($purchasing->items->sum('price'), 0, '.', ',') }}&nbsp;IDR</span>
							</td>
						  </tr>

						  <tr>
							<td class="text-right">{{ trans('app.total_with_currency') }}</td>
							<td class="text-right" width="40%">
							  <span id="summary-currency-total">{{ number_format($purchasing->items->sum('price') ?? 0 / $purchasing->exchange_rate ?? 1, 0, ',', '.') }}&nbsp;{{ $purchasing->currency ?? 'IDR' }}</span>
							</td>
						  </tr>

						  <tr>
							<td class="text-right">{{ trans('app.quantity_total') }}</td>
							<td class="text-right" width="40%">
							  <span id="summary-qty-total">{{ number_format($purchasing->items->sum('request_quantity'), 0, '.', ',') }}&nbsp;Pc</span>
							</td>
						  </tr>

						  <tr>
							<td class="text-right">{{ trans('app.product_total') }}</td>
							<td class="text-right" width="40%">
							  <span id="summary-product-total">{{ number_format($purchasing->items->count(), 0, '.', ',') }}&nbsp;Item</span>
							</td>
						  </tr>
					</table>
				</div>
			  </div>
			</div>
		</div>
	</div>
	<div class="col-md-4 nopadding-left">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title"> {{ trans('app.invoice') }} - {{ $purchasing->purchasing_invoice_number }}</h3>
			</div> <!-- /.box-header -->
			<div class="box-body">
				
				{{-- disabled jika curency belum di update / save --}}
				<a href="{{ route('admin.purchasing.purchasing.invoice', $purchasing->id) }}" class="btn btn-default btn-invoice disabled">{{ trans('app.purchasing_invoice') }}</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title"> {{ trans('app.purchasing_status') }}</h3>
			</div> <!-- /.box-header -->
			<div class="box-body">	
							
				@foreach ($purchasing->items as $items)
					{{ Form::hidden('ids[]', $items->id) }}
				@endforeach

				{{-- <span>currency is updated at : </span> --}}

				<div class="form-group">
					{!! Form::label('currency', trans('app.form.currency') . '*') !!}
					{!! Form::select('currency', [
						'USD' => '$ (USD)',
						'CNY' => 'Yuan (CNY)',
						'IDR' => 'Rupiah (IDR)',
					], $purchasing->currency ?? 'USD', ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.currency'), 'required']) !!}
					<div class="help-block with-errors"></div>
				</div>

				<div class="form-group">
					{!! Form::label('kurs', trans('app.form.kurs') . '*') !!}
					{!! Form::text('exchange_rate', (int) ($purchasing->exchange_rate ?? 1), ['class' => 'form-control', 'placeholder' => trans('app.placeholder.kurs'),'id' => 'exchange_rate', 'required']) !!}
					<div class="help-block with-errors"></div>
				</div>

				<div class="form-group">
					{!! Form::label('currency_timestamp', trans('app.form.currency_timestamp') . '*') !!}
					{!! Form::text('currency_timestamp', $purchasing->currency_timestamp ?? now(), ['class' => 'form-control datetimepicker', 'placeholder' => trans('app.placeholder.currency_timestamp'), 'required']) !!}
					<div class="help-block with-errors"></div>
				</div>
				{{-- TODO: currency form --}}

				<div class="form-group">
					{!! Form::label('transfer_status', trans('app.form.shipment_status') . '*') !!}
					{!! Form::select('shipment_status', [
						1 => 'Pending',
						2 => 'In Progress',
						3 => 'Departure',
						4 => 'Arrival'
					], $purchasing->shipment_status ?? 4, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.shipment_status'), 'required']) !!}
					<div class="help-block with-errors"></div>
				</div>

				<div class="form-group">
					{!! Form::label('transfer_status', trans('app.form.transfer_status') . '*') !!}
					{!! Form::select('transfer_status', [
						10 => 'Requested',
						5 => 'Shipment',
						6 => 'In Stock',
						7 => 'Complete',
					], $purchasing->transfer_status ?? 10, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.transfer_status'), 'required']) !!}
					<div class="help-block with-errors"></div>
				</div>

				<div class="form-group">
					{!! Form::label('request_status', trans('app.form.request_status') . '*') !!}
					{!! Form::select('request_status', [
						8 => 'Request',
						9 => 'Done',
					], $purchasing->request_status ?? 8, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.request_status'), 'required']) !!}
					<div class="help-block with-errors"></div>
				</div>
				<div class="box-tools pull-right">
					<button name='action' type="submit" class='btn btn-primary btn-lg btn-new action-submit'>
						{{ trans('app.save') }}
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
{{-- <div class="row">
	<div class="col-md-8">
		<div class="box">
			<div class="box-header with-border">
			  <h3 class="box-title">
				<i class="fa fa-clock-o"></i> {{ trans('app.form.status_history') }}
			  </h3>
			</div> <!-- /.box-header -->
	
			<div class="box-body">
			</div>
		</div>
	</div>
</div> --}}
@push('script')
<script>
	$(document).ready(function() {
		// Check invoice button state
		function updateInvoiceButtonState() {
			const exchangeRate = parseFloat($('#exchange_rate').val()) || 0;
			const currency = $('#currency').val();
			const $invoiceBtn = $('.btn-invoice');
			
			if (exchangeRate === 0 || exchangeRate <= 1) {
				$invoiceBtn.addClass('disabled').attr('disabled', true);
				$invoiceBtn.attr('title', 'Please update exchange rate first');
			} else {
				$invoiceBtn.removeClass('disabled').attr('disabled', false);
				$invoiceBtn.attr('title', '');
			}
		}

		// Initialize date time picker
		$('.datetimepicker').datetimepicker({
			format: 'YYYY-MM-DD HH:mm:ss'
		});

		// Handle price input changes
		function updateTotals() {
        let priceTotal = 0;
        let qtyTotal = 0;
        let productCount = 0;
        let exchangeRate = parseInt($('#exchange_rate').val()) || 1;
        let currency = $('#currency').val() || 'IDR';

        // Calculate totals from all item rows
        $('.itemQtt').each(function() {
            const price = parseFloat($(this).val()) || 0;
            const qtyInput = $(this).closest('tr').find('td:eq(2)').text();
            const qty = parseFloat(qtyInput) || 0;
            
            priceTotal += price * qty;
            qtyTotal += qty;
            productCount++;
        });

        // Update summary block
        $('#summary-price-total').text(formatNumber(priceTotal) + ' IDR');
		$('#summary-currency-total').text(formatNumberConverter((priceTotal / exchangeRate).toFixed(2)) + ' ' + currency);
        $('#summary-qty-total').text(formatNumber(qtyTotal) + ' Pc');
        $('#summary-product-total').text(formatNumber(productCount) + ' Item');
    }

    // Format numbers with thousand separators
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function formatNumberConverter(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "");
    }

    // Event listeners
    $('.itemQtt').on('input', updateTotals);
    $('#exchange_rate, #currency').on('change', function() {
        updateTotals();
        updateInvoiceButtonState();
    });

    // Handle shipment status changes
    $('select[id^="shipment_status_"]').on('change', function() {
        const status = $(this).val();
        const row = $(this).closest('tr');
        
        if (status == '4') { // Arrival status
            $(this).prop('disabled', true);
            row.find('td:last').append('<i class="fa fa-check"></i> Item Ready');
        }
    });

    // // Form submission handler
    // $('.action-submit').on('click', function(e) {
    //     e.preventDefault();
        
    //     // Validate required fields
    //     let isValid = true;
    //     $('input[required], select[required]').each(function() {
    //         if (!$(this).val()) {
    //             isValid = false;
    //             $(this).addClass('error');
    //         } else {
    //             $(this).removeClass('error');
    //         }
    //     });

    //     if (!isValid) {
    //         alert('Please fill in all required fields');
    //         return;
    //     }

    //     // Submit the form
    //     $(this).closest('form').submit();
    // });

    // Initialize page with current totals and button states
    updateTotals();
    updateInvoiceButtonState();

});
</script>
@endpush
@endsection
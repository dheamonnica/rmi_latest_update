@extends('admin.layouts.master')

@section('content')
<!-- Add this right after your manufacturer select -->
<div class="alert alert-info alert-dismissible" id="manufactureError" style="display: none;">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<strong>{{ trans('app.important') }}: </strong>
	{!! trans('app.please_select_manufacture') !!}
</div>

{!! Form::open(['route' => ['admin.purchasing.purchasing.generateInvoice'], 'files' => true,'method' => 'post', 'class' => 'form', 'id' => 'purchaseForm']) !!}

{{ Form::hidden('purchasing_invoice_number', $inv_number, ['id' => 'purchasing_invoice_number']) }}
{{ Form::hidden('manufacture_number', $manufacture_number, ['id' => 'shipping_rate_id']) }}

@foreach ($ids as $value) 
	{{ Form::hidden('ids[]', $value, ['id' => 'ids']) }}
@endforeach

<div class="row">
	<div class="col-md-12">
		<h3 class="box-title">
			{{ trans('app.assign_manufacture') }}
		  </h3>
	</div>
    <div class="col-md-8">
		<div class="box">
			<div class="box-header with-border">
			  <h3 class="box-title">
				<i class="fa fa-shopping-cart"></i> {{ trans('app.form.manufacture_numbers') . ': ' . $manufacture_number }}
			  </h3>
			  <div class="box-tools pull-right">
				{{-- {!! $purchasing->request_status !!} --}}
			  </div>
			</div> <!-- /.box-header -->
	
			<div class="box-body">
			  <div class="row">
				<div class="col-sm-12">
				  <div class="well well-lg">
					<span class="lead">
					  {{ trans('app.form.purchasing_date') . ': '. date('d-m-Y')}}
					</span>

				  </div>
				</div>
			  </div><!-- /.row -->
	
			  <div class="row">
				<div class="col-md-12">
				  {{-- <h4>{{ trans('app.purchasing_order_details') }}</h4> --}}

				  <div class="col-md-4 nopadding input-lg">
				  {!! Form::label('select_manufacture', trans('app.form.select_manufacture'), ['class' => 'with-help']) !!}
				  </div>
				  <div class="col-md-8 nopadding input-lg">
					{!! Form::select('manufacture', $manufacture, null, ['class' => 'form-control select2', 'placeholder' => trans('app.placeholder.choose_manufacture')]) !!}
				  </div>

				  <span class="spacer10"></span>
				  <table class="table table-sripe">
					<tbody id="items">
						<thead>
							<tr>
							  <th>{{ trans('app.sku') }}</th>
							  <th>{{ trans('app.manufacturer') }}</th>
							  <th>{{ trans('app.product_name') }}</th>
							  <th>{{ trans('app.form.request_quantity') }}</th>
							</tr>
						  </thead>
						  <tbody>
							{{-- grouping the product --}}
							@foreach ($purchasing as $items)
							{{ Form::hidden('product['.$items->product_id.'][id]', $items->id, ['id' => 'product_id']) }}
							<tr>
								<td>{{ $items->product->manufacture_skuid ?? '0'}}</td>
								<td>{{ $items->product->manufacturer->name ?? '0'}}</td>
								<td>{{ $items->product->name}}</td>
								<td>{{ $items->request_quantity ?? '0'}}</td>
							<tr>
							@endforeach
					</tbody>
				  </table>
			    </div>
			  </div><!-- /.row -->
			  <div class="row">
				<div class="col-md-6">
					<div class="form-group pt-5">
						{!! Form::label('admin_note', trans('app.form.admin_note'), ['class' => 'with-help']) !!}
						<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.admin_note') }}"></i>
						{!! Form::textarea('admin_note', null, ['class' => 'form-control summernote-without-toolbar', 'rows' => '2', 'placeholder' => trans('app.placeholder.admin_note')]) !!}
					</div>
				</div>
				<div class="col-md-6" id="summary-block">
					<table class="table">
						<tr>
							<td class="text-right">{{ trans('app.quantity_total') }}</td>
							<td class="text-right" width="40%">
							  <span id="summary-qty-total">{{ get_formated_decimal($purchasing->sum('request_quantity'), true, 0) }}&nbsp;Pc</span>
							</td>
						  </tr>

						  <tr>
							<td class="text-right">{{ trans('app.product_total') }}</td>
							<td class="text-right" width="40%">
							  <span id="summary-product-total">{{ get_formated_decimal($purchasing->count(), true, 0) }}&nbsp;Item</span>
							</td>
						  </tr>
					</table>
				</div>
			  </div>
			  <div class="box">
				  <div class="box-body">
					<div class="box-tools pull-right">
						<button name='action' type="submit" class='btn btn-primary btn-lg btn-new action-submit'>
							{{ trans('app.save') }}
						</button>
					</div>
				</div>
			  </div>
			</div>
		</div>
	</div>
	<div class="col-md-4 nopadding-left">
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">
					<i class="fa fa-shopping-cart"></i> {{ trans('app.form.invoice_numbers') . ': ' . $inv_number }}
				</h3>
			</div> <!-- /.box-header -->
			<div class="box-body">

				{{-- <a href="{{ route('admin.order.order.invoice', 1) }}" class="btn btn-sm btn-default btn-flat">{{ trans('app.purchasing_invoice') }}</a> --}}
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}

<script>
	document.getElementById('purchaseForm').addEventListener('submit', function(e) {
		e.preventDefault();
		
		const manufactureSelect = document.querySelector('select[name="manufacture"]');
		const errorDiv = document.getElementById('manufactureError');
		
		// Hide previous error if any
		errorDiv.style.display = 'none';
		
		if (!manufactureSelect.value) {
			errorDiv.style.display = 'block';
			manufactureSelect.focus();
			window.scrollTo({
				top: manufactureSelect.offsetTop - 100,
				behavior: 'smooth'
			});
			return false;
		}
		
		// If validation passes, submit the form
		this.submit();
	});
	
	// Optional: Hide error message when user selects a manufacturer
	document.querySelector('select[name="manufacture"]').addEventListener('change', function() {
		document.getElementById('manufactureError').style.display = 'none';
	});
</script>
@endsection
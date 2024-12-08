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
							  <th>{{ trans('app.price') }} <small>( {{ trans('app.excl_tax') }} )</small> </th>
							  <th>{{ trans('app.shipment_status') }}</th>
							  <th>{{ trans('app.action') }}</th>
							</tr>
						  </thead>
						  <tbody>
							@foreach ($purchasing->items as $items)
							<tr>
								<td>{{ $items->product->name}}</td>
								<td>{{ $items->manufacture->name ?? '-'}}</td>
								<td>{{ $items->request_quantity ?? '0'}}</td>
								<td>{{ $items->price ?? '0'}}</td>
								<td>{{ get_purchasing_status_name($items->shipment_status)}}
								</td>
								<td>
									@php
										if($items->shipment_status == 1) {
											$status_id = 2;
											$status = 'set_as_in_progress';
										} 

										if($items->shipment_status == 2) {
											$status_id = 3;
											$status = 'set_as_depature';
										} 

										if($items->shipment_status == 3) {
											$status_id = 4;
											$status = 'set_as_arrival';
										}
										
									@endphp

									@if ($items->shipment_status < 4)
										{!! Form::open(['route' => ['admin.purchasing.purchasing.setShippingStatus', $status_id], 'method' => 'POST', 'class' => 'inline']) !!}
										{{ Form::hidden('ids[]', $items->id) }}
										<button type="submit" class="confirm ajax-silent btn btn-sm btn-grey">{{ trans('app.'.$status.'') }}</button>
										{!! Form::close() !!}
									@else
										<i class="fa fa-check"></i>  Item Ready
									@endif
									
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
							<td class="text-right">{{ trans('app.quantity_total') }}</td>
							<td class="text-right" width="40%">
							  <span id="summary-qty-total">{{ get_formated_decimal($purchasing->items->sum('request_quantity'), true, 0) }}&nbsp;Pc</span>
							</td>
						  </tr>

						  <tr>
							<td class="text-right">{{ trans('app.product_total') }}</td>
							<td class="text-right" width="40%">
							  <span id="summary-product-total">{{ get_formated_decimal($purchasing->items->count(), true, 0) }}&nbsp;Item</span>
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
				<a href="{{ route('admin.purchasing.purchasing.invoice', $purchasing->id) }}" class="btn btn-sm btn-default btn-flat">{{ trans('app.purchasing_invoice') }}</a>
			</div>
		</div>
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title"> {{ trans('app.purchasing_status') }}</h3>
			</div> <!-- /.box-header -->
			<div class="box-body">
				{!! Form::open(['route' => ['admin.purchasing.purchasing.setShippingStatus', 0], 'method' => 'POST', 'class' => 'inline']) !!}
				
				@foreach ($purchasing->items as $items)
					{{ Form::hidden('ids[]', $items->id) }}
				@endforeach

				<div class="form-group">
					{!! Form::label('transfer_status', trans('app.form.transfer_status') . '*') !!}
					{!! Form::select('transfer_status', [
						'10' => 'Requested',
						'5' => 'Shipment',
						'6' => 'Stock',
						'7' => 'Complete',
					], $purchasing->transfer_status, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.transfer_status'), 'required']) !!}
					<div class="help-block with-errors"></div>
				</div>

				<div class="form-group">
					{!! Form::label('request_status', trans('app.form.request_status') . '*') !!}
					{!! Form::select('request_status', [
						'8' => 'Request',
						'9' => 'Done',
					], $purchasing->request_status, ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.request_status'), 'required']) !!}
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
<div class="row">
	<div class="col-md-8">
		<div class="box">
			<div class="box-header with-border">
			  <h3 class="box-title">
				<i class="fa fa-clock-o"></i> {{ trans('app.form.status History') }}
			  </h3>
			</div> <!-- /.box-header -->
	
			<div class="box-body">
			</div>
		</div>
	</div>
</div>
@endsection
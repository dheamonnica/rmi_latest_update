@extends('admin.layouts.master')

@section('content')
	<div class="row">
		<div class="col-md-8">
			<div class="box">
			  <div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-cart-plus"></i> {{ trans('app.stock_transfer') }} {{ $stock_transfer->movement_number }}</h3>
			  </div> <!-- /.box-header -->
			  <div class="box-body">

					{{-- @include('admin.order._cart') --}}
					<div class="row">
						<div class="col-md-12">
						  <table class="table table-sripe">
							<tbody id="items">
								<tr>
									<td>{{ trans('app.image') }}</td>
									<td>{{ trans('app.product_name') }}</td>
									<td>{{ trans('app.expired_date') }}</td>
									<td>{{ trans('app.transfer_qty') }}</td>
								</tr>
								@foreach ($stock_transfer->items as $item)
									<tr>
										<td>
										<img src="{{ get_product_img_src($item->fromInventory, 'tiny') }}" class="img-circle img-md" alt="{{ trans('app.image') }}">
										</td>
										<td class="nopadding-right" width="55%">
										{{ $item->fromInventory->title }}
										<a href="{{ route('show.product', $item->product->slug) }}" target="_blank" class="indent5 small"><i class=" fa fa-external-link"></i></a>
										</td>
										<td class="nopadding-right" width="10%">
										{{ $item->fromInventory->expired_date }}
										</td>
										<td class="nopadding-right" width="10%">
										{{ $item->transfer_qty }}
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					  </div>
					</div>

					<hr>

					<div class="row">
						<div class="col-md-6">
							<div class="col-md-6">
								Warehouse From {{ $stock_transfer->fromWarehouse->name }}
							</div>
			
							<div class="col-md-6">
								Warehouse Destination {{ $stock_transfer->toWarehouse->name }}
							</div>
							
							<div class="form-group pt-5">
								{!! Form::label('admin_note', trans('app.form.admin_note'), ['class' => 'with-help']) !!}
								<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="{{ trans('help.admin_note') }}"></i>
								{!! Form::textarea('admin_note', isset($stock_transfer->admin_note) ? $stock_transfer->admin_note : null, ['class' => 'form-control summernote-without-toolbar', 'rows' => '2', 'placeholder' => trans('app.placeholder.admin_note')]) !!}
							</div>
						</div>
						<div class="col-md-6" id="summary-block">
							<table class="table">
								<tr>
									<td class="text-right">{{ trans('app.quantity_total') }}</td>
									<td class="text-right" width="40%">
									<span id="summary-qty-total">{{ get_formated_decimal($stock_transfer->items->sum('transfer_qty'), true, 0) }}&nbsp;</span>
									</td>
								</tr>

								<tr>
									<td class="text-right">{{ trans('app.product_total') }}</td>
									<td class="text-right" width="40%">
									<span id="summary-product-total">{{ get_formated_decimal($stock_transfer->items->count(), true, 0) }}&nbsp;</span>
									</td>
								</tr>
							</table>
						</div>
					</div>
					
					<div class="box-tools pull-left">
						@if ((int) Auth::user()->shop_id == (int) $stock_transfer->shop_depature_id)
							{{-- 1 --}}
							@if ((int) $stock_transfer->status == 1)	
								{!! Form::open(['route' => ['admin.stock.inventory.updateStatusStocktransfer', $stock_transfer], 'method' => 'post', 'class' => 'inline']) !!}
								{{ Form::hidden('status', 2, ['id' => 'status']) }}
								<button type="submit" class="confirm ajax-silent btn btn-lg btn-secondary">PACKED ORDER</button>
								{!! Form::close() !!}
							@endif

							{{-- 2 --}}
							@if ((int) $stock_transfer->status == 2)
								{!! Form::open(['route' => ['admin.stock.inventory.updateStatusStocktransfer', $stock_transfer], 'method' => 'post', 'class' => 'inline']) !!}
								{{ Form::hidden('status', 3, ['id' => 'status']) }}
								<button type="submit" class="confirm ajax-silent btn btn-lg btn-secondary">SET AS SEND BY WAREHOUSE</button>
								{!! Form::close() !!}
							@endif

							{{-- 3 --}}
							@if ((int) $stock_transfer->status == 3)
								{!! Form::open(['route' => ['admin.stock.inventory.updateStatusStocktransfer', $stock_transfer], 'method' => 'post', 'class' => 'inline']) !!}
								{{ Form::hidden('status', 4, ['id' => 'status']) }}
								<button type="submit" class="confirm ajax-silent btn btn-lg btn-secondary">SET AS ON DELIVERY</button>
								{!! Form::close() !!}
							@endif

							{{-- 5 --}}
							@if ((int) $stock_transfer->status == 4)
								{!! Form::open(['route' => ['admin.stock.inventory.updateStatusStocktransfer', $stock_transfer], 'method' => 'post', 'class' => 'inline']) !!}
								{{ Form::hidden('status', 5, ['id' => 'status']) }}
									<button type="submit" class="confirm ajax-silent btn btn-lg btn-secondary">SET AS DELIVERED</button>
								{!! Form::close() !!}
							@endif
						@endif

						@if ((int) Auth::user()->shop_id == (int) $stock_transfer->shop_depature_id)
							{{-- 6 --}}
							@if ((int) $stock_transfer->status == 5)
								{!! Form::open(['route' => ['admin.stock.inventory.updateStatusStocktransfer', $stock_transfer], 'method' => 'post', 'class' => 'inline']) !!}
								{{ Form::hidden('status', 6, ['id' => 'status']) }}
									<button type="submit" class="confirm ajax-silent btn btn-lg btn-secondary">SET AS RECEIVED</button>
								{!! Form::close() !!}
							@endif

							{{-- 7 --}}
							@if ((int) $stock_transfer->status == 6)
									{!! Form::open(['route' => ['admin.stock.inventory.updateStatusStocktransfer', $stock_transfer], 'method' => 'post', 'class' => 'inline']) !!}
									{{ Form::hidden('status', 7, ['id' => 'status']) }}
									<button type="submit" class="confirm ajax-silent btn btn-lg btn-primary">SET AS APPROVED</button>
								{!! Form::close() !!}
							@endif
						@endif
					</div>
				</div>
			</div>	
		</div>
		<div class="col-md-4 nopadding-left">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title"> {{ trans('app.status') }}</h3>
				</div> <!-- /.box-header -->
				<div class="box-body">
					<div>
						@if ((int) Auth::user()->shop_id == (int) $stock_transfer->shop_depature_id)
							<span class="badge badge-secondary">Sender Information</span>
							<span class="spacer10"></span>
							<div class="col-12 nopadding">
								<table class="table no-border">
									<tr>
										<th class="text-right">{{ trans('app.status') }}:</th>
										<td style="width: 75%;">{{ get_stock_transfer_status_name($stock_transfer->status) }}</td>
									</tr>
									
									@if ($stock_transfer->packed_by)
										<tr>
											<th class="text-right">{{ trans('app.packed_by') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->packedBy->name }}</td>
										</tr>
										<tr>
											<th class="text-right">{{ trans('app.packed_time') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->packed_at }}</td>
										</tr>
									@endif

									{{-- @if ($stock_transfer->send_by_warehouse)
										<tr>
											<th class="text-right">{{ trans('app.send_by_warehouse') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->sendByWarehouse->name }}</td>
										</tr>
										<tr>
											<th class="text-right">{{ trans('app.send_by_warehouse_at') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->send_by_warehouse_time }}</td>
										</tr>
									@endif --}}

									@if ($stock_transfer->on_delivery_by)
										<tr>
											<th class="text-right">{{ trans('app.on_delivery_by') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->onDeliveredBy->name }}</td>
										</tr>
										<tr>
											<th class="text-right">{{ trans('app.on_delivery_at') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->on_delivery_time }}</td>
										</tr>
									@endif
									<tr>
										<th class="text-right">{{ trans('app.last_update') }}:</th>
										<td style="width: 75%;">{{ $stock_transfer->updated_at }}</td>
									</tr>
								</table>
							</div>
						@elseif ((int) Auth::user()->shop_id == (int) $stock_transfer->shop_arrival_id)
							<span class="badge badge-primary" style="background-color: blue">Receiver Information</span>
							<span class="spacer10"></span>
							<div class="col-12 nopadding">
								<table class="table no-border">
									<tr>
										<th class="text-right">{{ trans('app.status') }}:</th>
										<td style="width: 75%;">{{ get_stock_transfer_status_name($stock_transfer->status) }}</td>
									</tr>
									@if ($stock_transfer->delivered_by)
										<tr>
											<th class="text-right">{{ trans('app.delivered_by') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->deliveredBy->name }}</td>
										</tr>
										<tr>
											<th class="text-right">{{ trans('app.delivered_at') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->delivered_time }}</td>
										</tr>
									@endif

									@if ($stock_transfer->received_by)
										<tr>
											<th class="text-right">{{ trans('app.received_by') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->receivedBy->name }}</td>
										</tr>
										<tr>
											<th class="text-right">{{ trans('app.received_at') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->received_time }}</td>
										</tr>
									@endif

									@if ($stock_transfer->approved_by)
										<tr>
											<th class="text-right">{{ trans('app.approved_by') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->approvedBy->name }}</td>
										</tr>
										<tr>
											<th class="text-right">{{ trans('app.approved_at') }}:</th>
											<td style="width: 75%;">{{ $stock_transfer->approved_by_time }}</td>
										</tr>
									@endif
									<tr>
										<th class="text-right">{{ trans('app.last_update') }}:</th>
										<td style="width: 75%;">{{ $stock_transfer->updated_at }}</td>
									</tr>
								</table>
							@endif
						</div>
					</div>
					{{-- <div class="form-group">
						{!! Form::label('transfer_type', trans('app.form.transfer_type') . '*') !!}
						{!! Form::select('transfer_type', [], isset($stock_transfer->transfer_type) ? $stock_transfer->transfer_type : config('shop_settings.default_transfer_type'), ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.payment'), 'required']) !!}
						<div class="help-block with-errors"></div>
					</div> --}}
				</div>	
			</div>
		</div>
	</div>
@endsection
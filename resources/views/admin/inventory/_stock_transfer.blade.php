<div class="modal-dialog modal-md">
	<div class="modal-content">
	  {!! Form::open(['route' => 'admin.stock.inventory.stockTransferWarehouse', 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}
	  {{-- create stock transfer --}}
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		{{ trans('app.form.stock_transfer') }}
	  </div>
	  <div class="modal-body">
		@include('admin.partials._search_warehouse')
	  </div>
	  <div class="modal-footer">
		{!! Form::submit(trans('app.form.transfer_stock'), ['class' => 'btn btn-flat btn-new']) !!}
	  </div>
	  {!! Form::close() !!}
	</div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->
  
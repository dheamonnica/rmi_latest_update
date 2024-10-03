
{{-- @can('update', $stock_transfer) --}}
  <a href="{{ route('admin.stock.inventory.stockTransferDetails', $stock_transfer) }}"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.detail') }}" class="fa fa-expand"></i></a>&nbsp;
{{-- @endcan --}}

 {{-- @can('delete', $inventory)
   {!! Form::open(['route' => ['admin.stock.inventory.trash', $inventory->id], 'method' => 'delete', 'class' => 'data-form']) !!}
   {!! Form::button('<i class="fa fa-trash-o"></i>', ['type' => 'submit', 'class' => 'confirm ajax-silent', 'title' => trans('app.trash'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']) !!}
   {!! Form::close() !!}
 @endcan --}}

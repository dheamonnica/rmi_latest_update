{{-- @can('view', $purchasing) --}}
@if(!Auth::user()->shop_id)
  @if ( $purchasing->manufacture_number != null)
    <a href="{{ route('admin.purchasing.purchasing.show', $purchasing->id) }}"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.detail') }}" class="fa fa-expand"></i></a>&nbsp;
  @endif
  {{-- @endcan --}}

  {{-- @can('update', $purchasing) --}}
  @if ( $purchasing->manufacture_number != null)
    <a href="{{ route('admin.purchasing.purchasing.edit', $purchasing->id) }}"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
  @endif


  {!! Form::open(['route' => ['admin.purchasing.purchasing.trash', $purchasing->id], 'method' => 'delete', 'class' => 'data-form']) !!}
  {!! Form::button('<i class="fa fa-trash-o"></i>', ['type' => 'submit', 'class' => 'confirm ajax-silent', 'title' => trans('app.trash'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']) !!}
  {!! Form::close() !!}
@endif
{{-- @endcan --}}

{{-- @can('delete', $purchasing) --}}
  {{-- {!! Form::open(['route' => ['admin.purchasing.purchasing.trash', $purchasing->item_id], 'method' => 'delete', 'class' => 'data-form']) !!}
  {!! Form::button('<i class="fa fa-trash-o"></i>', ['type' => 'submit', 'class' => 'confirm ajax-silent', 'title' => trans('app.trash'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']) !!}
  {!! Form::close() !!} --}}
{{-- @endcan --}}

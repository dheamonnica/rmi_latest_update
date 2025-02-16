@extends('admin.layouts.master')

@section('buttons')
   
@endsection

@section('content')
<div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">{{ trans('app.purchasing') }}</h3>
      <div class="box-tools pull-right">
        @can('create', \App\Models\Purchasing::class)
          {{-- <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.bulk') }}" class="ajax-modal-btn btn btn-default btn-flat">{{ trans('app.bulk_import') }}</a> --}}
          <a href="{{ route('admin.purchasing.purchasing.create') }}" class=" btn btn-new btn-flat mr-2">{{ trans('app.add_purchasing') }}</a>
        @endcan

        @if (!Auth::user()->shop_id)
        <div class="btn-group">
          {{-- <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                data-toggle="dropdown"aria-expanded="false">
                {{ trans('app.assign_manufacture') }}
                <span class="sr-only">{{ trans('app.toggle_dropdown') }}</span>
                <span class="caret"></span>
            </button> --}}
            {{-- <ul class="dropdown-menu" role="menu">
                <li> --}}
                  <a href="javascript:void(0);"
                  data-link="{{ route('admin.purchasing.purchasing.assignManufacture', 2) }}" class="btn btn-default btn-flat massAction"
                  data-doafter="redirect">{{ trans('app.assign_manufacture') }}</a>
                {{-- </li>
            </ul> --}}
        </div>

        <div class="btn-group">
          <button type="button" class="btn btn-sm btn-default dropdown-toggle" id="assign-status"
                data-toggle="dropdown"aria-expanded="false" disabled="disabled">
                {{ trans('app.assign_purchasing_status') }}
                <span class="sr-only">{{ trans('app.toggle_dropdown') }}</span>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="javascript:void(0)"
                        data-link="{{ route('admin.purchasing.purchasing.setShippingStatus', 2) }}" class="massAction"
                        data-doafter="reload">{{ trans('app.mark_as_shipping_in_progress') }}</a></li>
                <li><a href="javascript:void(0)"
                        data-link="{{ route('admin.purchasing.purchasing.setShippingStatus', 3) }}" class="massAction"
                        data-doafter="reload">{{ trans('app.mark_as_shipping_depature') }}</a></li>
                <li><a href="javascript:void(0)"
                  data-link="{{ route('admin.purchasing.purchasing.setShippingStatus', 4) }}" class="massAction"
                  data-doafter="reload">{{ trans('app.mark_as_shipping_arrival') }}</a></li>
                <li><a href="javascript:void(0)"
                  data-link="{{ route('admin.purchasing.purchasing.setShippingStatus', 5) }}" class="massAction"
                  data-doafter="reload">{{ trans('app.mark_as_transfer_shipment') }}</a></li>
                <li><a href="javascript:void(0)"
                  data-link="{{ route('admin.purchasing.purchasing.setShippingStatus', 6) }}" class="massAction"
                  data-doafter="reload">{{ trans('app.mark_as_transfer_stock') }}</a></li>
                <li><a href="javascript:void(0)"
                  data-link="{{ route('admin.purchasing.purchasing.setShippingStatus', 7) }}" class="massAction"
                  data-doafter="reload">{{ trans('app.mark_as_transfer_complete') }}</a></li>
                <li><a href="javascript:void(0)"
                  data-link="{{ route('admin.purchasing.purchasing.setShippingStatus', 9) }}" class="massAction"
                  data-doafter="reload">{{ trans('app.mark_as_transfer_done') }}</a></li>
            </ul>
        </div>
        @endif
      </div>
    </div> <!-- /.box-header -->

    
  <div class="box">
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs nav-justified">
        <li class="{{ Request::has('tab') ? '' : 'active' }}">
          <a href="#purchasing" data-toggle="tab">
            <i class="fa fa-superpowers hidden-sm"></i>
            {{ trans('app.purchasing') }}
          </a>
        </li>
        <li class="{{ Request::input('tab') == 'inactive_listings' ? 'active' : '' }}">
          <a href="#request" data-toggle="tab">
            <i class="fa fa-bell-o hidden-sm"></i>
            {{ trans('app.request') }}
          </a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane {{ Request::has('tab') ? '' : 'active' }} responsive-table" id="purchasing">
          <table class="table table-hover" id="all-purchasing-table">
            <thead>
              <tr>
                @can('massDelete', \App\Models\Purchasing::class)
                  <th class="massActionWrapper">
                    <!-- Check all button -->
                    <div class="btn-group ">
                      <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                      <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top" title="{{ trans('app.select_all') }}"></i>
                      </button>

                      <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">{{ trans('app.toggle_dropdown') }}</span>
                      </button>

                      <ul class="dropdown-menu" role="menu">
                        <li>
                          <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.massTrash') }}" class="massAction" data-doafter="reload"><i class="fa fa-trash"></i> {{ trans('app.trash') }}</a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.massDestroy') }}" class="massAction" data-doafter="reload"><i class="fa fa-times"></i> {{ trans('app.delete_permanently') }}</a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.massDestroy') }}" class="massAction" data-doafter="reload"><i class="fa fa-factory"></i> {{ trans('app.assign_manufacture') }}</a>
                        </li>
                      </ul>
                    </div>
                  </th>
                @else
                  <th></th>
                @endcan
                <th>{{ trans('app.request_date') }}</th>
                <th>{{ trans('app.purchasing_number') }}</th>
                <th>{{ trans('app.manufacture_number') }}</th>
                {{-- <th>{{ trans('app.warehouse') }}</th> --}}
                {{-- <th>{{ trans('app.form.product') }}</th> --}}
                <th>{{ trans('app.form.item_quantity') }}</th>
                <th>{{ trans('app.form.total_quantity') }}</th>
                <th>{{ trans('app.form.currency') }}</th>
                <th>{{ trans('app.form.rate') }}</th>
                <th>{{ trans('app.form.grand_total') }}</th>
                <th>{{ trans('app.form.shipment_status') }}</th>
                <th>{{ trans('app.form.transfer_status') }}</th>
                <th>{{ trans('app.form.request_status') }}</th>
                <th>{{ trans('app.option') }}</th>
              </tr>
            </thead>
            <tbody id="massSelectArea">
            </tbody>
          </table>
        </div> <!-- /.tab-pane -->

        <div class="tab-pane {{ Request::input('tab') == 'request' ? 'active' : '' }} responsive-table" id="request">
          <table class="table table-hover" id="all-request-table">
            <thead>
              <tr>
                @can('massDelete', \App\Models\Purchasing::class)
                  <th class="massActionWrapper">
                    <!-- Check all button -->
                    <div class="btn-group ">
                      <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                      <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top" title="{{ trans('app.select_all') }}"></i>
                      </button>

                      <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">{{ trans('app.toggle_dropdown') }}</span>
                      </button>

                      <ul class="dropdown-menu" role="menu">
                        <li>
                          <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.massTrash') }}" class="massAction" data-doafter="reload"><i class="fa fa-trash"></i> {{ trans('app.trash') }}</a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.massDestroy') }}" class="massAction" data-doafter="reload"><i class="fa fa-times"></i> {{ trans('app.delete_permanently') }}</a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.massDestroy') }}" class="massAction" data-doafter="reload"><i class="fa fa-factory"></i> {{ trans('app.assign_manufacture') }}</a>
                        </li>
                      </ul>
                    </div>
                  </th>
                @else
                  <th></th>
                @endcan
                <th>{{ trans('app.warehouse') }}</th>
                <th>{{ trans('app.request_date') }}</th>
                <th>{{ trans('app.purchasing_number') }}</th>
                <th>{{ trans('app.form.product') }}</th>
                <th>{{ trans('app.form.quantity') }}</th>
                <th>{{ trans('app.form.shipment_status') }}</th>
                <th>{{ trans('app.form.transfer_status') }}</th>
                <th>{{ trans('app.form.request_status') }}</th>
                <th>{{ trans('app.option') }}</th>
              </tr>
            </thead>
            <tbody id="massSelectArea">
            </tbody>
          </table>
        </div> <!-- /.tab-pane -->
      </div> <!-- /.tab-content -->

    </div> <!-- /.nav-tabs-custom -->
  </div> <!-- /.box -->

  {{-- @if (Auth::user()->isFromPlatform()) --}}
  <div class="box collapsed-box">
    <div class="box-header with-border">
      <h3 class="box-title">
        @can('massDelete', \App\Models\Purchasing::class)
          {!! Form::open(['route' => ['admin.catalog.product.emptyTrash'], 'method' => 'delete', 'class' => 'data-form']) !!}
          {!! Form::button('<i class="fa fa-trash-o"></i>', ['type' => 'submit', 'class' => 'confirm btn btn-default btn-flat ajax-silent', 'title' => trans('help.empty_trash'), 'data-toggle' => 'tooltip', 'data-placement' => 'right']) !!}
          {!! Form::close() !!}
        @else
          <i class="fa fa-trash-o"></i>
        @endcan
        {{ trans('app.trash') }}
      </h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
      </div>
    </div> <!-- /.box-header -->
    <div class="box-body responsive-table">
      <table class="table table-hover table-2nd-sort">
        <thead>
          <tr>
            <th>{{ trans('app.image') }}</th>
            <th>{{ trans('app.name') }}</th>
            {{-- <th>{{ trans('app.type') }}</th> --}}
            {{-- <th>{{ trans('app.model_number') }}</th> --}}
            {{-- <th>{{ trans('app.category') }}</th> --}}
            <th>{{ trans('app.option') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($trashes as $trash)
            <tr>
              <td>
                @if ($trash->featuredImage)
                  <img src="{{ get_storage_file_url(optional($trash->featuredImage)->path, 'tiny') }}" class="img-sm" alt="{{ trans('app.featured_image') }}">
                @else
                  <img src="{{ get_storage_file_url(optional($trash->image)->path, 'tiny') }}" class="img-sm" alt="{{ trans('app.image') }}">
                @endif
              </td>
              <td>{{ $trash->name }}</td>
              {{-- <td>{{ $trash->type }}</td> --}}
              {{-- <td>{{ $trash->model_number }}</td> --}}
              {{-- <td>
                @foreach ($trash->categories as $category)
                  <span class="label label-outline">{{ $category->name }}</span>
                @endforeach
              </td> --}}
              <td class="row-options">
                @can('delete', $trash)
                  <a href="{{ route('admin.catalog.product.restore', $trash->id) }}"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.restore') }}" class="fa fa-database"></i></a>&nbsp;

                  {!! Form::open(['route' => ['admin.catalog.product.destroy', $trash->id], 'method' => 'delete', 'class' => 'data-form']) !!}
                  {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'confirm ajax-silent', 'title' => trans('app.delete_permanently'), 'data-toggle' => 'tooltip', 'data-placement' => 'top']) !!}
                  {!! Form::close() !!}
                @endcan
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div> <!-- /.box-body -->
  </div> <!-- /.box -->

  <script>
    // #WIP
    // Check if the tab is active based on the request input
    if (window.location.search.includes('tab=request')) {
        // Find the button within the request tab
        const dropdownButton = document.querySelector('#request .btn.dropdown-toggle[disabled]');
        
        // Remove the disabled attribute if the button exists
        if (dropdownButton) {
            dropdownButton.removeAttribute('disabled');
        }
    }
  </script>
  @endsection
  

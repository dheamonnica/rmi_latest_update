@extends('admin.layouts.master')

@section('buttons')
    @if (is_incevio_package_loaded('ebay') && is_ebay_configured())
        @include('ebay::_pull_btn')
    @endif
@endsection

@section('content')
    @php
        $order_statuses = \App\Helpers\ListHelper::order_statuses();
        $payment_statuses = \App\Helpers\ListHelper::payment_statuses();
    @endphp
    <div class="box">
        <div class="box-header with-border">
            <div class="pull-left">
                <h1 class="box-title mr-5 mt-2">{{ trans('app.orders') }}</h1>
            </div>
            <br><br>
            <div class="pull-left">
                <div class="pull-left">
                    <div class="row">
                        <div class="col-md-9 nopadding-right">
                            <select id="merchantOrderTableFilter" class="btn btn-sm btn-default">
                                <option value="" selected>Select Business Unit</option>
                                @foreach ($merchants as $merchant)
                                    <option value="{{ $merchant }}">{{ $merchant }}</option>
                                @endforeach
                            </select>

                            <select id="customerOrderTableFilter" class="btn btn-sm btn-default">
                                <option value="" selected>Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer }}">{{ $customer }}</option>
                                @endforeach
                            </select>

                            <select id="statusOrderTableFilter" class="btn btn-sm btn-default">
                                <option value="" selected>Select Order Status</option>
                                @foreach ($order_statuses as $order_status_number => $order_status)
                                    <option value={{ $order_status_number }}>{{ $order_status }}</option>
                                @endforeach
                            </select>

                            <select id="paymentStatusOrderTableFilter" class="btn btn-sm btn-default">
                                <option value="" selected>Select Payment Status</option>
                                @foreach ($payment_statuses as $payment_status_number => $payment_status)
                                    <option value={{ $payment_status_number }}>{{ $payment_status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 nopadding-right">
                            <div class="row">
                                <div class="col-md-5 nopadding-right">
                                    {!! Form::text('startDateOrderTableFilter', null, [
                                        'class' => 'form-control datepicker',
                                        'placeholder' => trans('app.form.from'),
                                        'id' => 'startDateOrderTableFilter',
                                    ]) !!}
                                </div>
                                <div class="col-md-5 nopadding-left">
                                    {!! Form::text('endDateOrderTableFilter', null, [
                                        'class' => 'form-control datepicker',
                                        'placeholder' => trans('app.form.to'),
                                        'id' => 'endDateOrderTableFilter',
                                    ]) !!}
                                </div>
                                <div class="col-md-2 nopadding-left py-1">
                                    <button id="dateRangeOrderTableFilterButton">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pull-left py-2">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle"
                        data-toggle="dropdown"aria-expanded="false">
                        {{ trans('app.assign_payment_status') }}
                        <span class="sr-only">{{ trans('app.toggle_dropdown') }}</span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="javascript:void(0)"
                                data-link="{{ route('admin.order.order.assignPaymentStatus', 'paid') }}" class="massAction"
                                data-doafter="reload">{{ trans('app.mark_as_paid') }}</a></li>
                        <li><a href="javascript:void(0)"
                                data-link="{{ route('admin.order.order.assignPaymentStatus', 'unpaid') }}"
                                class="massAction" data-doafter="reload">{{ trans('app.mark_as_unpaid') }}</a></li>
                    </ul>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                        {{ trans('app.assign_order_status') }}
                        <span class="sr-only">{{ trans('app.toggle_dropdown') }}</span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        @foreach ($order_statuses as $order_status_number => $order_status)
                            <li><a href="javascript:void(0)"
                                    data-link="{{ route('admin.order.order.assignOrderStatus', $order_status_number) }}"
                                    class="massAction" data-doafter="reload">{{ $order_status }}</a></li>
                        @endforeach
                        <li><a href="javascript:void(0)" data-link="{{ route('admin.order.order.downloadSelected') }}"
                                class="massAction" data-doafter="reload">{{ trans('app.download') }}
                                {{ trans('app.invoices') }}</a></li>
                    </ul>
                </div>
                @if (Auth::user()->isFromMerchant())
                    <a href="javascript:void(0)" data-link="{{ route('admin.order.order.searchCustomer') }}"
                        class="ajax-modal-btn btn btn-new btn-lg btn-flat ml-5">{{ trans('app.add_order') }}</a>
                @endif
            </div>
        </div> {{-- Box header --}}
        <div class="">
            <table class="table table-hover" id="all-order-table">
                <thead>
                    <tr>
                        <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th>
                        <th>{{ trans('app.form.po_number_ref') }}</th>
                        <th>{{ trans('app.order_date') }}</th>
                        <th>{{ trans('app.form.due_date_payment') }}</th>
                        <th>{{ trans('app.form.business_unit') }}</th>
                        <th>{{ trans('app.client') }}</th>
                        <th>{{ trans('app.grand_total') }}</th>
                        <th>{{ trans('app.payment_status') }}</th>
                        <th>{{ trans('app.payment_status') }}</th>
                        <th>{{ trans('app.order_status') }}</th>
                        <th>{{ trans('app.partial') }}</th>
                        <th>{{ trans('app.options') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div><!-- /.box -->
    <div class="box collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">
                @can('massDestroy', \App\Models\Order::class)
                    {!! Form::open(['route' => ['admin.order.order.emptyTrash'], 'method' => 'delete', 'class' => 'data-form']) !!}
                    {!! Form::button('<i class="fa fa-trash-o"></i>', [
                        'type' => 'submit',
                        'class' => 'confirm btn btn-default btn-flat ajax-silent',
                        'title' => trans('help.empty_trash'),
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'right',
                    ]) !!}
                    {!! Form::close() !!}
                    {{ trans('app.archived_orders') }}
                @else
                    <i class="fa fa-trash-o"></i> {{ trans('app.archived_orders') }}
                @endcan
            </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
            </div>
        </div> <!-- /.box-header -->
        <div class="box-body responsive-table">
            <table class="table table-hover table-no-sort">
                <thead>
                    <tr>
                        {{-- <th>{{ trans('app.order_number') }}</th> --}}
                        <th>{{ trans('app.order_date') }}</th>
                        <th>{{ trans('app.grand_total') }}</th>
                        <th>{{ trans('app.payment') }}</th>
                        <th>{{ trans('app.status') }}</th>
                        <th>{{ trans('app.archived_at') }}</th>
                        <th>{{ trans('app.option') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($archives as $archive)
                        <tr>
                            {{-- <td>
                                @can('view', $archive)
                                    <a href="{{ route('admin.order.order.show', $archive->id) }}">
                                        {{ $archive->order_number }}
                                    </a>
                                @else
                                    {{ $archive->order_number }}
                                @endcan
                            </td> --}}
                            <td>{{ $archive->created_at->toDayDateTimeString() }}</td>
                            <td>{{ get_formated_currency($archive->grand_total, 2, $archive->currency_id) }}</td>
                            <td>{!! $archive->paymentStatusName() !!}</td>
                            <td>{!! $archive->orderStatus() !!}</td>
                            <td>{{ $archive->deleted_at->diffForHumans() }}</td>
                            <td class="row-options">
                                @can('archive', $archive)
                                    <a href="{{ route('admin.order.order.restore', $archive->id) }}"><i data-toggle="tooltip"
                                            data-placement="top" title="{{ trans('app.restore') }}"
                                            class="fa fa-database"></i></a>
                                @endcan

                                @can('delete', $archive)
                                    {!! Form::open([
                                        'route' => ['admin.order.order.destroy', $archive->id],
                                        'method' => 'delete',
                                        'class' => 'data-form',
                                    ]) !!}
                                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', [
                                        'type' => 'submit',
                                        'class' => 'confirm ajax-silent',
                                        'title' => trans('app.delete_permanently'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                    ]) !!}
                                    {!! Form::close() !!}
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> <!-- /.box-body -->
    </div> <!-- /.box -->
</div @endsection

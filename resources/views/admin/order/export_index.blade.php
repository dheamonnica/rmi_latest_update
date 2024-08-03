@extends('admin.layouts.master')

@section('buttons')
    @if (is_incevio_package_loaded('ebay') && is_ebay_configured())
        @include('ebay::_pull_btn')
    @endif

    @if (Auth::user()->isFromMerchant())
        @can('create', \App\Models\Order::class)
            <a href="javascript:void(0)" data-link="{{ route('admin.order.order.searchCustomer') }}"
                class="ajax-modal-btn btn btn-new btn-lg btn-flat">{{ trans('app.add_order') }}</a>
        @endcan
    @endif
@endsection

@section('content')
    @php
        $order_statuses = \App\Helpers\ListHelper::order_statuses();
        $payment_statuses = \App\Helpers\ListHelper::payment_statuses()
    @endphp
    <div class="box">
        <div class="box-header with-border">
            <div class="pull-left">
                <h1 class="box-title mr-5 mt-2">{{ trans('app.orders') }}</h1>
            </div>
            <div class="pull-left">
                <select id="filter-all-order-table-order-status" class="btn btn-sm btn-default">
                    <option value="0" selected>{{ 'Filter by order status' }}</option>
                    <option value="0">{{ trans('app.all_orders') }}</option>
                    @foreach ($order_statuses as $order_status_number => $order_status)
                        <option value={{ $order_status_number }}>{{ $order_status }}</option>
                    @endforeach
                </select>
                <select id="filter-all-order-table-payment-status" class="btn btn-sm btn-default">
                    <option value="0" selected>{{ trans('app.placeholder.filter_by_status') }}</option>
                    <option value="0">{{ trans('app.all_orders') }}</option>
                    @foreach ($payment_statuses as $payment_status_number => $payment_status)
                        <option value={{ $payment_status_number }}>{{ $payment_status }}</option>
                    @endforeach
                </select>
            
            </div>
            {{-- <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                    data-toggle="dropdown"aria-expanded="false">
                    {{ trans('app.assign_payment_status') }}
                    <span class="sr-only">{{ trans('app.toggle_dropdown') }}</span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="javascript:void(0)" data-link="{{ route('admin.order.order.assignPaymentStatus', 'paid') }}" class="massAction" data-doafter="reload">{{ trans('app.mark_as_paid') }}</a></li>
                    <li><a href="javascript:void(0)" data-link="{{ route('admin.order.order.assignPaymentStatus', 'unpaid') }}" class="massAction" data-doafter="reload">{{ trans('app.mark_as_unpaid') }}</a></li>
                    <li><a href="javascript:void(0)" data-link="{{ route('admin.order.order.assignPaymentStatus', 'refunded') }}" class="massAction" data-doafter="reload">{{ trans('app.mark_as_refunded') }}</a></li>
                </ul>
              </div>
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    {{ trans('app.assign_order_status') }}
                    <span class="sr-only">{{ trans('app.toggle_dropdown') }}</span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    @foreach ($order_statuses as $order_status_number => $order_status)
                        <li><a href="javascript:void(0)" data-link="{{ route('admin.order.order.assignOrderStatus', $order_status_number) }}" class="massAction" data-doafter="reload">{{ $order_status }}</a></li>
                    @endforeach
                    <li><a href="javascript:void(0)" data-link="{{ route('admin.order.order.downloadSelected') }}" class="massAction" data-doafter="reload">{{ trans('app.download') }} {{ trans('app.invoices') }}</a></li>
                </ul>
              </div>
            </div> --}}
        </div> {{-- Box header --}}
        <div class="table-responsive">
            <table class="table table-hover" id="all-order-table-full">
                <thead>
                    <tr>
                        <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th>
                        <th>{{ trans('app.form.po_number_ref') }}</th>
                        <th>{{ trans('app.warehouse') }}</th>
                        <th>{{ trans('app.customer') }}</th>
                        <th>{{ trans('app.form.product_qty') }}</th>
                        <th>{{ trans('app.grand_total') }}</th>
                        <th>{{ trans('app.payment_status') }}</th>
                        <th>{{ trans('app.order_status') }}</th>
                        
                        <th>{{ trans('app.order_date') }}</th>
                        <th>{{ trans('app.form.create_order_by') }}</th>
                        <th>{{ trans('app.form.packed_date') }}</th>
                        <th>{{ trans('app.form.packed_by') }}</th>
                        <th>{{ trans('app.form.shipped_date') }}</th>
                        <th>{{ trans('app.form.shipped_by') }}</th>
                        <th>{{ trans('app.form.delivered_date') }}</th>
                        <th>{{ trans('app.form.delivered_by') }}</th>
                        <th>{{ trans('app.form.paid_date') }}</th>
                        <th>{{ trans('app.form.paid_by') }}</th>
                        <th>{{ trans('app.form.duedate_payment') }}</th>
                        <th>{{ trans('app.form.duedate_days_payment') }}</th>
                        <th>{{ trans('app.form.cancel_date') }}</th>
                        <th>{{ trans('app.form.cancel_by') }}</th>
                        @if (Auth::user()->isFromPlatform())
                            <th>{{ trans('app.business_area') }}</th>
                        @endif
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div><!-- /.box -->
   
</div @endsection

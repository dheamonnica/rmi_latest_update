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
        $payment_statuses = \App\Helpers\ListHelper::payment_statuses();
    @endphp
    <div class="box">
        <div class="box-header with-border">
            <div class="pull-left">
                <h1 class="box-title mr-5 mt-2">{{ trans('app.orders') }}</h1>
            </div>
            <div class="pull-left">
                <select id="merchantOrderReportFilter" class="btn btn-sm btn-default">
                    <option value="" selected>Select Business Unit</option>
                    @foreach ($merchants as $merchant)
                        <option value="{{ $merchant }}">{{ $merchant }}</option>
                    @endforeach
                </select>

                <select id="customerOrderReportFilter" class="btn btn-sm btn-default">
                    <option value="" selected>Select Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer }}">{{ $customer }}</option>
                    @endforeach
                </select>

                <select id="statusOrderReportFilter" class="btn btn-sm btn-default">
                    <option value="">{{ trans('app.all_orders') }}</option>
                    @foreach ($order_statuses as $order_status_number => $order_status)
                        <option value={{ $order_status }}>{{ $order_status }}</option>
                    @endforeach
                </select>
                <select id="paymentStatusOrderReportFilter" class="btn btn-sm btn-default">
                    <option value="">{{ trans('app.form.all_payment_status') }}</option>
                    @foreach ($payment_statuses as $payment_status_number => $payment_status)
                        <option value={{ $payment_status }}>{{ $payment_status }}</option>
                    @endforeach
                </select>

            </div>
        </div> {{-- Box header --}}
        <div class="">
            <table class="table table-hover" id="all-order-table-full">
                <thead>
                    <tr>
                        {{-- <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th> --}}
                        <th>{{ trans('app.form.order_number') }}</th>
                        <th>{{ trans('app.form.po_number_ref') }}</th>
                        <th>{{ trans('app.form.business_unit') }}</th>
                        <th>{{ trans('app.form.client') }}</th>
                        <th>{{ trans('app.form.selling_skuid') }}</th>
                        <th>{{ trans('app.product_name') }}</th>
                        <th>{{ trans('app.form.quantity') }}</th>
                        <th>{{ trans('app.form.unit_price') }}</th>
                        <th>{{ trans('app.form.purchase_price') }}</th>
                        <th>{{ trans('app.form.total') }}</th>
                        <th>{{ trans('app.form.discount') }}</th>
                        <th>{{ trans('app.form.taxrate') }}</th>
                        <th>{{ trans('app.form.grand_total') }}</th>

                        <th>{{ trans('app.form.created_at') }}</th>
                        <th>{{ trans('app.form.created_by') }}</th>
                        <th>{{ trans('app.form.packed_date') }}</th>
                        <th>{{ trans('app.form.packed_by') }}</th>
                        <th>{{ trans('app.form.shipped_date') }}</th>
                        <th>{{ trans('app.form.shipped_by') }}</th>
                        <th>{{ trans('app.form.delivered_date') }}</th>
                        <th>{{ trans('app.form.delivered_by') }}</th>
                        <th>{{ trans('app.form.paid_date') }}</th>
                        <th>{{ trans('app.form.paid_by') }}</th>

                        <th>{{ trans('app.form.sla_order') }} (minutes)</th>
                        <th>{{ trans('app.form.sla_gudang') }} (minutes)</th>
                        <th>{{ trans('app.form.sla_delivery') }} (minutes)</th>
                        <th>{{ trans('app.form.sla_payment') }} (days)</th>

                        <th>{{ trans('app.form.duedate_payment') }}</th>
                        <th>{{ trans('app.form.duedate_days_payment') }}</th>
                        <th>{{ trans('app.form.cancel_date') }}</th>
                        <th>{{ trans('app.form.cancel_by') }}</th>
                        <th>{{ trans('app.form.payment_status') }}</th>
                        <th>{{ trans('app.form.order_status') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div><!-- /.box -->

</div @endsection

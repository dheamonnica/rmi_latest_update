@extends('admin.layouts.master')

@section('content')
    @php
        $order_statuses = \App\Helpers\ListHelper::order_statuses();
        $payment_statuses = \App\Helpers\ListHelper::payment_statuses();
    @endphp
    <div class="box">
        <div class="box-header with-border">
            <div class="pull-left">
                <h1 class="box-title mr-5 mt-2">{{ trans('app.orders') }} PAYMENT DOCUMENt</h1>
            </div>
            <div class="pull-left">
                <select id="merchantOrderPaymentDocReportFilter" class="btn btn-sm btn-default">
                    <option value="" selected>Select Business Unit</option>
                    @foreach ($merchants as $merchant)
                        <option value="{{ $merchant }}">{{ $merchant }}</option>
                    @endforeach
                </select>

                <select id="customerOrderPaymentDocReportFilter" class="btn btn-sm btn-default">
                    <option value="" selected>Select Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer }}">{{ $customer }}</option>
                    @endforeach
                </select>

                <!-- Payment Status Dropdown -->
                <select id="paymentStatusOrderPaymentDocReportFilter" class="btn btn-sm btn-default">
                    <option value="">{{ trans('app.form.all_payment_status') }}</option>
                    <option value="Awaiting payment">Awaiting payment</option>
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                    <option value="Refund Initiated">Refund Initiated</option>
                    <option value="Partially Refunded">Partially Refunded</option>
                    <option value="Refunded">Refunded</option>
                </select>

                <!-- Order Status Dropdown -->
                <select id="statusOrderPaymentDocReportFilter" class="btn btn-sm btn-default">
                    <option value="">All Orders Status</option>
                    <option value="Waiting for Payment">Waiting for Payment</option>
                    <option value="Payment Error">Payment Error</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Fullfiled">Fullfiled</option>
                    <option value="Awaiting Delivery">Awaiting Delivery</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Refunded">Refunded</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Disputed">Disputed</option>
                    <option value="Packed">Packed</option>
                </select>

            </div>
        </div> {{-- Box header --}}
        <div class="">
            <table class="table table-hover" id="payment-doc-table">
                <thead>
                    <tr>
                        <th>{{ trans('app.form.created_at') }}</th>
                        <th>{{ trans('app.form.order_number') }}</th>
                        <th>{{ trans('app.form.po_number_ref') }}</th>
                        <th>{{ trans('app.form.business_unit') }}</th>
                        <th>{{ trans('app.form.client') }}</th>
                        <th>{{ trans('app.form.doc_SI') }}</th>
                        <th>{{ trans('app.form.doc_faktur_pajak') }}</th>
                        <th>{{ trans('app.form.doc_faktur_pajak_terbayar') }}</th>
                        <th>{{ trans('app.form.payment_status') }}</th>
                        <th>{{ trans('app.form.order_status') }}</th>
                        <th>{{ trans('app.options') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div><!-- /.box -->

</div @endsection

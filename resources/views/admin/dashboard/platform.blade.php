@extends('admin.layouts.master')

@section('page-style')
  @include('plugins.ionic')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  @livewireStyles
@endsection

@section('content')
  @include('admin.partials._check_misconfigured_subscription')

 @livewire('dashboard' ,[
  'customer_count' => $customer_count,
  'new_customer_last_30_days' => $new_customer_last_30_days,
  'merchant_count' => $merchant_count,
  'new_merchant_last_30_days' => $new_merchant_last_30_days,
  'total_order_count' => $total_order_count,
  'todays_all_order_count' => $todays_all_order_count,
  'yesterdays_all_order_count' => $yesterdays_all_order_count,
  'todays_sale_amount' => $todays_sale_amount,
  'yesterdays_sale_amount' => $yesterdays_sale_amount,
  'pending_verifications' => $pending_verifications,
  'pending_approvals' => $pending_approvals,
  'dispute_count' => $dispute_count,
  'last_60days_dispute_count' => $last_60days_dispute_count,
  'last_30days_dispute_count' => $last_30days_dispute_count,
])    
@endsection

@section('page-script')
  @livewireScripts

  @include('plugins.filter-orders')
  @include('plugins.chart')

  {!! $chart->script() !!}
  
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.2/Chart.min.js"></script> --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  @stack('js-scripts')
@endsection

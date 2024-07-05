<div>
  <div class="row">
      <div class="col-sm-12">
        <div id="filter-panel">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-md-2">
                  <h3>{{ trans('app.custom_filters')}}</h3>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2 nopadding-right">
                  <div class="form-group">
                    <label>{{ trans('app.warehouse') }}</label>
                    <select style="width: 100%" id="select_warehouse" wire:model="selectedWarehouseOption" class="form-control" >
                      <option value="">{{ trans('app.select_warehouse') }}</option>
                      @foreach ($warehouses as $key => $item)  
                        <option value="{{ $key }}">{{ $item }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-2 nopadding-right nopadding-left">
                  <div class="form-group">
                    <label>{{ trans('app.client') }}</label>
                    <select style="width: 100%" id="select_client" placeholder="placeholder" wire:model="selectedClientOption" class="form-control" >
                    <option value="">{{ trans('app.select_client') }}</option>
                    @foreach ($clients as $key => $item)  
                      <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-2 nopadding-right nopadding-left">
                  <div class="form-group">
                    <label>{{ trans('app.client_group') }}</label>
                    <select style="width: 100%" id="select_client_group" wire:model="selectedClientGroupOption" class="form-control" >
                      <option value="">{{ trans('app.select_client_group') }}</option>
                      @foreach ($client_groups as $key => $item)  
                        <option value="{{ $item }}">{{ $item }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-2 nopadding-right nopadding-left">
                  <div class="form-group">
                    <label>{{ trans('app.category_group') }}</label>
                    <select style="width: 100%" id="select_category_group" wire:model="selectedCategoryGroupOption" class="form-control" >
                      <option value="">{{ trans('app.select_category_group') }}</option>
                      @foreach ($category_groups as $key => $item)  
                        <option value="{{ $key }}">{{ $item }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-2 nopadding-right nopadding-left">
                  <div class="form-group">
                    <label>{{ trans('app.category_sub_group') }}</label>
                    <select style="width: 100%" id="select_category_sub_group" wire:model="selectedCategorySubGroupOption" class="form-control" >
                      <option value="">{{ trans('app.select_category_sub_group') }}</option>
                      @foreach ($category_sub_groups as $key => $item)  
                        <option value="{{ $key }}">{{ $item }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2 nopadding-right ">
                  <div class="form-group">
                    <label>{{ trans('app.username') }}</label>
                    <input type="text" id="orderNumber" name="order_number" value="{{ request()->get('order_number') }}" class="form-control" placeholder="{{ trans('app.user_name') }}" wire:model="userName">
                  </div>
                </div>
                <div class="col-md-2 nopadding-right nopadding-left">
                  <div class="form-group">
                    <label>{{ trans('app.product') }}</label>
                    <input type="text" id="orderNumber" name="order_number" value="{{ request()->get('order_number') }}" class="form-control" placeholder="{{ trans('app.product_name') }}" wire:model="productName">
                  </div>
                </div>
                <div class="col-md-2 nopadding-right nopadding-left">
                  <div class="form-group">
                    <label>{{ trans('app.order_status') }}</label>
                    <select id="orderStatus" class="form-control" name="order_status" wire:model="selectedOrderStatusOption" >
                      <option value="all" @if (request()->get('order_status') == 'all') selected @endif>{{ trans('app.all') }}</option>
                      <option value="STATUS_WAITING_FOR_PAYMENT" @if (request()->get('order_status') == 'STATUS_WAITING_FOR_PAYMENT') selected @endif>{{ trans('app.waiting_for_payment') }}</option>
                      <option value="STATUS_CONFIRMED" @if (request()->get('order_status') == 'STATUS_CONFIRMED') selected @endif>{{ trans('app.confirmed') }}</option>
                      <option value="STATUS_FULFILLED" @if (request()->get('order_status') == 'STATUS_FULFILLED') selected @endif>{{ trans('app.fulfilled') }}</option>
                      <option value="STATUS_AWAITING_DELIVERY" @if (request()->get('order_status') == 'STATUS_AWAITING_DELIVERY') selected @endif>{{ trans('app.awaiting_delivery') }}</option>
                      <option value="STATUS_DELIVERED" @if (request()->get('order_status') == 'STATUS_DELIVERED') selected @endif>{{ trans('app.delivered') }}</option>
                      <option value="STATUS_CANCELED" @if (request()->get('order_status') == 'STATUS_CANCELED') selected @endif>{{ trans('app.canceled') }}</option>
                      <option value="STATUS_PAYMENT_ERROR" @if (request()->get('order_status') == 'STATUS_PAYMENT_ERROR') selected @endif>{{ trans('app.payment_error') }}</option>
                      <option value="STATUS_RETURNED" @if (request()->get('order_status') == 'STATUS_RETURNED') selected @endif>{{ trans('app.returns') }}</option>
                      <option value="STATUS_DISPUTED" @if (request()->get('order_status') == 'STATUS_DISPUTED') selected @endif>{{ trans('app.disputed') }}</option>
                      <option value="STATUS_PACKED" @if (request()->get('order_status') == 'STATUS_PACKED') selected @endif>{{ trans('app.packed') }}</option>
                    </select>
                  </div>
                </div>
  
                <div class="col-md-2 nopadding-right nopadding-left">
                  <div class="form-group">
                    <label>{{ trans('app.payment_status') }}</label>
                    <select id="paymentStatus"class="form-control" name="payment_status" wire:model="selectedPaymentStatusOption" >
                      <option value="" @if (request()->get('order_status') == 'all') selected @endif>{{ trans('app.all') }}</option>
                      <option value="PAYMENT_STATUS_UNPAID" @if (request()->get('order_status') == 'PAYMENT_STATUS_UNPAID') selected @endif>{{ trans('app.unpaid') }}</option>
                      <option value="PAYMENT_STATUS_PENDING" @if (request()->get('order_status') == 'PAYMENT_STATUS_PENDING') selected @endif>{{ trans('app.pending') }}</option>
                      <option value="PAYMENT_STATUS_PAID" @if (request()->get('order_status') == 'PAYMENT_STATUS_PAID') selected @endif>{{ trans('app.paid') }}</option>
                      <option value="PAYMENT_STATUS_REFUNDED" @if (request()->get('order_status') == 'PAYMENT_STATUS_REFUNDED') selected @endif>{{ trans('app.refunded') }}</option>
                    </select>
                  </div>
                </div>
  
                <div class="col-md-2 nopadding-left">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-default pull-right" name="clear" id="clear"><i class="fa fa-caret-left"></i> {{ trans('app.clear') }}</button>
                  </div>
                </div>
              </div>

              <hr>
              <div class="row">
                <div class="col-md-2">
                  <h3>{{ trans('app.timeframe_filters')}}</h3>
                </div>
                <div class="col-md-2 nopadding-right">
                  <div class="form-group">
                    <label>{{ trans('app.interval') }}</label>
                    <select id="time_interval"class="form-control" name="interval" wire:model="selectedIntervalOption" >
                      <option value="" selected>{{ trans('app.select_timeframe_type') }}</option>
                      <option value="DAILY">{{ trans('app.daily') }}</option>
                      {{-- <option value="WEEK">{{ trans('app.week') }}</option> --}}
                      <option value="MONTH">{{ trans('app.month') }}</option>
                      <option value="YEAR">{{ trans('app.year') }}</option>
                    </select>
                  </div>
                </div>
                @if ($selectedIntervalOption == 'DAILY')
                  <div class="col-md-2 nopadding-right">
                    <div class="form-group">
                      <label>{{ trans('app.start_date') }}</label>
                      <input type="text" id="datepicker_start_date" class="form-control" wire:model="selectedStartDate">
                    </div>
                  </div>
                  <div class="col-md-2 nopadding-right">
                    <div class="form-group">
                      <label>{{ trans('app.end_date') }}</label>
                      <input type="text" id="datepicker_end_date" class="form-control" wire:model="selectedEndDate">
                    </div>
                  </div>
                @elseif ($selectedIntervalOption == 'WEEK')
                  <div class="col-md-2 nopadding-right">
                      <div class="form-group">
                          <label>{{ trans('app.year') }}</label>
                          <input type="text" id="yearPicker" wire:model="selectedYearWeek" class="form-control">
                      </div>
                  </div>
                  <div class="col-md-2 nopadding-right">
                      <div class="form-group">
                          <label>{{ trans('app.week') }}</label>
                          <input type="text" id="weekPicker" wire:model="selectedWeek" class="form-control">
                      </div>
                  </div>
                @elseif ($selectedIntervalOption == 'MONTH')
                  <div class="col-md-2 nopadding-right">
                    <div class="form-group">
                      <label>{{ trans('app.month_start') }}</label>
                      <input type="text" id="monthStartPicker" wire:model="selectedYearMonthStart" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-2 nopadding-right">
                    <div class="form-group">
                      <label>{{ trans('app.month_end') }}</label>
                      <input type="text" id="monthEndPicker" wire:model="selectedYearMonthEnd" class="form-control">
                    </div>
                  </div>
                @elseif ($selectedIntervalOption == 'MONTH')  
                  <div class="col-md-2 nopadding-right ">
                    <div class="form-group">
                      <label>{{ trans('app.year_start') }}</label>
                      <input type="text" id="yearStartPicker" wire:model="selectedYearStart" class="form-control">
                    </div>
                  </div>
                  <div class="col-md-2 nopadding-right ">
                    <div class="form-group">
                      <label>{{ trans('app.year_end') }}</label>
                      <input type="text" id="yearEndPicker" wire:model="selectedYearEnd" class="form-control">
                    </div>
                  </div>
                @else
                  <div></div>
                @endif
              </div> 
            </div>
          </div>
        </div>
      </div>
  </div>

  {{-- <div class="row">
    <div class="col-sm-12">
      <div id="filter-panel">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
                <div class="col-md-2 nopadding-right">
                  Filter value (debug only): <br> 
                  warehouse : {{ $selectedWarehouseOption }} <br>
                  client : {{ $selectedClientOption }} <br>
                  client_group : {{ $selectedClientGroupOption }} <br>
                  category group : {{ $selectedCategoryGroupOption }} <br>
                  category sub group : {{ $selectedCategorySubGroupOption }} <br>
                  order status : {{ $selectedOrderStatusOption }} <br>
                  payment status : {{ $selectedPaymentStatusOption }} <br>
                  product name : {{ $productName }} <br>
                  user name : {{ $userName }} <br>
                </div>
                <div class="col-md-2">
                  Filter Timeframe
                  interval: {{ $selectedIntervalOption }} <br>
                  start_date: {{ $selectedStartDate }} <br>
                  end_date : {{ $selectedEndDate }} <br>
                  Year_week : {{ $selectedYearWeek }} <br>
                  week : {{ $selectedWeek }} <br>
                  year month start : {{ $selectedYearMonthStart }} <br>
                  year month end : {{ $selectedYearMonthEnd }} <br>
                  year start : {{ $selectedYearStart }} <br>
                  year End : {{ $selectedYearEnd }} <br>
                </div>
                <div class="col-md-2">
                  result : <br>
                    'customer count' => {{ $customer_count }},<br>
                    'new_customer_last_30_days' => {{$new_customer_last_30_days}},<br>
                    'total_profit' => Rp {{$total_profit}},<br>
                    'total_order_created' => {{$total_order_created}},<br>
                    'qty_ordered' => {{$qty_ordered}},<br>
                    'gross_value' => {{$gross_value}},<br>
                    -- info boxes process status -- <br>
                    'orders_process' => {{$orders_process}},<br>
                    'packing_process' => {{$packing_process}},<br>
                    'delivery_process' => {{$delivery_process}},<br>
                    'payment_process' => {{$payment_process}},<br>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> --}}

  <div class="row">
    <div class="col-sm-12">
      <div id="filter-panel">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row dashboard-total">
              <x-dashboard-card :count=$customer_count :count2=$new_customer_last_30_days :options="
                [
                  'name' => trans('app.dashboard.customer_active'),
                  'is_count_plus' => true,
                  'is_currency' => false, 
                  'count_plus_name' => trans('app.dashboard.total_all_customer_active'),
                  'icon' => 'people',
                  'color' => 'danger' 
                ]" />
              <x-dashboard-card :count=$total_profit :count2=0 :options="
              [
                'name' => trans('app.dashboard.total_profit_exclude_ops'),
                'is_count_plus' => false, 
                'is_currency' => true, 
                'count_plus_name' => trans('app.dashboard.total_all_order'),
                'icon' => 'wallet',
                'color' => 'info' 
              ]"/>
              <x-dashboard-card :count=$qty_ordered :count2=$total_order_created :options="
              [
                'name' => trans('app.dashboard.qty_pcs'),
                'is_count_plus' => true,
                'is_currency' => false,  
                'count_plus_name' => trans('app.dashboard.total_all_order'),
                'icon' => 'cube',
                'color' => 'primary' 
              ]"/>
              <x-dashboard-card :count=$gross_value :count2=0 :options="
              [
                'name' => trans('app.dashboard.gross_value'),
                'is_count_plus' => false,
                'is_currency' => true,  
                'count_plus_name' => trans('app.dashboard.total_all_order'),
                'icon' => 'cash', 
                'color' => 'success'
              ]"/>
            </div>
            <div class="row">
              <x-info-card :count=$orders_process :options="
              [
                'name' => trans('app.dashboard.order'),
                'route' => 'admin.vendor.shop.verifications',
                'action' => trans('app.take_action'), 
                'icon' => 'cart', 
                'bg-color' => 'yellow'
              ]"/>
              <x-info-card :count=$packing_process :options="
              [
                'name' => trans('app.dashboard.packing'),
                'route' => 'admin.vendor.shop.verifications',
                'action' => trans('app.take_action'), 
                'icon' => 'archive', 
                'bg-color' => 'blue'
              ]"/>
              <x-info-card :count=$delivery_process :options="
              [
                'name' => trans('app.dashboard.delivery'),
                'route' => 'admin.vendor.shop.verifications',
                'action' => trans('app.take_action'), 
                'icon' => 'boat', 
                'bg-color' => 'green'
              ]"/>
              <x-info-card :count=$payment_process :options="
              [
                'name' => trans('app.dashboard.payment'),
                'route' => 'admin.vendor.shop.verifications',
                'action' => trans('app.take_action'), 
                'icon' => 'card', 
                'bg-color' => 'orange'
              ]"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <div id="filter-panel">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-6 nopadding-right">
                  <h3>Chart & Tables Time Filters</h3>
                  <div class="btn-group" role="group" aria-label="Basic example">
                      <button type="button" class="btn btn-primary {{ $selectedThisWeekFilter ? 'active' : '' }}" wire:click="toggleFilter('selectedThisWeekFilter')">This Week</button>
                      <button type="button" class="btn btn-primary {{ $selectedThisMonthFilter ? 'active' : '' }}" wire:click="toggleFilter('selectedThisMonthFilter')">This Month</button>
                      <button type="button" class="btn btn-primary {{ $selectedThisYearFilter ? 'active' : '' }}" wire:click="toggleFilter('selectedThisYearFilter')">This Year</button>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  @php
                      $chartDataDO = [
                        'labels' => [],  // Fill with month names later
                        'datasets' => [
                            [
                                'label' => 'Daily Orders (Excluding Cancelled)',
                                'data' => [],  // Fill with daily order counts later
                            ],
                        ],
                    ];

                    foreach ($chart1_data_d1 as $orderCount) {
                        $chartDataDO['labels'][] = $orderCount->order_date ?? '01-01-1900';  // Extract month name
                        $chartDataDO['datasets'][0]['data'][] = $orderCount->count ?? 0;
                    }

                  @endphp
                  <x-p-o-chart :chart-data="$chartDataDO" :options="[
                    'name' => trans('app.dashboard.table.po_status_timeframe')
                  ]"/>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  @php
                      $chartDataMTD = [
                          'labels' => [],  // Fill with month names later
                          'datasets' => [
                              [
                                  'label' => 'Daily Orders Count Month To Date',
                                  'data' => [],  // Fill with daily order counts later
                              ],
                              [
                                  'label' => 'Daily Orders Count Today',
                                  'data' => [],  // Fill with daily order counts later
                              ],
                          ],
                      ];

                      foreach ($chart1_data_d3 as $orderCount) {
                        $chartDataMTD['labels'][] = $orderCount->order_date ?? '01-01-1900';  // Extract month name
                        $chartDataMTD['datasets'][0]['data'][] = $orderCount->mtd_count ?? 0;
                        $chartDataMTD['datasets'][1]['data'][] = $orderCount->count ?? 0;
                    }
                  @endphp
                  <x-chart :chart-data="$chartDataMTD" :options="[
                    'name' => trans('app.dashboard.table.po_status')
                  ]"/>
                </div>
                <div class="col-md-6">
                  @php
                      $chartDataPie = [
                        'labels' => [],  // Fill with month names later
                        'datasets' => [
                            [
                                'label' => 'Warehouse Count',
                                'data' => [],  // Fill with daily order counts later
                            ],
                        ],
                    ];

                    foreach ($chart1_data_d2 as $warehouse) {
                        $chartDataPie['labels'][] = $warehouse->name ?? '-';  // Extract month name
                        $chartDataPie['datasets'][0]['data'][] = $warehouse->count_order ?? 0;
                    }
                  @endphp
                  <x-pie-chart :chart-data="$chartDataPie" :options="[
                    'name' => trans('app.dashboard.table.warehouse_total')
                  ]"/>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <div id="filter-panel">
          <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                  <div class="col-md-12">
                    <x-table :header="[
                        'warehouse_name' => trans('app.dashboard.table.warehouse_name'),
                        'product_name' => trans('app.dashboard.table.product_name'),
                        'expired_date' => trans('app.dashboard.table.expired_date'),
                        'qty' => trans('app.dashboard.table.qty'),
                        'avg_selling_qty' => trans('app.dashboard.table.avg_selling_qty'),
                        'selling_price' => trans('app.dashboard.table.selling_price'),
                        'buying_price' => trans('app.dashboard.table.buying_price'),
                        'total' => trans('app.dashboard.table.total'),
                        'note' => trans('app.dashboard.table.note'),
                        'grand_total' => trans('app.dashboard.table.grand_total'),
                    ]" :options="[
                      'table_name' => trans('app.dashboard.table.stock_preview'),
                    ]" :data-body="$table1_data" />
                  </div>
                  <div class="col-md-12">
                    <x-table :header="[
                      'date' => trans('app.dashboard.table.date'),
                      'from' => trans('app.dashboard.table.from'),
                      'to' => trans('app.dashboard.table.to'),
                      'product_desc' => trans('app.dashboard.table.product_desc'),
                      'qty' => trans('app.dashboard.table.qty'),
                      'updated_by' => trans('app.dashboard.table.updated_by'),
                  ]" :options="[
                      'table_name' => trans('app.dashboard.table.log_stock_movement'),
                    ]"
                  :data-body="$table2_data"  
                  />
                  </div>
                  <div class="col-md-12">
                    <x-table :header="[
                      'date_order' => trans('app.dashboard.table.date_order'),
                      'username' => trans('app.dashboard.table.username'),
                      'hospital_name' => trans('app.dashboard.table.hospital_name'),
                      'no_po_ref' => trans('app.dashboard.table.no_po_ref'),
                      'status' => trans('app.dashboard.table.status'),
                  ]" :options="[
                      'table_name' => trans('app.dashboard.table.log_activity_order')
                    ]" 
                    :data-body="$table3_data" 
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <x-table :header="[
                      'name' => trans('app.dashboard.table.name'),
                      'count_order' => trans('app.dashboard.table.count_order'),
                      'revenue' => trans('app.dashboard.table.revenue'),
                      'last_month' => trans('app.dashboard.table.last_month'),
                      'last_year' => trans('app.dashboard.table.last_year'),
                      'target' => trans('app.dashboard.table.target'),
                  ]" :options="[
                      'table_name' => trans('app.dashboard.table.top_customer')
                    ]"
                    :data-body="$table4_data" 
                    />
                  </div>
                  <div class="col-md-12">
                    <x-table :header="[
                      'name' => trans('app.dashboard.table.name'),
                      'count_order' => trans('app.dashboard.table.count_order'),
                      'revenue' => trans('app.dashboard.table.revenue'),
                      'last_month' => trans('app.dashboard.table.last_month'),
                      'last_year' => trans('app.dashboard.table.last_year'),
                      'target' => trans('app.dashboard.table.target'),
                      'acheivement' => trans('app.dashboard.table.acheivement'),
                    ]" :options="[
                      'table_name' => trans('app.dashboard.table.warehouse')
                    ]" 
                    :data-body="$table5_data"
                    />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <x-table :header="[
                      'name' => trans('app.dashboard.table.name'),
                      'count_order' => trans('app.dashboard.table.count_order'),
                      'revenue' => trans('app.dashboard.table.revenue'),
                      'last_month_revenue' => trans('app.dashboard.table.last_month_revenue'),
                      'last_year_revenue' => trans('app.dashboard.table.last_year_revenue'),
                  ]" :options="[
                      'table_name' => trans('app.dashboard.table.top_worst_product')
                    ]"
                    :data-body="$table6_data"
                    />
                  </div>
                  <div class="col-md-12">
                    <x-table :header="[
                      'employee_name' => trans('app.dashboard.table.employee_name'),
                      'warehouse_name' => trans('app.dashboard.table.warehouse_name'),
                      'confirmed' => trans('app.dashboard.table.confirmed'),
                      'packed' => trans('app.dashboard.table.packed'),
                      'delivered' => trans('app.dashboard.table.delivered'),
                      'paided' => trans('app.dashboard.table.paided'),
                      'total' => trans('app.dashboard.table.total'),
                  ]" :options="[
                      'table_name' => trans('app.dashboard.table.kpi')
                    ]"
                    :data-body="$table7_data"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.2/Chart.min.js"></script>
<script>
function initializeDatepicker(interval) {
        $('#datepicker').datepicker('destroy'); // Destroy any existing datepicker
        @this.emit('resetTimeFrameFilter');

        let optionsStart = {};
        let optionsEnd = {};
        let propsStart = '';
        let propsEnd= '';
        let livewireStart = '';
        let livewireEnd = '';

        switch(interval) {
            case 'DAILY':
                propsStart = '#datepicker_start_date';
                propsEnd= '#datepicker_end_date';
                livewireStart = 'startDateUpdated';
                livewireEnd = 'endDateUpdated';
                optionsStart = {format: 'yyyy-mm-dd'};
                optionsEnd = {format: 'yyyy-mm-dd'};
               
                break;
            case 'WEEK':

                propsStart = '#yearPicker';
                propsEnd= '#weekPicker';
                livewireStart = 'yearWeekUpdated';
                livewireEnd = 'weekUpdated';
                optionsStart = { format: 'yyyy', startView: 'years', minViewMode: 'years', autoclose: true }
                optionsEnd = { format: 'yyyy-WW', autoclose: true, calendarWeeks: true }

                break;
            case 'MONTH':
                propsStart = '#monthStartPicker';
                propsEnd= '#monthEndPicker';
                livewireStart = 'monthStartUpdated';
                livewireEnd = 'monthEndUpdated';
                optionsStart = { format: 'yyyy-mm', startView: 'months', minViewMode: 'months', autoclose: true };
                optionsEnd = { format: 'yyyy-mm', startView: 'months', minViewMode: 'months', autoclose: true };

                break;
            case 'YEAR':
                propsStart = '#yearStartPicker';
                propsEnd= '#yearEndPicker';
                livewireStart = 'yearStartUpdated';
                livewireEnd = 'yearEndUpdated';
                optionsStart = { format: 'yyyy', startView: 'years', minViewMode: 'years', autoclose: true };
                optionsEnd = { format: 'yyyy', startView: 'years', minViewMode: 'years', autoclose: true };

                break;
        }

        $(propsStart).datepicker(optionsStart).on('changeDate', function(e) {
          @this.emit(livewireStart, e.format(0, optionsStart.format));
        });

        $(propsEnd).datepicker(optionsEnd).on('changeDate', function(e) {
          @this.emit(livewireEnd, e.format(0, optionsEnd.format));
        });

    }

document.addEventListener('livewire:load', function () {
    // $('#select_warehouse').select2();

    $('#select_warehouse').on('change', function (e) {
        // var data = $('#select_warehouse').select2("val");
        @this.emit('updatedCustomerCount');
    });

    $('#clear').on('click', function (e) {
        @this.emit('resetFilters');
    });

    initializeDatepicker(@this.selectedIntervalOption);

    window.addEventListener('reinitialize-datepicker', event => {
        initializeDatepicker(event.detail.interval);
    });
});
</script>

<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-danger card-img-holder text-white">
            <div class="card-body">
            <img src="/images/circle.svg" class="card-img-absolute" alt="circle-image">
            <h4 class="font-weight-normal mb-3">{{ trans('app.dashboard.customer_active') }} <i class="icon ion-md-people float-right"></i>
            </h4>
            <h2 class="mb-5"> {{ $customer_count }} </h2>
            <h6 class="card-text"><i class="icon ion-md-add"></i> {{ trans('app.dashboard.total_all_customer_active', ['new' => 0, 'model' => trans('app.dashboard.customer_active')]) }}</h6>
            </div>
        </div>
    </div>

    <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-info card-img-holder text-white">
            <div class="card-body">
                <img src="/images/circle.svg" class="card-img-absolute" alt="circle-image">
                <h4 class="font-weight-normal mb-3">{{ trans('app.dashboard.total_profit_exclude_ops') }} <i class="fa fa-bar-chart-o float-right"></i></h4>
                <h2 class="mb-5"> 5 </h2>
                <h6 class="card-text"><i class="icon ion-md-add"></i> {{ trans('app.dashboard.total_all_order', ['new' => 0, 'model' => trans('app.dashboard.total_profit_exclude_ops')]) }}</h6>
            </div>
        </div>
    </div>

    <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-primary card-img-holder text-white">
            <div class="card-body">
            <img src="/images/circle.svg" class="card-img-absolute" alt="circle-image">
            <h4 class="font-weight-normal mb-3">{{ trans('app.dashboard.qty_pcs') }} <i class="icon ion-md-cart float-right"></i>
            </h4>
            <h2 class="mb-5"> 0 </h2>
    
            {{-- @php
                $difference = $todays_all_order_count - $yesterdays_all_order_count;
                $todays_order_percents = $todays_all_order_count == 0 ? 0 : round(($difference / $todays_all_order_count) * 100);
            @endphp --}}
    
            {{-- @if ($todays_all_order_count == 0) --}}
                <h6 class="card-text"><i class="icon ion-md-hourglass"></i> {{ trans('messages.no_orders', ['date' => trans('app.today')]) }}</h6>
            {{-- @else
                <h6 class="card-text"><i class="icon ion-md-arrow-{{ $difference < 0 ? 'down' : 'up' }}"></i> {{ trans('messages.todays_order_percents', ['percent' => $todays_order_percents, 'state' => $difference < 0 ? trans('app.down') : trans('app.up')]) }}</h6>
            @endif --}}
            </div>
        </div>
    </div>
    
    <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-success card-img-holder text-white">
            <div class="card-body">
            <img src="/images/circle.svg" class="card-img-absolute" alt="circle-image">
            <h4 class="font-weight-normal mb-3">{{ trans('app.dashboard.gross_value') }} <i class="icon ion-md-wallet float-right"></i>
            </h4>
            <h2 class="mb-5">
                {{ get_formated_currency(0, 2, config('system_settings.currency.id')) }}
            </h2>
        
            {{-- @php
                $difference = $todays_sale_amount - $yesterdays_sale_amount;
                $todays_sale_percents = $todays_sale_amount == 0 ? 0 : round(($difference / $todays_sale_amount) * 100);
            @endphp
        
            @if ($todays_sale_amount == 0) --}}
                <h6 class="card-text"><i class="icon ion-md-hourglass"></i> {{ trans('messages.no_sale', ['date' => trans('app.today')]) }}</h6>
            {{-- @else
                <h6 class="card-text"><i class="icon ion-md-arrow-{{ $difference < 0 ? 'down' : 'up' }}"></i> {{ trans('messages.todays_sale_percents', ['percent' => $todays_sale_percents, 'state' => $difference < 0 ? trans('app.down') : trans('app.up')]) }}</h6>
            @endif --}}
            </div>
        </div>
    </div> 
</div>

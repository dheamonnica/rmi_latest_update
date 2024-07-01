<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="nav-tabs-custom">
              <ul class="nav nav-tabs nav-justified">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-dollar"></i>
                    {{ trans('app.sales_graph') }}</h3>
                </div>
              </ul> <!-- /.nav .nav-tabs -->
    
              <div class="tab-content total-sale-graph">
                <!-- Tab buttons for user interaction -->
                <div class="tab-container">
                  <div class="tab-button active" data-timeframe="week">{{ trans('app.this_week') }}</div>
                  <div class="tab-button" data-timeframe="month">{{ trans('app.this_month') }}</div>
                  <div class="tab-button" data-timeframe="year">{{ trans('app.this_year') }}</div>
                </div>
    
                <!-- Chart canvas container -->
                <canvas id="saleChart" height="100vh"></canvas>
              </div> <!-- /.tab-content -->
            </div> <!-- /.nav-tabs-custom -->
          </div> <!-- /.box -->
        </div>
    </div>
</div>

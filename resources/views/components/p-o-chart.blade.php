<div>
    <!-- Live as if you were to die tomorrow. Learn as if you were to live forever. - Mahatma Gandhi -->
    <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="nav-tabs-custom">
              <ul class="nav nav-tabs nav-justified">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-dollar"></i>
                    {{ $options['name'] }}</h3>
                </div>
              </ul> <!-- /.nav .nav-tabs -->
    
              <div class="tab-content total-sale-graph">
                <!-- Chart canvas container -->
                <canvas id="saleChart" height="100vh"></canvas>
              </div> <!-- /.tab-content -->
            </div> <!-- /.nav-tabs-custom -->
          </div> <!-- /.box -->
        </div>
    </div>
</div>
@push('js-scripts')
<script>
  const ctx = document.getElementById('saleChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: JSON.parse('{!! json_encode($chartData['labels']) !!}'),
      datasets: [{
        label: JSON.parse('{!! json_encode($chartData['datasets'][0]['label']) !!}'),
        data: JSON.parse('{!! json_encode($chartData['datasets'][0]['data']) !!}'),
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
@endpush
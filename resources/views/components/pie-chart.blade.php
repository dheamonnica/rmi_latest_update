<div>
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs nav-justified">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-pie-chart"></i>
                {{ $options['name'] }}</h3>
            </div>
          </ul> <!-- /.nav .nav-tabs -->

          <div class="tab-content">
            <!-- Chart canvas container -->
            <canvas id="productChart" style="height: 50vh !important;"></canvas>
          </div> <!-- /.tab-content -->
        </div> <!-- /.nav-tabs-custom -->
      </div> <!-- /.box -->
    </div>
  </div>
</div>
@push('js-scripts')
<script>
    var ctx3 = document.getElementById("productChart").getContext('2d');
    var productChart = new Chart(ctx3, {
      type: 'doughnut',
      data: {
        labels: JSON.parse('{!! json_encode($chartData['labels']) !!}'),
        datasets: [{
          backgroundColor: [
            "#f39c12",
            "#ef486a",
            "#0abb75"
          ],
          data: JSON.parse('{!! json_encode($chartData['datasets'][0]['data']) !!}')
        }]
      }
    });
  </script>
@endpush
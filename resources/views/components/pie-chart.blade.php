<div>
    <div class="box">
        <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-pie-chart"></i>
            {{ $options['name'] }}
        </div>
        <div class="donutChart" style="min-height: 340px; max-height: 700px; padding: 30px 0;">
        <canvas id="productChart" class=""></canvas>
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
<div>
    <div class="box">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs nav-justified">
                <div class="box-header with-border">
                    <h3 class="box-title">
                    <i class="icon ion-md-pulse hidden-sm"></i>
                    {{ $options['name'] }}
                </div>
            </ul>
        <!-- /.nav .nav-tabs -->

            <div class="tab-content">
                <div class="tab-pane active" id="visitors_tab">
                    <div>
                        <canvas id="myChart1"></canvas>
                    </div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div><!-- /.box -->
</div>

@push('js-scripts')
<script>
    const ctx1 = document.getElementById('myChart1');

new Chart(ctx1, {
  data: {
    labels: JSON.parse('{!! json_encode($chartData['labels']) !!}'),
    datasets: [
      {
        type: 'line',
        label: JSON.parse('{!! json_encode($chartData['datasets'][0]['label']) !!}'),
        data: JSON.parse('{!! json_encode($chartData['datasets'][0]['data']) !!}'),
        borderWidth: 1,
        backgroundColor: 'rgba(255, 99, 132, 0.2)', // Optional: Set background color for the first line
        borderColor: 'rgba(255, 99, 132, 1)', // Optional: Set border color for the first line
        tension: 0.3,
      },
      {
        type: 'bar',
        label: JSON.parse('{!! json_encode($chartData['datasets'][1]['label']) !!}'),
        data: JSON.parse('{!! json_encode($chartData['datasets'][1]['data']) !!}'),
        borderWidth: 1,
        backgroundColor: 'rgba(54, 162, 235, 0.2)', // Optional: Set background color for the second line
        borderColor: 'rgba(54, 162, 235, 1)', // Optional: Set border color for the second line
      },
      // You can add more datasets here for additional lines
    ]
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
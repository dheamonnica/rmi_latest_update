<div>
  <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">
          {{ $options['table_name'] ?? '-'}}
      </div>
      <table class="table table-bordered">
        <thead>
          <tr>
            @if(isset($header))
              @foreach ($header as $item) 
              <th>{{ $item }}</th>
              @endforeach
            @endif
          </tr>
        </thead>
        <tbody class="box-body">
          @if(count($dataBody) > 0)
            @foreach ($dataBody as $row) <tr>
                @foreach ($header as $key => $item) <td>{{ $row[$key] ?? '-' }}</td> @endforeach
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="{{ count($header) }}">No data available.</td> </tr>
          @endif
        </tbody>
      </table>
  </div>
</div>
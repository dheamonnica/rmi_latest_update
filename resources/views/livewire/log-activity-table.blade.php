<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ trans('app.top_sale_brands') }}
          </div>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>{{ trans('app.image') }}</th>
                <th>{{ trans('app.name') }}</th>
                <th>{{ trans('app.sold') }}</th>
                <th>{{ trans('app.country') }}</th>
                <th>{{ trans('app.action') }}</th>
              </tr>
            </thead>
            <tbody class="box-body">
              @foreach ($top_selling_brands as $manufacturer)
                <tr>
                  <td><img src="{{ get_logo_url($manufacturer, 'tiny') }}" class="img-sm" alt="{{ trans('app.image') }}"></td>
                  <td><a href="#" class="ajax-modal-btn">
                      {{ $manufacturer->name }}
                    </a></td>
                  <td><span class="label label-outline">{{ $manufacturer->inventories_sum_sold_quantity }}</span></td>
                  <td>{{ optional($manufacturer->country)->name }}</td>
                  <td>
                    @can('update', $manufacturer)
                      <a href="javascript:void(0)" data-link="{{ route('admin.catalog.manufacturer.edit', $manufacturer->id) }}" class="ajax-modal-btn btn btn-primary"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
                    @endcan
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
</div>

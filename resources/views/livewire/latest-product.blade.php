<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ trans('app.recently_added_products') }}
          </div>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>{{ trans('app.image') }}</th>
                <th>{{ trans('app.name') }}</th>
                <th>{{ trans('app.sold') }}</th>
                <th>{{ trans('app.gtin') }}</th>
                <th>{{ trans('app.action') }}</th>
              </tr>
            </thead>
            <tbody class="box-body">
              @foreach ($latest_products as $product)
                <tr>
                  <td><img src="{{ get_storage_file_url(optional($product->featuredImage)->path, 'tiny') }}" alt="{{ $product->name }}" class="img-thumbnail"></td>
                  <td><a href="javascript:void(0)" data-link="{{ route('admin.catalog.product.show', $product->id) }}" class="ajax-modal-btn">
                      {{ $product->name }}
                    </a></td>
                  <td><span class="label label-outline">{{ $product->inventories_sum_sold_quantity }}</span></td>
                  <td><span class="label label-outline">{{ $product->gtin_type }}</span> {{ $product->gtin }}</td>
                  <td>
                    @can('update', $product)
                      <a class="btn btn-primary" href="{{ route('admin.catalog.product.edit', $product->id) }}"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>
                    @endcan
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>

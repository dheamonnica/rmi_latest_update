<div>
    {{-- The Master doesn't talk, he acts. --}}
    <div class="col-md-3">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ trans('app.top_vendors') }}
          </div>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>{{ trans('app.name') }}</th>
                <th><i class="icon ion-md-cart"></i></th>
                <th>{{ trans('app.revenue') }}</th>
              </tr>
            </thead>
            <tbody class="box-body">
              @forelse($top_vendors as $vendor)
                <tr>
                  <td>
                    <img src="{{ get_storage_file_url(optional($vendor->logoImage)->path, 'tiny') }}" class="img-circle" alt="{{ trans('app.logo') }}">
                    <p class="indent5">
                      @can('view', $vendor)
                        <a href="javascript:void(0)" data-link="{{ route('admin.vendor.shop.show', $vendor->id) }}" class="ajax-modal-btn modal-btn">{{ $vendor->name }}</a>
                      @else
                        {{ $vendor->name }}
                      @endcan
                    </p>
                  </td>
                  <td>
                    <span class="label label-outline">{{ $vendor->inventories_count }}</span>
                  </td>
                  <td>
                    {{ $vendor->lifetime_value }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3">{{ trans('app.no_data_found') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>

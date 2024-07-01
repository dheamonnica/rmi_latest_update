<div>
    {{-- Success is as dangerous as failure. --}}
    <div class="col-md-3">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ trans('app.top_customers') }}
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
              @forelse($top_customers as $customer)
                <tr>
                  <td>
                    @if ($customer->image)
                      <img src="{{ get_storage_file_url(optional($customer->image)->path, 'tiny') }}" class="img-circle" alt="{{ trans('app.avatar') }}">
                    @else
                      <img src="{{ get_gravatar_url($customer->email, 'tiny') }}" class="img-circle" alt="{{ trans('app.avatar') }}">
                    @endif
                    <p class="indent5">
                      @can('view', $customer)
                        <a href="javascript:void(0)" data-link="{{ route('admin.admin.customer.show', $customer->id) }}" class="ajax-modal-btn modal-btn">{{ $customer->getName() }}</a>
                      @else
                        {{ $customer->getName() }}
                      @endcan
                    </p>
                  </td>
                  <td>
                    <span class="label label-outline">{{ $customer->orders_count }}</span>
                  </td>
                  <td>{{ get_formated_currency(round($customer->orders->sum('total')), 2, config('system_settings.currency.id')) }}</td>
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

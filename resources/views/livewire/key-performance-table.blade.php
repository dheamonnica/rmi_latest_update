<div>
    {{-- In work, do what you enjoy. --}}

    <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ trans('app.top_sale_categories') }}
          </div>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>{{ trans('app.image') }}</th>
                <th>{{ trans('app.name') }}</th>
                <th>{{ trans('app.sold') }}</th>
                <th>{{ trans('app.parent') }}</th>
                <th>{{ trans('app.action') }}</th>
              </tr>
            </thead>
            <tbody class="box-body">
              @foreach ($top_selling_categories as $category)
                <tr>
                  <td><img src="{{ get_storage_file_url(optional($category->featureImage)->path, 'tiny') }}" alt="{{ $category->name }}" class="img-thumbnail"></td>
                  <td><a href="#" class="ajax-modal-btn">
                      {{ $category->name }}
                    </a></td>
                  <td><span class="label label-outline">{{ $category->listings_sum_sold_quantity }}</span></td>
                  <td>{{ $category->subGroup->name }} <i class="fa fa-angle-double-right small"></i> {{ $category->subGroup->group->name }}</td>
                  <td>
                    @can('update', $category)
                      <a href="javascript:void(0)" data-link="{{ route('admin.catalog.category.edit', $category->id) }}" class="ajax-modal-btn btn btn-primary"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
                    @endcan
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
</div>

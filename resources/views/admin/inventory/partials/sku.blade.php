@if ($inventory->variants_count)
  @can('view', $inventory)
    <a href="javascript:void(0)" data-link="{{ route('admin.stock.inventory.show', $inventory->id) }}" class="ajax-modal-btn">
      <span class="label label-default">
        {{ $inventory->variants_count . ' ' . trans('app.skus') }}
      </span>
    </a>
  @endcan
@else
  {{ $inventory->sku }}
@endif

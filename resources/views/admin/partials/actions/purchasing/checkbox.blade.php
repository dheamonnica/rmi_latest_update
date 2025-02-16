<td>
  {{-- @can('massDelete', \App\Models\Product::class)
    @if (\Auth::user()->isFromPlatform() || ($product->inventories_count == 0 && $product->shop_id == \Auth::user()->shop_id)) --}}
      {{-- @if ( $purchasing->manufacture_number != null)
          <i class="fa fa-check"></i>
      @else --}}
        <input id="{{ $purchasing->item_id }}" type="checkbox" class="massCheck ">
      {{-- @endif --}}
    {{-- @endif
  @endcan --}}
</td>

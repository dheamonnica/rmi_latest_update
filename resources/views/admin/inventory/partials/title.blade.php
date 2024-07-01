{{ $inventory->title }}

@if ($inventory->auctionable && $type != 'auction')
  <span class="label label-default pull-right">{{ trans('auction::lang.auction') }}</span>
@endif

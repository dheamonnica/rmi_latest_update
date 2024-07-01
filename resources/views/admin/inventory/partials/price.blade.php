@if ($inventory->auctionable)
  {{ get_formated_currency($inventory->base_price, 2, config('system_settings.currency.id')) }}
@elseif ($inventory->offer_price > 0 && $inventory->offer_end > \Carbon\Carbon::now())
  @php
    $offer_price_help = trans('help.offer_starting_time') . ': ' . $inventory->offer_start->diffForHumans() . ' ' . trans('app.and') . ' ' . trans('help.offer_ending_time') . ': ' . $inventory->offer_end->diffForHumans();
  @endphp

  <s class="text-muted">
    {{ get_formated_currency($inventory->sale_price, 2, config('system_settings.currency.id')) }}
  </s><br />
  {{ get_formated_currency($inventory->offer_price, 2, config('system_settings.currency.id')) }}

  <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ $offer_price_help }}"><sup><i class="fa fa-question"></i></sup></small>
@else
  {{ get_formated_currency($inventory->sale_price, 2, config('system_settings.currency.id')) }}
@endif

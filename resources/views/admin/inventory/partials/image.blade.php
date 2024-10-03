<img src="{{ get_product_img_src($inventory, 'tiny') }}" class="img-sm" alt="{{ trans('app.image') }}">
@if ($status == 'stockTransfer')
	@if ((int) Auth::user()->shop_id == (int) $inventory->shop_depature_id)
		<span class="badge badge-secondary">Sender</span>
	@elseif ((int) Auth::user()->shop_id == (int) $inventory->shop_arrival_id)
		<span class="badge badge-primary" style="background-color: blue">Receiver</span>
	@endif
@endif
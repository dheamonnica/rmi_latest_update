@if($customer->shop_id)
    {{ $customer->getCoverageArea->warehouse_name }}
@endif
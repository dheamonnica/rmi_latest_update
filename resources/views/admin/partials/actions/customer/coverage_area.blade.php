@if($customer->shop_id)
    {{ $customer->getCoverageArea->name }}
@endif
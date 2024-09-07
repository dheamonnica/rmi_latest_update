@if($customer->shop_id !== null)
    {{ $customer->getCoverageArea->name }}
@endif
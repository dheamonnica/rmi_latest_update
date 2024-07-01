<td>
    @if($order->shipped_by)
        {{ $order->getFulfilledName->warehouse_name ? $order->getFulfilledName->pic_name : $order->getFulfilledName->full_name }}
    @endif
</td>

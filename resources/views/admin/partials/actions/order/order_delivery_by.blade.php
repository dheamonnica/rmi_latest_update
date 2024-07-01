<td>
    @if($order->delivery_by)
        {{ $order->getDeliveredName->warehouse_name ? $order->getDeliveredName->pic_name : $order->getDeliveredName->full_name }}
    @endif
</td>

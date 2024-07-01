<td>
    @if($order->created_by)
        {{ $order->getOrderByName->warehouse_name ? $order->getOrderByName->pic_name : $order->getOrderByName->full_name }}
    @endif
</td>

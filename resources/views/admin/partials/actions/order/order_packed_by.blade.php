<td>
    @if($order->packed_by)
        {{ $order->getPackedByName->warehouse_name ? $order->getPackedByName->pic_name : $order->getPackedByName->full_name }}
    @endif
</td>

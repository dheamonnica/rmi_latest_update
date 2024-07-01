<td>
    @if($order->paid_by)
        {{ $order->getPaidByName->warehouse_name ? $order->getPaidByName->pic_name : $order->getPaidByName->full_name }}
    @endif
</td>

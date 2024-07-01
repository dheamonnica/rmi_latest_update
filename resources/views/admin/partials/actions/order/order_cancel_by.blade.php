<td>
    @if ($order->cancel_by)
        {{ $order->getCancelByName->warehouse_name ? $order->getCancelByName->pic_name : $order->getCancelByName->full_name }}
    @endif
</td>

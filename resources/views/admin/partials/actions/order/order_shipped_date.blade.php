<td>
    @if($order->shipping_date)
        {{date('d-m-Y h:i:s', strtotime($order->shipping_date)) }}               
    @endif
</td>

<td>
    @if($order->delivery_date)
        {{date('d-m-Y h:i:s', strtotime($order->delivery_date)) }}               
    @endif
</td>

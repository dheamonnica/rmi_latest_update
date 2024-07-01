<td>
    @if($order->cancel_date)
        {{date('d-m-Y h:i:s', strtotime($order->cancel_date)) }}               
    @endif
</td>

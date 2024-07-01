<td>
    @if($order->paid_date)
        {{date('d-m-Y h:i:s', strtotime($order->paid_date)) }}               
    @endif
</td>

<td>
    @if($order->packed_date)
        {{date('d-m-Y h:i:s', strtotime($order->packed_date)) }}               
    @endif
</td>

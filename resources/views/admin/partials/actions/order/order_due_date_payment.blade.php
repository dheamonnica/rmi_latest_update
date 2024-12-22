<td>
    @if ($order->created_at)
        @php
            $date1 = new DateTime($order->created_at);
            $date2 = new DateTime(); // Defaults to today

            $interval = $date1->diff($date2);
            $diffInDays = $interval->format('%a');

            $dd_payment = 40 - $diffInDays;
        @endphp

        @if ($dd_payment <= 5)
            <span class='label label-danger'>{{$dd_payment}} days</span>
        @else
            <span class='label label-info'>{{$dd_payment}} days</span>
        @endif
    @endif
</td>

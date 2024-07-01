<td>
    @if ($order->delivery_date)
        @php
            $date1 = new DateTime($order->delivery_date);
            $date2 = new DateTime(); // Defaults to today

            $interval = $date1->diff($date2);
            $diffInDays = $interval->format('%a');
        @endphp

        {{45 - $diffInDays}} days
    @endif
</td>

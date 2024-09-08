<td>
    @if ($order->created_at)
        @php
            $date1 = new DateTime($order->created_at);
            $date2 = new DateTime(); // Defaults to today

            $interval = $date1->diff($date2);
            $diffInDays = $interval->format('%a');
        @endphp

        {{40 - $diffInDays}} days
    @endif
</td>

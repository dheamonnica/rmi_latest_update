<td>
    @if ($order->delivery_date)
        @php
            $date1 = new DateTime($order->delivery_date);
            $date1->modify('+45 days');
            $dateString = $date1->format('Y-m-d');
        @endphp

        {{ $htmlSafeDateString = htmlspecialchars($dateString) }}
    @endif
</td>

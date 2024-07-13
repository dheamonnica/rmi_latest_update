@if ($visit->created_by)
    <td>{{ $visit->getCreatedVisitByName->warehouse_name }}</td>
@endif

@if ($visit->updated_by)
    <td>{{ $visit->getUpdatedVisitByName->name }}</td>
@endif

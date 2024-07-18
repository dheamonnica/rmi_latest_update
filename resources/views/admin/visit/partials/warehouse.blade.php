@if ($visit->created_by)
    <td>{{ $visit->getWarehouseByShop->name }}</td>
@endif

@if ($visit->status === 0)
    <td><span class="label label-danger">PENDING</span></td>
@else
    <td><span class="label label-primary">APPROVED</span></td>
@endif

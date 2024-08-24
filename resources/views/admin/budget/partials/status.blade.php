    @if ($budget->status === 1)
        <td><span class="label label-primary">APPROVED</span></td>
    @else
        <td><span class="label label-danger">NOT APPROVED</span></td>
    @endif

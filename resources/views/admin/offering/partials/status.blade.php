<td>
    @if ($offering->status === 0)
        <span style="background: #ff0000b0;padding: 5px 20px;border-radius: 30px;color: white;">
            Pending
        </span>
    @else
        <span style="background: #0000ffb0; padding: 5px 20px;border-radius: 30px;color: white;">
            Approve
        </span>
    @endif
</td>

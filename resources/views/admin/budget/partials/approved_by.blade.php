@if ($budget->approved_by)
    <td>{{ $budget->getApprovedBudget->name }}</td>
@endif
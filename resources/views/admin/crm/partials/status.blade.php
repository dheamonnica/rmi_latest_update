@if ((($crm->total_plan_actual / $crm->total_plan) * 100) >= 100)
    <td><span class="label label-primary">ACHIEVE</span></td>
@else
    <td><span class="label label-danger">FAIL</span></td>
@endif

@if (($getTotalVerifiedVisit->total_plan_actual / $getTotalPlanVisit->total_plan) * 100 >= 1)
    <td><span class="label label-primary">ACHIEVE</span></td>
@else
    <td><span class="label label-danger">FAIL</span></td>
@endif

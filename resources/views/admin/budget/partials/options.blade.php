
{{-- if status not approved and role leader, warehouse area leader --}}
@if ($budget->status === 0 && (Auth::user()->role_id === 13 || Auth::user()->role_id === 3 || Auth::user()->role_id === 1))
    {!! Form::open(['route' => ['admin.budget.setApprove', $budget], 'method' => 'put', 'class' => 'inline']) !!}
    <a href="javascript:void(0)"><i class="confirm ajax-silent fa fa-check"></i></a>
    {!! Form::close() !!}

    <a href="javascript:void(0)" data-link="{{ route('admin.budget.edit', $budget->id) }}"
        class="ajax-modal-btn"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}"
            class="fa fa-edit"></i></a>&nbsp;
@endif

  @if (Auth::user()->role_id === 13 || Auth::user()->role_id === 3 || Auth::user()->role_id === 1)
      {!! Form::open([
          'route' => ['admin.admin.budget.trash', $budget->id],
          'method' => 'delete',
          'class' => 'data-form',
      ]) !!}
      {!! Form::button('<i class="fa fa-trash-o text-info"></i>', [
          'type' => 'submit',
          'class' => 'confirm ajax-silent',
          'title' => trans('app.trash'),
          'data-toggle' => 'tooltip',
          'data-placement' => 'top',
      ]) !!}
      {!! Form::close() !!}
  @endif
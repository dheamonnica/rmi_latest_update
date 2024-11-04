@if (!$crm->verified_by && (new \App\Helpers\Authorize(Auth::user(), 'approve_crm'))->check())
    {!! Form::open(['route' => ['admin.crm.setApprove', $crm], 'method' => 'put', 'class' => 'inline']) !!}
    <a href="javascript:void(0)"><i class="confirm ajax-silent fa fa-check"></i></a>
    {!! Form::close() !!}
@endif

@if ((new \App\Helpers\Authorize(Auth::user(), 'edit_crm'))->check())
    <a href="javascript:void(0)" data-link="{{ route('admin.crm.edit', $crm->id) }}" class="ajax-modal-btn"><i
            data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
@endif

@if ((new \App\Helpers\Authorize(Auth::user(), 'delete_crm'))->check())
    {!! Form::open([
        'route' => ['admin.admin.crm.trash', $crm->id],
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

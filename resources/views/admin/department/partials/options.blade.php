@if ((new \App\Helpers\Authorize(Auth::user(), 'edit_department'))->check())
    <a href="javascript:void(0)" data-link="{{ route('admin.department.edit', $department->id) }}"
        class="ajax-modal-btn"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}"
            class="fa fa-edit"></i></a>&nbsp;
@endif
@if ((new \App\Helpers\Authorize(Auth::user(), 'delete_department'))->check())
    {!! Form::open([
        'route' => ['admin.admin.department.trash', $department->id],
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

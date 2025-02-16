@if ((new \App\Helpers\Authorize(Auth::user(), 'edit_logistic'))->check())
    <a href="javascript:void(0)" data-link="{{ route('admin.logistic.edit', $logistic->id) }}"
        class="ajax-modal-btn"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}"
            class="fa fa-edit"></i></a>&nbsp;
@endif
@if ((new \App\Helpers\Authorize(Auth::user(), 'delete_logistic'))->check())
    {!! Form::open([
        'route' => ['admin.admin.logistic.trash', $logistic->id],
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

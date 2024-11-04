@if ((new \App\Helpers\Authorize(Auth::user(), 'edit_reimburse'))->check())
    <a href="javascript:void(0)" data-link="{{ route('admin.segment.edit', $segment->id) }}" class="ajax-modal-btn"><i
            data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
@endif
@if ((new \App\Helpers\Authorize(Auth::user(), 'delete_reimburse'))->check())
    {!! Form::open([
        'route' => ['admin.admin.segment.trash', $segment->id],
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

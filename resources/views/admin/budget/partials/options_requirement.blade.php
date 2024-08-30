{{-- leader and warehouse area leader --}}
@if (Auth::user()->role_id === 13 || Auth::user()->role_id === 3)
    <a href="javascript:void(0)" data-link="{{ route('admin.requirement.edit', $requirement->id) }}" class="ajax-modal-btn"><i
            data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
    {!! Form::open([
        'route' => ['admin.admin.requirement.trash', $requirement->id],
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

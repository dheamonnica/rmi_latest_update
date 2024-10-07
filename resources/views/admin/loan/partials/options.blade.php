@if (Auth::user()->isAdmin())
    @if ($loan->status == 0 && Auth::user()->isFromPlatform())
        {!! Form::open(['route' => ['admin.loan.setApprove', $loan], 'method' => 'put', 'class' => 'inline']) !!}
        <a href="javascript:void(0)"><i class="confirm ajax-silent fa fa-check"></i></a>
        {!! Form::close() !!}
    @endif
    <a href="javascript:void(0)" data-link="{{ route('admin.loan.edit', $loan->id) }}" class="ajax-modal-btn"><i
            data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
    {!! Form::open([
        'route' => ['admin.admin.loan.trash', $loan->id],
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

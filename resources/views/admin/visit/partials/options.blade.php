{{-- leader and marketing --}}

@if (!$visit->verified_by)
    @if ( Auth::user()->role_id === 13)
        {!! Form::open(['route' => ['admin.visit.setApprove', $visit], 'method' => 'put', 'class' => 'inline']) !!}
        <a href="javascript:void(0)"><i class="confirm ajax-silent fa fa-check"></i></a>
        {!! Form::close() !!}
    @endif

    @if(Auth::user()->role_id === 8 || Auth::user()->role_id === 13)
        <a href="javascript:void(0)" data-link="{{ route('admin.visit.edit', $visit->id) }}" class="ajax-modal-btn"><i
                data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}" class="fa fa-edit"></i></a>&nbsp;
    @endif
@endif



@if (Auth::user()->isAdmin() || Auth::user()->isMerchant() || Auth::user()->isFromPlatform())
    {!! Form::open([
        'route' => ['admin.admin.visit.trash', $visit->id],
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

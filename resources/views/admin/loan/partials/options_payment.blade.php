@if ((new \App\Helpers\Authorize(Auth::user(), 'payment_loan'))->check())
    <a href="javascript:void(0)" data-link="{{ route('admin.loan.payment.edit', $loan_payment->id) }}"
        class="ajax-modal-btn"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}"
            class="fa fa-edit"></i></a>&nbsp;
    {!! Form::open([
        'route' => ['admin.admin.loan.payment.trash', $loan_payment->id],
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

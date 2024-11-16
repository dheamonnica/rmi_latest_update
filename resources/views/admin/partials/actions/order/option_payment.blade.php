@if ((new \App\Helpers\Authorize(Auth::user(), 'edit_order_payment'))->check())
    <td class="row-options">
        <a href="javascript:void(0)" data-link="{{ route('admin.order.order.orderPaymentEdit', $order->id) }}"
            class="ajax-modal-btn"><i data-toggle="tooltip" data-placement="top" title="{{ trans('app.edit') }}"
                class="fa fa-edit"></i></a>&nbsp;
    </td>
@endif

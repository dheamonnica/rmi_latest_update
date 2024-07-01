<td>
    @if ($order->deliveryBoyRole)
        {{ $order->deliveryBoyRole->getName() }}

        @can('fulfill', $order)
        @if(Auth::user()->role_id !== 9)
            <a data-link="{{ route('admin.order.deliveryboys', $order->id) }}" class="ajax-modal-btn fa fa-edit indent10"
                data-toggle="tooltip" data-placement="top" title="{{ trans('app.change_deliveryboy') }}"></a>
        @endif
        @endcan
    @else
        @can('fulfill', $order)
            <a data-link="{{ route('admin.order.deliveryboys', $order->id) }}"
                class="ajax-modal-btn btn btn-sm btn-flat btn-default" data-toggle="tooltip" data-placement="top"
                title="{{ trans('app.assign_deliveryboy') }}">
                <i class="fa fa-user"></i> {{ trans('app.assign') }}
            </a>
        @endcan
    @endif
</td>

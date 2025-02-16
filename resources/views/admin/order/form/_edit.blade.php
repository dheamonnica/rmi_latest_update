<div class="modal-dialog modal-lg">
    <div class="modal-content modal-lg" style="padding: 0 20px">
        {!! Form::model($order, [
            'method' => 'PUT',
            'route' => ['admin.order.order.updateForm', $order->id],
            'files' => true,
            'id' => 'orderForm',
            'data-toggle' => 'validator',
        ]) !!}
        <div class="modal-header d-flex justify-content-center align-items-center position-relative">
            <button type="button" class="close position-absolute" data-dismiss="modal" aria-hidden="true"
                style="right: 15px;" id="saveAndClose">&times;</button>
            <h5 class="modal-title text-center w-100">
                FORMULIR PURCHASE ORDER - PRODUCT QUALITY PURCHASING
            </h5>
        </div>
        <div class="modal-body">
            @include('admin.order.form._form')
        </div>
        <div class="modal-footer">
            {!! Form::submit(trans('app.update'), ['class' => 'btn btn-flat btn-new']) !!}
        </div>
        {!! Form::close() !!}
    </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        {!! Form::model($offering, [
            'method' => 'PUT',
            'route' => ['admin.admin.offering.update', $offering->id],
            'files' => true,
            'id' => 'form',
            'data-toggle' => 'validator',
        ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            {{ trans('app.form.form') }}
        </div>
        <div class="modal-body">
            @include('admin.offering._form')
        </div>
        <div class="modal-footer">
            {!! Form::submit(trans('app.update'), ['class' => 'btn btn-flat btn-new']) !!}
        </div>
        {!! Form::close() !!}
    </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->

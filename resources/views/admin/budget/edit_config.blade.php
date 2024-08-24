<div class="modal-dialog modal-lg">
    <div class="modal-content">
        {!! Form::model($segment, [
            'method' => 'PUT',
            'route' => ['admin.segment.update', $segment->id],
            'files' => true,
            'id' => 'form',
            'data-toggle' => 'validator',
        ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            {{ trans('app.form.form') }}
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 nopadding-right">
                    <div class="col-md-6 nopadding-left nopadding-right">
                        <div class="form-group">
                            {!! Form::label('name', trans('app.form.name'), ['class' => 'with-help']) !!}
                            {!! Form::text('name', null, [
                                'class' => 'form-control',
                                'placeholder' => trans('app.form.name'),
                                'required',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6 nopadding-left">
                        <div class="form-group">
                            {!! Form::label('value', trans('app.form.value'), ['class' => 'with-help']) !!}
                            {!! Form::text('value', null, [
                                'class' => 'form-control',
                                'placeholder' => trans('app.form.value'),
                                'required',
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::hidden('updated_by', Auth::user()->id) !!}
            {!! Form::hidden('updated_at', now()) !!}
            {!! Form::hidden('warehouse_id', Auth::user()->shop_id) !!}

        </div>
        <div class="modal-footer">
            {!! Form::submit(trans('app.update'), ['class' => 'btn btn-flat btn-new']) !!}
        </div>
        {!! Form::close() !!}
    </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->

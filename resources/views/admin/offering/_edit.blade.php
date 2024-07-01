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
            @if (Auth::user()->isAdmin() || Auth::user()->isMerchant())
                {!! Form::hidden('updated_by', Auth::user()->id) !!}
                {!! Form::hidden('updated_at', now()) !!}
                <div class="col-md-4 nopadding-left">
                    <div class="form-group">
                        {!! Form::label('active', trans('app.form.status') . '*') !!}
                        {!! Form::select(
                            'status',
                            ['1' => trans('app.approve'), '0' => trans('app.pending')],
                            isset($offering) ? $offering->status : 0,
                            ['class' => 'form-control select2-normal', 'placeholder' => trans('app.placeholder.status'), 'required'],
                        ) !!}
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
            @else
                @include('admin.offering._form')
            @endif
        </div>
        <div class="modal-footer">
            {!! Form::submit(trans('app.update'), ['class' => 'btn btn-flat btn-new']) !!}
        </div>
        {!! Form::close() !!}
    </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->

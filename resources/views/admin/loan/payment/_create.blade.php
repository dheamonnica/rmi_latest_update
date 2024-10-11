<div class="modal-dialog modal-lg">
    <div class="modal-content">
      {!! Form::open(['route' => 'admin.loan.payment.store', 'files' => true, 'id' => 'form', 'data-toggle' => 'validator']) !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        {{ trans('app.form.form') }}
      </div>
      <div class="modal-body">
        @include('admin.loan.payment._form')
        {!! Form::hidden('created_by', Auth::user()->id) !!}
        {!! Form::hidden('created_at', now()) !!}
        {!! Form::hidden('updated_at', null) !!}
        {!! Form::hidden('updated_by', null) !!}
      </div>
      <div class="modal-footer">
        {!! Form::submit(trans('app.form.save'), ['class' => 'btn btn-flat btn-new']) !!}
      </div>
      {!! Form::close() !!}
    </div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->
  
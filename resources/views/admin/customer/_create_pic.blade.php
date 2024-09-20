  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          {!! Form::open([
              'route' => 'admin.pic.store',
              'files' => true,
              'id' => 'form',
              'data-toggle' => 'validator',
          ]) !!}
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              Add Hospital PIC - {{$hospital->name}}
          </div>
          <div class="modal-body">
              @include('admin.customer._form_customer')
          </div>
          <div class="modal-footer">
              {!! Form::submit(trans('app.form.save'), ['class' => 'btn btn-flat btn-new']) !!}
          </div>
          {!! Form::close() !!}
      </div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->

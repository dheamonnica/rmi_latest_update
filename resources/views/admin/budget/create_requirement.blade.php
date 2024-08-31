<div class="modal-dialog modal-lg">
    <div class="modal-content">
        {!! Form::open([
            'route' => 'admin.requirement.store',
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
                    <div class="col-md-4 nopadding-left nopadding-right">
                        <div class="form-group">
                            {!! Form::label('name', trans('app.form.name'), ['class' => 'with-help']) !!}
                            {!! Form::text('name', null, [
                                'class' => 'form-control',
                                'placeholder' => trans('app.form.name'),
                                'required',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-4 nopadding-left nopadding-right">
                        {!! Form::label('type', trans('app.form.type'), ['class' => 'with-help']) !!}
                        <div class="form-group">
                            {!! Form::select('type', [
                                    'input' => 'Input',
                                    'selection' => 'Selection'
                                ], null, [
                                    'class' => 'form-control',
                                    'placeholder' => trans('app.form.type'),
                                    'required',
                                    'id' => 'type-select',
                                ]) !!}
                        </div>
                    </div>
                    <div class="col-md-4 nopadding-left" id="value-field" style="display: none;">
                        {!! Form::label('value', trans('app.form.value'), ['class' => 'with-help']) !!}
                        <div class="form-group">
                            {!! Form::text('value', null, [
                                'class' => 'form-control',
                                'placeholder' => trans('app.form.value'),
                                'required',
                            ]) !!}
                            {!! Form::hidden('value', 0, ['id' => 'value-hidden']) !!}
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::hidden('created_by', Auth::user()->id) !!}
            {!! Form::hidden('created_at', now()) !!}
            {!! Form::hidden('updated_at', null) !!}

        </div>
        <div class="modal-footer">
            {!! Form::submit(trans('app.form.save'), ['class' => 'btn btn-flat btn-new']) !!}
        </div>
        {!! Form::close() !!}
    </div> <!-- / .modal-content -->
</div> <!-- / .modal-dialog -->

<script>
     $(document).ready(function() {
        const typeSelect = $('#type-select');
        const valueInput = $('#value-input');
        const valueField = $('#value-field');
        const valueHidden = $('#value-hidden');

        function toggleValueField() {
            if (typeSelect.val() === 'selection') {
                valueField.show(); // Show the text input
                valueInput.prop('disabled', false); // Enable the text input
                valueHidden.prop('disabled', true); // Disable the hidden input
            } else if (typeSelect.val() === 'input') {
                valueField.hide(); // Hide the text input
                valueInput.prop('disabled', true); // Disable the text input
                valueHidden.prop('disabled', false); // Enable the hidden input
            }
        }

        // Initially call the function to set the correct state on page load
        toggleValueField();

        // Add an event listener for changes in the select dropdown
        typeSelect.on('change', toggleValueField);
    });
</script>
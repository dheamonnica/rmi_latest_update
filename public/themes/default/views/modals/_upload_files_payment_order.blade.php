<div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content p-2">
        <div class="modal-header p-3">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-title text-center">
                <h4>
                    Payment Upload Form
                </h4>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group my-2">
                        {!! Form::label('upload_document_SI', trans('app.form.upload_document_SI'), [
                            'class' => 'with-help',
                            'style' => 'text-transform: capitalize !important; font-weight: 900;',
                        ]) !!}
                        @if ($order->doc_SI)
                            {!! Form::model($order, [
                                'method' => 'DELETE',
                                'route' => 'order.uploadDocPayment.remove',
                                'class' => 'form-horizontal',
                                'data-toggle' => 'validator',
                            ]) !!}
                            {!! Form::hidden('id', $order->id) !!}
                            <p class="m-3">
                                <a href="{{ asset('storage/' . $order->doc_SI) }}" target="_blank">Dokumen SI</a>
                                <button class="btn btn-xs btn-default confirm rounded-0 ml-1"
                                    data-confirm="@lang('theme.confirm_action.delete')" type="submit" data-toggle="tooltip"
                                    data-title="{{ trans('theme.button.delete') }}" data-placement="left"><i
                                        class="fas fa-trash no-fill"></i></button>
                            </p>
                            {!! Form::close() !!}
                        @endif
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group my-4">
                        {!! Form::label('upload_document_faktur_pajak', trans('app.form.upload_document_faktur_pajak'), [
                            'class' => 'with-help',
                            'style' => 'text-transform: capitalize !important; font-weight: 900;',
                        ]) !!}
                        @if ($order->doc_faktur_pajak)
                            {!! Form::model($order, [
                                'method' => 'DELETE',
                                'route' => 'order.uploadDocPayment.removeFakturPajak',
                                'class' => 'form-horizontal',
                                'data-toggle' => 'validator',
                            ]) !!}
                            {!! Form::hidden('id', $order->id) !!}
                            <p class="m-3">
                                <a href="{{ asset('storage/' . $order->doc_faktur_pajak) }}" target="_blank">Dokumen Faktur Pajak</a>
                                <button class="btn btn-xs btn-default confirm rounded-0 ml-1"
                                    data-confirm="@lang('theme.confirm_action.delete')" type="submit" data-toggle="tooltip"
                                    data-title="{{ trans('theme.button.delete') }}" data-placement="left"><i
                                        class="fas fa-trash no-fill"></i></button>
                            </p>
                            {!! Form::close() !!}
                        @endif
                        <div class="help-block with-errors"></div>
                    </div>

                </div>
                <div class="col-md-6">
                    {!! Form::open(['route' => 'order.uploadDocPayment.save', 'files' => true, 'data-toggle' => 'validator']) !!}
                    {!! Form::hidden('id', $order->id) !!}
                    <div class="form-group {{ $order->doc_faktur_pajak ? 'm-5' : 'mx-4 mb-4' }}">
                        {!! Form::file('doc_SI') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group {{ $order->doc_faktur_pajak ? 'm-5' : 'mx-4 mb-4' }}">
                        {!! Form::file('doc_faktur_pajak') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                    <button type="submit"
                        class="btn btn-primary btn-medium pull-right">{{ trans('theme.button.submit') }}</button>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="help-block with-errors"></div>
        </div>
        <small class="help-block text-muted text-left mt-4">* {{ trans('theme.help.required_fields') }}</small>
    </div>
</div>
</div><!-- /.modal-dialog -->

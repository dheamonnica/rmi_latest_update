<div class="row">
    {!! Form::hidden('id') !!}
    <div class="col-md-4 nopadding-right">
        <div class="form-group">
            {!! Form::label('upload_document_SI', trans('app.form.upload_document_SI'), ['class' => 'with-help']) !!}
            {!! Form::file('doc_SI') !!}
            @if ($order->doc_SI)
                <a href="{{ asset('storage/' . $order->doc_SI) }}" target="_blank">Dokumen SI</a>
            @endif
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-4 nopadding-right">
        <div class="form-group">
            {!! Form::label('upload_document_faktur_pajak', trans('app.form.upload_document_faktur_pajak'), [
                'class' => 'with-help',
            ]) !!}
            {!! Form::file('doc_faktur_pajak') !!}
            @if ($order->doc_faktur_pajak)
                <a href="{{ asset('storage/' . $order->doc_faktur_pajak) }}" target="_blank">Dokumen Faktur Pajak</a>
            @endif
            <div class="help-block with-errors"></div>
        </div>
    </div>
    <div class="col-md-4 nopadding-right">
        <div class="form-group">
            {!! Form::label('upload_document_faktur_terbayar', trans('app.form.upload_document_faktur_terbayar'), [
                'class' => 'with-help',
            ]) !!}
            {!! Form::file('doc_faktur_pajak_terbayar') !!}
            @if ($order->doc_faktur_pajak_terbayar)
                <a href="{{ asset('storage/' . $order->doc_faktur_pajak_terbayar) }}" target="_blank">Dokumen Faktur
                    Terbayar</a>
            @endif
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>

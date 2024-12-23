<div class="row">
    {!! Form::hidden('id') !!}
    <div class="col-md-6 nopadding-right">
        <div class="form-group">
            {!! Form::hidden('doc_faktur_pajak_uploaded_at', now()) !!}
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
    <div class="col-md-6 nopadding-right">
        <div class="form-group">
            {!! Form::hidden('doc_faktur_pajak_terbayar_uploaded_at', now()) !!}
            {!! Form::label('upload_document_tukar_faktur_pajak', trans('app.form.upload_document_tukar_faktur_pajak'), [
                'class' => 'with-help',
            ]) !!}
            {!! Form::file('doc_faktur_pajak_terbayar') !!}
            @if ($order->doc_faktur_pajak_terbayar)
                <a href="{{ asset('storage/' . $order->doc_faktur_pajak_terbayar) }}" target="_blank">Dokumen Tukar Faktur
                    Terbayar</a>
            @endif
            <div class="help-block with-errors"></div>
        </div>
    </div>
</div>

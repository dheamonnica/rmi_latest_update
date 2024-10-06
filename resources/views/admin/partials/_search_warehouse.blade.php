<div class="form-group">
  {!! Form::label('warehouse_id', trans('app.form.search_warehouse') . '*', ['class' => 'with-help']) !!}
  <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="right" title="{{ trans('help.search_warehouse') }}"></i>
  {!! Form::select('warehouse_id', isset($warehouse) ? [$warehouse->id => $warehouse->name] : [], isset($warehouse) ? $warehouse->id : null, ['class' => 'form-control searchWarehouse', 'style' => 'width: 100%', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>

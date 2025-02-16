@extends('admin.layouts.master')

@section('content')
    <div class="box">
        <div class="box-header with-border">
            <div class="pull-left">
                <h1 class="box-title mr-5 mt-2">{{ trans('app.orders') }} FORM</h1>
            </div>
            <br><br>
            <div class="pull-left">
                <div class="pull-left">
                    <div class="row">
                        <div class="col-md-12 nopadding-right">
                            <div class="row">
                                <div class="col-md-5 nopadding-right">
                                    {!! Form::text('startDateOrderFormTableFilter', null, [
                                        'class' => 'form-control datepicker',
                                        'placeholder' => trans('app.form.from'),
                                        'id' => 'startDateOrderFormTableFilter',
                                    ]) !!}
                                </div>
                                <div class="col-md-5 nopadding-left">
                                    {!! Form::text('endDateOrderFormTableFilter', null, [
                                        'class' => 'form-control datepicker',
                                        'placeholder' => trans('app.form.to'),
                                        'id' => 'endDateOrderFormTableFilter',
                                    ]) !!}
                                </div>
                                <div class="col-md-2 nopadding-left py-1">
                                    <button id="dateRangeOrderFormTableFilterButton">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="">
            <table class="table table-hover" id="order-form-table">
                <thead>
                    <tr>
                        <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th>
                        <th>{{ trans('app.order_date') }}</th>
                        <th>{{ trans('app.form.po_number_ref') }}</th>
                        <th>{{ trans('app.form.purchase_order') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div>
</div @endsection

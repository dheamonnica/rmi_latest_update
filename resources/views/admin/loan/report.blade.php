@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <div class="box border-small p-2">
        <div class="box-header with-border">
            <h3 class="box-title">LOAN REPORT</h3>
            <div class="box-tools pull-right p-2">
            </div>
            <div class="pull-right">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="loan-report-tables">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>{{ trans('app.form.name') }}</th>
                        <th>{{ trans('app.form.total_loan') }}</th>
                        <th>{{ trans('app.form.total_payment') }}</th>
                        <th>{{ trans('app.form.total_outstanding_balance') }}</th>
                        <th>{{ trans('app.form.status') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div>
@endsection


<style>
    table.dataTable td.dt-control {
        cursor: pointer;
    }

    table.dataTable td.dt-control:before {
        display: inline-block;
        box-sizing: border-box;
        content: "";
        border-top: 5px solid transparent;
        border-left: 10px solid rgba(0, 0, 0, 0.5);
        border-bottom: 5px solid transparent;
        border-right: 0px solid transparent;
    }

    table.dataTable tr.dt-hasChild td.dt-control:before {
        border-top: 10px solid rgba(0, 0, 0, 0.5);
        border-left: 5px solid transparent;
        border-bottom: 0px solid transparent;
        border-right: 5px solid transparent;
    }
</style>
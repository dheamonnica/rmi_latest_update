@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <div class="box border-small p-2">
        <div class="box-header with-border">
            <h3 class="box-title">TARGET REPORT</h3>
            <div class="box-tools pull-right p-2">
            </div>
            <div class="pull-right">
                <select id="monthFilterTarget" class="btn btn-sm btn-default">
                    <option value="" selected>Select Month</option>
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <option value="March">March</option>
                    <option value="April">April</option>
                    <option value="May">May</option>
                    <option value="June">June</option>
                    <option value="July">July</option>
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                    <option value="December">December</option>
                </select>
                <select id="yearFilterTarget" class="btn btn-sm btn-default">
                    <option value="" selected>Select Year</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach

                </select>
                <select id="merchantFilterTarget" class="btn btn-sm btn-default">
                    <option value="" selected>Select Business Unit</option>
                    @foreach ($merchants as $merchant)
                        <option value="{{ $merchant }}">{{ $merchant }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="target-tables-report">
                <thead>
                    <tr>
                        <th></th>
                        <th>{{ trans('app.form.month') }}</th>
                        <th>{{ trans('app.form.year') }}</th>
                        <th>{{ trans('app.form.warehouse') }}</th>
                        <th>{{ trans('app.form.total_target') }}</th>
                        <th>{{ trans('app.form.total_selling') }}</th>
                        <th>{{ trans('app.form.rate') }}</th>
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

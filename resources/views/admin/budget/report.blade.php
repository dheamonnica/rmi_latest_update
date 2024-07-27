@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <div class="box border-small p-2">
        <div class="box-header with-border">
            <div class="box-tools pull-right p-2">
            </div>
            <div class="pull-left">
                <select id="monthFilter" class="btn btn-sm btn-default">
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
                <select id="yearFilter" class="btn btn-sm btn-default">
                    <option value="" selected>Select Year</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach

                </select>
                @if (Auth::user()->isAdmin())
                    <select id="merchantFilter" class="btn btn-sm btn-default">
                        <option value="" selected>Select Business Unit</option>
                        @foreach ($merchants as $merchant)
                            <option value="{{ $merchant }}">{{ $merchant }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="budget-report-tables">
                <thead>
                    <tr>
                        <th>{{ trans('app.form.month') }}</th>
                        <th>{{ trans('app.form.year') }}</th>
                        <th>{{ trans('app.form.business_unit') }}</th>
                        <th>{{ trans('app.form.buying_product') }}</th>
                        <th>{{ trans('app.form.fee_management') }}</th>
                        <th>{{ trans('app.form.marketing') }}</th>
                        <th>{{ trans('app.form.operational') }}</th>
                        <th>{{ trans('app.form.total_budget') }}</th>
                        <th>{{ trans('app.form.total_selling') }}</th>
                        <th>{{ trans('app.form.achieve') }}</th>
                        <th>{{ trans('app.form.status') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div>
@endsection

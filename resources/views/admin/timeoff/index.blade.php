@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <!-- Info boxes -->
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12 nopadding-right">
            <div class="info-box">
                <span class="info-box-icon bg-green">
                    <i class="icon ion-md-log-out"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Annual Leave</span>
                    <span class="info-box-number">
                        <span data-toggle="tooltip" data-placement="left"
                            title="Used">{{ 12 - $timeoff_user_annual_leave->sum_total_days }}</span>
                    </span>

                    <div class="progress">
                        <div class="progress-bar progress-bar-success"
                            style="width: {{ ($timeoff_user_annual_leave->sum_total_days / 12) * 100 }}%">
                            {{ $timeoff_user_annual_leave->sum_total_days }}</div>
                    </div>
                    <span class="progress-description text-muted">
                        <span data-toggle="tooltip" data-placement="left"
                            title="Used">{{ number_format(($timeoff_user_annual_leave->sum_total_days / 12) * 100, 2) }}%</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12 nopadding-right">
            <div class="info-box">
                <span class="info-box-icon bg-red">
                    <i class="icon ion-md-thermometer"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">SICK Leave</span>
                    <span class="info-box-number">
                        <span data-toggle="tooltip" data-placement="left"
                            title="Used">{{ 3 - $timeoff_user_sick_leave->sum_total_days }}</span>
                    </span>

                    <div class="progress">
                        <div class="progress-bar progress-bar-danger"
                            style="width: {{ ($timeoff_user_sick_leave->sum_total_days / 3) * 100 }}%">
                            {{ $timeoff_user_sick_leave->sum_total_days }}</div>
                    </div>
                    <span class="progress-description text-muted">
                        <span data-toggle="tooltip" data-placement="left"
                            title="Used">{{ number_format(($timeoff_user_sick_leave->sum_total_days / 12) * 100, 2) }}%</span>
                    </span>
                </div>
            </div>
        </div>


    </div>

    <div class="box border-small p-2">
        <div class="box-header with-border">
            <h3 class="box-title">TIMEOFF DATA</h3>
            <div class="box-tools pull-right p-2">
            </div>
            <div class="pull-right">
                <select id="monthFilterTimeoff" class="btn btn-sm btn-default">
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
                <select id="yearFilterTimeoff" class="btn btn-sm btn-default">
                    <option value="" selected>Select Year</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
                <select id="statusFilterTimeoff" class="btn btn-sm btn-default">
                    <option value="" selected>Select Status</option>
                    <option value="Approved">Approved</option>
                    <option value="Pending">Pending</option>
                </select>
                @if (Auth::user()->isAdmin())
                    <a href="javascript:void(0)" data-link="{{ route('admin.timeoff.create') }}"
                        class="ajax-modal-btn btn btn-new btn-flat ml-5">{{ trans('app.form.create_timeoff') }}</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="timeoff-tables">
                <thead>
                    <tr>
                        <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th>
                        <th>{{ trans('app.form.created_at') }}</th>
                        <th>{{ trans('app.form.month') }}</th>
                        <th>{{ trans('app.form.year') }}</th>
                        <th>{{ trans('app.form.name') }}</th>
                        <th>{{ trans('app.form.start_date') }}</th>
                        <th>{{ trans('app.form.end_date') }}</th>
                        <th>{{ trans('app.form.total_days') }}</th>
                        <th>{{ trans('app.form.category') }}</th>
                        <th>{{ trans('app.form.type') }}</th>
                        <th>{{ trans('app.form.notes') }}</th>
                        <th>{{ trans('app.form.status') }}</th>
                        <th>{{ trans('app.form.picture') }}</th>

                        <th>{{ trans('app.form.approved_by') }}</th>
                        <th>{{ trans('app.form.approved_at') }}</th>
                        <th>{{ trans('app.form.updated_by') }}</th>
                        <th>{{ trans('app.form.updated_at') }}</th>
                        <th>{{ trans('app.form.option') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div>
@endsection

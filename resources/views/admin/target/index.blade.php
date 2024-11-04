@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <div class="box border-small p-2">
        <div class="box-header with-border">
            <h3 class="box-title">TARGET DATA</h3>
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
                @if (Auth::user()->isAdmin())
                    <select id="merchantFilterTarget" class="btn btn-sm btn-default">
                        <option value="" selected>Select Business Unit</option>
                        @foreach ($merchants as $merchant)
                            <option value="{{ $merchant }}">{{ $merchant }}</option>
                        @endforeach
                    </select>
                @endif
                @if ((new \App\Helpers\Authorize(Auth::user(), 'add_target'))->check())
                    <a href="javascript:void(0)" data-link="{{ route('admin.target.create') }}"
                        class="ajax-modal-btn btn btn-new btn-flat ml-5">{{ trans('app.form.create_target') }}</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="target-tables">
                <thead>
                    <tr>
                        <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th>
                        <th>{{ trans('app.form.month') }}</th>
                        <th>{{ trans('app.form.year') }}</th>
                        <th>{{ trans('app.form.client') }}</th>
                        <th>{{ trans('app.form.actual_sales') }}</th>
                        <th width="100px">{{ trans('app.form.grand_total') }}</th>
                        <th>{{ trans('app.form.warehouse') }}</th>
                        <th>{{ trans('app.form.created_at') }}</th>
                        <th>{{ trans('app.form.created_by') }}</th>
                        <th>{{ trans('app.form.updated_at') }}</th>
                        <th>{{ trans('app.form.updated_by') }}</th>
                        <th>{{ trans('app.form.option') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>TOTAL TARGET</th>
                        <th id="totalAmountTarget"></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

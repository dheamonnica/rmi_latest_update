@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <div class="box border-small p-2">
        <div class="box-header with-border">
            <h3 class="box-title">PAYROLL DATA</h3>
            <div class="box-tools pull-right p-2">
            </div>
            <div class="pull-right">
                @if ((new \App\Helpers\Authorize(Auth::user(), 'add_payroll'))->check())
                    <a href="javascript:void(0)" data-link="{{ route('admin.payroll.create') }}"
                        class="ajax-modal-btn btn btn-new btn-flat ml-5">{{ trans('app.form.create_payroll') }}</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="payroll-tables">
                <thead>
                    <tr>
                        <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th>
                        <th>{{ trans('app.form.position_') }}</th>
                        <th>{{ trans('app.form.grade') }}</th>
                        <th>{{ trans('app.form.sub_grade') }}</th>
                        <th>{{ trans('app.form.level') }}</th>
                        <th>{{ trans('app.form.take_home_pay') }}</th>
                        <th>{{ trans('app.form.basic_sallary') }}</th>
                        <th>{{ trans('app.form.operational_allowance') }}</th>
                        <th>{{ trans('app.form.position_allowance') }}</th>
                        <th>{{ trans('app.form.child_education_allowance') }}</th>
                        <th>{{ trans('app.form.transportation') }}</th>
                        <th>{{ trans('app.form.quota') }}</th>
                        <th>{{ trans('app.form.created_by') }}</th>
                        <th>{{ trans('app.form.created_at') }}</th>
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

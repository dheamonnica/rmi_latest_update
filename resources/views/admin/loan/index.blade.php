@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <div class="box border-small p-2">
        <div class="box-header with-border">
            <h3 class="box-title">LOAN DATA</h3>
            <div class="box-tools pull-right p-2">
            </div>
            <div class="pull-right">
                @if (Auth::user()->isAdmin())
                    <a href="javascript:void(0)" data-link="{{ route('admin.loan.create') }}"
                        class="ajax-modal-btn btn btn-new btn-flat ml-5">{{ trans('app.form.create_loan') }}</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="loan-tables">
                <thead>
                    <tr>
                        <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th>
                        <th>{{ trans('app.form.loan_id') }}</th>
                        <th>{{ trans('app.form.created_at') }}</th>
                        <th>{{ trans('app.form.name') }}</th>
                        <th>{{ trans('app.form.status') }}</th>
                        <th>{{ trans('app.form.amount') }}</th>
                        <th>{{ trans('app.form.reason') }}</th>

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

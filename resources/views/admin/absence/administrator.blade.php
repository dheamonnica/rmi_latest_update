@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <div class="box border-small p-2">
        <div class="box-header with-border">
            <h3 class="box-title">ABSENCE DATA ADMINISTRATOR</h3>
            <div class="box-tools pull-right p-2">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="absence-tables">
                <thead>
                    <tr>
                        <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th>
                        <th>{{ trans('app.form.name') }}</th>
                        <th>{{ trans('app.form.clock_in') }}</th>
                        <th>{{ trans('app.form.clock_in_img') }}</th>
                        <th>{{ trans('app.form.clock_out') }}</th>
                        <th>{{ trans('app.form.clock_out_img') }}</th>
                        <th>{{ trans('app.form.office') }}</th>
                        <th>{{ trans('app.form.address') }}</th>
                        <th>{{ trans('app.form.total_hours') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <div class="box border-small p-2">
        <div class="box-header with-border">
            <h3 class="box-title">PAYROLL REPORT</h3>
            <div class="box-tools pull-right p-2">
            </div>
            <div class="pull-right">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="payroll-report-tables">
                <thead>
                    <tr>
                        <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th>
                        <th>{{ trans('app.form.full_name') }}</th>
                        <th>{{ trans('app.form.job_position') }}</th>
                        <th>{{ trans('app.form.organization') }}</th>
                        <th>{{ trans('app.form.basic_salary') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.position_allowance') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.transportation_allowance') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.daily_operational_allowance') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.child_education_allowance') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.sales_bonus') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.bonus') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.overtime') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.reimburse_e_toll_gasoline') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.medical_reimbursement') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.tax_allowance') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.total_allowance') }}</th>

                        <th style="background: rgb(186, 144, 138)">{{ trans('app.form.lateness_deduction') }}</th>
                        <th style="background: rgb(186, 144, 138)">{{ trans('app.form.alpha_deduction') }}</th>
                        <th style="background: rgb(186, 144, 138)">{{ trans('app.form.absence_deduction') }}</th>
                        <th style="background: rgb(186, 144, 138)">{{ trans('app.form.loan') }}</th>
                        <th style="background: rgb(186, 144, 138)">{{ trans('app.form.installment') }}</th>
                        <th style="background: rgb(186, 144, 138)">{{ trans('app.form.employee_pension_security') }}</th>
                        <th style="background: rgb(186, 144, 138)">{{ trans('app.form.employee_jht') }}</th>
                        <th style="background: rgb(186, 144, 138)">{{ trans('app.form.pph_21') }}</th>
                        <th style="background: rgb(192, 106, 93)">{{ trans('app.form.total_deduction') }}</th>

                        <th>{{ trans('app.form.pph_21_payment') }}</th>
                        <th>{{ trans('app.form.take_home_pay') }}</th>
                        <th style="background: rgb(163, 193, 163)">{{ trans('app.form.telecommunication_allowance') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div>
@endsection

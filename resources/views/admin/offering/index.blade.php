@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <div class="">

        <div class="box border-small p-2">
            <div class="box-header with-border">
                <div class="box-tools pull-right p-2">
                    @if (!Auth::user()->isAdmin() || !Auth::user()->isMerchant())
                        <a href="javascript:void(0)" data-link="{{ route('admin.offering.create') }}"
                            class="ajax-modal-btn btn btn-new btn-flat">{{ trans('app.form.create_offering') }}</a>
                    @endif
                </div>
                <div class="pull-left">
                    <select id="productFilter" class="btn btn-sm btn-default">
                        <option value="0" selected>{{ 'Filter Product Name' }}</option>
                        <option value="0">{{ trans('app.all_orders') }}</option>
                        @foreach ($products as $product)
                            <option value="{{ $product }}">{{ $product }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- <div class="container row m-0 p-0">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('product_id', trans('app.form.select_product') . '*', ['class' => 'with-help']) !!}
                        {!! Form::select('product_id', $products, null, [
                            'class' => 'form-control select2-normal',
                            'required',
                        ]) !!}
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
            </div> --}}
            <div>
                <div style="overflow: auto">
                    <table class="table table-hover" id="offering-table" style="width: 1500px;">
                        <thead>
                            <tr>
                                <th class="massActionWrapper">
                                    <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                        <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                            title="{{ trans('app.select_all') }}"></i>
                                    </button>
                                </th>
                                <th>{{ trans('app.form.product_name') }}</th>
                                <th>{{ trans('app.form.small_quantity') }}</th>
                                <th>{{ trans('app.form.medium_quantity') }}</th>
                                <th>{{ trans('app.form.large_quantity') }}</th>
                                <th>{{ trans('app.form.created_at') }}</th>
                                <th>{{ trans('app.form.created_by') }}</th>
                                <th>{{ trans('app.form.company_name') }}</th>
                                <th>{{ trans('app.form.email') }}</th>
                                <th>{{ trans('app.form.phone') }}</th>
                                <th>{{ trans('app.form.updated_at') }}</th>
                                <th>{{ trans('app.form.updated_by') }}</th>
                                <th>{{ trans('app.form.status') }}</th>
                                <th>{{ trans('app.form.option') }}</th>
                            </tr>
                        </thead>
                        <tbody id="massSelectArea">
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

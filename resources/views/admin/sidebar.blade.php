<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">
            {{-- VENDOR MANUFACTURING --}}
            @if (Auth::user()->role_id !== 16)
                <li class="{{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                    <a href="{{ url('admin/dashboard') }}">
                        <i class="fa fa-dashboard"></i> <span>
                            {{ trans('nav.dashboard') }}
                        </span>
                    </a>
                </li>
            @endif

            @if (Gate::allows('index', \App\Models\Category::class) ||
                    Gate::allows('index', \App\Models\Attribute::class) ||
                    Gate::allows('index', \App\Models\Product::class) ||
                    Gate::allows('index', \App\Models\Manufacturer::class) ||
                    Gate::allows('index', \App\Models\CategoryGroup::class) ||
                    Gate::allows('index', \App\Models\CategorySubGroup::class))
                <li class="treeview {{ Request::is('admin/catalog*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-tags"></i>
                        <span>{{ trans('nav.catalog') }}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if (Gate::allows('index', \App\Models\Category::class) ||
                                Gate::allows('index', \App\Models\CategoryGroup::class) ||
                                Gate::allows('index', \App\Models\CategorySubGroup::class))
                            <li class="{{ Request::is('admin/catalog/category*') ? 'active' : '' }}">
                                <a href="javascript:void(0)">
                                    {{ trans('nav.categories') }}
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    @can('index', \App\Models\CategoryGroup::class)
                                        <li class="{{ Request::is('admin/catalog/categoryGroup*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.catalog.categoryGroup.index') }}">
                                                {{ trans('nav.groups') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('index', \App\Models\CategorySubGroup::class)
                                        <li class="{{ Request::is('admin/catalog/categorySubGroup*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.catalog.categorySubGroup.index') }}">
                                                {{ trans('nav.sub-groups') }}
                                            </a>
                                        </li>
                                    @endcan

                                    @can('index', \App\Models\Category::class)
                                        <li class="{{ Request::is('admin/catalog/category') ? 'active' : '' }}">
                                            <a href="{{ url('admin/catalog/category') }}">
                                                {{ trans('nav.categories') }}
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endif

                        @if (is_catalog_enabled())
                            @can('index', \App\Models\Product::class)
                                <li class="{{ Request::is('admin/catalog/product*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/catalog/product') }}">
                                        {{ trans('nav.products') }}
                                    </a>
                                </li>
                            @endcan
                        @endif

                        @can('index', \App\Models\Manufacturer::class)
                            <li class="{{ Request::is('admin/catalog/manufacturer*') ? 'active' : '' }}">
                                <a href="{{ url('admin/catalog/manufacturer') }}">
                                    {{ trans('nav.manufacturers') }}
                                </a>
                            </li>
                        @endcan

                        @if ((new \App\Helpers\Authorize(Auth::user(), 'view_logistic'))->check())
                            <li class="{{ Request::is('admin/logistic') ? 'active' : '' }}">
                                <a href="{{ url('admin/logistic') }}">
                                    {{ trans('nav.logistics') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if ((new \App\Helpers\Authorize(Auth::user(), 'view_crm'))->check())
                <li class="treeview {{ Request::is('admin/crm*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-file-text-o"></i>
                        <span>CRM</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if ((new \App\Helpers\Authorize(Auth::user(), 'report_crm'))->check())
                            <li class="{{ Request::is('admin/crm') ? 'active' : '' }}">
                                <a href="{{ route('admin.crm.index') }}">
                                    CRM Report
                                </a>
                            </li>
                        @endif

                        <li class="{{ Request::is('admin/crm/data') ? 'active' : '' }}">
                            <a href="{{ route('admin.crm.data') }}">
                                CRM Data
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if ((new \App\Helpers\Authorize(Auth::user(), 'view_offering'))->check())
                <li class="{{ Request::is('admin/offering*') ? 'active' : '' }}">
                    <a href="{{ route('admin.offering.index') }}">
                        <i class="fa fa-handshake-o"></i>
                        Offering Approval
                    </a>
                </li>
            @endif

            @if ((new \App\Helpers\Authorize(Auth::user(), 'view_visit'))->check())
                <li class="{{ Request::is('admin/visit*') ? 'active' : '' }}">
                    <a href="{{ route('admin.visit.index') }}">
                        <i class="fa fa-street-view"></i>
                        Visit Plan
                    </a>
                </li>
            @endif

            @if ((new \App\Helpers\Authorize(Auth::user(), 'view_budget'))->check())
                <li
                    class="treeview {{ Request::is('admin/budget*') ? 'active' : '' }} {{ Request::is('admin/requirement*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-file-text-o"></i>
                        <span>Budget</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if ((new \App\Helpers\Authorize(Auth::user(), 'add_reimburse'))->check())
                            <li class="{{ Request::is('admin/budget') ? 'active' : '' }}">
                                <a href="{{ route('admin.budget.index') }}">
                                    Reimburse Data
                                </a>
                            </li>
                        @endif
                        @if ((new \App\Helpers\Authorize(Auth::user(), 'view_reimburse_category'))->check())
                            <li class="{{ Request::is('admin/requirement') ? 'active' : '' }}">
                                <a href="{{ route('admin.requirement.index') }}">
                                    Reimburse Category
                                </a>
                            </li>
                        @endif
                        @if ((new \App\Helpers\Authorize(Auth::user(), 'report_budget'))->check())
                            <li class="{{ Request::is('admin/budget/report-administrator') ? 'active' : '' }}">
                                <a href="{{ route('admin.budget.reportAdministrator') }}">
                                    Budget Report
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if ((new \App\Helpers\Authorize(Auth::user(), 'view_target'))->check())
                <li class="treeview {{ Request::is('admin/target*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-bullseye"></i>
                        <span>Target</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if ((new \App\Helpers\Authorize(Auth::user(), 'report_target'))->check())
                            @if (Auth::user()->isAdmin())
                                <li class="{{ Request::is('admin/target/report-administrator') ? 'active' : '' }}">
                                    <a href="{{ route('admin.target.reportAdministrator') }}">
                                        Target Report Admin
                                    </a>
                                </li>
                            @else
                                <li class="{{ Request::is('admin/target/report') ? 'active' : '' }}">
                                    <a href="{{ route('admin.target.report') }}">
                                        Target Report
                                    </a>
                                </li>
                            @endif

                            <li class="{{ Request::is('admin/target') ? 'active' : '' }}">
                                <a href="{{ route('admin.target.index') }}">
                                    Target Data
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if ((new \App\Helpers\Authorize(Auth::user(), 'view_loan'))->check())
                <li class="treeview {{ Request::is('admin/loan*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-credit-card"></i>
                        <span>Loan</span>
                        <i class="fa fa-angle-left pull-right"></i>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ Request::is('admin/loan') ? 'active' : '' }}">
                            <a href="{{ route('admin.loan.index') }}">
                                Data
                            </a>
                        </li>
                        @if ((new \App\Helpers\Authorize(Auth::user(), 'view_loan'))->check())
                            <li class="{{ Request::is('admin/loan-payment') ? 'active' : '' }}">
                                <a href="{{ route('admin.admin.loan.payment') }}">
                                    Payment
                                </a>
                            </li>
                        @endif
                        @if ((new \App\Helpers\Authorize(Auth::user(), 'report_loan'))->check())
                            <li class="{{ Request::is('admin/loan-report') ? 'active' : '' }}">
                                <a href="{{ route('admin.admin.loan.report') }}">
                                    Report
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <li
                class="treeview {{ Request::is('admin/overtime*') ? 'active' : '' }} {{ Request::is('admin/timeoff*') ? 'active' : '' }} {{ Request::is('admin/absence*') ? 'active' : '' }}">
                <a href="javascript:void(0)">
                    <i class="fa fa-hourglass"></i>
                    <span>Time Management</span>
                    <i class="fa fa-angle-left pull-right"></i>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                @if ((new \App\Helpers\Authorize(Auth::user(), 'view_overtime'))->check())
                    <ul class="treeview-menu">
                        <li class="{{ Request::is('admin/overtime*') ? 'active' : '' }}">
                            <a href="{{ route('admin.overtime.index') }}">
                                Overtime
                            </a>
                        </li>
                    </ul>
                @endif
                @if ((new \App\Helpers\Authorize(Auth::user(), 'view_timeoff'))->check())
                    <ul class="treeview-menu">
                        <li class="{{ Request::is('admin/timeoff*') ? 'active' : '' }}">
                            <a href="{{ route('admin.timeoff.index') }}">
                                Request Time Off
                            </a>
                        </li>
                    </ul>
                @endif
                <ul class="treeview-menu">
                    <li class="{{ Request::is('admin/absence') ? 'active' : '' }}">
                        <a href="{{ route('admin.absence.index') }}">
                            @if (Auth::user()->isAdmin())
                                Absence Management
                            @else
                                Absence
                            @endif
                        </a>
                    </li>
                </ul>
            </li>

            @if ((new \App\Helpers\Authorize(Auth::user(), 'customize_appearance'))->check())
                <li class="treeview {{ Request::is('admin/appearance*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-paint-brush"></i>
                        <span>{{ trans('nav.appearance') }}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ Request::is('admin/appearance/banner*') ? 'active' : '' }}">
                            <a href="{{ url('admin/appearance/banner') }}">
                                {{ trans('nav.banners') }}
                            </a>
                        </li>

                        <li class="{{ Request::is('admin/appearance/slider*') ? 'active' : '' }}">
                            <a href="{{ url('admin/appearance/slider') }}">
                                {{ trans('nav.sliders') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (Gate::allows('index', \App\Models\Inventory::class) ||
                    Gate::allows('index', \App\Models\Warehouse::class) ||
                    Gate::allows('index', \App\Models\Supplier::class) || 
                    Gate::allows('index', \App\Models\Purchasing::class)
                    )
                <li class="treeview {{ Request::is('admin/stock*') || Request::is('admin/purchasing*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-cubes"></i>
                        <span>{{ trans('nav.stock') }}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if (is_catalog_enabled())
                            @can('index', \App\Models\Inventory::class)
                                <li
                                    class="{{ (Request::is('admin/stock/inventory/physical') && !(Request::is('admin/stock/inventory/digital*') || Request::is('admin/stock/inventory/auction*'))) || (isset($inventory) && isset($product) && !$product->downloadable && !$inventory->auctionable) ? 'active' : '' }}">
                                    <a href="{{ route('admin.stock.inventory.index', ['type' => 'physical']) }}">
                                        {{ trans('nav.physical_products') }}
                                    </a>
                                </li>

                                {{-- <li
                                    class="{{ (Request::is('admin/stock/inventory/physical') && !(Request::is('admin/stock/inventory/digital*') || Request::is('admin/stock/inventory/auction*'))) || (isset($inventory) && isset($product) && !$product->downloadable && !$inventory->auctionable) ? 'active' : '' }}">
                                    <a href="{{ route('admin.stock.inventory.index', ['type' => 'opname']) }}">
                                        {{ trans('nav.opname_products') }}
                                    </a>
                                </li> --}}

                                @if (is_incevio_package_loaded('auction'))
                                    <li
                                        class="{{ Request::is('admin/stock/inventory/auction') || (isset($inventory) && $inventory->auctionable) ? 'active' : '' }}">
                                        <a href="{{ route('admin.stock.inventory.index', ['type' => 'auction']) }}">
                                            {{ trans('auction::lang.auction_items') }}
                                        </a>
                                    </li>
                                @endif
                            @endcan
                        @endif

                        @if (!is_catalog_enabled() && Auth::user()->isFromMerchant())
                            @can('index', \App\Models\Product::class)
                                <li class="{{ Request::is('admin/stock/product/physical*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/stock/product/physical') }}">
                                        {{ trans('nav.physical_products') }}
                                    </a>
                                </li>

                                <li class="{{ Request::is('admin/stock/product/digital*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/stock/product/digital') }}">
                                        {{ trans('nav.digital_products') }}
                                    </a>
                                </li>

                                @if (is_incevio_package_loaded('auction'))
                                    <li
                                        class="{{ Request::is('admin/stock/product/auction*') || (isset($inventory) && $inventory->auctionable) ? 'active' : '' }}">
                                        <a href="{{ url('admin/stock/product/auction') }}">
                                            {{ trans('auction::lang.auction_items') }}
                                        </a>
                                    </li>
                                @endif
                            @endcan
                        @endif

                        {{-- @can('index', \App\Models\Supplier::class)
                            <li class="{{ Request::is('admin/stock/supplier*') ? 'active' : '' }}">
                                <a href="{{ url('admin/stock/supplier') }}">
                                    {{ trans('nav.suppliers') }}
                                </a>
                            </li>
                        @endcan --}}

                        {{-- @can('index', \App\Models\Purchasing::class) --}}
                            <li
                                class="{{ (Request::is('admin/purchasing/purchasing*')) ? 'active' : '' }}">
                                <a href="{{ route('admin.purchasing.purchasing.index') }}">
                                    {{ trans('nav.purchasing') }}
                                </a>
                            </li>
                        {{-- @endcan --}}
                    </ul>
                </li>
            @endif

            @if (Gate::allows('index', \App\Models\Order::class) || Gate::allows('index', \App\Models\Cart::class))
                <li class="treeview {{ Request::is('admin/order*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-cart-plus"></i>
                        <span>{{ trans('nav.orders') }}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @can('index', \App\Models\Order::class)
                            <li class="{{ Request::is('admin/order/order') ? 'active' : '' }}">
                                <a href="{{ url('admin/order/order') }}">
                                    {{ trans('nav.orders') }}
                                </a>
                            </li>
                            <li class="{{ Request::is('admin/order/order-form') ? 'active' : '' }}">
                                <a href="{{ url('admin/order/order-form') }}">
                                    Order Form
                                </a>
                            </li>
                            @if ((new \App\Helpers\Authorize(Auth::user(), 'view_order_report'))->check())
                                <li class="{{ Request::is('admin/order/order-report') ? 'active' : '' }}">
                                    <a href="{{ url('admin/order/order-report') }}">
                                        Order Report
                                    </a>
                                </li>
                            @endif
                            @if ((new \App\Helpers\Authorize(Auth::user(), 'view_order_payment'))->check())
                                <li class="{{ Request::is('admin/order/order-payment-document') ? 'active' : '' }}">
                                    <a href="{{ url('admin/order/order-payment-document') }}">
                                        Payment Doc
                                    </a>
                                </li>
                            @endif
                        @endcan
                    </ul>
                </li>
            @endif

            @if (Gate::allows('index', \App\Models\User::class) ||
                    Gate::allows('index', \App\Models\Customer::class) ||
                    Gate::allows('index', \Incevio\Package\Inspector\Models\InspectorModel::class))
                <li
                    class="treeview {{ Request::is('admin/admin*') || Request::is('address/addresses/customer*') || Request::is('admin/department*') || Request::is('admin/inspector*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-user-secret"></i>
                        <span>Administrator</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @can('index', \App\Models\User::class)
                            <li class="{{ Request::is('admin/admin/user') ? 'active' : '' }}">
                                <a href="{{ url('admin/admin/user') }}">
                                    {{ trans('nav.users') }}
                                </a>
                            </li>
                        @endcan

                        @if ((new \App\Helpers\Authorize(Auth::user(), 'view_department'))->check())
                            <li class="{{ Request::is('admin/department') ? 'active' : '' }}">
                                <a href="{{ url('admin/department') }}">
                                    {{ trans('nav.departments') }}
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->isMerchant())
                            <li class="{{ Request::is('admin/admin/deliveryboys*') ? 'active' : '' }}">
                                <a href="{{ route('admin.admin.deliveryboy.index') }}">
                                    {{ trans('nav.delivery_boys') }}
                                </a>
                            </li>
                        @endif

                        @can('index', \App\Models\Customer::class)
                            <li
                                class="{{ Request::is('admin/admin/customer*') || Request::is('address/addresses/customer*') ? 'active' : '' }}">
                                <a href="{{ url('admin/admin/customer') }}">
                                    {{ trans('nav.customers') }}
                                </a>
                            </li>
                        @endcan

                        @if (
                            (Auth::user()->isAdmin() || Gate::allows('index', \Incevio\Package\Inspector\Models\InspectorModel::class)) &&
                                is_incevio_package_loaded('inspector'))
                            @can('index', \Incevio\Package\Inspector\Models\InspectorModel::class)
                                <li class="{{ Request::is('admin/inspector/inspectables*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/inspector/inspectables') }}">
                                        {{ trans('inspector::lang.inspectables') }}
                                        @include('partials._addon_badge')
                                    </a>
                                </li>
                            @endcan
                        @endif
                    </ul>
                </li>

                @if ((new \App\Helpers\Authorize(Auth::user(), 'view_payroll'))->check())
                    <li class="treeview {{ Request::is('admin/payroll*') ? 'active' : '' }}">
                        <a href="javascript:void(0)">
                            <i class="fa fa-list"></i>
                            <span>Payroll</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::is('admin/payroll') ? 'active' : '' }}">
                                <a href="{{ url('admin/payroll') }}">
                                    {{ trans('nav.payroll_data') }}
                                </a>
                            </li>
                            @if ((new \App\Helpers\Authorize(Auth::user(), 'report_payroll'))->check())
                                <li class="{{ Request::is('admin/payroll-report') ? 'active' : '' }}">
                                    <a href="{{ url('admin/payroll-report') }}">
                                        {{ trans('nav.payroll_reports') }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
            @endif

            @if (Gate::allows('index', \App\Models\Merchant::class) || Gate::allows('index', \App\Models\Shop::class))
                <li class="treeview {{ Request::is('admin/vendor*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-bar-chart"></i>
                        <span>Business Unit</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @can('index', \App\Models\Shop::class)
                            <li class="{{ Request::is('admin/vendor/merchant*') ? 'active' : '' }}">
                                <a href="{{ url('admin/vendor/merchant') }}">
                                    Area
                                </a>
                            </li>
                        @endcan

                        @can('index', \App\Models\Shop::class)
                            <li class="{{ Request::is('admin/vendor/shop*') ? 'active' : '' }}">
                                <a href="{{ url('admin/vendor/shop') }}">
                                    Employee
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @if (is_incevio_package_loaded('wallet'))
                @can('payout', \Incevio\Package\Wallet\Models\Wallet::class)
                    <li
                        class="treeview {{ Request::is('admin/payouts*') || Request::is('admin/payout*') || Request::is('admin/wallet/bulkupload/*') ? 'active' : '' }}">
                        <a href="javascript:void(0)">
                            <i class="fa fa-money"></i>
                            <span>{{ trans('wallet::lang.wallet') }}</span>
                            @include('partials._addon_badge')
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ Request::is('admin/payouts*') ? 'active' : '' }}">
                                <a href="{{ url('admin/payouts') }}">
                                    {{ trans('wallet::lang.payouts') }}
                                </a>
                            </li>

                            <li class="{{ Request::is('admin/payout/requests*') ? 'active' : '' }}">
                                <a href="{{ url('admin/payout/requests') }}">
                                    {{ trans('wallet::lang.payout_requests') }}
                                </a>
                            </li>

                            <li class="{{ Request::is('admin/wallet/bulkupload/*') ? 'active' : '' }}">
                                <a href="{{ route('admin.wallet.bulkupload.index') }}">
                                    {{ trans('wallet::lang.wallet_bulk_upload') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @if (Auth::user()->isMerchant())
                    <li class="{{ Request::is('admin/wallet*') ? 'active' : '' }}">
                        <a href="{{ route('merchant.wallet') }}">
                            <i class="fa fa-money"></i> <span>{{ trans('wallet::lang.wallet') }}</span>
                            @include('partials._addon_badge')
                        </a>
                    </li>
                @endif
            @endif

            @if (Auth::user()->isFromMerchant())
                @if (Gate::allows('index', \App\Models\Carrier::class) ||
                        Gate::allows('index', \Incevio\Package\Packaging\Models\Packaging::class))
                    <li class="treeview {{ Request::is('admin/shipping*') ? 'active' : '' }}">
                        <a href="javascript:void(0)">
                            <i class="fa fa-truck"></i>
                            <span>{{ trans('nav.shipping') }}</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            @can('index', \App\Models\Carrier::class)
                                <li class="{{ Request::is('admin/shipping/carrier*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/shipping/carrier') }}">
                                        {{ trans('nav.carriers') }}
                                    </a>
                                </li>
                            @endcan

                            @if (is_incevio_package_loaded('packaging'))
                                @can('index', \Incevio\Package\Packaging\Models\Packaging::class)
                                    <li class="{{ Request::is('admin/shipping/packaging*') ? 'active' : '' }}">
                                        <a href="{{ url('admin/shipping/packaging') }}">
                                            {{ trans('nav.packaging') }}
                                            @include('partials._addon_badge')
                                        </a>
                                    </li>
                                @endcan
                            @endif

                            @can('index', \App\Models\ShippingZone::class)
                                <li class="{{ Request::is('admin/shipping/shippingZone*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/shipping/shippingZone') }}">
                                        {{ trans('nav.shipping_zones') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
            @endif

            @if (Auth::user()->isAdmin())
                <li class="treeview {{ Request::is('admin/setting*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-gears"></i>
                        <span>{{ trans('nav.settings') }}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @can('index', \App\Models\Role::class)
                            <li class="{{ Request::is('admin/setting/role*') ? 'active' : '' }}">
                                <a href="{{ url('admin/setting/role') }}">
                                    {{ trans('nav.user_roles') }}
                                </a>
                            </li>
                        @endcan

                        @can('index', \App\Models\Tax::class)
                            <li class="{{ Request::is('admin/setting/tax*') ? 'active' : '' }}">
                                <a href="{{ url('admin/setting/tax') }}">
                                    {{ trans('nav.taxes') }}
                                </a>
                            </li>
                        @endcan

                        @can('view', \App\Models\Config::class)
                            <li class="{{ Request::is('admin/setting/general*') ? 'active' : '' }}">
                                <a href="{{ url('admin/setting/general') }}">
                                    {{ trans('nav.shop_settings') }}
                                </a>
                            </li>

                            <li
                                class="{{ Request::is('admin/setting/config*') || Request::is('admin/setting/verify*') ? 'active' : '' }}">
                                <a href="{{ url('admin/setting/config') }}">
                                    {{ trans('nav.configurations') }}
                                </a>
                            </li>

                            @if (Auth::user()->isAdmin())
                                @if (vendor_get_paid_directly() || vendor_can_on_off_payment_method())
                                    <li class=" {{ Request::is('admin/setting/paymentMethod*') ? 'active' : '' }}">
                                        <a href="{{ url('admin/setting/paymentMethod') }}">
                                            {{ trans('nav.payment_methods') }}
                                        </a>
                                    </li>
                                @endif
                            @endif

                            <li class=" {{ Request::is('admin/setting/shippingMethod*') ? 'active' : '' }}">
                                <a href="{{ url('admin/setting/shippingMethod') }}">
                                    {{ trans('nav.shipping_methods') }}
                                </a>
                            </li>
                        @endcan

                        @if (Auth::user()->isAdmin())
                            @can('view', \App\Models\System::class)
                                <li class="{{ Request::is('admin/setting/system/general*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/setting/system/general') }}">
                                        {{ trans('nav.system_settings') }}
                                    </a>
                                </li>
                            @endcan

                            @can('view', \App\Models\SystemConfig::class)
                                <li class="{{ Request::is('admin/setting/system/config*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/setting/system/config') }}">
                                        {{ trans('nav.configurations') }}
                                    </a>
                                </li>
                            @endcan
                        @endif

                        @if (is_incevio_package_loaded('wallet'))
                            @can('setting', \Incevio\Package\Wallet\Models\Wallet::class)
                                <li class="{{ Request::is('admin/setting/wallet*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/setting/wallet') }}">
                                        {{ trans('wallet::lang.wallet_settings') }}
                                    </a>
                                </li>
                            @endcan
                        @endif

                        @if (is_incevio_package_loaded('inspector') &&
                                Gate::allows('setting', \Incevio\Package\Inspector\Models\InspectorModel::class))
                            <li class="{{ Request::is('admin/setting/inspector*') ? 'active' : '' }}">
                                <a href="{{ route(config('inspector.routes.settings')) }}">
                                    {{ trans('inspector::lang.inspector_settings') }}
                                    @include('partials._addon_badge')
                                </a>
                            </li>
                        @endif

                        @if (is_incevio_package_loaded('zipcode'))
                            <li class="{{ Request::is('admin/setting/zipcode*') ? 'active' : '' }}">
                                <a href="{{ route(config('zipcode.routes.settings')) }}">
                                    {{ trans('zipcode::lang.zipcode_setting') }}
                                    @include('partials._addon_badge')
                                </a>
                            </li>
                        @endif

                        @if (is_incevio_package_loaded('dynamicCommission') &&
                                (new \App\Helpers\Authorize(Auth::user(), 'manage_dynamic_commission'))->check())
                            <li class="{{ Request::is('admin/setting/dynamicCommission*') ? 'active' : '' }}">
                                <a href="{{ route(config('dynamicCommission.routes.settings')) }}">
                                    {{ trans('dynamicCommission::lang.commissions_settings') }}
                                    @include('partials._addon_badge')
                                </a>
                            </li>
                        @endif

                        @if (is_incevio_package_loaded('searchAutocomplete') && Auth::user()->isAdmin())
                            <li class="{{ Request::is('admin/setting/autocomplete*') ? 'active' : '' }}">
                                <a href="{{ route('admin.setting.autocomplete') }}">
                                    {{ trans('searchAutocomplete::lang.search_settings') }}
                                    @include('partials._addon_badge')
                                </a>
                            </li>
                        @endif

                        @if (is_incevio_package_loaded('ebay'))
                            <li class="{{ Request::is('admin/setting/ebay*') ? 'active' : '' }}">
                                <a href="{{ url('admin/setting/ebay') }}">
                                    {{ trans('ebay::lang.ebay_settings') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @can('report', \Incevio\Package\Wallet\Models\Wallet::class)
                <li
                    class="treeview {{ Request::is('admin/report*') || Request::is('admin/shop/report*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-bar-chart"></i>
                        <span>{{ trans('nav.reports') }}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if (is_incevio_package_loaded('wallet'))
                            @can('report', \Incevio\Package\Wallet\Models\Wallet::class)
                                <li class="{{ Request::is('admin/report/payout*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.wallet.payout.report') }}">
                                        {{ trans('nav.payout') }}
                                    </a>
                                </li>
                            @endcan
                        @endif
                    </ul>
                </li>
            @endcan
        </ul>
    </section> <!-- /.sidebar -->
</aside>

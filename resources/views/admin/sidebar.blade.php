<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="{{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>
                        @if (Auth::user()->isAdmin() || Auth::user()->isMerchant() || Auth::user()->role_id === 13)
                            {{ trans('nav.dashboard') }}
                        @else
                            Offering Status
                        @endif
                    </span>
                </a>
            </li>

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
                    </ul>
                </li>
            @endif

            @if (Gate::allows('index', \App\Models\Inventory::class) ||
                    Gate::allows('index', \App\Models\Warehouse::class) ||
                    Gate::allows('index', \App\Models\Supplier::class))
                <li class="treeview {{ Request::is('admin/stock*') ? 'active' : '' }}">
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
                            <li class="{{ Request::is('admin/order/order-report') ? 'active' : '' }}">
                                <a href="{{ url('admin/order/order-report') }}">
                                    Order Report
                                </a>
                            </li>
                            {{-- FINANCE --}}
                            @if (Auth::user()->role_id === 10 || Auth::user()->role_id === 1)
                                <li class="{{ Request::is('admin/order/order-payment-document') ? 'active' : '' }}">
                                    <a href="{{ url('admin/order/order-payment-document') }}">
                                        Payment Document
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
                    class="treeview {{ Request::is('admin/admin*') || Request::is('address/addresses/customer*') || Request::is('admin/inspector*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-user-secret"></i>
                        <span>Administrator</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @can('index', \App\Models\User::class)
                            <li class="{{ Request::is('admin/admin/user*') ? 'active' : '' }}">
                                <a href="{{ url('admin/admin/user') }}">
                                    {{ trans('nav.users') }}
                                </a>
                            </li>
                        @endcan

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

                        @if (Auth::user()->isAdmin())
                            <li
                                class="{{ Request::is('admin/payroll*') ? 'active' : '' }}">
                                <a href="{{ url('admin/payroll') }}">
                                    {{ trans('nav.payroll') }}
                                </a>
                            </li>
                        @endif

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

            @if (Auth::user()->role_id === 3 || Auth::user()->isAdmin())
                <li class="treeview {{ Request::is('admin/setting*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-gears"></i>
                        <span>{{ trans('nav.settings') }}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if (is_subscription_enabled())
                            @can('index', \App\Models\SubscriptionPlan::class)
                                <li class="{{ Request::is('admin/setting/subscriptionPlan*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/setting/subscriptionPlan') }}">
                                        {{ trans('nav.subscription_plans') }}
                                    </a>
                                </li>
                            @endcan
                        @endif

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

                            @if (is_incevio_package_loaded('announcement') && Auth::user()->isAdmin())
                                <li class="{{ Request::is('admin/setting/announcement*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/setting/announcement') }}">
                                        {{ trans('nav.announcements') }}
                                        @include('partials._addon_badge')
                                    </a>
                                </li>
                            @endif

                            @if (Auth::user()->isAdmin())
                                <li class="{{ Request::is('admin/setting/country*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/setting/country') }}">
                                        {{ trans('nav.countries') }}
                                    </a>
                                </li>

                                <li class="{{ Request::is('admin/setting/currency*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/setting/currency') }}">
                                        {{ trans('nav.currencies') }}
                                    </a>
                                </li>

                                <li class="{{ Request::is('admin/setting/language*') ? 'active' : '' }}">
                                    <a href="{{ url('admin/setting/language') }}">
                                        {{ trans('app.languages') }}
                                    </a>
                                </li>
                            @endif
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

            {{-- IF SUPERADMIN OR ADMIN CONTENT --}}
            @if (
                (new \App\Helpers\Authorize(Auth::user(), 'customize_appearance'))->check() ||
                    Auth::user()->role_id === 15 ||
                    Auth::user()->isAdmin())
                <li class="treeview {{ Request::is('admin/appearance*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-paint-brush"></i>
                        <span>{{ trans('nav.appearance') }}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @unless (Auth::user()->isMerchant())
                            @if (is_incevio_package_loaded('dynamic-popup'))
                                <li class="{{ Request::is('admin/appearance/popup*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.appearance.popup') }}">
                                        <i class="fa fa-angle-double-right"></i>
                                        {{ trans('DynamicPopup::lang.dynamic_popups') }}
                                    </a>
                                </li>
                            @endif
                        @endunless

                        <li class="{{ Request::is('admin/appearance/banner*') ? 'active' : '' }}">
                            <a href="{{ url('admin/appearance/banner') }}">
                                <i class="fa fa-angle-double-right"></i> {{ trans('nav.banners') }}
                            </a>
                        </li>

                        <li class="{{ Request::is('admin/appearance/slider*') ? 'active' : '' }}">
                            <a href="{{ url('admin/appearance/slider') }}">
                                <i class="fa fa-angle-double-right"></i> {{ trans('nav.sliders') }}
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- <li
                    class="treeview {{ Request::is('admin/promotions*') || Request::is('admin/flashdeal*') ? 'active' : '' }}">
                    <a href="javascript:void(0)">
                        <i class="fa fa-bullhorn"></i>
                        <span>{{ trans('nav.promotions') }}</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if (Auth::user()->isAdmin())
                            <li class="{{ Request::is('admin/promotions*') ? 'active' : '' }}">
                                <a href="{{ url('admin/promotions') }}">
                                    <i class="fa fa-angle-double-right"></i>
                                    <span>{{ trans('nav.promotions') }}</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->isAdmin() && is_incevio_package_loaded('trendingKeywords'))
                            <li class="{{ Request::is('admin/promotions/trendingKeywords*') ? 'active' : '' }}">
                                <a href="{{ route('admin.promotion.trendingKeywords') }}">
                                    <i class="fa fa-angle-double-right"></i>
                                    {{ trans('trendingKeywords::lang.trending_keywords') }}
                                    @include('partials._addon_badge')
                                </a>
                            </li>
                        @endif

                        @if ((new \App\Helpers\Authorize(Auth::user(), 'manage_flash_deal'))->check())
                            <li class="{{ Request::is('admin/flashdeal*') ? 'active' : '' }}">
                                <a href="{{ route('admin.flashdeal') }}">
                                    <i class="fa fa-angle-double-right"></i> {{ trans('theme.flash_deal') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li> --}}
            @endif

            {{-- Except vendor --}}
            @if (Auth::user()->id !== 10)
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

                        {{-- superadmin, marketing, leader and warehouse area leader --}}
                        @if (Auth::user()->role_id === 1 ||
                                Auth::user()->role_id === 8 ||
                                Auth::user()->role_id === 13 ||
                                Auth::user()->role_id === 3)
                            <li class="treeview {{ Request::is('admin/crm*') }}">
                                <a href="javascript:void(0)">
                                    <span>CRM</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="{{ Request::is('admin/crm*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.crm.index') }}">
                                            CRM Report
                                        </a>
                                    </li>

                                    <li class="{{ Request::is('admin/crm*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.crm.data') }}">
                                            CRM Data
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="treeview {{ Request::is('admin/budget*') }}">
                                <a href="javascript:void(0)">
                                    <span>Budget</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="{{ Request::is('admin/budget*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.budget.reportAdministrator') }}">
                                            Budget Report
                                        </a>
                                    </li>

                                    <li class="{{ Request::is('admin/budget*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.budget.index') }}">
                                            Budget Data
                                        </a>
                                    </li>

                                    <li class="{{ Request::is('admin/segment*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.segment.index') }}">
                                            Budget Config
                                        </a>
                                    </li>
                                    {{-- SUPERADMIN ONLY --}}
                                    @if (Auth::user()->id === 1)
                                        <li class="{{ Request::is('admin/requirement*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.requirement.index') }}">
                                                Budget Categories
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>

                            <li class="treeview {{ Request::is('admin/target*') }}">
                                <a href="javascript:void(0)">
                                    <span>Target</span>
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    @if (Auth::user()->role_id === 1)
                                        <li class="{{ Request::is('admin/target*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.target.reportAdministrator') }}">
                                                Target Report
                                            </a>
                                        </li>
                                    @else
                                        <li class="{{ Request::is('admin/target*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.target.report') }}">
                                                Target Report
                                            </a>
                                        </li>
                                    @endif


                                    <li class="{{ Request::is('admin/target*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.target.index') }}">
                                            Target Data
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        <li class="{{ Request::is('admin/visit*') ? 'active' : '' }}">
                            <a href="{{ route('admin.visit.index') }}">
                                Visit Plan Report
                            </a>
                        </li>

                        @if (Auth::user()->isAdmin())
                            <li class="{{ Request::is('admin/report/kpi*') ? 'active' : '' }}">
                                <a href="{{ route('admin.kpi') }}">
                                    {{ trans('nav.performance') }}
                                </a>
                            </li>

                            <li class="{{ Request::is('admin/report/sales*') ? 'active' : '' }}">
                                <a href="javascript:void(0)">
                                    {{ trans('nav.sales') }}
                                    <i class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    <li class="{{ Request::is('admin/report/sales/orders*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.sales.orders') }}">
                                            {{ trans('nav.orders') }}
                                        </a>
                                    </li>
                                    <li class="{{ Request::is('admin/report/sales/products*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.sales.products') }}">
                                            {{ trans('nav.products') }}
                                        </a>
                                    </li>
                                    <li class="{{ Request::is('admin/report/sales/payment*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.sales.payments') }}">
                                            {{ trans('nav.payments') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            @if (is_incevio_package_loaded('googleAnalytics'))
                                <li class="{{ Request::is('admin/report/googleAnalytics*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.report.googleAnalytics') }}">
                                        {{ trans('analytics::lang.analytics') }}
                                        @include('partials._addon_badge')
                                    </a>
                                </li>
                            @endif

                            <li class="{{ Request::is('admin/report/visitors*') ? 'active' : '' }}">
                                <a href="{{ route('admin.report.visitors') }}">
                                    {{ trans('nav.visitors') }}
                                </a>
                            </li>

                            <li class="{{ Request::is('admin/offering*') ? 'active' : '' }}">
                                <a href="{{ route('admin.offering.index') }}">
                                    Offering Approval
                                </a>
                            </li>
                        @elseif(Auth::user()->isMerchant())
                            <li class="{{ Request::is('admin/shop/report/kpi*') ? 'active' : '' }}">
                                <a href="{{ route('admin.shop-kpi') }}">
                                    {{ trans('nav.performance') }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
            {{-- @endif --}}

        </ul>
    </section> <!-- /.sidebar -->
</aside>

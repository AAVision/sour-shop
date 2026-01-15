<!--- Dashboard --->
<li>
    <a class="menu {{ $request->routeIs('shop.dashboard.*') ? 'active' : '' }}"
        href="{{ route('shop.dashboard.index') }}">
        <span>
            <img class="menu-icon" src="{{ asset('assets/icons-admin/dashboard.svg') }}" alt="icon" loading="lazy" />
            {{ __('Dashboard') }}
        </span>
    </a>
</li>

@if ($generaleSetting?->business_based_on == 'subscription')
    @hasPermission('shop.subscription.index')
        <!--- subscription --->
        <li>
            <a href="{{ route('shop.subscription.index') }}"
                class="menu {{ request()->routeIs('shop.subscription.*') ? 'active' : '' }}">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/crown.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('Subscription') }}
                </span>
            </a>
        </li>
    @endhasPermission
@endif

@php
    use App\Enums\OrderStatus;
    $orderStatuses = OrderStatus::cases();
@endphp
@hasPermission('shop.order.index')
    <!--- Orders--->
    <li>
        <a class="menu {{ request()->routeIs('shop.order.*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#settingMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/orders.svg') }}" alt="icon" loading="lazy" />
                {{ __('All Orders') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon" loading="lazy" />
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.order.*') ? 'show' : '' }}" id="settingMenu">
            <div class="listBar">
                <a href="{{ route('shop.order.index') }}"
                    class="subMenu hasCount {{ request()->url() === route('shop.order.index') ? 'active' : '' }}">
                    {{ __('All') }} <span class="count statusAll">{{ $allOrders > 99 ? '99+' : $allOrders }}</span>
                </a>
                @foreach ($orderStatuses as $status)
                    <a href="{{ route('shop.order.index', str_replace(' ', '_', $status->value)) }}"
                        class="subMenu hasCount {{ request()->url() === route('shop.order.index', str_replace(' ', '_', $status->value)) ? 'active' : '' }}">
                        <span>{{ __($status->value) }}</span>
                        <span class="count status{{ Str::camel($status->value) }}">
                            {{ ${Str::camel($status->value)} > 99 ? '99+' : ${Str::camel($status->value)} }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </li>
@endhasPermission

@hasPermission(['shop.pos.index', 'shop.pos.draft', 'shop.pos.sales'])
    <li>
        <a class="menu {{ request()->routeIs('shop.pos.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#posMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/pos.svg') }}" alt="icon" loading="lazy" />
                {{ __('POS Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.pos.*') ? 'show' : '' }}" id="posMenu">
            <div class="listBar">
                @hasPermission('shop.pos.index')
                    <a href="{{ route('shop.pos.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.pos.index') ? 'active' : '' }}">
                        {{ __('POS') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.pos.sales')
                    <a href="{{ route('shop.pos.sales') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.pos.sales') ? 'active' : '' }}">
                        {{ __('POS Sales History') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.pos.draft')
                    <a href="{{ route('shop.pos.draft') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.pos.draft') ? 'active' : '' }}">
                        {{ __('POS Draft') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

@hasPermission('shop.returnOrder.index')
  <li>
        <a class="menu {{ $request->routeIs('shop.returnOrder.*') ? 'active' : '' }}" href="{{ route('shop.returnOrder.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/delivery-cart-arrow-up.svg') }}" alt="icon" loading="lazy" />
                {{ __('Refund Management') }}
            </span>
        </a>
    </li>
@endhaspermission

<li>
    <a class="menu {{ $request->routeIs('shop.customer.chat.index') ? 'active' : '' }}"
        href="{{ route('shop.customer.chat.index') }}">
        <span class="position-relative">
            <img class="menu-icon" src="{{ asset('assets/icons-admin/message.svg') }}" alt="icon" loading="lazy" />
            {{ __('Messages') }}
            <span id="unread-message-badge" class="bg-success text-white ms-2 position-absolute d-none"
                style="top: 0; transform: translateY(-50%); left: 5px; height: 16px; width: 16px; border-radius: 50%; font-size: 10px; display: flex; align-items: center; justify-content: center;">
                0
            </span>
        </span>
    </a>
</li>

@hasPermission(['shop.category.index', 'shop.subcategory.index'])
    <!--- categories--->
    <li>
        <a class="menu {{ request()->routeIs('shop.category.*', 'shop.subcategory.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#categoryMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/category.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Category Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon" loading="lazy" />
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.category.*', 'shop.subcategory.*') ? 'show' : '' }}"
            id="categoryMenu">
            <div class="listBar">
                <!---  categories--->
                @hasPermission('shop.category.index')
                    <a href="{{ route('shop.category.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.category.index') ? 'active' : '' }}">
                        {{ __('Category') }}
                    </a>
                @endhasPermission
                <!--- sub categories--->
                @hasPermission('shop.subcategory.index')
                    <a href="{{ route('shop.subcategory.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.subcategory.index') ? 'active' : '' }}">
                        {{ __('Sub Category') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

@hasPermission(['shop.product.index'])
    <!--- Products--->
    <li>
        <a class="menu {{ request()->routeIs('shop.product.*','shop.digital.product.*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#productMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/product.svg') }}" alt="icon" loading="lazy" />
                {{ __('Product Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon" loading="lazy" />
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.product.*','shop.digital.product.*') ? 'show' : '' }}"
            id="productMenu">
            <div class="listBar">
                @hasPermission('shop.product.index')
                    <a href="{{ route('shop.product.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.product.index') ? 'active' : '' }}">
                        {{ __('All Product') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.product.create')
                    <a href="{{ route('shop.product.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.product.create') ? 'active' : '' }}">
                        {{ __('Add Product') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.product.create')
                    <a href="{{ route('shop.digital.product.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.digital.product.create') ? 'active' : '' }}">
                        {{ __('Add Digital Product') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission


@hasPermission(['shop.brand.index', 'shop.color.index', 'shop.size.index', 'shop.unit.index'])
    <!--- Product Varient --->
    <li>
        <a class="menu {{ request()->routeIs('shop.brand.*', 'shop.color.*', 'shop.size.*', 'shop.unit.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#productVarientMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/boxes.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Product Variant Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.brand.*', 'shop.color.*', 'shop.size.*', 'shop.unit.*') ? 'show' : '' }}"
            id="productVarientMenu">
            <div class="listBar">
                @hasPermission('shop.brand.index')
                    <a href="{{ route('shop.brand.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.brand.index') ? 'active' : '' }}">
                        {{ __('Brand') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.color.index')
                    <a href="{{ route('shop.color.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.color.index') ? 'active' : '' }}">
                        {{ __('Color') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.size.index')
                    <a href="{{ route('shop.size.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.size.index') ? 'active' : '' }}">
                        {{ __('Size') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.unit.index')
                    <a href="{{ route('shop.unit.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.unit.index') ? 'active' : '' }}">
                        {{ __('Unit') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

@if (module_exists('purchase') )
    @include('purchase::layouts.purchaseSidebar')
@endif

@hasPermission(['shop.flashSale.index', 'shop.banner.index', 'shop.voucher.index'])
    <!--- Promotion management--->
    <li>
        <a class="menu {{ request()->routeIs('shop.flashSale.*', 'shop.banner.*', 'shop.voucher.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#promotionMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/ads.svg') }}" alt="icon" loading="lazy" />
                {{ __('Promotion Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.flashSale.*', 'shop.banner.*', 'shop.voucher.*') ? 'show' : '' }}"
            id="promotionMenu">
            <div class="listBar">
                @hasPermission('shop.flashSale.index')
                    <a href="{{ route('shop.flashSale.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.flashSale.*') ? 'active' : '' }}">
                        {{ __('Flash Deals') }}
                    </a>
                @endhaspermission
                @if ($businessModel == 'multi')
                    @hasPermission('shop.banner.index')
                        <a href="{{ route('shop.banner.index') }}"
                            class="subMenu hasCount {{ request()->routeIs('shop.banner.*') ? 'active' : '' }}">
                            {{ __('Banner Setup ') }}
                        </a>
                    @endhasPermission
                @endif
                @hasPermission('shop.voucher.index')
                    <a href="{{ route('shop.voucher.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.voucher.*') ? 'active' : '' }}">
                        {{ __('Promo Code') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

@if (module_exists('report') )
    @include('report::layouts.sidebar')
@endif

@hasPermission(['shop.employee.index'])
    <!--- employee--->
    <li>
        <a class="menu {{ request()->routeIs('shop.employee.*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#employeeMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/employee.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Employee Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon"
                loading="lazy" />
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.employee.*') ? 'show' : '' }}"
            id="employeeMenu">
            <div class="listBar">
                @hasPermission('shop.employee.index')
                    <a href="{{ route('shop.employee.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.employee.index') ? 'active' : '' }}">
                        {{ __('Employees') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.employee.create')
                    <a href="{{ route('shop.employee.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.employee.create') ? 'active' : '' }}">
                        {{ __('Add Employee') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

@if (module_exists('purchase') )
    @include('purchase::layouts.supplierSidebar')
@endif

@hasPermission(['shop.profile.index'])
    <!--- Profile --->
    <li>
        <a class="menu {{ $request->routeIs('shop.profile.*') ? 'active' : '' }}"
            href="{{ route('shop.profile.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/shop.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('My Shop') }}
            </span>
        </a>
    </li>
@endhasPermission



@if (!auth()->user()->hasRole('root'))
    @hasPermission('shop.withdraw.index')
        <!--- withdraw --->
        <li>
            <a class="menu {{ $request->routeIs('shop.withdraw.*') ? 'active' : '' }}"
                href="{{ route('shop.withdraw.index') }}">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/withdraw.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('Withdraws') }}
                </span>
            </a>
        </li>
    @endhasPermission
@endif

@hasPermission(['shop.bulk-product-export.index', 'shop.bulk-product-import.index', 'shop.gallery.index'])
    <!--- Import / Export --->
    <li>
        <a class="menu {{ request()->routeIs('shop.bulk-product-export.*', 'shop.bulk-product-import.*', 'shop.gallery.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#supportMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/download.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Import/Export') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.bulk-product-export.*', 'shop.bulk-product-import.*', 'shop.gallery.*') ? 'show' : '' }}"
            id="supportMenu">
            <div class="listBar">
                @hasPermission('shop.bulk-product-export.index')
                    <a href="{{ route('shop.bulk-product-export.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.bulk-product-export.*') ? 'active' : '' }}">
                        {{ __('Product Export') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.bulk-product-import.index')
                    <a href="{{ route('shop.bulk-product-import.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.bulk-product-import.*') ? 'active' : '' }}">
                        {{ __('Product Import') }}
                    </a>
                @endhasPermission
                @hasPermission('shop.gallery.index')
                    <a href="{{ route('shop.gallery.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('shop.gallery.*') ? 'active' : '' }}">
                        {{ __('Gallery Import') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

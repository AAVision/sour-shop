<!--- Dashboard --->
<li>
    <a class="menu {{ $request->routeIs('admin.dashboard.*') ? 'active' : '' }}"
        href="{{ route('admin.dashboard.index') }}">
        <span>
            <img class="menu-icon" src="{{ asset('assets/icons-admin/dashboard.svg') }}" alt="icon" loading="lazy" />
            {{ __('Dashboard') }}
        </span>
    </a>
</li>


@php
    use App\Enums\OrderStatus;
    $orderStatuses = OrderStatus::cases();
@endphp
@hasPermission('admin.order.index')
    <!--- Orders --->
    <li>
        <a class="menu {{ $request->routeIs('admin.order.*') ? 'active' : '' }}" href="{{ route('admin.order.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/orders.svg') }}" alt="icon" loading="lazy" />
                {{ __('Order Management') }}
            </span>
        </a>
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

@hasPermission('admin.returnOrder.index')
    <li>
        <a class="menu {{ $request->routeIs('admin.returnOrder.*') ? 'active' : '' }}"
            href="{{ route('admin.returnOrder.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/delivery-cart-arrow-up.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Refund Management') }}
            </span>
        </a>
    </li>
@endhasPermission

@hasPermission([
    'admin.conversation.customer.chat.index',
    'admin.conversation.getUsers',
    'admin.conversation.getMessageAdmin'
])
    <li>
        <a class="menu {{ $request->routeIs('shop.customer.chat.index') ? 'active' : '' }}"
            href="{{ route('shop.customer.chat.index') }}">
            <span class="position-relative">
                <img class="menu-icon" src="{{ asset('assets/icons-admin/message.svg') }}" alt="icon" loading="lazy" />
                {{ __('Conversations') }}
                <span id="unread-message-badge" class="bg-success text-white ms-2 position-absolute d-none"
                    style="top: 0; transform: translateY(-50%); left: 5px; height: 16px; width: 16px; border-radius: 50%; font-size: 10px; display: flex; align-items: center; justify-content: center;">
                    0
                </span>
            </span>
        </a>
    </li>
@endhasPermission

@hasPermission([
    'admin.category.index',
    'admin.subcategory.index',
    'admin.category.create',
    'admin.subcategory.create'
])
    <!--- categories --->
    <li>
        <a class="menu {{ request()->routeIs('admin.category.*', 'admin.subcategory.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#categoryMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/category.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Category Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.category.*', 'admin.subcategory.*') ? 'show' : '' }}"
            id="categoryMenu">
            <div class="listBar">
                <!---categories--->
                @hasPermission('admin.category.index')
                    <a href="{{ route('admin.category.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.category.index') ? 'active' : '' }}">
                        {{ __('All Category') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.category.create')
                    <a href="{{ route('admin.category.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.category.create') ? 'active' : '' }}">
                        {{ __('Add Category') }}
                    </a>
                @endhasPermission
                <!--- sub categories--->
                @hasPermission('admin.subcategory.index')
                    <a href="{{ route('admin.subcategory.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.subcategory.index') ? 'active' : '' }}">
                        {{ __('All Sub Category') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.subcategory.create')
                    <a href="{{ route('admin.subcategory.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.subcategory.create') ? 'active' : '' }}">
                        {{ __('Add Sub Category') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
@hasPermission(['shop.product.index', 'shop.product.create'])
    <!--- Products--->
    <li>
        <a class="menu {{ request()->routeIs('shop.product.*', 'shop.digital.product.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#productMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/product.svg') }}" alt="icon" loading="lazy" />
                {{ __('Product Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.product.*', 'shop.digital.product.*') ? 'show' : '' }}"
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

@hasPermission(['admin.brand.index', 'admin.color.index', 'admin.size.index', 'admin.unit.index'])
    <!--- Product Varient --->
    <li>
        <a class="menu {{ request()->routeIs('admin.brand.*', 'admin.color.*', 'admin.size.*', 'admin.unit.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#productVarientMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/boxes.svg') }}" alt="icon" loading="lazy" />
                {{ __('Product Variant Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.brand.*', 'admin.color.*', 'admin.size.*', 'admin.unit.*') ? 'show' : '' }}"
            id="productVarientMenu">
            <div class="listBar">
                @hasPermission('admin.brand.index')
                    <a href="{{ route('admin.brand.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.brand.index') ? 'active' : '' }}">
                        {{ __('Brand') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.color.index')
                    <a href="{{ route('admin.color.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.color.index') ? 'active' : '' }}">
                        {{ __('Color') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.size.index')
                    <a href="{{ route('admin.size.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.size.index') ? 'active' : '' }}">
                        {{ __('Size') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.unit.index')
                    <a href="{{ route('admin.unit.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.unit.index') ? 'active' : '' }}">
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

@hasPermission(['admin.banner.index', 'admin.flashSale.index', 'admin.ad.index', 'admin.coupon.index'])
    <!--- Promotion management--->
    <li>
        <a class="menu {{ request()->routeIs('admin.flashSale.*', 'admin.banner.*', 'admin.ad.*', 'admin.coupon.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#promotionMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/ads.svg') }}" alt="icon" loading="lazy" />
                {{ __('Promotion Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.flashSale.*', 'admin.banner.*', 'admin.ad.*', 'admin.coupon.*') ? 'show' : '' }}"
            id="promotionMenu">
            <div class="listBar">
                @hasPermission('admin.flashSale.index')
                    <a href="{{ route('admin.flashSale.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.flashSale.*') ? 'active' : '' }}">
                        {{ __('Flash Deals') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.banner.index')
                    <a href="{{ route('admin.banner.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.banner.*') ? 'active' : '' }}">
                        {{ __('Banner Setup') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.ad.index')
                    <a href="{{ route('admin.ad.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.ad.*') ? 'active' : '' }}">
                        {{ __('Ads Campaign ') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.coupon.index')
                    <a href="{{ route('admin.coupon.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.coupon.*') ? 'active' : '' }}">
                        {{ __('Promo Code') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

@hasPermission('admin.customerNotification.index')
    <!--- notification--->
    <li>
        <a class="menu {{ $request->routeIs('admin.customerNotification.*') ? 'active' : '' }}"
            href="{{ route('admin.customerNotification.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/notification.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Push Notification') }}
            </span>
        </a>
    </li>
@endhasPermission


@hasPermission(['admin.blog.index', 'admin.blog.create'])
    <!--- blogs--->
    <li>
        <a class="menu {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#blogMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/promotional.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Blog Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.blog.*') ? 'show' : '' }}" id="blogMenu">
            <div class="listBar">
                @hasPermission('admin.blog.index')
                    <a href="{{ route('admin.blog.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.blog.index') ? 'active' : '' }}">
                        {{ __('All Blog') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.blog.create')
                    <a href="{{ route('admin.blog.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.blog.create') ? 'active' : '' }}">
                        {{ __('Add Blog') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

@if (module_exists('report') )
    @include('report::layouts.sidebar')
@endif

@hasPermission(['admin.customer.index', 'admin.customer.create'])
    <!--- customers--->
    <li>
        <a class="menu {{ request()->routeIs('admin.customer.*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#customerMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/customer.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Customer Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.customer.*') ? 'show' : '' }}"
            id="customerMenu">
            <div class="listBar">
                @hasPermission('admin.customer.index')
                    <a href="{{ route('admin.customer.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.customer.index') ? 'active' : '' }}">
                        {{ __('All Customer') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.customer.create')
                    <a href="{{ route('admin.customer.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.customer.create') ? 'active' : '' }}">
                        {{ __('Add Customer') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
@hasPermission(['admin.rider.index', 'admin.rider.create'])
    <!--- rider--->
    <li>
        <a class="menu {{ request()->routeIs('admin.rider.*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#riderMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/rider.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Driver Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.rider.*') ? 'show' : '' }}"
            id="riderMenu">
            <div class="listBar">
                @hasPermission('admin.rider.index')
                    <a href="{{ route('admin.rider.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.rider.index') ? 'active' : '' }}">
                        {{ __('All Driver') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.rider.create')
                    <a href="{{ route('admin.rider.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.rider.create') ? 'active' : '' }}">
                        {{ __('Add Driver') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
@hasPermission(['admin.employee.index', 'admin.employee.create'])
    <!--- employees--->
    <li>
        <a class="menu {{ request()->routeIs('admin.employee.*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#employeeMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/employee.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Employee Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.employee.*') ? 'show' : '' }}"
            id="employeeMenu">
            <div class="listBar">
                @hasPermission('admin.employee.index')
                    <a href="{{ route('admin.employee.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.employee.index') ? 'active' : '' }}">
                        {{ __('All Employee') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.employee.create')
                    <a href="{{ route('admin.employee.create') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.employee.create') ? 'active' : '' }}">
                        {{ __('Add Employee') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
@if ($businessModel == 'multi')
    @hasPermission(['admin.shop.index', 'admin.shop.create'])
        <!--- shop management--->
        <li>
            <a class="menu {{ request()->routeIs('admin.shop.*') ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#shopMenu">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/shop.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('Shop Management') }}
                </span>
                <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
            </a>
            <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.shop.*') ? 'show' : '' }}"
                id="shopMenu">
                <div class="listBar">

                    @hasPermission('admin.shop.index')
                        <a href="{{ route('admin.shop.index') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.shop.index') ? 'active' : '' }}">
                            {{ __('All Shop') }}
                        </a>
                    @endhasPermission
                    @hasPermission('admin.shop.create')
                        <a href="{{ route('admin.shop.create') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.shop.create') ? 'active' : '' }}">
                            {{ __('Add Shop') }}
                        </a>
                    @endhasPermission

                </div>
            </div>
        </li>
    @endhasPermission
@endif

@hasPermission('shop.profile.index')
    <!--- profile--->
    <li>
            <a class="menu {{ $request->routeIs('shop.profile.*') ? 'active' : '' }}"
                href="{{ route('shop.profile.index') }}">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/user-circle.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('My Profile') }}
                </span>
            </a>
        </li>
@endhasPermission

@if (module_exists('purchase') )
    @include('purchase::layouts.supplierSidebar')
@endif

@if ($businessModel == 'multi')
    <!--- admin Shop products --->
    @hasPermission(['admin.product.index'])
        <li>
            <a class="menu {{ request()->routeIs('admin.product.index') ? 'active' : '' }}" data-bs-toggle="collapse"
                href="#shopProducts">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/shop-product.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('Shop Product Management') }}
                </span>
                <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon">
            </a>
            <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.product.index') ? 'show' : '' }}"
                id="shopProducts">
                <div class="listBar">
                    @if ($generaleSetting?->new_product_approval)
                        <a href="{{ route('admin.product.index', 'status=0') }}"
                            class="subMenu {{ request()->filled('status') && request()->status == 0 ? 'active' : '' }}"
                            title="{{ __('Pending Product') }}">
                            {{ __('Pending Product') }}
                        </a>
                    @endif

                    @if ($generaleSetting?->update_product_approval)
                        <a href="{{ route('admin.product.index', 'status=1') }}"
                            class="subMenu {{ request()->filled('status') && request()->status == 1 ? 'active' : '' }}"
                            title="{{ __('Update Request Product') }}">
                            {{ __('Update Product Request') }}
                        </a>
                    @endif

                    <a href="{{ route('admin.product.index', 'approve=true') }}"
                        class="subMenu {{ request()->filled('approve') && request()->approve == 'true' ? 'active' : '' }}"
                        title="{{ __('Accepted Item') }}">
                        {{ __('Accepted Product') }}
                    </a>
                </div>
            </div>
        </li>
    @endhasPermission

    @hasPermission([
        'admin.subscription-plan.index',
        'admin.subscription-plan.create',
        'admin.subscription-plan.subscription.list'
    ])
        <!--- subscription plans --->
        <li>
            <a class="menu {{ request()->routeIs('admin.subscription-plan.*') ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#subscriptionMenu">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/crown.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('Subscription Management') }}
                </span>
                <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
            </a>
            <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.subscription-plan.*') ? 'show' : '' }}"
                id="subscriptionMenu">
                <div class="listBar">
                    @hasPermission('admin.subscription-plan.subscription.list')
                        <a href="{{ route('admin.subscription-plan.subscription.list') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.subscription-plan.subscription.list') ? 'active' : '' }}">
                            {{ __('All Subscription') }}
                        </a>
                    @endhasPermission
                    @hasPermission('admin.subscription-plan.index')
                        <a href="{{ route('admin.subscription-plan.index') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.subscription-plan.index') ? 'active' : '' }}">
                            {{ __('Subscription Plan') }}
                        </a>
                    @endhasPermission
                    @hasPermission('admin.subscription-plan.create')
                        <a href="{{ route('admin.subscription-plan.create') }}"
                            class="subMenu hasCount {{ request()->routeIs('admin.subscription-plan.create') ? 'active' : '' }}">
                            {{ __('Add Subscription Plan') }}
                        </a>
                    @endhasPermission
                </div>
            </div>
        </li>
    @endhasPermission
@endif
@hasPermission(['admin.supportTicket.index', 'admin.support.index'])
    <li>
        <a class="menu {{ request()->routeIs('admin.supportTicket.*', 'admin.support.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#supportMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/3rd-config.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Support Management') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.supportTicket.*', 'admin.support.*') ? 'show' : '' }}"
            id="supportMenu">
            <div class="listBar">
                @hasPermission('admin.supportTicket.index')
                    <a href="{{ route('admin.supportTicket.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.supportTicket.*') ? 'active' : '' }}">
                        {{ __('Help Requests') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.support.index')
                    <a href="{{ route('admin.support.index') }}"
                        class="subMenu hasCount {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                        {{ __('Help Notes') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission
@if ($businessModel == 'multi')
    @hasPermission(['admin.withdraw.index'])
        <!--- withdraw --->
        <li>
            <a class="menu {{ $request->routeIs('admin.withdraw.*') ? 'active' : '' }}"
                href="{{ route('admin.withdraw.index') }}">
                <span>
                    <img class="menu-icon" src="{{ asset('assets/icons-admin/withdraw.svg') }}" alt="icon"
                        loading="lazy" />
                    {{ __('Withdrawal Management') }}
                </span>
            </a>
        </li>
    @endhasPermission
@endif

@hasPermission(['shop.bulk-product-export.index', 'shop.bulk-product-import.index', 'shop.gallery.index'])
    <!--- Import / Export --->
    <li>
        <a class="menu {{ request()->routeIs('shop.bulk-product-export.*', 'shop.bulk-product-import.*', 'shop.gallery.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#exportImportMenu">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/download.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Import/Export') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('shop.bulk-product-export.*', 'shop.bulk-product-import.*', 'shop.gallery.*') ? 'show' : '' }}"
            id="exportImportMenu">
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

<!--- country --->
@hasPermission(['admin.country.index'])
    <li>
        <a class="menu {{ $request->routeIs('admin.country.*') ? 'active' : '' }}"
            href="{{ route('admin.country.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/country.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Country') }}
            </span>
        </a>
    </li>
@endhasPermission

@hasPermission(['admin.language.index'])
    <!--- Languages --->
    <li>
        <a href="{{ route('admin.language.index') }}"
            class="menu {{ request()->routeIs('admin.language.*') ? 'active' : '' }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/Language.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Languages') }}
            </span>
        </a>
    </li>
@endhasPermission

@hasPermission([
    'admin.generale-setting.index',
    'admin.business-setting.index',
    'admin.socialLink.index',
    'admin.themeColor.index',
    'admin.deliveryCharge.index',
    'admin.ticketIssueType.index',
    'admin.verification.index',
    'admin.vatTax.index',
    'admin.currency.index',
    'admin.aiPrompt.index'
])
    <!--- Settings --->
    <li>
        <a class="menu {{ request()->routeIs('admin.generale-setting.*', 'admin.business-setting.*', 'admin.socialLink.*', 'admin.themeColor.*', 'admin.deliveryCharge.*', 'admin.ticketIssueType.*', 'admin.verification.*', 'admin.vatTax.*', 'admin.currency.*', 'admin.aiPrompt.index') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#settings">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/settings.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Business Settings') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.generale-setting.*', 'admin.business-setting.*', 'admin.socialLink.*', 'admin.themeColor.*', 'admin.deliveryCharge.*', 'admin.ticketIssueType.*', 'admin.verification.*', 'admin.vatTax.*', 'admin.currency.*', 'admin.aiPrompt.index') ? 'show' : '' }}"
            id="settings">
            <div class="listBar">
                @hasPermission('admin.generale-setting.index')
                    <a href="{{ route('admin.generale-setting.index') }}"
                        class="subMenu {{ request()->routeIs('admin.generale-setting.index') ? 'active' : '' }}">
                        {{ __('General Settings') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.business-setting.index')
                    <a href="{{ route('admin.business-setting.index') }}"
                        class="subMenu {{ request()->routeIs('admin.business-setting.*') ? 'active' : '' }}">
                        {{ __('Business Setup') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.verification.index')
                    <a href="{{ route('admin.verification.index') }}"
                        class="subMenu {{ request()->routeIs('admin.verification.*') ? 'active' : '' }}">
                        {{ __('Manage Verification') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.aiPrompt.index')
                    <a href="{{ route('admin.aiPrompt.index') }}"
                        class="subMenu {{ request()->routeIs('admin.aiPrompt.index') ? 'active' : '' }}">
                        {{ __('Ai Prompt') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.currency.index')
                    <a href="{{ route('admin.currency.index') }}"
                        class="subMenu {{ request()->routeIs('admin.currency.*') ? 'active' : '' }}">
                        {{ __('Currency') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.deliveryCharge.index')
                    <a href="{{ route('admin.deliveryCharge.index') }}"
                        class="subMenu {{ request()->routeIs('admin.deliveryCharge.*') ? 'active' : '' }}">
                        {{ __('Delivery Charge') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.vatTax.index')
                    <a href="{{ route('admin.vatTax.index') }}"
                        class="subMenu {{ request()->routeIs('admin.vatTax.*') ? 'active' : '' }}">
                        {{ __('VAT & Tax') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.themeColor.index')
                    <a href="{{ route('admin.themeColor.index') }}"
                        class="subMenu {{ request()->routeIs('admin.themeColor.*') ? 'active' : '' }}">
                        {{ __('Theme Colors') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.socialLink.index')
                    <a href="{{ route('admin.socialLink.index') }}"
                        class="subMenu {{ request()->routeIs('admin.socialLink.index') ? 'active' : '' }}">
                        {{ __('Social Links') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.ticketIssueType.index')
                    <a href="{{ url('/admin/ticket-issue-types') }}"
                        class="subMenu {{ request()->routeIs('admin.ticketIssueType.index') ? 'active' : '' }}">
                        {{ __('Ticket Issue Types') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

<!--- cms --->
@hasPermission(['admin.menu.index', 'admin.page.index', 'admin.footer.index'])
    <li>
        <a class="menu {{ request()->routeIs('admin.menu.index*', 'admin.page.*', 'admin.footer.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#cms">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/legal.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('CMS') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.menu.*', 'admin.page.*', 'admin.footer.*') ? 'show' : '' }}"
            id="cms">
            <div class="listBar">
                @hasPermission('admin.page.index')
                    <a href="{{ route('admin.page.index') }}"
                        class="subMenu {{ request()->routeIs('admin.page.*') ? 'active' : '' }}">
                        {{ __('Pages') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.menu.index')
                    <a href="{{ route('admin.menu.index') }}"
                        class="subMenu {{ request()->routeIs('admin.menu.index') ? 'active' : '' }}">
                        {{ __('Menus') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.footer.index')
                    <a href="{{ route('admin.footer.index') }}"
                        class="subMenu {{ request()->routeIs('admin.footer.index') ? 'active' : '' }}">
                        {{ __('Footer') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

<!--- third party configuration --->
@hasPermission([
    'admin.pusher.index',
    'admin.mailConfig.index',
    'admin.paymentGateway.index',
    'admin.sms-gateway.index',
    'admin.firebase.index',
    'admin.googleReCaptcha.index',
    'admin.aiPrompt.configure'
])
    <li>
        <a class="menu {{ request()->routeIs('admin.pusher.*', 'admin.mailConfig.*', 'admin.paymentGateway.*', 'admin.sms-gateway.*', 'admin.firebase.*', 'admin.googleReCaptcha.*', 'admin.aiPrompt.configure') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#thirdPartConfig" title="Third Party configuration">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/3rd-config.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('3rd Party Configuration') }}
            </span>
            <img src="{{ asset('assets/icons-admin/caret-down.svg') }}" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse {{ $request->routeIs('admin.pusher.*', 'admin.mailConfig.*', 'admin.paymentGateway.*', 'admin.sms-gateway.*', 'admin.firebase.*', 'admin.googleReCaptcha.*', 'admin.aiPrompt.configure') ? 'show' : '' }}"
            id="thirdPartConfig">
            <div class="listBar">
                @hasPermission('admin.paymentGateway.index')
                    <a href="{{ route('admin.paymentGateway.index') }}"
                        class="subMenu {{ request()->routeIs('admin.paymentGateway.*') ? 'active' : '' }}">
                        {{ __('Payment Gateway') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.sms-gateway.index')
                    <a href="{{ route('admin.sms-gateway.index') }}"
                        class="subMenu {{ request()->routeIs('admin.sms-gateway.*') ? 'active' : '' }}">
                        {{ __('SMS Gateway') }}
                    </a>
                @endhasPermission

                {{-- @hasPermission('admin.socialAuth.index')
                    <a href="{{ route('admin.socialAuth.index') }}"
                        class="subMenu {{ request()->routeIs('admin.socialAuth.*') ? 'active' : '' }}">
                        {{ __('Social Authentication') }}
                    </a>
                @endhasPermission --}}

                @hasPermission('admin.pusher.index')
                    <a href="{{ route('admin.pusher.index') }}"
                        class="subMenu {{ request()->routeIs('admin.pusher.*') ? 'active' : '' }}">
                        {{ __('Pusher Setup') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.mailConfig.index')
                    <a href="{{ route('admin.mailConfig.index') }}"
                        class="subMenu {{ request()->routeIs('admin.mailConfig.*') ? 'active' : '' }}">
                        {{ __('Mail Config') }}
                    </a>
                @endhasPermission
                @hasPermission('admin.aiPrompt.configure')
                    <a href="{{ route('admin.aiPrompt.configure') }}"
                        class="subMenu {{ request()->routeIs('admin.aiPrompt.configure') ? 'active' : '' }}">
                        {{ __('OpenAI Config') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.firebase.index')
                    <a href="{{ route('admin.firebase.index') }}"
                        class="subMenu {{ request()->routeIs('admin.firebase.*') ? 'active' : '' }}">
                        {{ __('Firebase Notification') }}
                    </a>
                @endhasPermission

                @hasPermission('admin.googleReCaptcha.index')
                    <a href="{{ route('admin.googleReCaptcha.index') }}"
                        class="subMenu {{ request()->routeIs('admin.googleReCaptcha.*') ? 'active' : '' }}">
                        {{ __('Google ReCaptcha') }}
                    </a>
                @endhasPermission
            </div>
        </div>
    </li>
@endhasPermission

<!--- roles and permissions --->
@hasPermission(['admin.role.index'])
    <li>
        <a class="menu {{ $request->routeIs('admin.role.*') ? 'active' : '' }}"
            href="{{ route('admin.role.index') }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/role-permission.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Roles & Permissions') }}
            </span>
        </a>
    </li>
@endhasPermission

<!--- contact us --->
@hasPermission('admin.contactUs.index')
    <li>
        <a href="{{ route('admin.contactUs.index') }}"
            class="menu {{ request()->routeIs('admin.contactUs.*') ? 'active' : '' }}">
            <span>
                <img class="menu-icon" src="{{ asset('assets/icons-admin/contacts.svg') }}" alt="icon"
                    loading="lazy" />
                {{ __('Contact Us') }}
            </span>
        </a>
    </li>
@endhasPermission

<li>
    <a href="{{ route('marketplace.addons') }}"
        class="menu {{ request()->routeIs('marketplace.addons') ? 'active' : '' }}">
        <span>
            <img class="menu-icon" src="{{ asset('assets/icons-admin/plug.svg') }}" alt="icon"
                loading="lazy" />
            {{ __('Add-ons') }}
        </span>
    </a>
</li>


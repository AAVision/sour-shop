@php
    $directory = app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="en" dir="{{ $directory }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- App favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ $generaleSetting?->favicon ?? asset('assets/favicon.png') }}" />

    <!-- App title -->
    <title>{{ $generaleSetting?->title ?? config('app.name', 'Laravel') }}</title>

    <!-- Meta -->
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <!-- Font-Awesome--Min-Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">

    <!-- sweetalert css-->
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">

    <!-- Bootstrap--Min-Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

    <!-- quill css -->
    <link rel="stylesheet" href="{{ asset('assets/css/quill.snow.css') }}">

    <!-- Custom--Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <!--Responsive--Css-Link -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

    <!-- Toastr Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/jquery.timepicker.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/daterangepicker.css') }}" />

    @stack('css')

    <style>
        .has-passport.fixed-header .app-header {
            top: 55px;
        }

        .has-passport.fixed-sidebar .app-main .app-main-outer {
            padding-top: 50px;
        }

        .has-passport.fixed-sidebar .app-sidebar {
            top: 80px;
            height: 100svh;
        }

        .profilePic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #eee;
        }
    </style>
</head>

<body class="loading">
    <div class="app-container body-tabs-shadow fixed-sidebar fixed-header" id="appContent">
        <div class="app-header">
            <div class="app-header-logo"></div>
            <div class="app-header-mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header-menu">
                <span>
                    <button type="button"
                        class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="app-header-content">
                <!-- Header-left-Section -->
                <div class="app-header-left">
                    <div class="header-pane ">
                        <div>
                            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                                data-class="closed-sidebar">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Header-Text-Section -->
                    <div class="header-text">
                        <h4 class="mb-0 header-title">
                            @yield('header-title')
                        </h4>
                        <p class="mb-0 header-subtitle">
                            @yield('header-subtitle')
                        </p>
                    </div>
                </div>
                <!-- End-Header-Left-section -->

                <!-- Header-Right-Section -->
                <div class="app-header-right">

                    @if ($businessModel == 'multi')
                        @php
                            $user = auth()->user();
                            $isShop = true;
                            if (!$user->hasRole('root') && ($user->shop || $user->myShop)) {
                                $isShop = false;
                            }
                        @endphp
                    @endif

                    <!-- search bar -->
                    <div class="searchingBox">
                        <div class="d-flex position-relative">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search Menu"
                                autocomplete="off">
                            <span class="searchIcon"><i class="fa fa-search"></i></span>
                        </div>
                        <ul class="search-list" style="display: none"></ul>
                    </div>
                    <div class="badgeButtonBox me-1 me-md-3">
                        <div id="searchBtn" class="notificationIcon">
                            <button type="button" class="emailBadge">
                                <img src="{{ asset('assets/icons-admin/search.svg') }}" alt="search"
                                    loading="lazy" />
                            </button>
                        </div>
                    </div>

                    <!-- Theme dark and light -->
                    <div class="badgeButtonBox me-1 me-md-3">
                        <div class="notificationIcon" onclick="switchTheme()">
                            <button type="button" class="emailBadge">
                                <img class="lightModeIcon" src="{{ asset('assets/icons-admin/moon.svg') }}"
                                    alt="bell" loading="lazy" />
                                <img class="darkModeIcon" src="{{ asset('assets/icons-admin/sun.svg') }}"
                                    alt="bell" loading="lazy" />
                            </button>
                        </div>
                    </div>

                    @hasPermission(['admin.dashboard.notification', 'shop.dashboard.notification'])
                        <!-- Notification Section -->
                        <div class="badgeButtonBox me-1 me-md-3">
                            <div class="notificationIcon">
                                <button type="button" class="emailBadge dropdown-toggle position-relative"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ asset('assets/icons/bell-on.svg') }}" alt="bell" loading="lazy" />
                                    <span class="position-absolute notificationCount" id="totalNotify"></span>
                                </button>
                                <div class="dropdown-menu p-0 emailNotificationSection">
                                    <div class="dropdown-item emailNotification">
                                        <div class="emailHeader">
                                            <h6 class="massTitle">
                                                {{ __('Notifications') }}
                                            </h6>
                                            <a href="@hasPermission('admin.dashboard.notification')
{{ route('admin.notification.readAll') }}
@else
{{ route('shop.notification.readAll') }}
@endhasPermission"
                                                class="text-dark">
                                                {{ __('Marks all as read') }}
                                            </a>
                                        </div>
                                        <div class="message-section" id="notifications">

                                        </div>
                                        <div class="emailFooter">
                                            <a href="@hasPermission('admin.dashboard.notification')
{{ route('admin.notification.show') }}
@else
{{ route('shop.notification.show') }}
@endhasPermission"
                                                class="massPera text-dark">
                                                {{ __('View All Notifications') }}
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endhasPermission

                    <!-- Language Dropdown -->
                    <div class="user-profile-box dropdown mx-3">
                        <div class="nav-profile-box dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            @php
                                $selectedLang = null;
                                foreach ($languages as $lang) {
                                    if ($lang->name == app()->getLocale()) {
                                        $selectedLang = $lang;
                                        break;
                                    }
                                }
                            @endphp
                            <div class="lang">
                                <img src="{{ asset('assets/icons-admin/Language.svg') }}" alt="icon"
                                    loading="lazy" />
                                <span>{{ ucfirst($selectedLang ? $selectedLang->title : __('English')) }}</span>
                                <i class="fa-solid fa-angle-down dropIcon"></i>
                            </div>

                        </div>

                        <div class="dropdown-menu profile-item" style="width:240px !important">
                            @foreach ($languages as $lang)
                                <a href="{{ route('change.language', 'language=' . $lang->name) }}"
                                    class="dropdown-item {{ $lang->name == app()->getLocale() ? 'language-active' : '' }}">
                                    <i class="fa fa-language mr-3"></i>
                                    {{ __($lang->title) }}
                                </a>
                                @unless ($loop->last)
                                    <hr class="dropdown-profile-divider">
                                @endunless
                            @endforeach
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="user-profile-box user-profile dropdown">
                        <div class="nav-profile-box dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="profile-info">
                                <span class="name">{{ ucfirst(Str::limit(auth()->user()?->name, 20)) }}</span>
                                <span class="role">{{ ucfirst(auth()->user()?->getRoleNames()?->first()) }}</span>
                            </div>
                            <div class="profile-image">
                                <img class="profilePic"
                                    src="{{ auth()->user()?->thumbnail ?? asset('assets/icons/user.svg') }}"
                                    alt="profile" loading="lazy" />
                            </div>
                        </div>

                        <div class="dropdown-menu profile-item">

                            <div class="d-flex justify-content-start p-2">
                                <img src="{{ auth()->user()?->thumbnail ?? asset('assets/icons/user.svg') }}"
                                    class="profilePic" alt="profile" loading="lazy">
                                <div class="ms-1">
                                    <span class="name pb-1">{{ auth()->user()?->name }}</span>
                                    <p class="pb-2 text-muted" style="font-size:12px ">{{ auth()->user()?->email }}
                                    </p>
                                    @if (request()->is('admin/*', 'admin'))
                                        <a href="{{ route('admin.profile.edit') }}"
                                            class="btn btn-sm btn-primary">{{ __('Edit Profile') }}</a>
                                    @endif
                                </div>
                            </div>

                            @if (request()->is('admin/*', 'admin'))
                                @hasPermission('admin.profile.index')
                                    <hr class="dropdown-profile-divider">
                                    <a href="{{ route('admin.profile.index') }}" class="dropdown-item">
                                        <img src="{{ asset('assets/icons-admin/user-circle.svg') }}" alt="user"
                                            loading="lazy" />
                                        {{ __('Profile') }}
                                    </a>
                                @endhasPermission
                            @else
                                @hasPermission('shop.profile.index')
                                    <hr class="dropdown-profile-divider">
                                    <a href="{{ route('shop.profile.index') }}" class="dropdown-item">
                                        <img src="{{ asset('assets/icons-admin/user-circle.svg') }}" alt="user"
                                            loading="lazy" />
                                        {{ __('Profile') }}
                                    </a>
                                @endhasPermission
                            @endif

                            @if (request()->is('admin/*', 'admin'))
                                @hasPermission('admin.generale-setting.index')
                                    <hr class="dropdown-profile-divider">
                                    <a href="{{ route('admin.generale-setting.index') }}" class="dropdown-item">
                                        <img src="{{ asset('assets/icons-admin/settings.svg') }}" alt="setting"
                                            loading="lazy" />
                                        {{ __('Settings') }}
                                    </a>
                                @endhasPermission
                                @hasPermission('admin.profile.change-password')
                                    <hr class="dropdown-profile-divider">
                                    <a href="{{ route('admin.profile.change-password') }}" class="dropdown-item">
                                        <img src="{{ asset('assets/icons-admin/role-permission.svg') }}" alt="key"
                                            loading="lazy" />
                                        {{ __('Change Password') }}
                                    </a>
                                @endhasPermission
                            @else
                                @hasPermission('shop.profile.change-password')
                                    <hr class="dropdown-profile-divider">
                                    <a href="{{ route('shop.profile.change-password') }}" class="dropdown-item">
                                        <img src="{{ asset('assets/icons-admin/role-permission.svg') }}" alt="key"
                                            loading="lazy" />
                                        {{ __('Change Password') }}
                                    </a>
                                @endhasPermission
                            @endif

                            <hr class="dropdown-profile-divider">

                            <button class="dropdown-item cursor-pointer logout text-danger">
                                <img src="{{ asset('assets/icons-admin/log-out.svg') }}" alt="key"
                                    loading="lazy" />
                                {{ __('Logout') }}
                            </button>
                        </div>
                    </div>
                </div>
                <!-- End-Header-Right-Section -->

            </div>
        </div>
        <div class="app-main">

            @include('layouts.sidebar')

            <!-- ****Body-Section***** -->

            <div class="app-main-outer">
                <!-- ****End-Body-Section**** -->
                <div class="app-main-inner">
                    <div class="container-fluid">
                        <!-- seeder run -->
                        @if ($seederRun)
                            <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                role="alert" style="padding: 10px">
                                <strong><i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                        data-placement="bottom"
                                        title='If you do not run this seeder, you will not be able to use the system.'></i>
                                    Seeder dose not run.</strong> Please run <code class="text-danger">php artisan
                                    migrate:fresh
                                    --seed</code> or <a href="{{ route('seeder.run.index') }}"
                                    class="btn btn-sm common-btn"> Click
                                    here</a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                                    id="closeAlert"></button>
                            </div>
                        @endif

                        <!-- storage link -->
                        @if ($storageLink)
                            <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                role="alert" style="padding: 10px">
                                <strong><i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                        data-placement="bottom"
                                        title='If you can not install storage link, then image not found.'></i>
                                    Storage link dose not exist or image not found then</strong> please run <code
                                    class="text-danger">php artisan
                                    storage:link</code> or <a href="{{ route('storage.install.index') }}"
                                    class="btn btn-sm common-btn">
                                    Click here</a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                                    id="closeAlert"></button>
                            </div>
                        @endif

                        <!-- subscription warning -->
                        @if (request()->subscription_required && !request()->routeIs('shop.subscription.index'))
                            @if (!request()->current_subscription)
                                <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                    role="alert" style="padding: 10px">
                                    <strong>
                                        <i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                            data-placement="bottom"
                                            title='If you do not have subscription, you will not be able to sell your products.'></i>
                                        You currently do not have an active subscription. Please purchase a plan to
                                        start selling.
                                    </strong>
                                    <a href="{{ route('shop.subscription.index') }}" class="btn btn-sm common-btn">
                                        Choose Plan
                                    </a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close" id="closeAlert"></button>
                                </div>
                            @endif

                            @if (request()->current_subscription &&
                                    !request()->subscription_expired &&
                                    request()->current_subscription->remaining_sales === 0)
                                <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                    role="alert" style="padding: 10px">
                                    <strong>
                                        <i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                            data-placement="bottom"
                                            title='If you do not have subscription, you will not be able to sell your products.'></i>
                                        You have reached the maximum number of sales for your current subscription.
                                    </strong>
                                    <a href="{{ route('shop.subscription.index') }}" class="btn btn-sm common-btn">
                                        Renew Subscription
                                    </a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close" id="closeAlert"></button>
                                </div>
                            @endif

                            @if (request()->subscription_about_to_expire)
                                <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                    role="alert" style="padding: 10px">
                                    <strong>
                                        <i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                            data-placement="bottom"
                                            title='If you do not have subscription, you will not be able to sell your products.'></i>
                                        Your subscription will expire in {{ request()->subscription_time_left }}.
                                    </strong>
                                    <a href="{{ route('shop.subscription.index') }}" class="btn btn-sm common-btn">
                                        Renew Subscription
                                    </a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close" id="closeAlert"></button>
                                </div>
                            @endif

                            @if (request()->subscription_expired)
                                <div class="alert alert-danger alert-dismissible fade show mb-3 w-100 text-center rounded-0 text-black"
                                    role="alert" style="padding: 10px">
                                    <strong>
                                        <i class="fa fa-exclamation-circle" data-toggle="tooltip"
                                            data-placement="bottom"
                                            title='If you do not have subscription, you will not be able to sell your products.'></i>
                                        Your subscription has expired.
                                    </strong>
                                    <a href="{{ route('shop.subscription.index') }}" class="btn btn-sm common-btn">
                                        Renew Subscription
                                    </a>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close" id="closeAlert"></button>
                                </div>
                            @endif
                        @endif

                        <!-- Main Content -->
                        @yield('content')
                        <!-- End Main Content -->

                    </div>
                </div>
                <!-- Footer-Section -->

                @if (!$generaleSetting || $generaleSetting?->show_footer)
                    <div class="app-wrapper-footer">
                        <div class="app-footer">
                            <div class="app-footer-inner">
                                <div>
                                    © {{ date('Y') }} {{ $generaleSetting?->footer_text }}
                                </div>
                                <div class="d-none d-sm-block">
                                    <i class="bi bi-telephone"></i>
                                    <span>
                                        {{ $generaleSetting?->footer_phone ?? '0123456789' }}
                                    </span>
                                </div>
                                <div class="d-none d-sm-block">
                                    <i class="fa-solid fa-envelope"></i>
                                    <span>
                                        {{ $generaleSetting?->email ?? 'example@gmail.com' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Logout Form -->
    @php
        $action = request()->is('admin/*', 'admin') ? route('admin.logout') : route('shop.logout');
    @endphp
    <form action="{{ $action }}" method="POST" id="logoutForm">
        @csrf
    </form>

    <script src="{{ asset('assets/scripts/jquery-3.6.3.min.js') }}"></script>
    <!-- Bootstrap-Min-Bundil-Link -->
    <script src="{{ asset('assets/scripts/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('assets/scripts/script.js') }}"></script>
    <!-- Main-Script-Js-Link -->
    <script src="{{ asset('assets/scripts/main.js') }}"></script>

    <!-- Full-Screen-Js-Link -->
    <script src="{{ asset('assets/scripts/full-screen.js') }}"></script>

    <!--select2 -->
    <script src="{{ asset('assets/scripts/select2.min.js') }}"></script>

    <!-- sweetalert js-->
    <script src="{{ asset('assets/scripts/sweetalert2.min.js') }}"></script>

    <!-- quill  editor-->
    <script src="{{ asset('assets/scripts/quill.js') }}"></script>

    <script src="{{ asset('assets/scripts/jQuery.print.min.js') }}"></script>

    <script src="{{ asset('assets/scripts/toastr.min.js') }}"></script>

    <script src="{{ asset('assets/scripts/jquery.timepicker.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/jquery-ui.js') }}"></script>

    {{-- daterange-picker --}}
    <script type="text/javascript" src="{{ asset('assets/scripts/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/scripts/daterangepicker.min.js') }}"></script>

    <!-- Pusher-Js-Link -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>


    <script>
        $(function() {
            $('input[name="date_range"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            });
            $('input[name="date_range"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(
                    picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD')
                );
            });
            $('input[name="date_range"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var themeColor = "{{ $generaleSetting?->primary_color ?? '#EE456B' }}";
            var themeHoverColor = "{{ $generaleSetting?->secondary_color ?? '#FEE5E8' }}";
            document.documentElement.style.setProperty('--theme-color', themeColor);
            document.documentElement.style.setProperty('--theme-hover-bg', themeHoverColor);

            // manage menu active svg color
            var svgImages = document.querySelectorAll(".menu.active .menu-icon");
            changeSvgImageColor(svgImages, themeColor);

            var selectedSvgImage;
            var svgColor;
            if (document.querySelectorAll(".btn-outline-primary img")) {
                selectedSvgImage = document.querySelectorAll(".btn-outline-primary img");
                svgColor = themeColor;
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            if (document.querySelectorAll(".btn-primary img")) {
                selectedSvgImage = document.querySelectorAll(".btn-primary img");
                svgColor = "#ffffff";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            if (document.querySelectorAll(".btn-outline-info img")) {
                selectedSvgImage = document.querySelectorAll(".btn-outline-info img");
                svgColor = "#0ea5e9";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            if (document.querySelectorAll(".btn-outline-warning img")) {
                selectedSvgImage = document.querySelectorAll(".btn-outline-warning img");
                svgColor = "#f97316";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            if (document.querySelectorAll(".btn-danger img")) {
                selectedSvgImage = document.querySelectorAll(".btn-danger img");
                svgColor = "#ffffff";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            if (document.querySelectorAll(".btn-outline-danger img")) {
                selectedSvgImage = document.querySelectorAll(".btn-outline-danger img");
                svgColor = "#ef4444";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            if (document.querySelectorAll(".btn-outline-success img")) {
                selectedSvgImage = document.querySelectorAll(".btn-outline-success img");
                svgColor = "#059669";
                changeSvgImageColor(selectedSvgImage, svgColor);
            }

            if (document.querySelectorAll(".svg-bg img")) {
                selectedSvgImage = document.querySelectorAll(".svg-bg img");
                svgColor = themeColor;
                changeSvgImageColor(selectedSvgImage, svgColor);
            }
        });

        function changeSvgImageColor(svgImages, svgColor, defaultColor = "#25314C") {
            svgImages.forEach(function(svgImage) {
                var svgPath = svgImage.getAttribute("src");
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var svgContent = xhr.responseText;

                        const strokeRegex = new RegExp(`stroke="${defaultColor}"`, 'g');
                        const fillRegex = new RegExp(`fill="${defaultColor}"`, 'g');

                        svgContent = svgContent.replace(strokeRegex, `stroke="${svgColor}"`);
                        svgContent = svgContent.replace(fillRegex, `fill="${svgColor}"`);

                        svgImage.src = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(svgContent);
                    }
                };
                xhr.open("GET", svgPath, true);
                xhr.send();
            });
        }

        // Fetch Admin Notifications
        const fetchAdminNotifications = () => {
            $.ajax({
                type: 'GET',
                url: "{{ route('admin.dashboard.notification') }}",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    $('#totalNotify').text(response.data.total)
                    $('#notifications').empty()
                    $.each(response.data.notifications, function(key, value) {
                        var id = value.id;
                        var link = "{{ route('admin.notification.read', ':id') }}";
                        link = link.replace(':id', id);
                        $('#notifications').append(
                            `<a href="${link}" class="item d-flex gap-2 align-items-center">
                            <div class="iconBox ${value.type == 'danger' ? 'cardIcon' : 'pdfIcon'}">
                                <i class="bi ${value.icon}"></i>
                            </div>
                            <div class="notification w-100 ${!value.is_read ? 'unread' : ''}">
                                <div class="userName">
                                    <p class="massTitle">${value.title} </p>
                                    <span class="time">${value.time}</span>
                                </div>
                                <div>
                                    <p class="description">${value.content}</p>
                                </div>
                            </div>
                        </a>`
                        );
                    })
                },
                error: function(e) {
                    $('#notifications').empty()
                    if (e.status == 401 || e.status == 403) {
                        $('#totalNotify').text(0)
                        $("#notifications").html("emply");
                    } else {
                        $("#notifications").html(e.responseText);
                    }
                }
            });
        }

        // fetch shop notifications
        const fetchShopNotifications = () => {
            $.ajax({
                type: 'GET',
                url: "{{ route('shop.dashboard.notification') }}",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    $('#totalNotify').text(response.data.total)
                    $('#notifications').empty()
                    $.each(response.data.notifications, function(key, value) {
                        var id = value.id;
                        var link = "{{ route('shop.notification.read', ':id') }}";
                        link = link.replace(':id', id);
                        $('#notifications').append(
                            `<a href="${link}" class="item d-flex gap-2 align-items-center">
                            <div class="iconBox ${value.type == 'danger' ? 'cardIcon' : 'pdfIcon'}">
                                <i class="bi ${value.icon}"></i>
                            </div>
                            <div class="notification w-100 ${!value.is_read ? 'unread' : ''}">
                                <div class="userName">
                                    <p class="massTitle">${value.title} </p>
                                    <span class="time">${value.time}</span>
                                </div>
                                <div>
                                    <p class="description">${value.content}</p>
                                </div>
                            </div>
                        </a>`
                        );
                    })
                },
                error: function(e) {
                    $('#notifications').empty()
                    if (e.status == 401 || e.status == 403) {
                        $('#totalNotify').text(0)
                        $("#notifications").html("empty");
                    } else {
                        $("#notifications").html(e.responseText);
                    }
                }
            });
        }
    </script>

    <!-- Pusher Scripts -->
    <script>
        var pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
            cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
        });

        var channel = pusher.subscribe('notification-channel');
    </script>

    <!-- Show Notifications Using Pusher JS -->

    @if (request()->is('admin/*', 'admin'))
        @hasPermission('admin.dashboard.notification')
            <script>
                channel.bind('admin-product-request', function(data) {
                    var message = data.message;
                    if (message.startsWith('"') && message.endsWith('"')) {
                        message = message.slice(1, -1);
                    }
                    toastr.success(message)
                    // new Audio("{{ asset('assets/audio/notification.mp3') }}").play();
                    fetchAdminNotifications()
                });

                channel.bind('support-ticket-event', function(data) {
                    var message = data.message;
                    if (message.startsWith('"') && message.endsWith('"')) {
                        message = message.slice(1, -1);
                    }
                    toastr.success(message)
                    fetchAdminNotifications()
                });

                fetchAdminNotifications() // fetch Admin Notifications
            </script>
        @endhasPermission
    @else
        @hasPermission('shop.dashboard.notification')
            <script>
                var shopID = "{{ generaleSetting('shop')?->id }}";
                channel.bind('product-approve-event', function(data) {
                    var requestShopId = data.shop_id;
                    var message = data.message;
                    if (requestShopId == shopID) {
                        if (message.startsWith('"') && message.endsWith('"')) {
                            message = message.slice(1, -1);
                        }
                        toastr.success(message)
                        fetchShopNotifications()
                    }
                });
                fetchShopNotifications() // fetch Shop Notifications
            </script>
        @endhasPermission
    @endif

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>

    @stack('scripts')

    @if (session('success'))
        <script>
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            })
        </script>
    @endif

    @if (session('demoMode'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "{{ session('demoMode') }}",
            });
        </script>
    @endif

    @if (session('alertError'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                html: `{{ session('alertError')['message'] }} <br><br> {{ isset(session('alertError')['message2']) ? session('alertError')['message2'] : '' }}`,
            });
        </script>
    @endif

    <Script>
        document.addEventListener("DOMContentLoaded", function() {
            var root = document.documentElement;

            // Get the value of --theme-color
            var themeColor = getComputedStyle(root).getPropertyValue("--theme-color");

            $(".deleteConfirm").on("click", function(e) {
                e.preventDefault();
                const url = $(this).attr("href");
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: '{{ __('You will not be able to revert this!') }}',
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: themeColor,
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ __('Yes, delete it!') }}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });

            $(".logout").on("click", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: "{{ __('Are you sure you want to log out?') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: themeColor,
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ __('Yes, Logout!') }}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById("logoutForm").submit();
                    }
                });
            });

            // form submit loader
            $('form').on('submit', function() {
                var submitButton = $(this).find('button[type="submit"]');

                submitButton.prop('disabled', true);
                submitButton.removeClass('px-5');

                submitButton.html(`<div class="d-flex align-items-center gap-1">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span>Loading...</span>
                </div>`)
            });
        });
    </Script>

    <script>
        const lastSeenTime = "{{ session('last_online') }}";

        const shouldUpdateLastSeen = () => {
            if (!lastSeenTime) return true;

            const last = new Date(lastSeenTime);
            const now = new Date();

            const diffInMinutes = Math.floor((now - last) / 60000); // in minutes
            return diffInMinutes >= 10
        };

        const addTime = () => {
            $.ajax({
                type: 'POST',
                url: "/update/last/seen",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    // console.log("Last seen updated at:", response.time);
                }
            });
        };

        if (shouldUpdateLastSeen()) {
            addTime(); // Immediate update if needed
        }

        let timeoutOnline = setInterval(addTime, 10 * 60 * 1000); // 10 minutes

        $(window).on('beforeunload', function() {
            clearInterval(timeoutOnline);
        });
    </script>


    <script>
        var shopID = "{{ generaleSetting('shop')?->id }}";
        const fetchUnreadMessageCount = () => {
            $.ajax({
                url: "/api/unread-messages",
                method: "GET",
                data: {
                    shop_id: shopID
                },
                dataType: 'json',
                success: function(response) {

                    if (response.data.unread_messages > 0) {
                        $('#unread-message-badge').text(response.data.unread_messages).removeClass(
                            'd-none');
                    } else {
                        $('#unread-message-badge').addClass('d-none');
                    }

                    console.log(response);

                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        const connectPusher = () => {
            var channel = pusher.subscribe(`chat_shop_${shopID}`);
            channel.bind('send-message-to-shop', function(data) {
                if (!window.location.href.includes('user')) {
                    fetchUnreadMessageCount();
                }
            });
        }

        connectPusher();
        fetchUnreadMessageCount();
    </script>


    <script>
        // show active menu
        $(document).ready(function() {
            let activeMenuItem = $('.vertical-nav-menu .menu.active');

            if (activeMenuItem.length) {
                activeMenuItem.closest('.collapse').addClass('show');

                let sidebar = $('.scrollbar-sidebar');

                let offsetTop = activeMenuItem.offset().top;
                let sidebarTop = sidebar.offset().top;
                let scrollPosition = sidebar.scrollTop() + (offsetTop - sidebarTop) - 80;

                sidebar.animate({
                    scrollTop: scrollPosition
                }, 500);
            }
        });
    </script>

    <script>
        function deleteAction(deleteUrl) {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('This instance and all associated data will be permanently deleted!') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.replace(deleteUrl);
                }
            });
        }
    </script>

</body>

</html>

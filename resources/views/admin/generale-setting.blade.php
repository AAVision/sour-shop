@extends('layouts.app')

@section('title', __('Admin Settings'))

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="bi bi-gear-fill"></i> {{ __('Admin Settings') }}
            <button class="btn btn-primary btn-sm ms-3" id="runUpdateScript">
                {{ __('Run Latest Update Script') }}
            </button>
        </div>
    </div>
    <form action="{{ route('admin.generale-setting.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card mt-3">
            <div class="card-body">

                <div class="row">
                    <div class="col-lg-6">
                        <div class="">
                            <x-input type="text" label="Website Name" name="name" placeholder="Enter Website Name"
                                :value="$generaleSetting?->name" />
                        </div>

                        <div class="mt-4">
                            <x-input label="Website Title" name="title" type="text"
                                placeholder="Enter Website Title for title bar" :value="$generaleSetting?->title" />
                        </div>

                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <x-select label="Default Currency" name="currency">
                                    <option value="">
                                        {{ __('Select Currency') }}
                                    </option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}"
                                            {{ $generaleSetting?->currency_id == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->symbol }})
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>

                            <div class="col-sm-6 mt-4 mt-sm-0">
                                <x-select label="Currency Position" name="currency_position">
                                    <option value="prefix"
                                        {{ $generaleSetting?->currency_position == 'prefix' ? 'selected' : '' }}>
                                        {{ __('Prefix') }}
                                    </option>
                                    <option value="suffix"
                                        {{ $generaleSetting?->currency_position == 'suffix' ? 'selected' : '' }}>
                                        {{ __('Suffix') }}
                                    </option>
                                </x-select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-sm-6 mt-4 mt-sm-5">
                                <div class="mt-3 d-flex align-items-center justify-content-center">
                                    <div class="logoRatio">
                                        <img id="previewLogo"
                                            src="{{ $generaleSetting?->logo ?? 'https://placehold.co/200x50/png' }}"
                                            alt="" width="100%" loading="lazy" />
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <x-file name="logo" label="Logo Ratio4:1 (200x50)" preview="previewLogo" />
                                </div>
                            </div>

                            <div class="col-sm-6 mt-4">
                                <div class="mt-3 d-flex align-items-center justify-content-center">
                                    <div class="logoFav">
                                        <img id="previewFavicon"
                                            src="{{ $generaleSetting?->favicon ?? 'https://placehold.co/300x300/png' }}"
                                            alt="" width="100%" loading="lazy" />
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <x-file name="favicon" label="Favicon (300x300)" preview="previewFavicon" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-6 mt-4">
                        <div class="mt-3 d-flex align-items-center justify-content-center">
                            <div class="logoFav">
                                <img id="previewAppIcon"
                                    src="{{ $generaleSetting?->appLogo ?? 'https://placehold.co/300x300/png' }}"
                                    alt="" width="100%" loading="lazy" />
                            </div>
                        </div>
                        <div class="mt-3">
                            <x-file name="app_logo" label="App Logo (300x300)" preview="previewAppIcon" />
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!--######## Others Information ##########-->
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center gap-2 py-3">
                <i class="bi bi-app-indicator"></i>
                <h5 class="mb-0">
                    {{ __('Others Information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <label for="">Mobile Number</label>
                        <input type="text" name="mobile" class="form-control" oninput="this.value = this.value.replace(/[^0-9+]/g, '').replace(/(?!^)\+/g, '')" value="{{ $generaleSetting?->mobile }}" placeholder="Enter Mobile Number">
                    </div>

                    <div class="col-lg-4 col-md-6 mt-4 mt-lg-0">
                        <x-input type="email" name="email" label="Email Address" placeholder="Enter Email Address"
                            :value="$generaleSetting?->email" />
                    </div>

                    <div class="col-lg-4 col-md-6 mt-4 mt-lg-0">
                        <x-input type="text" name="address" label="Address" placeholder="Enter Address"
                            :value="$generaleSetting?->address" />
                    </div>

                </div>
            </div>
        </div>

        <!--######## download app link ##########-->
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-app-indicator"></i>
                    <h5 class="mb-0">
                        {{ __('Download App Link') }}
                    </h5>
                </div>

                <div>
                    <label class="m-0 fw-bold" for="toggle">
                        {{ __('Show/Hide Website Navigation Download App') }}
                    </label>
                    <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-title="Show/Hide">
                        <input id="toggle" type="checkbox" {{ $generaleSetting?->show_download_app ? 'checked' : '' }}
                            name="show_download_app">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="" class="mb-1">
                            {{ __('Google PlayStore App Link') }}
                        </label>
                        <textarea name="google_playstore_url" class="form-control" rows="3"
                            placeholder="Enter Google PlayStore App Link">{{ $generaleSetting?->google_playstore_url }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="" class="mb-1">
                            {{ __('Apple Store App Link') }}
                        </label>
                        <textarea name="app_store_url" class="form-control" rows="3" placeholder="Enter Apple Store App Link">{{ $generaleSetting?->app_store_url }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        <!--######## Footer Information ##########-->
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap py-3">
                <div class="d-flex align-items-center gap-1">
                    <i class="bi bi-align-bottom"></i>
                    <h5 class="mb-0">
                        {{ __('Footer Section Info') }}
                    </h5>
                </div>

                <div>
                    <label class="m-0 fw-bold" for="toggle">
                        {{ __('Show/Hide Admin Bottom Footer Section') }}
                    </label>
                    <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-title="Show/Hide">
                        <input id="toggle" type="checkbox" {{ $generaleSetting?->show_footer ? 'checked' : '' }}
                            name="show_footer">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Hotline Number</label>
                        <input type="text" name="footer_phone" class="form-control" oninput="this.value = this.value.replace(/[^0-9+]/g, '').replace(/(?!^)\+/g, '')" value="{{ $generaleSetting?->footer_phone }}" placeholder="Enter Hotline Number">
                    </div>

                    <div class="col-md-6 mt-4 mt-lg-0">
                        <x-input type="text" name="footer_text" label="Footer Text" placeholder="Enter Footer Text"
                            :value="$generaleSetting?->footer_text ?? 'All right reserved by company'" />
                    </div>

                    <div class="col-md-6 mt-4">
                        <div class="mt-4 d-flex align-items-center justify-content-center">
                            <div class="logoRatio">
                                <img id="previewFooterLogo"
                                    src="{{ $generaleSetting?->footerLogo ?? 'https://placehold.co/200x50/png' }}"
                                    alt="" width="100%" loading="lazy" />
                            </div>
                        </div>
                        <div class="mt-3">
                            <x-file name="footer_logo" label="Frontend Footer Logo Ratio4:1"
                                preview="previewFooterLogo" />
                        </div>
                    </div>

                    <div class="col-md-6 mt-4">
                        <div class="mt-2 d-flex align-items-center justify-content-center">
                            <div class="logoFav">
                                <img id="footerQrCode"
                                    src="{{ $generaleSetting?->footerQr ?? 'https://placehold.co/200x200/png' }}"
                                    alt="" width="100%" loading="lazy" />
                            </div>
                        </div>
                        <div class="mt-3">
                            <x-file name="footer_qrcode" label="Frontend Scan the QR (200x200)" preview="footerQrCode" />
                        </div>
                    </div>

                </div>
            </div>
        </div>

        @hasPermission('admin.generale-setting.update')
            <div class="d-flex justify-content-end mt-4 mb-3">
                <button type="submit" class="btn btn-primary py-2.5 px-3">
                    {{ __('Save And Update') }}
                </button>
            </div>
        @endhasPermission

    </form>

    <form action="{{ route('admin.generale-setting.update.command') }}" method="POST" id="scriptRunForm">
        @csrf
    </form>



@endsection
@push('scripts')
    <script>
        $('#runUpdateScript').click(function() {
            Swal.fire({
                title: "{{ __('Are you sure? want to run update script') }}",
                text: "When you run this script, all data related to the latest version (v{{ config('app.version') }}) will be reset. Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "{{ __('Yes, Run!') }}",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("scriptRunForm").submit();
                }
            });
        })
    </script>
    @if (session('runUpdateScriptError'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                html: `@foreach (session('runUpdateScriptError') as $error)
                    {{ $error }} <br><br>
                @endforeach`,
            });
        </script>
    @endif
@endpush

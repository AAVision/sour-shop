@extends('layouts.app')
@section('header-title', __('Edit Shop'))
@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="fa-solid fa-shop"></i>{{ __('Edit Shop') }}
        </div>
    </div>

    <form action="{{ route('shop.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card mt-3">
            <div class="card-body">

                <div class="d-flex gap-2 border-bottom pb-2">
                    <i class="fa-solid fa-user"></i>
                    <h5>
                        {{ __('User Information') }}
                    </h5>
                </div>

                <div class="row">
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <x-input label="First Name" name="first_name" type="text" placeholder="First Name"
                                        :value="$shop->user?->name" required="true" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <x-input label="Last Name" name="last_name" type="text" placeholder="Last Name"
                                        :value="$shop->user?->last_name" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <x-input label="Phone Number" name="phone" type="number" placeholder="phone number"
                                :value="$shop->user?->phone" required="true" />
                        </div>

                        <div class="mt-3">
                            <x-select label="Gender" name="gender">
                                <option value="male" {{ $shop->user?->gender == 'male' ? 'selected' : '' }}>
                                    {{ __('Male') }}</option>
                                <option value="female" {{ $shop->user?->gender == 'female' ? 'selected' : '' }}>
                                    {{ __('Female') }}</option>
                                <option value="other" {{ $shop->user?->gender == 'other' ? 'selected' : '' }}>
                                    {{ __('Other') }}</option>
                            </x-select>
                        </div>

                        <div class="mt-3">
                            <x-input type="email" name="email" label="Email" required="true"
                                placeholder="Enter Email Address" :value="$shop->user?->email" :readonly="$shop->user_id != auth()->id() ? true : false" />
                        </div>

                    </div>
                    <div class="col-lg-5">
                        <div class="mt-3 mt-lg-5 d-flex align-items-center justify-content-center">
                            <div class="ratio1x1 mt-lg-5">
                                <img id="previewProfile" src="{{ $shop->user?->thumbnail ?? asset('default/default.jpg') }}"
                                    alt="" width="100%">
                            </div>
                        </div>
                        <div class="mt-3">
                            <x-file name="profile_photo" label="User profile (Ratio 1:1)" preview="previewProfile" />
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!--######## Shop Information ##########-->
        <div class="card mt-4 mb-4">
            <div class="card-body">

                <div class="d-flex gap-2 border-bottom pb-2">
                    <i class="fa-solid fa-user"></i>
                    <h5>
                        {{ __('Shop Information') }}
                    </h5>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <x-input type="text" name="shop_name" label="Shop Name" placeholder="Shop Name" :value="$shop->name"
                            required="true" />
                    </div>

                    <div class="col-md-4 mt-3 mt-md-0">
                        <x-input type="text" name="address" label="Address" placeholder="Address" :value="$shop->address" />
                    </div>

                    <div class="col-lg-4 mt-3">
                        <div>
                            <x-input type="text" name="min_order_amount" label="Minimum Order Amount" :value="$shop->min_order_amount"
                                onlyNumber="true" />
                        </div>
                    </div>

                    <div class="col-md-4 mt-4">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="ratio1x1">
                                <img src="{{ $shop->logo ?? asset('default/default.jpg') }}" id="previewShopLogo"
                                    alt="" width="100%">
                            </div>
                        </div>
                        <x-file name="shop_logo" label="Shop logo(Ratio 1:1)" preview="previewShopLogo" />
                    </div>

                    <div class="col-md-4 mt-4">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="ratio4x1">
                                <img src="{{ $shop->banner ?? asset('default/default.jpg') }}" id="shopBanner"
                                    alt="" width="100%">
                            </div>
                        </div>
                        <x-file name="shop_banner" label="Shop banner Ratio 4:1 (2000 x 500 px)" preview="shopBanner" />
                    </div>

                    <div class="col-md-4 mt-3">
                        <div>
                            <x-input type="text" name="opening_time" label="Opening Time" :value="Carbon\Carbon::parse($shop->opening_time)->format('H:i')"
                                id="timepicker" required="true" />
                        </div>

                        <div class="mt-3">
                            <x-input type="text" name="closing_time" label="Closing Time" :value="Carbon\Carbon::parse($shop->closing_time)->format('H:i')"
                                id="timepicker2" required="true" />
                        </div>

                        <div class="mt-3">
                            <x-input type="text" name="estimated_delivery_time" label="Estimated Delivery"
                                :value="$shop->estimated_delivery_time" required="true" />
                        </div>

                    </div>

                    <div class="col-lg-4 mt-3">
                        <div>
                            <x-input type="text" name="prefix" label="Order ID Prefix" :value="$shop->prefix"
                                required="true" />
                        </div>
                    </div>

                </div>

                <div class="mt-3">
                    <label for="">
                        {{ __('Description') }}
                    </label>
                    <textarea name="description" class="form-control" id="description" rows="2" placeholder="Enter Description" onkeyup="checkDescription()">{{ old('description') ?? $shop->description }}</textarea>
                    @error('description')
                        <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror
                    <p class="text text-danger m-0" id="descriptionError"></p>
                </div>

                <div class="border-top mt-4 mb-4"></div>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary py-2 px-5">
                        {{ __('Update') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#timepicker').timepicker({
                'timeFormat': 'H:i:s'
            });

            $('#timepicker2').timepicker({
                'timeFormat': 'H:i:s'
            });
        });
        function checkDescription() {
            if (document.getElementById('description').value.length > 200) {
                document.getElementById('descriptionError').innerHTML =
                    'Description must be less than or equal to 220 characters';
            } else {
                document.getElementById('descriptionError').innerHTML = '';
            }
        }
    </script>
@endpush

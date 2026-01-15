@extends('layouts.app')

@section('header-title', __('Add New Product'))

@section('content')
    <form action="{{ route('shop.product.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="page-title">
            <div class="d-flex gap-2 align-items-center mb-4">
                {{ __('Add New Product') }}
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="pb-0 fz-18 mb-3">
                    {{ __('Product Info') }}
                    <hr class="text-muted">
                </div>

                <div class="">
                    <x-input label="Product Name" name="name" id="product_name" type="text"
                        placeholder="Enter Product Name" required="true" />
                </div>

                <div class="mt-3">
                    <label for="">
                        {{ __('Short Description') }}
                        <span class="text-danger">*</span>
                    </label>
                    <textarea required name="short_description" id="short_description"
                        class="form-control @error('short_description') is-invalid @enderror" rows="2"
                        placeholder="Enter short description">{{ old('short_description') }}</textarea>
                    @error('short_description')
                        <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-3">
                    <label for="">
                        {{ __('Description') }}
                        <span class="text-danger">*</span>
                    </label>
                    @hasPermission('shop.product.generate.AI.data')
                        <button class="btn btn-sm btn-primary rounded mb-1" id="generateAi" type="button">🧠 Generate Via
                            Ai</button>
                    @endhasPermission
                    <div id="editor" style="max-height: 750px; overflow-y: auto">
                        {!! old('description') !!}
                    </div>
                    <input type="hidden" id="description" name="description" value="{{ old('description') }}">
                    @error('description')
                        <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror
                </div>

                <!--######## General Information ##########-->
                <div class="pb-2 fz-18 mt-4">
                    {{ __('Generale Information') }}
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">
                                    {{ __('Select Category') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="category" class="form-control select2" style="width: 100%">
                                    <option value="" selected disabled>
                                        {{ __('Select Category') }}
                                    </option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="text text-danger m-0">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-6 col-lg-4 mt-3 mt-md-0">
                                <label class="form-label">
                                    {{ __('Select Sub Categories') }}
                                </label>
                                <select name="sub_category[]" data-placeholder="Select Sub Category"
                                    class="form-control select2" multiple style="width: 100%">
                                    <option value="" disabled>{{ __('Select Sub Category') }}</option>
                                </select>
                                @error('sub_category')
                                    <p class="text text-danger m-0">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-md-6 col-lg-4 mt-3 mt-md-0">
                                <x-select label="Select Brand" name="brand">
                                    <option value="">
                                        {{ __('Select Brand') }}
                                    </option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </x-select>
                            </div>

                            <div class="col-md-6 col-lg-4 mt-4">
                                <label class="form-label">{{ __('Select Color') }}</label>
                                <select name="colorIds[]" data-placeholder="Select Color" class="form-control colorSelect"
                                    multiple style="width: 100%">
                                    <option value="">
                                        {{ __('Select Color') }}
                                    </option>
                                    @foreach ($colors as $color)
                                        <option value="{{ $color->id }}" data-color="{{ $color->color_code }}"
                                            data-name="{{ $color->name }}">
                                            {{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4 col-md-6 mt-4">
                                <x-select label="Select Unit" name="unit" placeholder="Select Unit">
                                    <option value="">
                                        {{ __('Select Unit') }}
                                    </option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </x-select>
                            </div>

                            <div class="col-md-6 col-lg-4 mt-4">
                                <label class="form-label">{{ __('Select Size') }}</label>
                                <select name="sizeIds[]" data-placeholder="Select Size" class="form-control sizeSelector"
                                    multiple="true" style="width: 100%">
                                    <option value="">
                                        {{ __('Select Size') }}
                                    </option>
                                    @foreach ($sizes as $size)
                                        <option value="{{ $size->id }}" data-size="{{ $size->name }}">
                                            {{ $size->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 col-lg-4 mt-4">
                                <label class="form-label d-flex align-items-center gap-2 justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <span>
                                            {{ __('Product SKU') }}
                                            <span class="text-danger">*</span>
                                        </span>
                                        <span class="info" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="{{ __('Create a unique product code. This will be used generate barcode') }}">
                                            <i class="bi bi-info"></i>
                                        </span>
                                    </div>
                                    <span class="text-primary cursor-pointer" onclick="generateCode()">
                                        {{ __('Generate Code') }}
                                    </span>
                                </label>
                                <input type="text" id="barcode" name="code" placeholder="Ex: 134543"
                                    class="form-control" value="{{ old('code') }}"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" />
                                @error('code')
                                    <p class="text text-danger m-0">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!--######## Price Information ##########-->
                <div class="pb-2 fz-18 mt-4">
                    {{ __('Price Information') }}
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <x-input type="text" name="buy_price" label="Buying Price" placeholder="Buying Price"
                                    required="true" onlyNumber="true" />
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <x-input type="text" name="price" label="Selling Price" placeholder="Selling Price"
                                    required="true" onlyNumber="true" value="10" />
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3 mt-md-0">
                                <x-input type="text" name="discount_price" label="Discount Price"
                                    placeholder="Discount Price" onlyNumber="true" value="0" />
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3">
                                <x-input type="text" name="quantity" label="Current Stock Quantity"
                                    placeholder="Current Stock Quantity" onlyNumber="true" />
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3">
                                <x-input type="text" onlyNumber="true" name="min_order_quantity"
                                    label="Minimum Order Quantity" placeholder="Minimum Order Quantity" value="1" />
                            </div>
                        </div>

                        <!--######## color wise price table ##########-->
                        <div class="border rounded p-0 position-relative overflow-hidden" id="colorBox"
                            style="display: none">
                            <p class="fw-bolder box-title">
                                {{ __('Color wise extra price') }}
                            </p>
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>
                                            {{ __('Name') }}
                                        </th>
                                        <th>
                                            {{ __('Extra Price') }}
                                        </th>
                                        <th>
                                            {{ __('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="selectedColorsTableBody"></tbody>
                            </table>
                        </div>

                        <!--######## Size wise price table ##########-->
                        <div class="border rounded p-0 position-relative overflow-hidden" id="sizeBox"
                            style="display: none">
                            <p class="fw-bold box-title">
                                {{ __('Size wise extra price') }}
                            </p>
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>
                                            {{ __('Size') }}
                                        </th>
                                        <th>
                                            {{ __('Extra Price') }}
                                        </th>
                                        <th>
                                            {{ __('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="selectedSizesTableBody"></tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <!--######## Thumbnail Information ##########-->
                <div>
                    <div class="pb-2 fz-18 mt-4">
                        {{ __('Images') }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card card-body h-100">
                            <div class="mb-2">
                                <h5>
                                    {{ __('Thumbnail') }}
                                    <span class="text-primary">{{ __('(Ratio 1:1 (500 x 500 px))') }}</span>
                                    <span class="text-danger">*</span>
                                </h5>
                                @error('thumbnail')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <label for="thumbnail" class="additionThumbnail">
                                <img src="https://placehold.co/500x500/f1f5f9/png" id="preview" alt=""
                                    width="100%">
                            </label>
                            <input id="thumbnail" accept="image/*" type="file" name="thumbnail" class="d-none"
                                onchange="previewFile(event, 'preview')">
                            <small class="text-muted mt-1">{{ __('Supported formats: jpg, jpeg, png') }}</small>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="mb-2">
                                    <h5>
                                        {{ __('Additional Thumbnail') }}
                                        <span class="text-primary">{{ __('(Ratio 1:1 (500 x 500 px))') }}</span>
                                    </h5>
                                    @error('additionThumbnail')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="d-flex flex-wrap gap-3" id="additionalElements">

                                    <div id="addition">
                                        <label for="additionThumbnail1" class="additionThumbnail">
                                            <img src="https://placehold.co/500x500/f1f5f9/png" id="preview2"
                                                alt="" width="100%" height="100%">
                                            <button onclick="removeThumbnail('addition')" id="removeThumbnail1"
                                                type="button" class="delete btn btn-sm btn-outline-danger circleIcon"
                                                style="display: none">
                                                <img src="{{ asset('assets/icons-admin/trash.svg') }}" loading="lazy"
                                                    alt="trash" />
                                            </button>
                                        </label>
                                        <input id="additionThumbnail1" accept="image/*" type="file"
                                            name="additionThumbnail[]" class="d-none"
                                            onchange="previewAdditionalFile(event, 'preview2', 'removeThumbnail1')">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--######## Product Video ##########-->
                <div class="card mt-4">
                    <div class="card-body">

                        <div class="d-flex gap-2 border-bottom pb-2">
                            <i class="fa-solid fa-play"></i>
                            <h5>
                                {{ __('Upload or Add Product Video') }}
                            </h5>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <!-- Select Upload Type -->
                            <div class="mb-3">
                                <label for="uploadType" class="form-label">
                                    {{ __('Select Video Type') }}
                                </label>
                                <select class="form-select" name="uploadVideo[type]" id="uploadType"
                                    onchange="toggleFields()">
                                    <option value="file" {{ old('uploadVideo.type') == 'file' ? 'selected' : '' }}>
                                        {{ __('Upload Video File') }}
                                    </option>
                                    <option value="youtube" {{ old('uploadVideo.type') == 'youtube' ? 'selected' : '' }}>
                                        {{ __('YouTube Link') }}
                                    </option>
                                    <option value="vimeo" {{ old('uploadVideo.type') == 'vimeo' ? 'selected' : '' }}>
                                        {{ __('Vimeo Link') }}
                                    </option>
                                    <option value="dailymotion"
                                        {{ old('uploadVideo.type') == 'dailymotion' ? 'selected' : '' }}>
                                        {{ __('Dailymotion Link') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Upload File Section -->
                            <div class="mb-3 flex-grow-1" id="fileUploadField">
                                <label for="productVideo" class="form-label">
                                    {{ __('Upload Product Video') }}
                                </label>
                                <input type="file" class="form-control" name="uploadVideo[file]" id="productVideo"
                                    accept="video/*">
                                <small class="text-muted">
                                    {{ __('Supported formats: MP4, AVI, MOV, WMV') }}
                                </small>
                            </div>

                            <!-- YouTube Link Section -->
                            <div class="mb-3 d-none flex-grow-1" id="youtubeField">
                                <label for="youtubeLink" class="form-label">
                                    {{ __('YouTube Video Link') }}
                                </label>
                                <textarea class="form-control" name="uploadVideo[youtube_url]" id="youtubeLink" rows="3"
                                    placeholder='<iframe width="560" height="315" src="https://www.youtube.com/embed/MxcgrT_Kdxw?si=V63-aJ-4tPZUEKyk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'></textarea>
                                <small class="text-muted">{{ __('Paste a valid YouTube video embed code') }}</small>
                            </div>

                            <!-- Vimeo Link Section -->
                            <div class="mb-3 d-none flex-grow-1" id="vimeoField">
                                <label for="vimeoLink" class="form-label">
                                    {{ __('Vimeo Video Link') }}
                                </label>
                                <textarea name="uploadVideo[vimeo_url]" id="vimeoLink" class="form-control" rows="3"
                                    placeholder="please enter valid vimeo video embed code"></textarea>
                                <small class="text-muted">{{ __('Paste a valid Vimeo video embed code') }}</small>
                            </div>

                            <!-- Dailymotion Link Section -->
                            <div class="mb-3 d-none flex-grow-1" id="dailymotionField">
                                <label for="dailymotionLink" class="form-label">
                                    {{ __('Dailymotion Video Link') }}
                                </label>
                                <textarea name="uploadVideo[dailymotion_url]" id="dailymotionLink" class="form-control" rows="3"
                                    placeholder="please enter valid dailymotion video embed code"></textarea>
                                <small class="text-muted">{{ __('Paste a valid Dailymotion video embed code') }}</small>
                            </div>
                        </div>
                        @error('uploadVideo.file')
                            <p class="text text-danger m-0">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!--######## SEO section ##########-->
                <div class="card mt-4 mb-3">
                    <div class="card-body">

                        <div class="d-flex gap-2 border-bottom pb-2">
                            <i class="fa-solid fa-square-poll-vertical"></i>
                            <h5>
                                {{ __('SEO Information') }}
                            </h5>
                        </div>
                        <div class="mt-3">
                            <label for="uploadType" class="form-label">
                                {{ __('Meta Title') }}
                            </label>
                            <x-input name="meta_title" type="text" placeholder="Meta Title" />
                        </div>

                        <div class="mt-3">
                            <label for="uploadType" class="form-label">
                                {{ __('Meta Description') }}
                            </label>
                            <textarea name="meta_description" type="text" placeholder="{{ __('Meta Description') }}" class="form-control">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <p class="text text-danger m-0">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-3">
                            <label for="tags" class="form-label">@lang('Meta Keywords')</label>
                            <select id="tags" name="meta_keywords[]" class="form-control selectTags" multiple
                                style="width: 100%">
                                @foreach (old('meta_keywords', []) as $keyword)
                                    <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                                @endforeach
                            </select>
                            <small>@lang('Write keywords and Press enter to add new one')</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="d-flex gap-3 justify-content-end align-items-center my-3">
            <button type="reset" class="btn btn-lg btn-outline-secondary rounded py-2">
                {{ __('Reset') }}
            </button>
            <button type="submit" class="btn btn-lg btn-primary rounded py-2 px-5">
                {{ __('Submit') }}
            </button>
        </div>

    </form>
@endsection
@push('css')
    <style>
        .box-title {
            background: #f1f5f9;
            padding: 6px 10px;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
        }

        .app-theme-dark .box-title {
            background: #2d2d2d;
            border-color: #2d2d2d;
        }

        #colorBox,
        #sizeBox {
            margin-top: 20px;
        }

        .boxName {
            font-size: 16px;
            margin-bottom: 0;
        }

        .extraPriceForm {
            padding: 4px 6px;
            min-height: 34px;
        }

        #selectedSizesTableBody tr:last-child td,
        #selectedColorsTableBody tr:last-child td {
            border: 0 !important;
        }
    </style>
@endpush
@push('scripts')
    <script>
        function toggleFields() {
            // Hide all fields
            document.getElementById('fileUploadField').classList.add('d-none');
            document.getElementById('youtubeField').classList.add('d-none');
            document.getElementById('vimeoField').classList.add('d-none');
            document.getElementById('dailymotionField').classList.add('d-none');

            // Get selected type
            const selectedType = document.getElementById('uploadType').value;

            // Show relevant field
            if (selectedType === 'file') {
                document.getElementById('fileUploadField').classList.remove('d-none');
            } else if (selectedType === 'youtube') {
                document.getElementById('youtubeField').classList.remove('d-none');
            } else if (selectedType === 'vimeo') {
                document.getElementById('vimeoField').classList.remove('d-none');
            } else if (selectedType === 'dailymotion') {
                document.getElementById('dailymotionField').classList.remove('d-none');
            }
        }
        $(document).ready(function() {
            $('.sizeSelector').select2();

            $(".selectTags").select2({
                tags: true,
                placeholder: "{{ __('Write keywords and Press enter to add new one') }}"
            });

            $('#price').on('input', function() {
                var productPrice = $(this).val() ?? 0;
                var productDiscountPrice = $('#discount_price').val() ?? 0;
                var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;
                $('.mainProductPrice').text(mainPrice);
            });

            $('#discount_price').on('input', function() {
                var productPrice = $('#price').val() ?? 0;
                var productDiscountPrice = $(this).val() ?? 0;
                var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;
                $('.mainProductPrice').text(mainPrice);
            });

            $('.sizeSelector').on('change', function() {

                var productPrice = $('#price').val() ?? 0;
                var productDiscountPrice = $('#discount_price').val() ?? 0;
                var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;

                // Get the selected options
                var selectedOptions = $(this).find(':selected');

                // Check if there are selected options
                if (selectedOptions.length > 0) {
                    $('#sizeBox').show();
                } else {
                    $('#sizeBox').hide();
                }

                selectedOptions.each(function() {
                    var sizeName = $(this).data('size');
                    var sizeId = $(this).val();

                    // Check if the row already exists
                    if (!$(`#selectedSizeRow_${sizeId}`).length) {
                        $('#selectedSizesTableBody').append(`
                        <tr id="selectedSizeRow_${sizeId}" style="display: table-row !important">
                            <td>
                                <h4 class="mb-0 boxName">${sizeName}</h4>
                                <input type="hidden" name="size[${sizeId}][name]" value="${sizeName}">
                                <input type="hidden" name="size[${sizeId}][id]" value="${sizeId}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bolder mainProductPrice">${mainPrice}</span>
                                    <span class="bg-light px-2 py-1 rounded">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                    <input type="text" class="form-control extraPriceForm" name="size[${sizeId}][price]" value="0" style="width: 140px;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\d*\.\d{0,2}|\d*)$/, '$1');">
                                </div>
                            </td>
                            <td>
                                <button class="btn circleIcon btn-outline-danger btn-sm" type="button"
                                    onclick="deleteSizeRow(${sizeId})">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                    }
                });

                $(this).find(':not(:selected)').each(function() {
                    var sizeId = $(this).val();
                    $(`#selectedSizeRow_${sizeId}`).remove();
                });

                setDefaultPrice();
            });

            // Add color wise extra price
            $('.colorSelect').on('change', function() {

                var selectedOptions = $(this).find(':selected');

                if (selectedOptions.length > 0) {
                    $('#colorBox').show();
                } else {
                    $('#colorBox').hide();
                }

                var productPrice = $('#price').val() ?? 0;
                var productDiscountPrice = $('#discount_price').val() ?? 0;
                var mainPrice = productDiscountPrice > 0 ? productDiscountPrice : productPrice;

                selectedOptions.each(function() {
                    var colorName = $(this).data('name');
                    var colorCode = $(this).data('color');
                    var colorId = $(this).val();

                    // Check if the row already exists
                    if (!$(`#selectedColorRow_${colorId}`).length) {
                        $('#selectedColorsTableBody').append(`
                            <tr id="selectedColorRow_${colorId}" style="display: table-row !important">
                                <td>
                                    <h4 class="mb-0 boxName d-flex align-items-center gap-1">
                                        <span style="background-color:${colorCode};width:20px;height:19px;display:inline-block; border-radius:5px;"></span>
                                        ${colorName}
                                    </h4>
                                    <input type="hidden" name="color[${colorId}][name]" value="${colorName}">
                                    <input type="hidden" name="color[${colorId}][id]" value="${colorId}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bolder mainProductPrice">${mainPrice}</span>
                                        <span class="bg-light px-2 py-1 rounded">
                                            <i class="fa-solid fa-plus"></i>
                                        </span>
                                        <input type="text" class="form-control extraPriceForm" name="color[${colorId}][price]" value="0" style="width: 140px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\d*\.\d{0,2}|\d*)$/, '$1');">
                                    </div>
                                </td>
                                <td>
                                    <button class="btn circleIcon btn-outline-danger btn-sm" type="button"
                                        onclick="deleteColorRow(${colorId})">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    }
                });

                // Remove the row from the table
                $(this).find(':not(:selected)').each(function() {
                    var colorId = $(this).val();
                    $(`#selectedColorRow_${colorId}`).remove();
                });

                setDefaultPrice();
            });

            $('select[name="category"]').on('change', function() {
                var categoryId = $(this).val();

                if (categoryId) {
                    $.ajax({
                        url: '/api/sub-categories?category_id=' + categoryId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            var subCategorySelected = $('select[name="sub_category[]"]');
                            subCategorySelected.empty();

                            $.each(data.data.sub_categories, function(key, value) {
                                subCategorySelected.append('<option value="' + value
                                    .id +
                                    '">' + value.name + '</option>');
                            });
                            subCategorySelected.trigger('change');
                        },
                        error: function() {
                            console.log('Error retrieving subcategories. Please try again.');
                        }
                    });
                } else {
                    $('select[name="subCategory[]"]').empty();
                }
            });

            // form submit loader
            $('form').on('submit', function() {
                var submitButton = $(this).find('button[type="submit"]');

                submitButton.prop('disabled', true);
                submitButton.removeClass('px-5');

                submitButton.html(`<div class="d-flex align-items-center gap-1">
                    <div class="spinner-border" role="status"></div>
                    <span>Submitting...</span>
                </div>`)
            });
        });

        // remove size from price section
        function deleteSizeRow(id) {
            $(`#selectedSizeRow_${id}`).remove();

            $('.sizeSelector option').each(function() {
                if ($(this).val() == id) {
                    $(this).prop('selected', false);
                }
            });
            $('.sizeSelector').trigger('change');

            setDefaultPrice();
        }

        // remove color from price section
        function deleteColorRow(id) {
            $(`#selectedColorRow_${id}`).remove();

            $('.colorSelect option').each(function() {
                if ($(this).val() == id) {
                    $(this).prop('selected', false);
                }
            });
            $('.colorSelect').trigger('change');

            setDefaultPrice();
        }

        // set default price
        function setDefaultPrice() {
            $('#selectedColorsTableBody').find('tr').each(function() {
                let index = $(this).index();
                var rowId = $(this).attr('id').split('_')[1];

                if (index == 0) {
                    var priceInput = $(`input[name="color[${rowId}][price]"]`);
                    priceInput.val(0);
                    priceInput.attr('type', 'hidden');

                    $('#defaultPriceColor').remove();
                    $(`<span id="defaultPriceColor" class="defaultPrice fst-italic">Default Price</span>`)
                        .insertAfter(priceInput);
                }
            });

            $('#selectedSizesTableBody').find('tr').each(function() {
                let index = $(this).index();
                var rowId = $(this).attr('id').split('_')[1];

                if (index == 0) {
                    var priceInput = $(`input[name="size[${rowId}][price]"]`);
                    priceInput.val(0);
                    priceInput.attr('type', 'hidden');

                    $('#defaultPriceSize').remove();
                    $(`<span id="defaultPriceSize" class="defaultPrice fst-italic">Default Price</span>`)
                        .insertAfter(priceInput);
                }
            });
        }
    </script>

    <!-- additional thumbnail script -->
    <script>
        var thumbnailCount = 1;

        const previewAdditionalFile = (event, id, removeId) => {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById(id);
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);

            // increment count
            thumbnailCount++;

            document.getElementById(removeId).style.display = 'block';

            // Create a new box dynamically
            const newThumbnailId = `additionThumbnail${thumbnailCount + 1}`;
            const newPreviewId = `preview${thumbnailCount + 1}`;
            const mainId = 'addition' + thumbnailCount + 1;

            // Add the new box
            const newThumbnailBox = document.createElement('div');
            newThumbnailBox.id = mainId;

            newThumbnailBox.innerHTML = `
            <label for="${newThumbnailId}" class="additionThumbnail">
                <img src="{{ asset('default/upload.png') }}" id="${newPreviewId}" alt="" width="100%" height="100%">
                <button onclick="removeThumbnail('${mainId}')" type="button" id="removeThumbnail${thumbnailCount + 1}" class="delete btn btn-sm btn-outline-danger circleIcon" style="display: none"><img src="{{ asset('assets/icons-admin/trash.svg') }}" loading="lazy" alt="trash" /></button>
                <input id="${newThumbnailId}" accept="image/*" type="file" name="additionThumbnail[]" class="d-none" onchange="previewAdditionalFile(event, '${newPreviewId}', 'removeThumbnail${thumbnailCount +1 }')">
            </label>
        `;

            document.getElementById('additionalElements').insertBefore(newThumbnailBox, document.getElementById(
                'additionalElements').firstChild);

            // get current file
            var inputElement = event.target;
            var newOnchangeFunction = `previewFile(event, '${id}')`;
            // Set the new onchange attribute
            inputElement.setAttribute("onchange", newOnchangeFunction);

        }

        const removeThumbnail = (thumbnailId) => {
            const thumbnailToRemove = document.getElementById(thumbnailId);
            if (thumbnailToRemove) {
                thumbnailToRemove.parentNode.removeChild(thumbnailToRemove);
            }
        }

        const generateCode = () => {
            const code = document.getElementById('barcode');
            code.value = Math.floor(Math.random() * 900000) + 100000;
        }
    </script>

    <!-- color select2 script -->
    <script>
        function formatState(state) {
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<span class="d-flex align-items-center"> <span style="background-color:' + state.element.dataset
                .color +
                ';width:20px;height:20px;display:inline-block; border-radius:5px;margin-right:5px;"></span>' + state
                .text + '</span>'
            );
            return $state;
        };

        $(document).ready(function() {
            $('.colorSelect').select2({
                templateResult: formatState
            });
        });
    </script>

    <script>
        correctULTagFromQuill = (str) => {
            if (str) {
                let re = /(<ol><li data-list="bullet">)(.*?)(<\/ol>)/;
                let strArr = str.split(re);

                while (
                    strArr.findIndex((ele) => ele === '<ol><li data-list="bullet">') !== -1
                ) {
                    let index = strArr.findIndex(
                        (ele) => ele === '<ol><li data-list="bullet">'
                    );
                    if (index) {
                        strArr[index] = '<ul><li data-list="bullet">';
                        let endTagIndex = strArr.findIndex((ele) => ele === "</ol>");
                        strArr[endTagIndex] = "</ul>";
                    }
                }
                return strArr.join("");
            }
            return str;
        };

        const quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }],
                    [{
                        'font': []
                    }],
                    ['bold', 'italic', 'underline', 'strike', 'blockquote'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'align': []
                    }],
                    [{
                        'script': 'sub'
                    }, {
                        'script': 'super'
                    }],
                    [{
                        'indent': '-1'
                    }, {
                        'indent': '+1'
                    }],
                    [{
                        'direction': 'rtl'
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    ['link', 'image', 'video', 'formula']
                ]
            }
        });

        quill.on('text-change', function(delta, oldDelta, source) {
            document.getElementById('description').value = correctULTagFromQuill(quill.root.innerHTML);
        });
    </script>

    <script>
        $(document).on('click', '#generateAi', function() {
            var name = $('#product_name').val();
            var short_description = $('#short_description').val();
            $('#description').val("Generating description... Please wait ⏳");
            quill.clipboard.dangerouslyPasteHTML("<p><em>Generating description... Please wait ⏳</em></p>");
            $.ajax({
                url: "{{ route('shop.product.generate.AI.data') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    short_description: short_description
                },
                success: function(response) {
                    $('#description').val("");
                    quill.setText("");
                    console.log(response);

                    let lastResponse = "";
                    let fullText = response;
                    let index = 0;

                    function typeStep() {
                        if (index >= fullText.length) return;
                        lastResponse += fullText[index++];
                        $('#description').val(lastResponse);
                        quill.clipboard.dangerouslyPasteHTML(lastResponse);
                        quill.setSelection(quill.getLength(), 0);
                        setTimeout(typeStep, 10); // 10ms delay per character
                    }

                    typeStep();
                },
                error: function(error) {
                    if (error.responseJSON && error.responseJSON.errors) {
                        let firstError = Object.values(error.responseJSON.errors)[0][0];
                        toastr.error(firstError);
                    } else if (error.responseJSON && error.responseJSON.message) {
                        toastr.error(error.responseJSON.message);
                    } else {
                        toastr.error("Something went wrong");
                    }
                    $('#description').val("");
                    quill.setText("");
                }
            })
        });
    </script>
@endpush

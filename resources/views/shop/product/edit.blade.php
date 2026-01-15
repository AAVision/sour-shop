@extends('layouts.app')

@section('header-title', __('Edit Product'))

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            {{ __('Edit Product') }}
        </div>
    </div>
    <form action="{{ route('shop.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card mt-3">
            <div class="card-body">

                <div class="">
                    <x-input label="Product Name" name="name" id="product_name" type="text" placeholder="Product Name"
                        required="true" value="{{ $product->name }}" />
                </div>

                <div class="mt-3">
                    <label for="">
                        {{ __('Short Description') }}
                        <span class="text-danger">*</span>
                    </label>
                    <textarea name="short_description" class="form-control" id="short_description" rows="2"
                        placeholder="Short Description">{{ old('short_description') ?? $product->short_description }}</textarea>
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
                        {!! old('description') ?? $product->description !!}
                    </div>
                    <input type="hidden" id="description" name="description"
                        value="{{ old('description') ?? $product->description }}">
                    @error('description')
                        <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror
                </div>


                <!--######## General Information ##########-->
                <div class="card mt-4">
                    <div class="card-body">

                        <div class="d-flex gap-2 border-bottom pb-2">
                            <i class="fa-solid fa-user"></i>
                            <h5>
                                {{ __('Generale Information') }}
                            </h5>
                        </div>

                        <div class="row mt-3">

                            <div class="col-md-6 col-lg-4">
                                <label class="form-label">
                                    {{ __('Select Category') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="category" class="form-control select2" style="width: 100%">
                                    <option value="" disabled>
                                        {{ __('Select Category') }}
                                    </option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ in_array($category->id, $product->categories?->pluck('id')->toArray()) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
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
                                <select name="sub_category[]" class="form-control select2" multiple style="width: 100%"
                                    data-placeholder="Select Sub Category">
                                    @foreach ($subCategories as $subCategory)
                                        <option value="{{ $subCategory->id }}"
                                            {{ in_array($subCategory->id, $product->subcategories?->pluck('id')->toArray()) ? 'selected' : '' }}>
                                            {{ $subCategory->name }}
                                        </option>
                                    @endforeach
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
                                        <option value="{{ $brand->id }}"
                                            {{ $brand->id == $product->brand_id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>

                            @if ($product->is_digital == false)
                                <div class="col-md-6 col-lg-4 mt-4">
                                    <label class="form-label">{{ __('Select Color') }}</label>
                                    <select name="colorIds[]" data-placeholder="Select Color"
                                        class="form-control colorSelect" multiple style="width: 100%">
                                        <option value="">
                                            {{ __('Select Color') }}
                                        </option>
                                        @foreach ($colors as $color)
                                            <option value="{{ $color->id }}" data-color="{{ $color->color_code }}"
                                                data-name="{{ $color->name }}"
                                                {{ in_array($color->id, $product->colors?->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $color->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-4 col-md-6 mt-4">
                                    <x-select label="Select Unit" name="unit" placeholder="Select Unit">
                                        <option value="">
                                            {{ __('Select Unit') }}
                                        </option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                {{ $unit->id == $product->unit_id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </x-select>
                                </div>

                                <div class="col-md-6 col-lg-4 mt-4">
                                    <label class="form-label">{{ __('Select Size') }}</label>
                                    <select name="sizeIds[]" data-placeholder="Select Size"
                                        class="form-control sizeSelector" multiple style="width: 100%">
                                        <option value="">
                                            {{ __('Select Size') }}
                                        </option>
                                        @foreach ($sizes as $size)
                                            <option value="{{ $size->id }}" data-size="{{ $size->name }}"
                                                {{ in_array($size->id, $product->sizes?->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $size->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

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
                                    class="form-control" value="{{ $product->code }}"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" />
                                @error('code')
                                    <p class="text text-danger m-0">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!--######## Digital product section ##########-->
                <div class="card mt-4 mb-3 {{ $product->is_digital == true ? 'd-block' : 'd-none' }}"
                    id="digitalProductSection">
                    <div class="card-body">

                        <div class="d-flex gap-2 border-bottom pb-2">
                            <i class="fa-solid fa-square-poll-vertical"></i>
                            <h5>
                                {{ __('Digital Product Attachments (Downloadable)') }}
                            </h5>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <h5>
                                            {{ __('Digital Additional Documents') }}
                                        </h5>
                                        @error('digitalAttachment')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="d-flex flex-wrap gap-3 pt-3" id="digitalAttachments">
                                        {{-- new attachment --}}

                                        <div id="attachment">
                                            <label for="digital_attachment1" class="digitalAttachmentLebel">
                                                <img src="{{ asset('default/upload-files.png') }}"
                                                    id="previewAttachment2" alt="" width="100%"
                                                    height="100%">
                                                <button onclick="removeAttachment('attachment')" id="removeAttachment1"
                                                    type="button" class="delete btn btn-sm btn-outline-danger circleIcon"
                                                    style="display: none">
                                                    <img src="{{ asset('assets/icons-admin/trash.svg') }}" loading="lazy"
                                                        alt="trash" />
                                                </button>
                                            </label>
                                            <input id="digital_attachment1" accept=".pdf,.doc,.docx,.zip,.csv"
                                                type="file" name="digitalAttachment[]" class="d-none"
                                                onchange="previewAttachmentFile(event, 'previewAttachment2', 'removeAttachment1')">
                                        </div>

                                        <!-- previous additional attachment -->
                                        @foreach ($product->attachments as $attachment)
                                            @php
                                                $source = asset('default/upload.png');
                                                if (Storage::exists($attachment->src)) {
                                                    $source = getFileImages($attachment);
                                                }
                                            @endphp

                                            <div id="attachmentShow">
                                                <label for="attachmentPreviousThumbnailShow{{ $attachment->id }}"
                                                    class="digitalAttachmentLebel">
                                                    <img src="{{ $source }}"
                                                        id="attachmentPreviewShow{{ $attachment->id }}" alt="thumbnail"
                                                        width="100%" height="100%">
                                                    <a href="{{ route('shop.product.remove.attachment', ['product' => $product->id, 'media' => $attachment->id]) }}"
                                                        class="delete btn btn-sm btn-outline-danger circleIcon">
                                                        <img src="{{ asset('assets/icons-admin/trash.svg') }}"
                                                            loading="lazy" alt="trash" />
                                                    </a>
                                                </label>
                                                <input type="hidden"
                                                    name="attachmentPreviousThumbnail[{{ $loop->index }}][id]"
                                                    value="{{ $attachment->id }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <label class="form-label">{{ __('License Keys') }}</label>

                                <!-- Container for license rows -->
                                @if ($product->licenses->count() >= 1)
                                    <div id="licenseList" class="license-list mb-2">
                                        @foreach ($product->licenses as $license)
                                            <div class="license-row">
                                                <input name="previous_license_key[{{ $license->id }}]" type="text"
                                                    placeholder="License Key" class="license-text"
                                                    value="{{ $license->product_license }}" />
                                                <div class="license-actions">
                                                    <button type="button" class="generate-btn"
                                                        title="Generate">{{ __('Generate key') }}</button>
                                                    <button type="button" class="copy-btn"
                                                        title="Copy">{{ __('Copy key') }}</button>
                                                    <a href="javascript::void(0)"
                                                        onclick="deleteAction('{{ route('shop.product.remove.license', ['product' => $product->id, 'license' => $license->id]) }}')"
                                                        class="remove-show-license-btn" title="Remove">X</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div id="licenseList" class="license-list mb-2">
                                        <div class="license-row">
                                            <input name="license_key[]" type="text" placeholder="License Key"
                                                class="license-text" />
                                            <div class="license-actions">
                                                <button type="button" class="generate-btn"
                                                    title="Generate">{{ __('Generate key') }}</button>
                                                <button type="button" class="copy-btn"
                                                    title="Copy">{{ __('Copy key') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif


                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <button type="button" id="addLicenseBtn" class="btn btn-success btn-sm">
                                        {{ __('+ Add License') }}
                                    </button>
                                    <button type="button" id="bulkGenBtn" class="btn btn-outline-primary btn-sm">
                                        {{ __('Bulk Generate (3)') }}
                                    </button>
                                </div>

                                @error('license_key')
                                    <p class="text-danger mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                <!--######## Price Information ##########-->
                <div class="card mt-4 mb-4">
                    <div class="card-body">

                        <div class="d-flex gap-2 border-bottom pb-2">
                            <i class="fa-solid fa-user"></i>
                            <h5>
                                {{ __('Price Information') }}
                            </h5>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-4 col-md-6">
                                <x-input type="text" name="buy_price" label="Buying Price" placeholder="Buying Price"
                                    required="true" onlyNumber="true" :value="$product->buy_price" />
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <x-input type="text" name="price" label="Selling Price" placeholder="Selling Price"
                                    required="true" onlyNumber="true" :value="$product->price" />
                            </div>

                            <div class="col-lg-4 col-md-6 mt-3 mt-md-0">
                                <x-input type="text" name="discount_price" label="Discount Price"
                                    placeholder="Discount Price" onlyNumber="true" :value="$product->discount_price" />
                            </div>
                            <div class="col-lg-4 col-md-6 mt-3 {{ $product->is_digital ? 'd-none' : 'd-block' }}">
                                <input type="hidden"  value="{{$product->is_digital}}" id="is_digital_product">
                                <x-input id="licenseQtyCount" type="text" name="quantity"
                                    label="Current Stock Quantity" placeholder="Current Stock Quantity" onlyNumber="true"
                                    :value="$product->quantity"/>
                            </div>

                            @if ($product->is_digital == false)
                                <div class="col-lg-4 col-md-6 mt-3">
                                    <x-input type="text" onlyNumber="true" name="min_order_quantity"
                                        label="Minimum Order Quantity" placeholder="Minimum Order Quantity"
                                        :value="$product->min_order_quantity" />
                                </div>
                            @endif
                        </div>

                        @if ($product->is_digital == false)
                            <!--######## color wise price table ##########-->
                            <div class="border rounded p-0 position-relative overflow-hidden" id="colorBox"
                                @if ($product->colors->isEmpty()) style="display: none" @endif>
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
                                    @php
                                        $productPrice =
                                            $product->discount_price > 0 ? $product->discount_price : $product->price;
                                    @endphp
                                    <tbody id="selectedColorsTableBody">
                                        @foreach ($product->colors as $color)
                                            <tr id="selectedColorRow_{{ $color->id }}"
                                                style="display: table-row !important">
                                                <td>
                                                    <h4 class="mb-0 boxName d-flex align-items-center gap-1">
                                                        <span
                                                            style="background-color:{{ $color->color_code }};width:20px;height:19px;display:inline-block; border-radius:5px;"></span>
                                                        {{ $color->name }}
                                                    </h4>
                                                    <input type="hidden" name="color[{{ $color->id }}][name]"
                                                        value="{{ $color->name }}">
                                                    <input type="hidden" name="color[{{ $color->id }}][id]"
                                                        value="{{ $color->id }}">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="fw-bolder mainProductPrice">
                                                            {{ $productPrice }}
                                                        </span>
                                                        <span class="bg-light px-2 py-1 rounded">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </span>
                                                        <input type="text" class="form-control extraPriceForm"
                                                            name="color[{{ $color->id }}][price]"
                                                            value="{{ $color->pivot->price }}" style="width: 140px"
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn circleIcon btn-outline-danger btn-sm"
                                                        type="button" onclick="deleteColorRow({{ $color->id }})">
                                                        <i class="fa-solid fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!--######## Size wise price table ##########-->
                            <div class="border rounded p-0 position-relative overflow-hidden" id="sizeBox"
                                @if ($product->sizes->isEmpty()) style="display: none" @endif>
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
                                    <tbody id="selectedSizesTableBody">
                                        @foreach ($product->sizes as $size)
                                            <tr id="selectedSizeRow_{{ $size->id }}">
                                                <td>
                                                    <h4 class="mb-0 boxName">{{ $size->name }}</h4>
                                                    <input type="hidden" name="size[{{ $size->id }}][name]"
                                                        value="{{ $size->name }}">
                                                    <input type="hidden" name="size[{{ $size->id }}][id]"
                                                        value="{{ $size->id }}">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="fw-bolder mainProductPrice">
                                                            {{ $productPrice }}
                                                        </span>
                                                        <span class="bg-light px-2 py-1 rounded">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </span>
                                                        <input type="text" class="form-control extraPriceForm"
                                                            name="size[{{ $size->id }}][price]"
                                                            value="{{ $size->pivot->price }}" style="width: 140px"
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn circleIcon btn-outline-danger btn-sm"
                                                        type="button" onclick="deleteSizeRow({{ $size->id }})">
                                                        <i class="fa-solid fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!--######## Thumbnail Information ##########-->
                <div class="row mb-3">
                    <div class="col-md-5 col-xl-3">
                        <div class="card card-body h-100">
                            <div class="mb-2">
                                <h5>
                                    {{ __('Thumbnail') }}
                                    <span class="text-primary">(Ratio 1:1 (500 x 500 px))</span> *
                                </h5>
                                @error('thumbnail')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <label for="thumbnail" class="additionThumbnail">
                                <img src="{{ $product->thumbnail ?? asset('default/upload.png') }}" id="preview"
                                    alt="" width="100%">
                            </label>
                            <input id="thumbnail" accept="image/*" type="file" name="thumbnail" class="d-none"
                                onchange="previewFile(event, 'preview')">
                        </div>
                    </div>

                    <div class="col-md-7 col-xl-9 mt-3 mt-md-0">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="mb-2">
                                    <h5>
                                        {{ __('Additional Thumbnail') }}
                                        <span class="text-primary">(Ratio 1:1 (500 x 500 px))</span> *
                                    </h5>
                                    @error('additionThumbnail')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="d-flex flex-wrap gap-3" id="additionalElements">

                                    <!-- previous additional thumbnail -->
                                    @foreach ($product->medias as $media)
                                        @php
                                            $source = asset('default/upload.png');
                                            if (Storage::exists($media->src)) {
                                                $source = Storage::url($media->src);
                                            }
                                        @endphp

                                        <div id="additionShow">
                                            <label for="previousThumbnailShow{{ $media->id }}"
                                                class="additionThumbnail">
                                                <img src="{{ $source }}" id="previewShow{{ $media->id }}"
                                                    alt="thumbnail" width="100%" height="100%">
                                                <a href="{{ route('shop.product.remove.thumbnail', ['product' => $product->id, 'media' => $media->id]) }}"
                                                    class="delete btn btn-sm btn-outline-danger circleIcon">
                                                    <img src="{{ asset('assets/icons-admin/trash.svg') }}"
                                                        loading="lazy" alt="trash" />
                                                </a>
                                            </label>
                                            <input type="hidden" name="previousThumbnail[{{ $loop->index }}][id]"
                                                value="{{ $media->id }}">
                                            <input id="previousThumbnailShow{{ $media->id }}" accept="image/*"
                                                type="file" name="previousThumbnail[{{ $loop->index }}][file]"
                                                class="d-none"
                                                onchange="previewFile(event, 'previewShow{{ $media->id }}')" />
                                        </div>
                                    @endforeach

                                    <!-- New additional thumbnail -->
                                    <div id="addition">
                                        <label for="additionThumbnail1" class="additionThumbnail">
                                            <img src="{{ asset('default/upload.png') }}" id="preview2" alt=""
                                                width="100%" height="100%">
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
                                    <option value="file" {{ $product->video?->type == 'file' ? 'selected' : '' }}>
                                        {{ __('Upload Video File') }}
                                    </option>
                                    <option value="youtube" {{ $product->video?->type == 'youtube' ? 'selected' : '' }}>
                                        {{ __('YouTube Link') }}
                                    </option>
                                    <option value="vimeo" {{ $product->video?->type == 'vimeo' ? 'selected' : '' }}>
                                        {{ __('Vimeo Link') }}
                                    </option>
                                    <option value="dailymotion"
                                        {{ $product->video?->type == 'dailymotion' ? 'selected' : '' }}>
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
                                    placeholder='<iframe width="560" height="315" src="https://www.youtube.com/embed/MxcgrT_Kdxw?si=V63-aJ-4tPZUEKyk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'>{{ $product->video?->type == 'youtube' ? $product->video->url : '' }}</textarea>
                                <small class="text-muted">{{ __('Paste a valid YouTube video embed code') }}</small>
                            </div>

                            <!-- Vimeo Link Section -->
                            <div class="mb-3 d-none flex-grow-1" id="vimeoField">
                                <label for="vimeoLink" class="form-label">
                                    {{ __('Vimeo Video Link') }}
                                </label>
                                <textarea name="uploadVideo[vimeo_url]" id="vimeoLink" class="form-control" rows="3">{{ $product->video?->type == 'vimeo' ? $product->video->url : '' }}</textarea>
                                <small class="text-muted">{{ __('Paste a valid Vimeo video embed code') }}</small>
                            </div>

                            <!-- Dailymotion Link Section -->
                            <div class="mb-3 d-none flex-grow-1" id="dailymotionField">
                                <label for="dailymotionLink" class="form-label">
                                    {{ __('Dailymotion Video Link') }}
                                </label>
                                <textarea name="uploadVideo[dailymotion_url]" id="dailymotionLink" class="form-control" rows="3">{{ $product->video?->type == 'dailymotion' ? $product->video->url : '' }}</textarea>
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
                            <x-input name="meta_title" type="text" placeholder="Meta Title"
                                value="{{ old('meta_title', $product->meta_title) }}" />
                        </div>

                        <div class="mt-3">
                            <label for="uploadType" class="form-label">
                                {{ __('Meta Description') }}
                            </label>
                            <textarea name="meta_description" type="text" placeholder="{{ __('Meta Description') }}" class="form-control">{{ old('meta_description', $product->meta_description) }}</textarea>
                            @error('meta_description')
                                <p class="text text-danger m-0">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-3">
                            <label for="tags" class="form-label">@lang('Meta Keywords')</label>
                            <select id="tags" name="meta_keywords[]" class="form-control selectTags" multiple
                                style="width: 100%">
                                @foreach (old('meta_keywords', $metaKeywords) as $keyword)
                                    <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                                @endforeach
                            </select>
                            <small>@lang('Write keywords and Press enter to add new one')</small>
                            @error('meta_keywords')
                                <p class="text text-danger m-0">{{ $message }}</p>
                            @enderror
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
                {{ __('Update') }}
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

        .upload-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .upload-label-attachment {
            cursor: pointer;
            width: 200px;
            padding: 4px 16px;
            border: 2px dashed rgba(255, 0, 0, 0.7);
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            background: #f9fafb;
        }

        .file-preview {
            position: relative;
            width: 120px;
            height: 120px;
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            overflow: hidden;
            background: #f9fafb;
            transition: 0.3s;
        }

        .file-preview img,
        .file-preview .file-icon {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-preview:hover {
            border-color: rgba(255, 0, 0, 0.7);
        }

        .license-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .license-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .license-text {
            flex: 1;
            height: 32px !important;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 0 10px;
            font-size: 12px !important;
        }

        .license-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        /* Common style for all buttons */
        .generate-btn,
        .copy-btn,
        .remove-show-license-btn,
        .remove-btn,
        .remove-btn-license {
            height: 32px;
            padding: 0 12px;
            font-size: 12px;
            border-radius: 6px;
            border: 1px solid #ced4da;
            background-color: #f8f9fa;
            color: #333;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Hover effects */
        .generate-btn:hover {
            background-color: #198754;
            border-color: #198754;
            color: #fff;
        }

        .copy-btn:hover {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        .remove-show-license-btn,
        .remove-btn,
        .remove-btn-license {
            background-color: #f8f9fa;
            color: #dc3545;
            text-decoration: none;
            font-weight: bold;
            border-color: #dc3545;
            width: 32px;
            text-align: center;
        }

        .remove-show-license-btn:hover {
            background-color: #dc3545;
            color: #fff;
        }

        .remove-btn:hover {
            background-color: #dc3545;
            color: #fff;
        }

        .remove-btn-license:hover {
            background-color: #dc3545;
            color: #fff;
        }

        @media (max-width: 576px) {

            .generate-btn,
            .copy-btn,
            .remove-show-license-btn,
            .remove-btn,
            .remove-btn-license {
                padding: 0 8px;
                font-size: 12px;
                border-radius: 4px;
            }
        }

        @media (max-width: 430px) {
            .license-text {
                width: 100%;
            }
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
            toggleFields();

            var productName = $('input[name="name"]').val();

            var replace = productName.replace(/&amp;/g, '&').replace(/&#039;/g, "'").replace(/&quot;/g, '"')
                .replace(/&lt;/g, '<').replace(/&gt;/g, '>');
            $('input[name="name"]').val(replace);

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
                                        <input type="text" class="form-control" name="size[${sizeId}][price]" value="0" style="width: 140px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\d*\.\d{0,2}|\d*)$/, '$1');" />
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

            // Get the category ID
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
                    <span>Updating...</span>
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

        // initially set default price
        setDefaultPrice();
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

            document.getElementById('additionalElements').appendChild(newThumbnailBox);

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

    {{-- digital product attachments --}}

    <script>
        var thumbnailCount = 1;

        const fileIcons = {
            'pdf': "https://cdn-icons-png.flaticon.com/512/337/337946.png",
            'doc': "https://cdn-icons-png.flaticon.com/512/337/337932.png",
            'docx': "https://cdn-icons-png.flaticon.com/512/337/337932.png",
            'zip': "https://cdn-icons-png.flaticon.com/512/337/337960.png",
            'csv': "https://cdn-icons-png.flaticon.com/512/337/337936.png",
            'xls': "https://cdn-icons-png.flaticon.com/512/337/337936.png",
            'xlsx': "https://cdn-icons-png.flaticon.com/512/337/337936.png",
            'mp3': "https://cdn-icons-png.flaticon.com/512/337/337938.png",
            'wav': "https://cdn-icons-png.flaticon.com/512/337/337938.png",
            'mp4': "https://cdn-icons-png.flaticon.com/512/337/337959.png",
            'avi': "https://cdn-icons-png.flaticon.com/512/337/337959.png",
            'default': "https://cdn-icons-png.flaticon.com/512/337/337957.png"
        };

        const previewAttachmentFile = (event, id, removeId) => {
            const file = event.target.files[0];
            if (!file) return;

            const output = document.getElementById(id);
            const fileName = file.name.toLowerCase();
            const ext = fileName.split('.').pop();

            // check if file is image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function() {
                    output.src = reader.result;
                };
                reader.readAsDataURL(file);
            } else {
                // non-image: show icon
                output.src = fileIcons[ext] || fileIcons['default'];
            }

            // increment count
            thumbnailCount++;

            document.getElementById(removeId).style.display = 'block';

            // Create a new box dynamically
            const newThumbnailId = `digital_attachment1${thumbnailCount + 1}`;
            const newPreviewId = `previewAttachment${thumbnailCount + 1}`;
            const mainId = 'attachment' + thumbnailCount + 1;

            // Add the new box
            const newThumbnailBox = document.createElement('div');
            newThumbnailBox.id = mainId;

            newThumbnailBox.innerHTML = `
            <label for="${newThumbnailId}" class="digitalAttachmentLebel">
                <img src="{{ asset('default/upload-files.png') }}" id="${newPreviewId}" alt="" width="100%" height="100%">
                <button onclick="removeAttachment('${mainId}')" type="button" id="removeAttachment${thumbnailCount + 1}" class="delete btn btn-sm btn-outline-danger circleIcon" style="display: none"><img src="{{ asset('assets/icons-admin/trash.svg') }}" loading="lazy" alt="trash" /></button>
                <input id="${newThumbnailId}" accept=".pdf,.doc,.docx,.zip,.csv" type="file" name="digitalAttachment[]" class="d-none" onchange="previewAttachmentFile(event, '${newPreviewId}', 'removeAttachment${thumbnailCount +1 }')">
            </label>
        `;

            document.getElementById('digitalAttachments').insertBefore(newThumbnailBox, document.getElementById(
                'digitalAttachments').firstChild);

            // get current file
            var inputElement = event.target;
            var newOnchangeFunction = `previewFileAttachment(event, '${id}')`;
            // Set the new onchange attribute
            inputElement.setAttribute("onchange", newOnchangeFunction);

        }

        const removeAttachment = (thumbnailId) => {
            const thumbnailToRemove = document.getElementById(thumbnailId);
            if (thumbnailToRemove) {
                thumbnailToRemove.parentNode.removeChild(thumbnailToRemove);
            }
        }
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const licenseLists = document.querySelectorAll('#licenseList');
            const licenseList = licenseLists[licenseLists.length - 1];
            const addBtn = document.getElementById('addLicenseBtn');
            const bulkGenBtn = document.getElementById('bulkGenBtn');
            const counter = document.getElementById('licenseQtyCount');
            const isDigital= document.getElementById('is_digital_product');


            function updateLicenseCount() {
                const count = licenseList.querySelectorAll('.license-row').length;
                if(isDigital.value == 0) {
                    return counter.value;
                }else{
                    if (counter) counter.value = count;
                }
            }

            // Helper: create a new license row element
            function createLicenseRow(initialValue = '', isRemovable = true) {
                const wrapper = document.createElement('div');
                wrapper.className = 'license-row';

                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'license_key[]';
                input.className = 'license-text';
                input.placeholder = 'License Key';
                input.value = initialValue;


                const actions = document.createElement('div');
                actions.className = 'license-actions';

                const genBtn = document.createElement('button');
                genBtn.type = 'button';
                genBtn.className = 'generate-btn';
                genBtn.title = 'Generate';
                genBtn.textContent = "{{ __('Generate key') }}";

                const copyBtn = document.createElement('button');
                copyBtn.type = 'button';
                copyBtn.className = 'copy-btn';
                copyBtn.title = 'Copy to clipboard';
                copyBtn.textContent = "{{ __('Copy key') }}";

                let removeBtn = null;
                if (isRemovable) {
                    removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-btn-license';
                    removeBtn.title = 'Remove';
                    removeBtn.textContent = 'X';
                    actions.appendChild(removeBtn);
                }

                actions.appendChild(genBtn);
                actions.appendChild(copyBtn);
                actions.appendChild(removeBtn);

                wrapper.appendChild(input);
                wrapper.appendChild(actions);

                // attach behaviors
                genBtn.addEventListener('click', () => input.value = generateLicenseKey());
                copyBtn.addEventListener('click', () => copyToClipboard(input.value, copyBtn));

                if (removeBtn) {
                    removeBtn.addEventListener('click', () => {
                        if (licenseList.children.length > 1) {
                            wrapper.remove();
                        } else {
                            const input = wrapper.querySelector('.license-text');
                            if (input) input.value = '', removeBtn.remove();
                        }
                    });
                }

                return wrapper;
            }

            // Add initial listeners to existing elements (in case of initial markup)
            (function wireExisting() {
                document.querySelectorAll('#licenseList .license-row').forEach(row => {
                    const gen = row.querySelector('.generate-btn');
                    const copy = row.querySelector('.copy-btn');
                    const remove = row.querySelector('.remove-btn-license');
                    const ta = row.querySelector('.license-text');

                    if (gen) gen.addEventListener('click', () => ta.value = generateLicenseKey());
                    if (copy) copy.addEventListener('click', () => copyToClipboard(ta.value, copy));
                    if (remove) remove.addEventListener('click', () => {
                        if (licenseList.children.length > 1) row.remove();
                        else ta.value = '';
                        updateLicenseCount();
                    });
                });
            })();

            addBtn.addEventListener('click', () => {
                licenseList.appendChild(createLicenseRow());
                updateLicenseCount();
                // optional: focus last textarea
                const last = licenseList.lastElementChild.querySelector('.license-text');
                if (last) last.focus();
            });

            bulkGenBtn.addEventListener('click', () => {
                const count = 3; // default bulk generate count

                // remove empty rows before generating
                document.querySelectorAll('#licenseList .license-row').forEach(row => {
                    const input = row.querySelector('.license-text');
                    if (input && input.value.trim() === '') {
                        row.remove();
                    }
                });

                // generate new rows
                for (let i = 0; i < count; i++) {
                    licenseList.appendChild(createLicenseRow(generateLicenseKey()));
                }

                updateLicenseCount();
            });

            // Clipboard helper
            function copyToClipboard(text, btn) {
                if (!text) {
                    flashButton(btn, 'Nothing');
                    return;
                }
                navigator.clipboard?.writeText(text).then(() => {
                    flashButton(btn, 'Copied');
                }, () => {
                    flashButton(btn, 'Fail');
                });
            }

            // small UI flash on button
            function flashButton(btn, msg) {
                const original = btn.innerHTML;
                btn.innerHTML = msg;
                setTimeout(() => btn.innerHTML = original, 900);
            }

            // License generator (pattern: XXXX-XXXX-XXXX-XXXX)
            function generateLicenseKey() {
                const blockCount = 4;
                const blockLen = 4;
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no confusing I,1,O,0
                let parts = [];
                for (let b = 0; b < blockCount; b++) {
                    let part = '';
                    for (let i = 0; i < blockLen; i++) {
                        part += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    parts.push(part);
                }
                return parts.join('-');
            }

            // when remove button is clicked
            licenseList.addEventListener('click', e => {
                if (e.target.classList.contains('remove-btn-license')) {
                    setTimeout(updateLicenseCount, 100); // small delay to allow DOM update
                }
            });

            // initial update
            updateLicenseCount();
        });
    </script>
@endpush

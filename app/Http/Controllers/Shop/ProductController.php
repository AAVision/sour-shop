<?php

namespace App\Http\Controllers\Shop;

use App\Services\Chat;
use App\Models\User;
use App\Models\Media;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Storage;
use App\Events\AdminProductRequestEvent;
use App\Repositories\FlashSaleRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\NotificationRepository;
use App\Repositories\ProductLicenseRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Display the product list.
     */
    public function index(Request $request)
    {
        // get category, brand, color and search from request
        $category = $request->category;
        $brand = $request->brand;
        $color = $request->color;
        $search = $request->search;

        $rootShop = generaleSetting('rootShop');
        $shop = generaleSetting('shop');

        // filter products based on category, brand, color and search
        $products = $shop?->products()->when($brand, function ($query) use ($brand) {
            return $query->where('brand_id', $brand);
        })->when($category, function ($query) use ($category) {
            return $query->whereHas('categories', function ($query) use ($category) {
                return $query->where('category_id', $category);
            });
        })->when($color, function ($query) use ($color) {
            return $query->whereHas('colors', function ($query) use ($color) {
                return $query->where('color_id', $color);
            });
        })->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->latest()->paginate(20)->withQueryString();

        // get brands, colors and categories
        $brands = $rootShop?->brands()->get();
        $colors = $rootShop?->colors()->get();
        $categories = $rootShop?->categories()->get();

        $flashSale = FlashSaleRepository::getIncoming();

        return view('shop.product.index', compact('products', 'brands', 'colors', 'categories', 'flashSale'));
    }

    /**
     * Display the product details.
     */
    public function show(Product $product)
    {
        return view('shop.product.show', compact('product'));
    }

    /**
     * crete new product.
     */
    public function create()
    {
        $shop = generaleSetting('rootShop');

        // get brands, colors and categories
        $brands = $shop?->brands()->isActive()->get();
        $colors = $shop?->colors()->isActive()->get();
        $categories = $shop?->categories()->active()->get();
        $units = $shop?->units()->isActive()->get();
        $sizes = $shop?->sizes()->isActive()->get();

        return view('shop.product.create', compact('brands', 'colors', 'categories', 'units', 'sizes'));
    }

    public function digitalProductCreate()
    {
        $shop = generaleSetting('rootShop');

        // get brands, colors and categories
        $brands = $shop?->brands()->isActive()->get();
        $colors = $shop?->colors()->isActive()->get();
        $categories = $shop?->categories()->active()->get();
        $units = $shop?->units()->isActive()->get();
        $sizes = $shop?->sizes()->isActive()->get();

        return view('shop.product.digitalProductCreate', compact('brands', 'colors', 'categories', 'units', 'sizes'));
    }

    /**
     * store new product.
     */
    public function store(ProductRequest $request)
    {
        $shop = generaleSetting('shop');

        $skuCode = $shop?->products()->where('code', $request->code)->exists();

        if ($skuCode) {
            return back()->withInput()->withErrors(['code' => __('Product code already exists!')])->with('error', __('Product code already exists!'));
        }

        ProductRepository::storeByRequest($request);

        /** @var User $user */
        $user = auth()->user();
        $isRootUser = $user?->hasRole('root');

        // admin notification message
        if (! $isRootUser && generaleSetting('setting')->shop_type != 'single') {
            $message = 'New product Created Request';
            try {
                AdminProductRequestEvent::dispatch($message);
            } catch (\Throwable $th) {
            }

            $data = (object) [
                'title' => $message,
                'content' => 'New product Created Request from ' . $shop->name,
                'url' => '/admin/products?status=0',
                'icon' => 'bi-shop',
                'type' => 'success',
            ];
            // store notification
            NotificationRepository::storeByRequest($data);
        }

        return to_route('shop.product.index')->withSuccess(__('Product created successfully!'));
    }

    /**
     * Display the product edit form.
     */
    public function edit(Product $product)
    {
        $shop = generaleSetting('shop');
        $rootShop = generaleSetting('rootShop');

        // get brands, colors, units, sizes and categories
        $brands = $rootShop?->brands()->isActive()->get();
        $colors = $rootShop?->colors()->isActive()->get();
        $categories = $rootShop?->categories()->active()->get();
        $units = $rootShop?->units()->isActive()->get();
        $sizes = $rootShop?->sizes()->isActive()->get();

        $categoryId = $product->categories()?->latest('id')->first()?->id;

        $subCategories = SubCategory::whereHas('categories', function ($query) use ($categoryId) {
            return $query->where('category_id', $categoryId);
        })->isActive()->get();

        $metaKeywords = explode(',', $product->meta_keywords) ?: [];

        return view('shop.product.edit', compact('product', 'brands', 'colors', 'categories', 'units', 'sizes', 'subCategories', 'metaKeywords'));
    }

    /**
     * Update the product.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $shop = generaleSetting('shop');

        $skuCode = $shop?->products()->where('code', $request->code)->where('id', '!=', $product->id)->exists();

        if ($skuCode) {
            return back()->withInput()->withErrors(['code' => __('Product code already exists!')])->with('error', __('Product code already exists!'));
        }

        ProductRepository::updateByRequest($request, $product);

        /** @var User $user */
        $user = auth()->user();
        $isRootUser = $user?->hasRole('root');

        // admin notification message
        if (! $isRootUser && generaleSetting('setting')->shop_type != 'single') {
            $message = 'Product Updated Request';
            try {
                AdminProductRequestEvent::dispatch($message);
            } catch (\Throwable $th) {
            }

            $data = (object) [
                'title' => $message,
                'content' => 'Product Updated Request from ' . $shop->name,
                'url' => '/admin/products?status=1',
                'icon' => 'bi-shop',
                'type' => 'success',
            ];
            // store notification
            NotificationRepository::storeByRequest($data);
        }

        return to_route('shop.product.index')->withSuccess(__('Product updated successfully!'));
    }

    /**
     * delete thumbnail
     */
    public function thumbnailDestroy(Product $product, Media $media)
    {
        $product->medias()->detach($media->id);
        if (Storage::exists($media->src)) {
            Storage::delete($media->src);
        }

        $media->delete();

        return back()->withSuccess(__('Thumbnail deleted successfully!'));
    }

    public function attachmentDestroy(Product $product, Media $media)
    {
        $product->attachments()->detach($media->id);
        if (Storage::exists($media->src)) {
            Storage::delete($media->src);
        }

        $media->delete();

        return back()->withSuccess(__('Attachment deleted successfully!'));
    }

    public function licenseDestroy(Product $product , $license){

        $productLicense = ProductLicenseRepository::query()->where('id', $license)->where('product_id', $product->id)->first();
        $productLicense->delete();

        return back()->withSuccess(__('License deleted successfully!'));
    }

    /**
     * status toggle a product
     */
    public function statusToggle(Product $product)
    {
        if (! $product->is_approve) {
            return back()->withError(__('Sorry! Your Product is not approved yet!'));
        }

        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return back()->withSuccess(__('Status updated successfully'));
    }

    /**
     * generate barcode
     */
    public function generateBarcode(Product $product)
    {
        if (! $product->code) {
            return back()->withError(__('Sorry! Your Product code is not generated yet!'));
        }

        $quantities = request('qty', 4);

        return view('shop.product.barcode', compact('product', 'quantities'));
    }

    /**
     * generate ai data
     */
    public function generateAIData(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'short_description' => 'nullable|string',
            ]);

            $chat = new Chat();
            $chat->systemMessage($request->name);

            $question = str_replace(
                ['{product_name}', '{short_description}'],
                [$request->name, $request->short_description],
                generaleSetting()->product_description
            );

            $question .= "Format the description with proper HTML tags (<p>, <h2>, <ul>, <li>) for CKEditor. Do not include extra phrases like 'The product is','```html', 'Sure', or 'Here is'. Just output the final formatted description.";

            $response = $chat->send($question);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

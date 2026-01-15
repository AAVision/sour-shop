<?php

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\DeliveryCharge;
use Illuminate\Support\Number;
use App\Models\CartAccessToken;
use App\Models\GeneraleSetting;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

if (! function_exists('getDistance')) {
    /**
     * Calculate the distance between two geographical coordinates.
     *
     * @param  array  $firstLatLng  The first [latitude, longitude] coordinates
     * @param  array  $secondLatLng  The second [latitude, longitude] coordinates
     * @return float The distance between the two coordinates in kilometers
     */
    function getDistance(array $firstLatLng, array $secondLatLng): float
    {
        if (empty($firstLatLng) || empty($secondLatLng)) {
            return 0;
        }

        $theta = ($firstLatLng[1] - $secondLatLng[1]);
        $dist  = sin(deg2rad($firstLatLng[0])) *
        sin(deg2rad($secondLatLng[0])) +
        cos(deg2rad($firstLatLng[0])) *
        cos(deg2rad($secondLatLng[0])) *
        cos(deg2rad($theta));

        $dist  = acos($dist);
        $dist  = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return $miles * 1.609344;
    }
}

if (! function_exists('generaleSetting')) {
    /**
     * Get the generale setting Or shop Or default currency.
     *
     * @param  string|null  $type  = 'setting|shop|rootShop|defaultCurrency'
     *
     * @type setting|shop|rootShop|defaultCurrency
     *
     * @default GeneraleSetting
     *
     * @return GeneraleSetting|Shop|Currency
     *
     * @throws \Exception
     */
    function generaleSetting($type = null, $authUser = null)
    {
        // Cache general setting data for  30 days
        $generaleSetting = Cache::remember('generale_setting', 60 * 24 * 30, function () {
            return GeneraleSetting::first();
        });

        if ($type == 'setting' || $type == null) {
            return $generaleSetting;
        }

        if ($type == 'rootShop') {
            return Cache::remember('admin_shop', 60 * 24 * 7, function () {
                return User::role('root')->whereHas('shop')->first()?->shop;
            });
        }

        if ($type == 'shop') {
            if ($generaleSetting?->shop_type == 'single') {
                $shop = User::role('root')->whereHas('shop')->first()?->shop;
            } else {
                /** @var User */
                $user = $authUser ?? auth()->user();
                $shop = $user?->shop ?? $user?->myShop;
            }

            if (! $shop) {
                $shop = User::role('root')->whereHas('shop')->first()?->shop;
            }

            return $shop;
        }

        if ($type == 'defaultCurrency') {
            $defaultCurrency = Cache::remember('default_currency', 60 * 24 * 30, function () {
                return Currency::where('is_default', 1)->first();
            });

            return $defaultCurrency;
        }

        return $generaleSetting;
    }
}

if (! function_exists('showCurrency')) {

    /**
     * Show the currency in the given amount.
     *
     * @param  float  $amount
     */
    function showCurrency($amount = null): string
    {
        $generaleSetting = generaleSetting('setting');

        $currency = $generaleSetting?->currency ?? '$';

        $amount = ($amount == 0 || $amount == null) ? 0 : $amount;

        if ($generaleSetting?->currency_position == 'suffix') {
            return $amount . $currency;
        }

        return $currency . $amount;
    }
}

if (! function_exists('getDeliveryCharge')) {

    /**
     * get the delivery charge.
     *
     * @param  int  $orderQuantity
     */
    function getDeliveryCharge($orderQuantity): float
    {
        $deliveryCharge = DeliveryCharge::where('min_qty', '<=', $orderQuantity)
            ->where('max_qty', '>=', $orderQuantity)
            ->first();

        return $deliveryCharge?->charge ?? 0;
    }
}

if (! function_exists('permissionName')) {

    /**
     * get the permission name for the customer readable.
     *
     * @param  string  $permission
     */
    function permissionName($permission): string
    {
        $customerReadAbleNames = config('acl.customerReadableNames');

        if (isset($customerReadAbleNames[$permission])) {
            return trans($customerReadAbleNames[$permission]);
        }

        return trans($permission);
    }
}

if (! function_exists('diffInLargestUnit')) {
    function diffInLargestUnit(Carbon $from, ?Carbon $to = null): string
    {
        $to = $to ?? now();

        $diff = $from->diff($to);

        if ($diff->y >= 1) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
        }

        if ($diff->m >= 1) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
        }

        if ($diff->d >= 1) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
        }

        if ($diff->h >= 1) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        }

        if ($diff->i >= 1) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }

        return $diff->s . ' second' . ($diff->s !== 1 ? 's' : '');
    }
}

if (! function_exists('daysToLargestUnit')) {
    function daysToLargestUnit(int $days): string
    {
        if ($days >= 365) {
            $years = floor($days / 365);
            return $years . ' year' . ($years > 1 ? 's' : '');
        }

        if ($days >= 30) {
            $months = floor($days / 30);
            return $months . ' month' . ($months > 1 ? 's' : '');
        }

        if ($days >= 7) {
            $weeks = floor($days / 7);
            return $weeks . ' week' . ($weeks > 1 ? 's' : '');
        }

        return $days . ' day' . ($days > 1 ? 's' : '');
    }
}

if (! function_exists('getFileImages')) {

    function getFileImages($attachment)
    {
        $source = Storage::url($attachment->src);

        $fileIcons = [
            'pdf' => asset('assets/attachments/pdf.svg'),
            'doc' => asset('assets/attachments/doc.svg'),
            'docx' => asset('assets/attachments/doc.svg'),
            'zip' => asset('assets/attachments/zip.svg'),
            'csv' => asset('assets/attachments/csv.svg'),
            'default' => asset('assets/attachments/random.svg'),
        ];

        $extension = strtolower(pathinfo($attachment->src, PATHINFO_EXTENSION));

        if (array_key_exists($extension, $fileIcons)) {
            return $fileIcons[$extension];
        }
        return $source;

    }
}

if (! function_exists('generateLicenseKey')) {

    function generateLicenseKey($blockCount = 4, $blockLen = 4)
        {
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // excludes I, 1, O, 0
            $licenseParts = [];

            for ($b = 0; $b < $blockCount; $b++) {
                $part = '';
                for ($i = 0; $i < $blockLen; $i++) {
                    $part .= $chars[random_int(0, strlen($chars) - 1)];
                }
                $licenseParts[] = $part;
            }

            return implode('-', $licenseParts);
        }
}



if (!function_exists('module_exists')) {
        /**
         * Check if a given module exists either by folder or in config/modules.php
         *
         * @param  string  $moduleName
         * @return bool
         */
        function module_exists(string $moduleName): bool
        {
            $moduleName = ucfirst($moduleName);
            $modulePath = base_path("Modules/{$moduleName}");
            $configPath = config_path('modules.php');

            if (class_exists(Module::class) && Module::has($moduleName) && Module::isEnabled($moduleName) && File::isDirectory($modulePath) && File::exists($configPath)) {
                return true;
            }
            // Otherwise, module not found
            return false;
        }
    }





if (! function_exists('cartAccessToken')) {
    function cartAccessToken(Request $request): array
    {
        $user = auth()->user();

        $customerId = $user->customer->id ?? null;
        $accessTokenValue = $request->header('X-Guest-Token') ?? $request->access_token;
        $guestToken = CartAccessToken::where('access_token', $accessTokenValue)->first();
        $isAuth =false;

        if ($request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());
            if ($accessToken) {
                $user = $accessToken->tokenable;
                $customerId = $user->customer->id ?? null;
                $isAuth =true;
            }
        }

        if (!$isAuth && $guestToken) {
            $customerId=$guestToken->customer_id ?? null;
        }

        return [
            'customer_id' => $customerId ?? null,
            'access_token' => $guestToken->access_token ?? null,
            'is_auth' =>$isAuth
        ];
    }
}

if (! function_exists('userCart')) {
    function userCart(Request $request)
    {
        $tokens = cartAccessToken($request);
        $query = Cart::query();
        if ($tokens['access_token'] && $tokens['customer_id']) {
            // if both exist prefer guest cart via access_token
            $query->where('access_token', $tokens['access_token']);
        } elseif ($tokens['customer_id']) {
            $query->where('customer_id', $tokens['customer_id']);
        } elseif ($tokens['access_token']) {
            $query->where('access_token', $tokens['access_token']);
        } else {
            $query->whereRaw('1 = 0');
        }
        return $query;
    }
}


if (! function_exists('formatAmount')) {
    function formatAmount($amount)
    {
        return Number::abbreviate($amount ?? 0,2);
    }
}

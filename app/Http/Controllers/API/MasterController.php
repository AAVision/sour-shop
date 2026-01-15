<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FooterResource;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\MenuResource;
use App\Http\Resources\PaymentGatewayResource;
use App\Http\Resources\SocialLinkResource;
use App\Models\Currency;
use App\Models\Footer;
use App\Models\Menu;
use App\Models\PaymentGateway;
use App\Models\SocialAuth;
use App\Models\SocialLink;
use App\Models\VerifyManage;
use App\Repositories\LanguageRepository;
use App\Repositories\ThemeColorRepository;
use Illuminate\Support\Facades\Cache;

class MasterController extends Controller
{
    /**
     * Get master data for all.
     *
     * @return mixed
     */
    public function index()
    {
        $generaleSetting = generaleSetting('setting');

        $paymentGateways = Cache::rememberForever('payment_gateway', function () {
            return PaymentGateway::where('is_active', true)->get();
        });

        $verifyManage = Cache::rememberForever('verify_manage', function () {
            return VerifyManage::first();
        });

        $shopType = $generaleSetting?->shop_type ?? 'multi';

        $socialLinks = SocialLink::active()->get();

        $themeColor = ThemeColorRepository::query()->where('is_default', true)->first();

        $themeColors = (object) [
            'primary' => $themeColor ? $themeColor['primary'] : '#EE456B',
            'primary50' => $themeColor ? $themeColor['variant_50'] : '#fdecf0',
            'primary100' => $themeColor ? $themeColor['variant_100'] : '#fcdae1',
            'primary200' => $themeColor ? $themeColor['variant_200'] : '#f8b5c4',
            'primary300' => $themeColor ? $themeColor['variant_300'] : '#f58fa6',
            'primary400' => $themeColor ? $themeColor['variant_400'] : '#f16a89',
            'primary500' => $themeColor ? $themeColor['variant_500'] : '#EE456B',
            'primary600' => $themeColor ? $themeColor['variant_600'] : '#d63e60',
            'primary700' => $themeColor ? $themeColor['variant_700'] : '#be3756',
            'primary800' => $themeColor ? $themeColor['variant_800'] : '#a7304b',
            'primary900' => $themeColor ? $themeColor['variant_900'] : '#8f2940',
            'primary950' => $themeColor ? $themeColor['variant_950'] : '#772336',
        ];

        $languages = Cache::remember('languages', 60 * 24 * 30, function () {
            return LanguageRepository::getAll();
        });

        $socialAuths = collect([]);
        foreach (SocialAuth::all() as $socialAuth) {
            $socialAuths[$socialAuth->provider] = [
                'name' => $socialAuth->name,
                'client_id' => $socialAuth->client_id,
                'is_active' => (bool) $socialAuth->is_active,
                'redirect_url' => $socialAuth->redirect,
            ];
        }

        $allCurrencies = Cache::rememberForever('currencies', function () {
            return Currency::where('is_active', true)->get();
        });

        // default currency rate
        $defaultCurrency = generaleSetting('defaultCurrency');
        $defaultRate = 1;

        $currencies = $allCurrencies->map(function ($currency) use ($defaultRate) {
            $rate = $currency->is_default ? 1 : $currency->rate;
            $rateFromDefault = $rate / $defaultRate;

            return [
                'id' => $currency->id,
                'name' => $currency->name,
                'symbol' => $currency->symbol,
                'rate' => (float) round($rate, 4),
                'rate_from_default' => round($rateFromDefault, 4),
                'is_default' => (bool) $currency->is_default,
            ];
        });

        // phone min and max length
        $phoneMinLength = $verifyManage?->phone_min_length > 0 ? $verifyManage?->phone_min_length : 9;
        $phoneMaxLength = $verifyManage?->phone_max_length > 0 ? $verifyManage?->phone_max_length : 16;

        // menu
        $menus = Menu::where('is_active', true)->when($shopType != 'multi', function ($query) {
            return $query->where('url', '!=', '/shops');
        })->orderBy('order')->get();

        // footer
        $footers = Footer::with('items')->OrderBy('order')->get();

        return $this->json('Master data', [
            'currency' => [
                'name' => $defaultCurrency?->name ?? 'USD',
                'symbol' => $generaleSetting?->currency ?? '$',
                'rate' => (float) $defaultRate,
                'position' => $generaleSetting?->currency_position ?? 'prefix',
            ],
            'currencies' => $currencies,
            'app_name' => $generaleSetting?->name ?? config('app.name'),
            'app_locale' => config('app.locale'),
            'cash_on_delivery' => (bool) ($generaleSetting?->cash_on_delivery ?? true),
            'online_payment' => (bool) ($generaleSetting?->online_payment ?? true),
            'show_download_app' => (bool) ($generaleSetting?->show_download_app ?? true),
            'google_playstore_link' => $generaleSetting?->google_playstore_url ?? null,
            'app_store_link' => $generaleSetting?->app_store_url ?? null,
            'payment_gateways' => PaymentGatewayResource::collection($paymentGateways),
            'multi_vendor' => (bool) ($shopType == 'multi' ? true : false),
            'mobile' => $generaleSetting?->footer_phone ?? '+880123456789',
            'address' => $generaleSetting?->address ?? 'Dhaka, Bangladesh',
            'web_show_footer' => (bool) ($generaleSetting?->show_footer ?? true),
            'web_footer_text' => $generaleSetting?->footer_text ?? 'All right reserved by company',
            'web_logo' => $generaleSetting?->logo ?? asset('assets/logo.png'),
            'web_footer_logo' => $generaleSetting?->footerLogo ?? asset('assets/logoWhite.png'),
            'app_name' => $generaleSetting?->name ?? config('app.name'),
            'app_logo' => $generaleSetting?->appLogo ?? asset('assets/favicon.png'),
            'footer_qr' => $generaleSetting?->footerQr ?? null,
            'social_links' => SocialLinkResource::collection($socialLinks),
            'theme_colors' => $themeColors,
            'pusher_app_key' => config('broadcasting.connections.pusher.key'),
            'pusher_app_cluster' => config('broadcasting.connections.pusher.options.cluster'),
            'app_environment' => config('app.env'),
            'register_otp_verify' => (bool) ($verifyManage?->register_otp ?? false),
            'register_otp_type' => $verifyManage?->register_otp_type ?? 'email',
            'forgot_otp_type' => $verifyManage?->forgot_otp_type ?? 'email',
            'order_place_account_verify' => (bool) ($verifyManage?->order_place_account_verify ?? false),
            'phone_required' => (bool) $verifyManage?->phone_required ?? true,
            'phone_min_length' => (int) $phoneMinLength,
            'phone_max_length' => (int) $phoneMaxLength,
            'languages' => LanguageResource::collection($languages),
            'social_auths' => $socialAuths,
            'menus' => MenuResource::collection($menus),
            'footers' => FooterResource::collection($footers),
            'return_order_within_days' => $generaleSetting?->return_order_within_days ?? 3,
            'baseUrl' => config('app.url'),
        ]);
    }
}

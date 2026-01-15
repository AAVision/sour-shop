<?php

namespace App\Http\Controllers\Gateway\QiCard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProcessController extends Controller
{
    /**
     * Process to Paystack
     *
     * @return string
     */

    public static function process($paymentGateway, Payment $payment, ?array $info = null)
    {
        $config = json_decode($paymentGateway->config);

        $successUrl = route('payment.success', $payment->id);
        $cancelUrl = route('payment.cancel', $payment->id);

        $amount = $payment->amount;

        $amountIQD = round($amount, 2);
        $requestId = (string) Str::uuid();

        $payload = [
            "requestId" => $requestId,
            "amount" => $amountIQD,
            "currency" => "IQD",
            "locale" => "en_US",
            "finishPaymentUrl" => $successUrl,
            "notificationUrl" => "",
            "customerInfo" => [
                "firstName" => $info['name'] ?? 'Not Available',
                "email" => $info['email'] ?? 'Not Available',
                "phone" => $info['phone'] ?? '0000000000',
            ],
        ];

        $username = trim($config->username);
        $password = trim($config->password);
        $terminalId = trim($config->terminalId);

        $baseUrl = $paymentGateway->mode == 'test'
            ? 'https://uat-sandbox-3ds-api.qi.iq/api/v1/payment'
            : 'https://3ds-api.qi.iq/api/v1/payment';

        $response = Http::withHeaders([
            'X-Terminal-Id' => $terminalId,
            'Authorization' => 'Basic ' . base64_encode("{$username}:{$password}"),
        ])->post($baseUrl, $payload);

        if ($response->ok()) {
            $data = $response->json();

            if (!empty($data['formUrl'])) {
                return $data['formUrl'];
            }
        }

        return $cancelUrl;
    }
}

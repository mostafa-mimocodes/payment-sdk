<?php

namespace Mimocodes\Payment;

use Illuminate\Support\Facades\Http;
use Exception;


class Opay
{
    public function createCashier()
    {
//        $expireAt = 30;
//        $settings = OwnerSetting::first();
//        $cashierTotal = $this->evaluateTotalCashierValue($cashierData['value']);
//        if (array_key_exists('due_date', $cashierData)) {
//            $expireAt = Carbon::now()->diffInMinutes(Carbon::parse($cashierData['due_date']));
//        }
//        $cashierUrl = '/cashier/create';
//        $url = $testMode ?
//            $settings->sandbox_plugin_url . $cashierUrl
//            : $settings->live_plugin_url .$cashierUrl ;
//        $merchantId =  $testMode ?
//            $settings->opay_sandbox_merchant_id
//            : $settings->opay_live_merchant_id ;
//        $publicKey =  $testMode ?
//            $settings->sandbox_public_key
//            : $settings->live_public_key ;
//        $baseUrl = env('BOBAY_URL')  ?? URL::to('/');;
//        log::info('cashier base url ');
//        log::info($baseUrl);
        $url = 'https://sandboxapi.opaycheckout.com/api/v1/international/cashier/create';
        $data = [
            'country' => 'EG',
            'reference' => 'test#000000001',
            'amount' => [
                "total" => 4000,
                "currency" => 'EGP',
            ],
            'returnUrl' => 'https://your-return-url',
            'callbackUrl' => 'https://your-call-back-url',
            'cancelUrl' => 'https://your-cacel-url',
            'expireAt' => 30,
            'userInfo' => [
                "userEmail"=> 'xxx@xxx.com',
                "userId"=> 'userid001',
                "userMobile"=> '13056288895',
                "userName"=> 'xxx',
            ],
            'productList' => [
                [
                    "productId" => 'productId',
                    "name" => 'name',
                    "description" => 'description',
                    "price" => 100,
                    "quantity" => 2,
                ]
            ]
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer OPAYPUB16278941467290.7141670975876302',
            'MerchantId' => '281821080236026'
        ])->post($url, $data);
        if( $response['code'] == '00000'){
            return $response;
        }else{
            throw new Exception('Something Went Wrong While trying to create cashier link!!');
        }
    }
}
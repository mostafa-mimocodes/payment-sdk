<?php

namespace Mimocodes\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Exception;
use Mimocodes\Payment\Controllers\PaymentController;


class Opay
{
    public function createCashier( $data,$publicKey,$merchantId,$mode = 'test')
    {

        if($mode === 'test'){
            $url = 'https://sandboxapi.opaycheckout.com/api/v1/international/cashier/create';
        }elseif ($mode === 'live'){
            $url = 'https://api.opaycheckout.com/api/v1/international/cashier/create';
        }
        URL::forceScheme('https');
        $data['callbackUrl'] = URL::to('/').'/mimocodes/payment/opay/payment-callback-url';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$publicKey,
            'MerchantId' => $merchantId
        ])->post($url, $data);
        if( $response['code'] == '00000'){
            return json_decode($response);
        }else{
            throw new Exception('Something Went Wrong While trying to create cashier link!!');
        }
    }

    public function invoiceCallbackResponse($data)
    {
        return $data;
    }
}
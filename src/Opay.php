<?php

namespace Mimocodes\Payment;

use Illuminate\Support\Facades\Http;
use Exception;


class Opay
{
    public function createCashier( $data,$publicKey,$merchantId,$mode = 'test')
    {
        $mode === 'test' ? $url = 'https://sandboxapi.opaycheckout.com/api/v1/international/cashier/create' : $mode === 'live' ? $url = 'https://api.opaycheckout.com/api/v1/international/cashier/create': null;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$publicKey,
            'MerchantId' => $merchantId
        ])->post($url, $data);
        if( $response['code'] == '00000'){
            return $response;
        }else{
            throw new Exception('Something Went Wrong While trying to create cashier link!!');
        }
    }
}
<?php

namespace Mimocodes\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Exception;
use Mimocodes\Payment\Controllers\PaymentController;


class Opay
{
    public static function createCashier( $data,$publicKey,$merchantId,$mode = 'test')
    {

        if($mode === 'test'){
            $url = 'https://sandboxapi.opaycheckout.com/api/v1/international/cashier/create';
        }elseif ($mode === 'live'){
            $url = 'https://api.opaycheckout.com/api/v1/international/cashier/create';
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$publicKey,
            'MerchantId' => $merchantId
        ])->post($url, $data);
        if( $response['code'] == '00000'){
            return json_decode($response);
        }else{
            throw new Exception('Something Went Wrong While trying to create cashier link!');
        }
    }

    public static function getHmac($data,$secret): string
    {
        $amount = $data['amount'];
        $currency = $data['currency'];
        $reference = $data['reference'];
        $refunded = $data['refunded'];
        $status = $data['status'];
        $timeStamp = $data['timestamp'];
        $token = $data['token'];
        $transactionId = $data['transactionId'];
        $txt = sprintf("{Amount:\"%s\",Currency:\"%s\",Reference:\"%s\",Refunded:%s,Status:\"%s\",Timestamp:\"%s\",Token:\"%s\",TransactionID:\"%s\"}", $amount, $currency, $reference, $refunded ? "t" : "f", $status, $timeStamp, $token, $transactionId);
        return hash_hmac('sha3-512', ($txt), $secret);
    }


    public static function getSignature($data,$secret): string
    {
        ksort($data);
        $data = json_encode($data,JSON_UNESCAPED_SLASHES);
        $secret = (string) $secret;
        return hash_hmac('sha512',$data,$secret);
    }

    public static function refund($data,$merchantId,$secret,$mode='test'): string
    {

        if($mode === 'test'){
            $url = 'https://sandboxapi.opaycheckout.com/api/v1/international/payment/refund/create';
        }elseif ($mode === 'live'){
            $url = 'https://api.opaycheckout.com/api/v1/international/payment/refund/create';
        }

        $signature = Opay::getSignature($data,$secret);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$signature,
            'MerchantId' => $merchantId
        ])->post($url, $data);
        if( $response['code'] == '00000'){
            return json_decode($response);
        }else{
            throw new Exception('Something Went Wrong While trying to create cashier link!');
        }
    }

}
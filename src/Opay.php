<?php

namespace Mimocodes\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Exception;


class Opay
{

    private const SANDBOX_URL = 'https://sandboxapi.opaycheckout.com/api/v1/international/';
    private const LIVE_URL = 'https://api.opaycheckout.com/api/v1/international/';


    public static function createCashier( $data,$publicKey,$merchantId,$mode = 'test')
    {
        $url = self::getUrl($mode);
        $url ? $url .= 'cashier/create' : null;

        return self::signDataAndSendRequest($data, $publicKey, $merchantId, $url,true);
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

        $secret = (string) $secret;
        return hash_hmac('sha512',$data,$secret);
    }

    public static function refund($data,$merchantId,$secret,$mode='test')
    {

        $url = self::getUrl($mode);
        $url ? $url .= 'payment/refund/create' : null;

        ksort($data);

        return self::signDataAndSendRequest($data, $secret, $merchantId, $url);

    }

    public static function getPaymentStatus($data,$merchantId,$secret,$mode='test')
    {
        $url = self::getUrl($mode);
        $url ? $url .= 'cashier/status' : null;
        return self::signDataAndSendRequest($data, $secret, $merchantId, $url);
    }

    public static function cancelPayment($data,$merchantId,$secret,$mode='test')
    {
        $url = self::getUrl($mode);
        $url ? $url .= 'payment/close' : null;
        return self::signDataAndSendRequest($data, $secret, $merchantId, $url);
    }

    private static function getUrl($mode)
    {
        return $mode === 'test' ? $url = self::SANDBOX_URL : ($mode === 'live' ? $url = self::LIVE_URL : null);
    }

    private static function http_post ($url, $header, $data) {
        if (!function_exists('curl_init')) {
            throw new Exception('php not found curl', 500);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error=curl_error($ch);
        curl_close($ch);
        if (200 != $httpStatusCode) {
            print_r("invalid httpstatus:{$httpStatusCode} ,response:$response,detail_error:" . $error, $httpStatusCode);
        }
        return $response;
    }


    private static function signDataAndSendRequest($data, $secret, $merchantId, string $url,$cashier = false)
    {
        $data2 = (string)json_encode($data, JSON_UNESCAPED_SLASHES);
        $auth = self::getSignature($data2, $secret);
        $header = ['Content-Type:application/json', 'Authorization:Bearer ' . ($cashier ? $secret : $auth), 'MerchantId:' . $merchantId];
        $response = self::http_post($url, $header, json_encode($data));
        $result = $response ?: null;
        return $result;
    }

}

<?php

namespace Mimocodes\Payment\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;


class PaymentController
{
    public function callback(Request $request)
    {
        Log::info($request);
        Log::info("Invoice Callback");
        Log::info("received At");
        Log::info(\Carbon\Carbon::now());
        $data = $request['payload'];
        $hash = $this->get_hmacs($data);
        Log::info($hash);
        //sandbox hash is being used for now
        if ($request['sha512'] == $hash[0]) {
            $reference = $data['reference'];
            return 'SUCCESS';
        }else{
            return 'FAILED';
        }
    }

    public function get_hmacs($data): array
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
        $live_secret = "OPAYPRV16445011695740.18689005222620036";
        $snad_box_secret = "OPAYPRV16278941467300.15083044597151063";
        $computed_hmac_sand_box = hash_hmac('sha3-512', ($txt), $snad_box_secret);
        $computed_hmac_live = hash_hmac('sha3-512', ($txt), $live_secret);
        return array($computed_hmac_sand_box, $computed_hmac_live);
    }
}

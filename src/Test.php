<?php

namespace Mimocodes\Payment;





use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class Test
{
    public function getRes()
    {
        $client = new Client([
           'base_uri' => 'https://api.goprogram.ai/inspiration/'
        ]);

        $response = $client->request('GET')->getBody()->getContents();

        $response = json_decode($response);

        return $response;
    }
    public function justDoIt()
    {
        $response = Http::get('https://inspiration.goprogram.ai/');

        return $response['quote'] . ' -' . $response['author'];
    }
}

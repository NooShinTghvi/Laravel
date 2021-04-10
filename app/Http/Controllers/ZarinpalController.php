<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use nusoap_client;
use Webpatser\Uuid\Uuid;

class ZarinpalController extends Controller
{
    public $MerchantID;

    public function __construct()
    {
        $this->MerchantID = env('MERCHANT_ID');
    }

    public function pay($Amount,$CallbackURL, $Email, $Mobile)
    {

        if (env('ZARINPAL_IS_MOCKED')) {
            return $CallbackURL.'?Authority='.Uuid::generate(4)->string.'&Status=OK';
        }

        $Description = 'فروش محصول';  // Required
//        $CallbackURL = route('home'); // Required

        $client = new nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
        $client->soap_defencoding = 'UTF-8';
        $result = $client->call('PaymentRequest', [
            [
                'MerchantID'     => $this->MerchantID,
                'Amount'         => $Amount,
                'Description'    => $Description,
                'Email'          => $Email,
                'Mobile'         => $Mobile,
                'CallbackURL'    => $CallbackURL,
            ],
        ]);

        //Redirect to URL You can do it also by creating a for  m

        if($result['Status'] == 100){
            return 'https://www.zarinpal.com/pg/StartPay/'.$result['Authority'];
        } else {
            return false;
        }
    }

    public function order(Request $request, $Amount)
    {
        if(env('ZARINPAL_IS_MOCKED')){
            $refId=rand(111111111,999999999);
            return [
                'ok' => true,
                'massage' => 'Transaction success. RefID:' . $refId,
                'refId' =>  $refId,
            ];
        }

        $Authority = $request->input('Authority');
        if ($request->get('Status') == 'OK') {
            $client = new nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
//            $client = new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
            $client->soap_defencoding = 'UTF-8';

            $result = $client->call('PaymentVerification', [
                [
                    'MerchantID' => $this->MerchantID,
                    'Authority' => $Authority,
                    'Amount' => $Amount,
                ],
            ]);

            if ($result['Status'] == 100) {
                return [
                    'ok' => true,
                    'massage' => 'Transaction success. RefID:' . $result['RefID'],
                    'refId' =>  $result['RefID'],
                ];

            } else {
                return [
                    'ok' => false,
                    'massage' => 'Transaction failed. Status:' . $result['Status'],
                ];
            }
        } else {
            return [
                'ok' => false,
                'massage' => 'Transaction canceled by user',
            ];
        }
    }
}

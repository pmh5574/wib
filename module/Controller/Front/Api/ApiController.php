<?php
namespace Controller\Front\Api;

class ApiController extends \Controller\Front\Controller{
    public function index()
    {
        $method = "GET";

        $data = array(
            'apiKey' => 'test'
        );

        $url = "https://srv2.best-fashion.net/ApiV3/token/affc472c99627a92620c6a47f452f67d/callType/allStockGroup";

        //$url = "https://srv2.best-fashion.net/ApiV3/token/affc472c99627a92620c6a47f452f67d/callType/singleProductCheck/productID/8354554";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $response = curl_exec($ch);
        //$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //$error = curl_error($ch);
        curl_close($ch);
        echo 'test33333333333333';

        $result = json_decode($response,true);
        gd_debug($result);

        exit;
    }
}

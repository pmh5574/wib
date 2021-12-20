<?php

namespace Controller\Mobile\Goods;

use Component\Wib\WibGoods;
use Request;

class GoodsFilterPsController extends \Controller\Mobile\Controller
{
    public function index()
    {
        $wibGoods = new WibGoods();
        
        $postValue = Request::post()->toArray();
        
        switch($postValue['mode']){
            case 'getBrand':
                $data = $wibGoods->getBrandList(gd_isset($postValue['brandNm']), gd_isset($postValue['orderBy']));
                echo $data;
                break;
            default :
                break;
        }
        
        exit();
    }
}
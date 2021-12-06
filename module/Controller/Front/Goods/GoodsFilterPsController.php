<?php

namespace Controller\Front\Goods;

use Component\Wib\WibGoods;
use Request;

class GoodsFilterPsController extends \Controller\Front\Controller
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
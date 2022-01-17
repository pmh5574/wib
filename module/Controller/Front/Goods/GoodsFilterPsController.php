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
                $data = $wibGoods->getBrandList(gd_isset($postValue['brandNm']), gd_isset($postValue['orderBy']), $postValue['cateCd']);
                echo $data;
                break;
            case 'getSearchBrand':
                $data = $wibGoods->getSearchBrandList(gd_isset($postValue['brandNm']), gd_isset($postValue['orderBy']), $postValue['goodsNm']);
                echo $data;
                break;
            default :
                break;
        }
        
        exit();
    }
}
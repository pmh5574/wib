<?php

namespace Controller\Admin\Goods;

use Request;
use Framework\Debug\Exception\LayerException;
use Component\Wib\WibBrand;

class GoodsBestPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $wibBrand = new WibBrand();
        
        $postValue = Request::post()->toArray();
        
        try {

            switch ($postValue['mode']) {
                case 'best_goods_update' :
                    $wibBrand->putSaveBestBrand($postValue);
                    $this->layer(__('저장이 완료되었습니다.'));

                    break;
            }
        } catch (\Exception $e) {
            throw new LayerException($e->getMessage());
        }

        
        exit();
    }
}

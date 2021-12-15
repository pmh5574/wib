<?php

namespace Controller\Front\Mypage;

use Component\Wib\WibBrand;

class CancelListController extends \Bundle\Controller\Front\Mypage\CancelListController
{
    public function post()
    {
        $wibBrand = new WibBrand();
        
        $orderData = $this->getData('orderData');
        foreach ($orderData as $key => $value) {
            foreach ($value['goods'] as $k => $val) {
                $orderData[$key]['goods'][$k]['brandNm'] = $wibBrand->getBrandNm($val['brandCd'])['cateNm'];
            }
        }
        
        $this->setData('orderData', $orderData);
    }
}

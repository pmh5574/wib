<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Front\Mypage;

use Component\Wib\WibBrand;

class OrderViewController extends \Bundle\Controller\Front\Mypage\OrderViewController
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
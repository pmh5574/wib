<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Mobile\Mypage;

use Component\Wib\WibBrand;

class OrderViewController extends \Bundle\Controller\Mobile\Mypage\OrderViewController
{
    public function post()
    {
        $wibBrand = new WibBrand();
        
        $ordersByRegisterDay = $this->getData('ordersByRegisterDay');
        
        foreach ($ordersByRegisterDay as $key => $value) {
            foreach ($value as $k => $val) {
                foreach ($val['goods'] as $lKey => $lValue) {

                    $ordersByRegisterDay[$key][$k]['goods'][$lKey]['brandNm'] = $wibBrand->getBrandNm($lValue['brandCd'])['cateNm'];
                }
            }
        
        }
        
        $this->setData('ordersByRegisterDay', $ordersByRegisterDay);
    }
}
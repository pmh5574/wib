<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Mobile\Order;

use Component\Wib\WibBrand;
/**
 * 관련상품
 *
 * @author Ahn Jong-tae <qnibus@godo.co.kr>
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class CartController extends \Bundle\Controller\Mobile\Order\CartController
{
    public function post()
    {
        $wibBrand = new WibBrand();
        
        $cartInfo = $this->getData('cartInfo');
        
        foreach ($cartInfo as $key => $value) {
            foreach ($value as $k => $val) {
                foreach ($val as $lKey => $lValue) {
                    $cartInfo[$key][$k][$lKey]['brandNm'] = $wibBrand->getBrandNm($lValue['brandCd'])['cateNm'];
                }
            }
        }
        
        $this->setData('cartInfo', $cartInfo);
    }
}
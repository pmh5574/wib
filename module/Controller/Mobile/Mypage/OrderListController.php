<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */
namespace Controller\Mobile\Mypage;

use Component\Wib\WibBrand;
/**
 * 마이페이지 > 주문배송/조회
 *
 * @package Bundle\Controller\Mobile\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>/**
 */
class OrderListController extends \Bundle\Controller\Mobile\Mypage\OrderListController
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

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
 * @link http://www.godo.co.kr
 */
namespace Controller\Front\Mypage;

use Component\Wib\WibBrand;
/**
 * 마이페이지 > 주문배송/조회
 *
 * @package Bundle\Controller\Front\Mypage
 * @author  Jong-tae Ahn <qnibus@godo.co.kr>/**
 */
class OrderListController extends \Bundle\Controller\Front\Mypage\OrderListController
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

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
namespace Controller\Mobile\Mypage;

use Component\Wib\WibBrand;
/**
 * 관련상품
 *
 * @author Ahn Jong-tae <qnibus@godo.co.kr>
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class WishListController extends \Bundle\Controller\Mobile\Mypage\WishListController
{
    public function post()
    {
        $wibBrand = new WibBrand();
        
        //찜 브랜드 리스트
        $brandWishInfo = $wibBrand->getBrandWishData();
        
        //찜리스트 브랜드명 추가
        $wishInfo = $this->getData('wishInfo');
        
        foreach ($wishInfo as $key => $value) {
            foreach ($value as $k => $val) {
                foreach ($val as $lKey => $lValue) {
                    $wishInfo[$key][$k][$lKey]['brandNm'] = $wibBrand->getBrandNm($lValue['brandCd'])['cateNm'];
                }
                
            }
        }
        
        $page = \App::load('\\Component\\Page\\Page'); // 페이지 재설정
        
        $this->setData('page', gd_isset($page));
        $this->setData('brandWishInfo', $brandWishInfo);
        $this->setData('wishInfo', $wishInfo);
    }
}

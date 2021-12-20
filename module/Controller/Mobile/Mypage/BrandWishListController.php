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

class BrandWishListController extends \Controller\Mobile\Controller
{
    public function index()
    {
        $wibBrand = new WibBrand();
        $brandWishInfo = $wibBrand->getBrandWishData();
        
        $page = \App::load('\\Component\\Page\\Page'); // 페이지 재설정
        
        $this->setData('page', gd_isset($page));
        $this->setData('brandWishInfo', $brandWishInfo);
    }
}

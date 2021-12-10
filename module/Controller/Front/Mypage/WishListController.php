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
 * 관련상품
 *
 * @author Ahn Jong-tae <qnibus@godo.co.kr>
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class WishListController extends \Bundle\Controller\Front\Mypage\WishListController
{
    public function post()
    {
        $wibBrand = new WibBrand();
        $brandWishInfo = $wibBrand->getBrandWishData();
        
        $this->setData('brandWishInfo', $brandWishInfo);
    }
}

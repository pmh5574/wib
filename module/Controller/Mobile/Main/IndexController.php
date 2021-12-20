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
namespace Controller\Mobile\Main;

use Component\Wib\WibBrand;
use Component\Wib\WibBoard;
/**
 * 메인 페이지
 *
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class IndexController extends \Bundle\Controller\Mobile\Main\IndexController
{
    public function post()
    {
        $wibBrand = new WibBrand();
        $wibBoard = new WibBoard();
        
        $brandListInfo = $wibBrand->getBrandData();
        $reviews = $wibBoard->getMainReview();

        $this->setData('reviews',$reviews);
        $this->setData('brandListInfo', $brandListInfo);
    }
}
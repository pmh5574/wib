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
namespace Widget\Mobile\Proc;

use Component\Wib\WibBrand;

class MyWishBrandWidget extends \Widget\Mobile\Widget
{
    public function index()
    {
        $wibBrand = new WibBrand();
        
        $data = $wibBrand->getAllBrandWishData();
        
        $this->setData('data', $data);
    }
}
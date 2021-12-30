<?php

namespace Controller\Mobile\Main;

use Component\Wib\WibBrand;
use Component\Wib\WibBoard;

class IndexPreController extends \Controller\Mobile\Controller
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
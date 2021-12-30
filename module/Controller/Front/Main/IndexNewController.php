<?php

namespace Controller\Front\Main;

use Component\Wib\WibBrand;
use Component\Wib\WibBoard;

class IndexNewController extends \Bundle\Controller\Front\Main\IndexController
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
<?php

namespace Controller\Admin\Goods;

use Component\Wib\WibBrand;

class GoodsBestBrandController extends \Controller\Admin\Controller
{
    public function index()
    {
        // --- 메뉴 설정
        $this->callMenu('goods', 'category', 'best_brand');
        
        $this->addScript(
            [
                'jquery/jquery.multi_select_box.js',
            ]
        );
        
        $wibBrnad = new WibBrand();
        
        $data = $wibBrnad->getBestBrand();
    }
}

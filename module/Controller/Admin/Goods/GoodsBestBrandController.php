<?php

namespace Controller\Admin\Goods;


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
    }
}

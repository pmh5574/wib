<?php

namespace Controller\Admin\Partners;

class PartnersApplyProductListController extends \Controller\Admin\Controller
{
    public function index()
    {
        // --- 메뉴 설정
        $this->callMenu('partners', 'partners', 'partners_apply_product_list');
    }
}

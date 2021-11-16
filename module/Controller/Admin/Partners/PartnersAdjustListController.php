<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Admin\Partners;

class PartnersAdjustListController extends \Controller\Admin\Controller
{
    public function index()
    {
        // --- 메뉴 설정
        $this->callMenu('partners', 'partners', 'partners_adjust_list');
        
        $combineSearch = [
            'memId' => '아이디',
            'orderName' => '주문자 이름',
            'orderNo' => '주문 번호',
            'orderCode' => '상품 코드'
        ];
        
        $this->setData('combineSearch', $combineSearch);
    }
}

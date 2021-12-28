<?php

namespace Controller\Admin\Partners;

class PartnersMileageListController extends \Controller\Admin\Controller
{
    public function index()
    {
        // --- 메뉴 설정
        $this->callMenu('partners', 'partners', 'partners_mileage_list');
        
        $combineSearch = [
            'memId' => '아이디',
            'orderName' => '주문자 이름',
            'orderNo' => '주문 번호',
            'orderCode' => '상품 코드'
        ];
        
        $this->setData('combineSearch', $combineSearch);
    }
}

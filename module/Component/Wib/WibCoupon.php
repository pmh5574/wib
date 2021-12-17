<?php

namespace Component\Wib;

use Session;
use Request;
use Component\Wib\WibSql;

class WibCoupon
{
    public $wibSql;
    
    public function __construct() 
    {
        $this->wibSql = new WibSql();
    }
    
    //마이페이지 쿠폰 리스트 불러오기
    public function getCouponList()
    {
        $memNo = Session::get('member.memNo');
        $coupon = \App::load('\\Component\\Coupon\\Coupon');
        
        if($memNo){
            
            Request::get()->set('wDate',['2020-01-01',date('Y-m-d')]);
            //mypage에서 쿠폰 불러오는 방식
            $getData = $coupon->getMemberCouponList(Session::get('member.memNo'));
            $getData['data'] = $coupon->getCouponApplyExceptArrData($getData['data']);
            $getData['data'] = $coupon->getMemberCouponUsableDisplay($getData['data']);
            
            return $getData['data'];
        }else{
            return array();
        }
        
        
    }
    
}


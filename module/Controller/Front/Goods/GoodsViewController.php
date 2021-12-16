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
namespace Controller\Front\Goods;

use Component\Wib\WibBrand;
use Component\Wib\WibWish;
use Component\Wib\WibCoupon;
use Session;
use Request;
use Cookie;

class GoodsViewController extends \Bundle\Controller\Front\Goods\GoodsViewController
{
    public function post()
    {
        $wibBrand = new WibBrand();

        $goodsView = $this->getData('goodsView');
        
        //브랜드 한글명 추가 노출
        $goodsView['brandKrNm'] = $wibBrand->getBrandNm($goodsView['brandCd'])['cateKrNm'];
        
        //회원일때 위시리스트에 있는지 체크
        if(Session::has('member')) {
            $wibWish = new WibWish();
            
            $memNo = Session::get('member.memNo');
            $goodsNo = Request::get()->get('goodsNo');
            
            $sno = $wibWish->getWishList($memNo, $goodsNo);
            
            if($sno){
                $wishCheck = 'on';
                $goodsView['wishSno'] = $sno;
            }else{
                $wishCheck = '';
            }

            $this->setData('wishCheck', $wishCheck);
        }
        
        $couponArrData = $this->getData('couponArrData');
        $goodsCategoryList = $this->getData('goodsCategoryList');
        
        // 상품의 쿠폰리스트
        if(Session::has('member')) {
            $wibCoupon = new WibCoupon();
            
            $getData = $wibCoupon->getCouponList();

            foreach ($couponArrData as $key => $value){
                foreach ($getData as $k => $val){
                    if($value['couponNo'] == $val['couponNo']){
                        unset($getData[$k]);
                    }
                }
            }

            //사용 가능한 쿠폰리스트
            $goodsCouponArrData = array_merge($couponArrData,$getData);
        }else{
            $goodsCouponArrData = $couponArrData;
        }

        $allCoupon = [];
        $dcCoupon = [];
        $wibDcPrice = [];

        //최저가 쿠폰 리스트 + 가격
        foreach ($goodsCouponArrData as $key => $value) {

            if($value['couponApplyProductType'] == 'category'){
                $cateChecks = false;


                foreach ($goodsCategoryList as $cateCd => $cValue) {

                    //쿠폰 불러오는 방식이 달라서 형태가 다를수도 있음
                    if(is_array($value['couponApplyCategory'])){

                        foreach ($value['couponApplyCategory'] as $k => $val) {
                            $value['couponApplyCategoryNew'] .= $val['no'].'||';
                        }

                        //카테고리 배열 중 하나라도 맞으면 쿠폰 사용가능
                        if(strpos($value['couponApplyCategoryNew'],$cateCd) !== false){

                            $cateChecks = true;
                        }

                    }else{

                        if(strpos($value['couponApplyCategory'],$cateCd) !== false){

                            $cateChecks = true;
                        }
                    }
                }

                if($cateChecks == false){
                    unset($value);
                }


            }

            if($value['couponApplyProductType'] == 'brand'){

                $brandChecks = false;

                if(is_array($value['couponApplyBrand'])){

                    foreach ($value['couponApplyBrand'] as $k => $val) {
                        $value['couponApplyBrandNew'] .= $val['no'].'||';
                    }


                    if(strpos($value['couponApplyBrandNew'],$goodsView['brandCd']) !== false){

                        $brandChecks = true;

                    }

                    if(!$brandChecks){
                        unset($value);
                    }

                }else{

                    if(strpos($value['couponApplyBrand'],$goodsView['brandCd']) === false){
                        unset($value);
                    }

                }


            }

            if($value['couponApplyProductType'] == 'goods'){

                $goodsNoChecks = false;

                if(is_array($value['couponApplyGoods'])){

                    foreach ($value['couponApplyGoods'] as $k => $val) {
                        $value['couponApplyGoodsNew'] .= $val['no'].'||';
                    }


                    if(strpos($value['couponApplyGoodsNew'],$goodsView['goodsNo']) !== false){

                        $goodsNoChecks = true;

                    }

                    if(!$goodsNoChecks){
                        unset($value);
                    }

                }else{

                    if(strpos($value['couponApplyGoods'],$goodsView['goodsNo']) === false){
                        unset($value);
                    }

                }

            }

            //특정 상품 제외 일때
            foreach ($value['couponExceptGoods'] as $k => $val) {
                if($val['goodsNo'] == $goodsView['goodsNo']){
                    unset($value);
                }
            }

            if($value['couponUseType'] == 'product'){
                $allCoupon['product'][] = $value;
            }
            if($value['couponUseType'] == 'order'){
                $allCoupon['order'][] = $value;
            }
            if($value['couponUseType'] == 'delivery'){
                $allCoupon['delivery'][] = $value;
            }
            if($value['couponUseType'] == 'gift'){
                $allCoupon['gift'][] = $value;
            }



        }



        foreach ($allCoupon as $couponUseType => $value) {
            foreach ($value as $key => $val){

                //각 쿠폰마다 퍼센트인값은 계산해서 금액으로 바꿈
                if($val['couponBenefitType'] == 'percent'){
                    $val['couponBenefit'] = ($val['couponBenefit']/100) * $goodsView['goodsPrice'];
                }
                
                if($val['couponMaxBenefitType'] == 'y' && $val['couponMaxBenefit'] < $val['couponBenefit']){
                    $val['couponBenefit'] = $val['couponMaxBenefit'];
                }

                //각 쿠폰 종류별로 중복된 값이냐 아니냐에 따라 배열을 나눔
                if($couponUseType == 'product'){

                    if($val['couponApplyDuplicateType'] == 'y'){

                        $dcCoupon['product']['y']['price'] += $val['couponBenefit'];
                        $dcCoupon['product']['y']['couponNm'][] = $val['couponNm'];

                    }else if($val['couponApplyDuplicateType'] == 'n'){

                        if($dcCoupon['product']['n']['price'] < $val['couponBenefit']){

                            $dcCoupon['product']['n']['price'] = $val['couponBenefit'];
                            $dcCoupon['product']['n']['couponNm'][0] = $val['couponNm'];

                        }
                    }

                }
                if($couponUseType == 'order'){

                    if($val['couponApplyDuplicateType'] == 'y'){

                        $dcCoupon['order']['y']['price'] += $val['couponBenefit'];
                        $dcCoupon['order']['y']['couponNm'][] = $val['couponNm'];

                    }else if($val['couponApplyDuplicateType'] == 'n'){

                        if($dcCoupon['order']['n']['price'] < $val['couponBenefit']){

                            $dcCoupon['order']['n']['price'] = $val['couponBenefit'];
                            $dcCoupon['order']['n']['couponNm'][0] = $val['couponNm'];

                        }
                    }

                }
                if($couponUseType == 'delivery'){

                    if($val['couponApplyDuplicateType'] == 'y'){

                        $dcCoupon['delivery']['y']['price'] += $val['couponBenefit'];
                        $dcCoupon['delivery']['y']['couponNm'][] = $val['couponNm'];

                    }else if($val['couponApplyDuplicateType'] == 'n'){

                        if($dcCoupon['delivery']['n']['price'] < $val['couponBenefit']){

                            $dcCoupon['delivery']['n']['price'] = $val['couponBenefit'];
                            $dcCoupon['delivery']['n']['couponNm'][0] = $val['couponNm'];

                        }
                    }

                }
                if($couponUseType == 'gift'){

                    if($val['couponApplyDuplicateType'] == 'y'){

                        $dcCoupon['gift']['y']['price'] += $val['couponBenefit'];
                        $dcCoupon['gift']['y']['couponNm'][] = $val['couponNm'];

                    }else if($val['couponApplyDuplicateType'] == 'n'){

                        if($dcCoupon['gift']['n']['price'] < $val['couponBenefit']){

                            $dcCoupon['gift']['n']['price'] = $val['couponBenefit'];
                            $dcCoupon['gift']['n']['couponNm'][0] = $val['couponNm'];

                        }
                    }

                }


            }
        }

        if($dcCoupon['product']['n']['price'] > $dcCoupon['product']['y']['price']){

            $wibDcPrice['product'][0] = $dcCoupon['product']['n']['price'];
            $wibDcPrice['product'][1] = $dcCoupon['product']['n']['couponNm'];

        }else if($dcCoupon['product']['n']['price'] <= $dcCoupon['product']['y']['price']){

            $wibDcPrice['product'][0] = $dcCoupon['product']['y']['price'];
            $wibDcPrice['product'][1] = $dcCoupon['product']['y']['couponNm'];

        }

        if($dcCoupon['order']['n']['price'] > $dcCoupon['order']['y']['price']){

            $wibDcPrice['order'][0] = $dcCoupon['order']['n']['price'];
            $wibDcPrice['order'][1] = $dcCoupon['order']['n']['couponNm'];


        }else if($dcCoupon['order']['n']['price'] <= $dcCoupon['order']['y']['price']){

            $wibDcPrice['order'][0] = $dcCoupon['order']['y']['price'];
            $wibDcPrice['order'][1] = $dcCoupon['order']['y']['couponNm'];

        }

        if($dcCoupon['delivery']['n']['price'] > $dcCoupon['delivery']['y']['price']){

            $wibDcPrice['delivery'][0] = $dcCoupon['delivery']['n']['price'];
            $wibDcPrice['delivery'][1] = $dcCoupon['delivery']['n']['couponNm'];

        }else if($dcCoupon['delivery']['n']['price'] <= $dcCoupon['delivery']['y']['price']){

            $wibDcPrice['delivery'][0] = $dcCoupon['delivery']['y']['price'];
            $wibDcPrice['delivery'][1] = $dcCoupon['delivery']['y']['couponNm'];

        }

        if($dcCoupon['gift']['n']['price'] > $dcCoupon['gift']['y']['price']){

            $wibDcPrice['gift'][0] = $dcCoupon['gift']['n']['price'];
            $wibDcPrice['gift'][1] = $dcCoupon['gift']['n']['couponNm'];

        }else if($dcCoupon['gift']['n']['price'] <= $dcCoupon['gift']['y']['price']){

            $wibDcPrice['gift'][0] = $dcCoupon['gift']['y']['price'];
            $wibDcPrice['gift'][1] = $dcCoupon['gift']['y']['couponNm'];

        }



        $wibTotalPrice = $wibDcPrice['product'][0]+$wibDcPrice['order'][0]+$wibDcPrice['delivery'][0]+$wibDcPrice['gift'][0];

        if(count($wibDcPrice['product'][1]) > 0){
            $totalCouponNm[] = $wibDcPrice['product'][1];
        }
        if(count($wibDcPrice['order'][1]) > 0){
            $totalCouponNm[] = $wibDcPrice['order'][1];
        }
        if(count($wibDcPrice['delivery'][1]) > 0){
            $totalCouponNm[] = $wibDcPrice['delivery'][1];
        }
        if(count($wibDcPrice['gift'][1]) > 0){
            $totalCouponNm[] = $wibDcPrice['gift'][1];
        }

        $gMem = $this->getData('gMemberInfo');


        //최저가 쿠폰 리스트 + 가격end
        $wibLastDcPrice = $goodsView['goodsDcPricePrint'] - $wibTotalPrice - $gMem['mileage'];
        
        
        $this->setData('wibDcPrice', $wibTotalPrice);
        $this->setData('wibDcCouponNm', $totalCouponNm);

        $this->setData('wibLastDcPrice', $wibLastDcPrice);
        
        $this->setData('goodsView', $goodsView);
    }
}

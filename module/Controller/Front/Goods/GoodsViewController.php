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
use Session;
use Request;

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
        
        $this->setData('goodsView', $goodsView);
    }
}

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
namespace Controller\Mobile\Goods;


use Component\Wib\WibWish;
use Session;
use Request;
use Cookie;

class LayerOptionController extends \Bundle\Controller\Mobile\Goods\LayerOptionController
{
    public function post()
    {
        //회원일때 위시리스트에 있는지 체크
        if(Session::has('member')) {
            $wibWish = new WibWish();
            
            $memNo = Session::get('member.memNo');
            $goodsNo = Request::post()->get('goodsNo');
            
            $sno = $wibWish->getWishList($memNo, $goodsNo);
            
            if($sno){
                $wishCheck = 'on';
                $wishSno = $sno;
            }else{
                $wishCheck = '';
            }

            $this->setData('wishSno', $wishSno);
            $this->setData('wishCheck', $wishCheck);
        }
    }
}

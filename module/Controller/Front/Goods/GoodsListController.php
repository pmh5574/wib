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

use Component\Wib\WibWish;
use Session;

class GoodsListController extends \Bundle\Controller\Front\Goods\GoodsListController
{
    public function post()
    {
        if(Session::has('member')) {
            $wibWish = new WibWish();
            
            $goodsListData = $this->getData('goodsList');
            $memNo = Session::get('member.memNo');
            
            $goodsList = $wibWish->getGoodsWishList($goodsListData, $memNo);
            
            $this->setData('goodsList', $goodsList);
        }
        
    }
}

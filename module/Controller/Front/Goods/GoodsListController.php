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
use Component\Wib\WibBrand;
use Session;

class GoodsListController extends \Bundle\Controller\Front\Goods\GoodsListController
{
    public function post()
    {
        $cateType = $this->getData('cateType');
        
        if($cateType == 'brand'){
            $wibBrand = new WibBrand();
            
            $themeInfo = $this->getData('themeInfo');
            $brandMoreInfo = $wibBrand->getBrandNm($themeInfo['cateCd']);
            
            $themeInfo['bigBrandImg'] = $brandMoreInfo['bigBrandImg'];
            $themeInfo['whiteBrandImg'] = $brandMoreInfo['whiteBrandImg'];
            $themeInfo['blackBrandImg'] = $brandMoreInfo['blackBrandImg'];
            $themeInfo['brandLike'] = $brandMoreInfo['brandLike'];
            $themeInfo['brandCnt'] = $brandMoreInfo['cnt'];
            
            $this->setData('themeInfo', $themeInfo);
        }
        

        
        if(Session::has('member')) {
            $wibWish = new WibWish();
            
            $goodsListData = $this->getData('goodsList');
            $memNo = Session::get('member.memNo');
            
            $goodsList = $wibWish->getGoodsWishList($goodsListData, $memNo);
            
            $this->setData('goodsList', $goodsList);
        }
        
    }
}

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
namespace Widget\Mobile\Goods;

use Request;
use Framework\Utility\ArrayUtils;
use Framework\Utility\SkinUtils;
use UserFilePath;
use Component\Wib\WibWish;
use Session;


class GoodsBrandDisplayMainWidget extends \Widget\Mobile\Widget
{
    public function index()
    {
        $goods = \App::load('\\Component\\Goods\\Goods');
        $cate = \App::load('\\Component\\Category\\Brand');
//        $cateCd = '024';
        if(!$cateCd){
            $cateCd = $this->getData('brandCd');
        }

        $cateInfo = $cate->getCategoryGoodsList($cateCd);

        $cateInfo['lineCnt'] = 3;
        $cateInfo['rowCnt'] = 1;

        if ($cateInfo['soldOutDisplayFl'] == 'n') $displayOrder[] = "soldOut asc";

        if ($cateInfo['sortAutoFl'] == 'y') $displayOrder[] = "gl.fixSort desc," . gd_isset($cateInfo['sortType'], 'gl.goodsNo desc');
        else $displayOrder[] = "gl.fixSort desc,gl.goodsSort desc";

        $displayCnt = gd_isset($cateInfo['lineCnt']) * gd_isset($cateInfo['rowCnt']);
        $pageNum = gd_isset($getValue['pageNum'],$displayCnt);
        $optionFl = in_array('option', array_values($cateInfo['displayField'])) ? true : false;
        $soldOutFl = (gd_isset($cateInfo['soldOutFl']) == 'y' ? true : false); // 품절상품 출력 여부
        $brandFl = in_array('brandCd', array_values($cateInfo['displayField'])) ? true : false;
        $couponPriceFl = in_array('coupon', array_values($cateInfo['displayField'])) ? true : false;     // 쿠폰가 출력 여부
        $cateMode = 'brand';

        $goods->setThemeConfig($cateInfo);
        $goodsData = $goods->getGoodsList($cateCd, $cateMode, $pageNum, $displayOrder, gd_isset($cateInfo['imageCd']), $optionFl, $soldOutFl, $brandFl, $couponPriceFl);
        
        //품절상품 설정
        $soldoutDisplay = gd_policy('soldout.pc');

        // 마일리지 정보
        $mileage = gd_mileage_give_info();
        // 카테고리 노출항목 중 상품할인가
        if (in_array('goodsDcPrice', $cateInfo['displayField'])) {
            foreach ($goodsList as $key => $val) {
                foreach ($val as $key2 => $val2) {
                    $goodsList[$key][$key2]['goodsDcPrice'] = $goods->getGoodsDcPrice($val2);
                }
            }
        }
        
        // 장바구니 설정
        $cartInfo = gd_policy('order.cart');
        $this->setData('cartInfo', gd_isset($cartInfo));
        
        
        
        if ($goodsData['listData']) {
            $goodsList = array_chunk($goodsData['listData'], $cateInfo['lineCnt']);
        }

        $this->setData('mileageData', gd_isset($mileage['info']));
        $this->setData('soldoutDisplay', gd_isset($soldoutDisplay));
        $this->setData('cateType', 'brand');
        $this->setData('cateCd', $cateCd);
        $this->setData('themeInfo', gd_isset($cateInfo));
        $this->setData('goodsList', $goodsList);
        $this->getView()->setDefine('goodsTemplate', 'goods/list/list_15.html');
    }
    
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
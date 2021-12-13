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
namespace Widget\Front\Goods;

use Request;
use Framework\Utility\ArrayUtils;
use Framework\Utility\SkinUtils;
use UserFilePath;


class GoodsBrandDisplayMainWidget extends \Widget\Front\Widget
{
    public function index()
    {
        $goods = \App::load('\\Component\\Goods\\Goods');
        $cate = \App::load('\\Component\\Category\\Brand');
        $cateCd = Request::get()->get('brandCd');
        if(!$cateCd){
            $cateCd = $this->getData('brandCd');
        }


        $cateInfo = $cate->getCategoryGoodsList($cateCd);

        $cateInfo['lineCnt'] = 5;
        $cateInfo['rowCnt'] = 1;

        if ($cateInfo['soldOutDisplayFl'] == 'n') $displayOrder[] = "soldOut asc";

        if ($cateInfo['sortAutoFl'] == 'y') $displayOrder[] = "gl.fixSort desc," . gd_isset($cateInfo['sortType'], 'gl.goodsNo desc');
        else $displayOrder[] = "gl.fixSort desc,gl.goodsSort desc";

        $displayCnt = gd_isset($cateInfo['lineCnt']) * gd_isset($cateInfo['rowCnt']);
        $pageNum = 5;
        $optionFl = in_array('option', array_values($cateInfo['displayField'])) ? true : false;
        $soldOutFl = (gd_isset($cateInfo['soldOutFl']) == 'y' ? true : false); // 품절상품 출력 여부
        $brandFl = in_array('brandCd', array_values($cateInfo['displayField'])) ? true : false;
        $couponPriceFl = in_array('coupon', array_values($cateInfo['displayField'])) ? true : false;     // 쿠폰가 출력 여부
        $cateMode = 'brand';



        $goods->setThemeConfig($cateInfo);
        $goodsData = $goods->getGoodsList($cateCd, $cateMode, $pageNum, $displayOrder, gd_isset($cateInfo['imageCd']), $optionFl, $soldOutFl, $brandFl, $couponPriceFl);
        
        if ($goodsData['listData']) {
            $goodsList = array_chunk($goodsData['listData'], $cateInfo['lineCnt']);
        }

        $this->setData('goodsList', $goodsList);
        $this->getView()->setDefine('goodsTemplate', 'goods/list/list_'.$cateInfo['displayType'].'.html');
    }
}
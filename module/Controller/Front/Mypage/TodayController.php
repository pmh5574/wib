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
namespace Controller\Front\Mypage;

use Request;
use Cookie;
/**
 * 관련상품
 *
 * @author Ahn Jong-tae <qnibus@godo.co.kr>
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class TodayController extends \Controller\Front\Controller
{
    public function index()
    {
        $arrTodayGoodsNo = json_decode(Cookie::get('todayGoodsNo'));
        
        if (is_null($this->getData('soldoutDisplay'))) {
            $this->setData('soldoutDisplay', gd_policy('soldout.pc'));
        }

        if (is_null($cateInfo)) {
            $cateInfo = [
                'lineCnt' => '4',
                'rowCnt' => '2',
                'iconFl' => 'y',
                'soldOutIconFl' => 'y',
                'displayField' => [
                    'img',
                    'brandCd',
                    'makerNm',
                    'goodsNm',
                    'fixedPrice',
                    'goodsPrice',
                    'coupon',
                    'mileage',
                    'shortDescription',
                ],
            ];
        }
        $goodsNoData = implode(INT_DIVISION,$arrTodayGoodsNo);
        
        $orderBy = "FIELD(g.goodsNo," . str_replace(INT_DIVISION, ",", $goodsNoData) . ")";
        
        $goods = \App::load('\\Component\\Goods\\Goods');
        
        $goods->setThemeConfig($cateInfo);
        
        $goodsData = $goods->getGoodsSearchList(8, $orderBy, 'list', $optionFl , $soldOutFl , $brandFl, $couponPriceFl ,8,$brandDisplayFl, true, null, $arrTodayGoodsNo);
        
        if($goodsData['listData']) $goodsList = array_chunk($goodsData['listData'],$cateInfo['lineCnt']);
        
        $typeClass = gd_isset($cateInfo['displayType'],'01');
        
        
        if (in_array('goodsDcPrice', $cateInfo['displayField'])) {
            foreach ($goodsList as $key => $val) {
                foreach ($val as $key2 => $val2) {
                    $goodsList[$key][$key2]['goodsDcPrice'] = $goods->getGoodsDcPrice($val2);
                }
            }
        }
        
        $page = \App::load('\\Component\\Page\\Page'); // 페이지 재설정
       
        $this->setData('goodsList', $goodsList);
        $this->setData('themeInfo', $cateInfo);
        $this->setData('mainData', ['sno'=>'widget']);
        $this->setData('page', gd_isset($page));
        $this->getView()->setPageName('mypage/today');
        $this->getView()->setDefine('goodsTemplate', 'goods/list/list_' . $typeClass . '.html');
    }
}

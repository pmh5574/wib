<?php

namespace Controller\Admin\Goods;

use Exception;
use Framework\Utility\ArrayUtils;
use Globals;
use Request;

class GoodsDragSortBrandController extends \Bundle\Controller\Admin\Goods\GoodsSortBrandController
{
    public function index()
    {
        // --- 메뉴 설정
        $this->callMenu('goods', 'display', 'drag_brand');
        
        // --- 모듈 호출
        $cate = \App::load('\\Component\\Category\\BrandAdmin');
        $display = \App::load('\\Component\\Display\\DisplayConfigAdmin');
        $goods = \App::load('\\Component\\Goods\\GoodsAdmin');

        // --- 상품 데이터
        try {

            $cateInfo = array();

            $getValue = Request::get()->toArray();
            $cateGoods = ArrayUtils::last(gd_isset($getValue['cateGoods']));

            if($cateGoods) {
                list($cateInfo)  = $cate->getCategoryData($cateGoods);
                $cateInfo['pcThemeInfo'] = $display->getInfoThemeConfig($cateInfo['pcThemeCd']);
                $cateInfo['mobileThemeInfo'] = $display->getInfoThemeConfig($cateInfo['mobileThemeCd']);
                $sortTypeNmArray = $display->goodsSortList;
                $sortTypeNmArray['g.regDt desc'] = '최근 등록 상품 위로';
                $cateInfo['sortTypeNm'] = $sortTypeNmArray[$cateInfo['sortType']];
                if($cateInfo['sortAutoFl'] =='y') Request::get()->set('sort',$cateInfo['sortType']);
            }
            $navi = $display->getDateNaviDisplay();
            $getData = $goods->getAdminListSort('brand', true, $navi['data']['brand']['linkUse']);

            $page = \App::load('\\Component\\Page\\Page'); // 페이지 재설정

        } catch (Exception $e) {
            throw $e;
        }


        $this->getView()->setDefine('layoutContent', Request::getDirectoryUri() . '/goods_drag_sort.php');

        $this->addCss([
            'goodsChoiceStyle.css?'.time(),
        ]);
        $this->addScript([
            'jquery/jquery.multi_select_box.js',
            'goodsChoice.js?'.time(),
        ]);

        $this->setData('cateMode', 'brand');
        $this->setData('goods', $goods);
        $this->setData('cate', $cate);
        $this->setData('cateInfo', $cateInfo);
        $this->setData('data', $getData['data']);
        $this->setData('search', $getData['search']);
        $this->setData('fixCount', $getData['fixCount']);
        $this->setData('page', $page);
    }
}

<?php
namespace Controller\Admin\Share;

use Exception;
use Framework\Utility\ArrayUtils;
use Globals;
use Request;

class LayerDragAndDropController extends \Controller\Admin\Controller
{
    public function index()
    {

        // --- 모듈 호출
        $cate = \App::load('\\Component\\Category\\CategoryAdmin');
        $display = \App::load('\\Component\\Display\\DisplayConfigAdmin');
        $goods = \App::load('\\Component\\Goods\\GoodsAdmin');

        // --- 상품 데이터
        try {

            $cateInfo = array();

            $getValue = Request::get()->toArray();
            $cateGoods = ArrayUtils::last(gd_isset($getValue['cateGoods']));
//            print_r($getValue['cateCd']);

            if($cateGoods) {
                
                list($cateInfo)  = $cate->getCategoryData($cateGoods);
                $cateInfo['pcThemeInfo'] = $display->getInfoThemeConfig($cateInfo['pcThemeCd']);
                $cateInfo['mobileThemeInfo'] = $display->getInfoThemeConfig($cateInfo['mobileThemeCd']);
                $sortTypeNmArray = $display->goodsSortList;
//                print_r($sortTypeNmArray);
                $sortTypeNmArray['g.regDt desc'] = '최근 등록 상품 위로';
                $cateInfo['sortTypeNm'] = $sortTypeNmArray[$cateInfo['sortType']];
                if($cateInfo['sortAutoFl'] =='y') Request::get()->set('sort',$cateInfo['sortType']);
            }

            $getData = $goods->getAdminListSort('category', false);
            
//            gd_debug($getData);

            $page = \App::load('\\Component\\Page\\Page'); // 페이지 재설정

        } catch (Exception $e) {
            throw $e;
        }
        
        $this->getView()->setDefine('layout', 'layout_blank.php');
        
        $this->setData('cateMode', $getValue['cateType']);
        $this->setData('cateCd', $cateGoods);
        $this->setData('goods', $goods);
        $this->setData('cate', $cate);
        $this->setData('cateInfo', $cateInfo);
        $this->setData('data', $getData['data']);
        $this->setData('search', $getData['search']);
        $this->setData('fixCount', $getData['fixCount']);
        $this->setData('page', $page);
    }
}
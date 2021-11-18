<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Admin\Partners;

use App;
use Exception;
use Component\Category\CategoryAdmin;
use Component\Category\BrandAdmin;
use Globals;
use Request;

class GoodsListController extends \Controller\Admin\Controller
{
    public function index()
    {
        // --- 메뉴 설정
        $this->callMenu('partners', 'partners', 'goods_list');
        
        // 모듈호출
        $cate = \App::load('\\Component\\Category\\CategoryAdmin');
        $brand = \App::load('\\Component\\Category\\BrandAdmin');
        $goods = \App::load('\\Component\\Goods\\GoodsAdmin');

        // --- 상품 리스트 데이터
        try {

            /* 운영자별 검색 설정값 */
            $searchConf = \App::load('\\Component\\Member\\ManagerSearchConfig');
            $searchConf->setGetData();

            //검색 - 배송비관련
            $mode['fix'] = [
                'free'   => __('배송비무료'),
                'price'  => __('금액별배송'),
                'count'  => __('수량별배송'),
                'weight' => __('무게별배송'),
                'fixed'  => __('고정배송비'),
            ];
            //검색 - 아이콘 관련
            $getIcon = $goods->getManageGoodsIconInfo();

            $getData = $goods->getAdminListGoods();
            
            $getData['goodsGridConfigList'] = [
                'check' => '선택',
                'no' => '번호',
                'a' => '상태',
                'b' => '업체명',
                'aa' => '스타일 코드',
                'ab' => '원단 코드',
                'ac' => '색상 코드',
                'goodsImage' => '이미지',
                'goodsNo' => '상품 번호',
                'c' => '등록 상품 번호',
                'd' => '상품 코드',
                'goodsNm' => '상품명',
                'option' => '옵션',
                'e' => '수량',
                'f' => '협력사 상품 번호',
                'cateCd' => '카테고리',
                'fa' => '원산지',
                'fb' => '구성',
                'g' => '화폐',
                'h' => '공급가(외화)',
                'i' => '공급가(KRW)',
                'ia' => '협력사 판매금액',
                'j' => '공급사 할인율',
                'regDt' => '등록일/수정일',
                'k' => '비고'
            ];

            $page = \App::load('\\Component\\Page\\Page'); // 페이지 재설정

            $this->setData('stateCount', $getData['stateCount']); // 상품 품절, 노출 개수
            //상품 그리드 설정
            $goodsAdminGrid = \App::load('\\Component\\Goods\\GoodsAdminGrid');
            $goodsAdminGridMode = $goodsAdminGrid->getGoodsAdminGridMode();
            $this->setData('goodsAdminGridMode', $goodsAdminGridMode);
            $this->setData('goodsGridConfigList', $getData['goodsGridConfigList']); // 상품 그리드 항목

            if(!gd_is_provider()) {
                $goodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');
                $goodsBenefitSelect = $goodsBenefit->goodsBenefitSelect($getData['search']);
            }

            // --- 관리자 디자인 템플릿

            $this->getView()->setDefine('goodsSearchFrm',  Request::getDirectoryUri() . '/goods_list_search.php');

            $this->addScript([
                'jquery/jquery.multi_select_box.js',
            ]);
//            print_r($getData['search']);
            $this->setData('goods', $goods);
            $this->setData('cate', $cate);
            $this->setData('brand', $brand);
            $this->setData('data', $getData['data']);
            $this->setData('search', $getData['search']);
            $this->setData('sort', $getData['sort']);
            $this->setData('checked', $getData['checked']);
            $this->setData('selected', $getData['selected']);
            $this->setData('page', $page);
            $this->setData('getIcon', $getIcon);
            $this->setData('mode', $mode);
            $this->setData('_delivery', Globals::get('gDelivery'));
            $this->setData('goodsBenefitSelect', $goodsBenefitSelect);

            if(Request::get()->get('delFl') =='y')  {
                $this->getView()->setPageName('goods/goods_list_delete');
                if(gd_is_provider()) $this->setData('searchConfigButton', 'hide');
            } else {
                $this->getView()->setPageName('partners/goods_list.php');
            }

            // 그리드 항목에 따른 페이지 include  - (인기, 메인, 카테고리 포함일 경우)
            if($getData['goodsGridConfigListDisplayFl'] === true ) { // 추가그리드항목 영역
                $this->getView()->setDefine('goodsListGridAddDisplay', 'goods/layer_goods_list_grid_add.php');// 리스트폼
            }

        } catch (Exception $e) {
            throw $e;
        }
       
    }
}

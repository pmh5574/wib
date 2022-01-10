<?php
namespace Controller\Admin\Share;

use Exception;
use Request;

class LayerBestBrandController extends \Controller\Admin\Controller
{
    public function index()
    {

        /**
         * 레이어 브랜드 등록 페이지
         *
         * [관리자 모드]  레이어 브랜드 등록 페이지
         * @author artherot
         * @version 1.0
         * @since 1.0
         * @copyright ⓒ 2016, NHN godo: Corp.
         */


        //--- 모듈 호출


        //--- 카테고리 설정
        $brand = \App::load('\\Component\\Category\\BrandAdmin');
        $getValue = Request::get()->toArray();

        //--- 상품 데이터
        try {

            $getData = $brand->getAdminSeachCategory('layer', 10);
            $page = \App::load('\\Component\\Page\\Page');    // 페이지 재설정

            //--- 관리자 디자인 템플릿
            $this->getView()->setDefine('layout', 'layout_layer.php');

            $this->setData('layerFormID', $getValue['layerFormID']);
            $this->setData('parentFormID', $getValue['parentFormID']);
            $this->setData('dataFormID', $getValue['dataFormID']);
            $this->setData('dataInputNm', $getValue['dataInputNm']);
            $this->setData('mode', gd_isset($getValue['mode'],'search'));
            $this->setData('callFunc', gd_isset($getValue['callFunc'],''));
            $this->setData('childRow',gd_isset($getValue['childRow']));


            $this->setData('brand', $brand);
            $this->setData('data', gd_isset($getData['data']));
            $this->setData('search', gd_isset($getData['search']));
            $this->setData('useMallList', gd_isset($getData['useMallList']));
            $this->setData('page', $page);

            // 공급사와 동일한 페이지 사용
            $this->getView()->setPageName('share/layer_best_brand.php');

        } catch (Exception $e) {
            throw $e;
        }

    }
}
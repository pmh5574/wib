<?php

namespace Controller\Front;

use Request;
use Session;
/**
 * Class pro 사용자들이 모든 컨트롤러에 공통으로 사용할 수 있는 컨트롤러
 * 컨트롤러에서 제공하는 메소드들을 사용할 수 있습니다. http://doc.godomall5.godomall.com/Godomall5_Pro_Guide/Controller
 */
class CommonController 
{
    /**
     *  카테고리 코드 세션값에 세팅
     *  index = 블루
     *  index_pre = 프리미엄샵
     *  index_new = 뉴니아
     */
    public function index($controller) 
    {

        $getURI = Request::server()->get('REQUEST_URI');

        //goods_list나 goods_view에 해당하는 얘들 앞에 주소값만 가져 가려고
        if (strpos($getURI, '?') !== false) {
            $tmp = explode('?', $getURI);
            $getURI = $tmp[0];
        }

        $pageName = $this->_setPageName($getURI);
        
        $shopNum = Session::get('WIB_SHOP_NUM');
        
        $goodsNo = Request::get()->get('goodsNo');

        switch ($pageName) {
            case '/main/index':
                try {

                    $shopNum = "1";
                    Session::del('WIB_SHOP_NUM');
                    Session::set('WIB_SHOP_NUM', $shopNum);
                    
                } catch (\Exception $e) {
                    echo json_encode($e);
                }
                break;
            case '/main/index_pre':
                try {

                    $shopNum = "2";
                    Session::del('WIB_SHOP_NUM');
                    Session::set('WIB_SHOP_NUM', $shopNum);
                    
                } catch (\Exception $e) {
                    echo json_encode($e);
                }
                break;
            case '/main/index_new':
                try {

                    $shopNum = "3";
                    Session::del('WIB_SHOP_NUM');
                    Session::set('WIB_SHOP_NUM', $shopNum);
                    
                } catch (\Exception $e) {
                    echo json_encode($e);
                }
                break;
            case '/goods/goods_view':
                try {

                    $goods = \App::load('\\Component\\Wib\\WibGoods');
                    $shopSetting = $goods->getShopNo($goodsNo);
                    if(!$shopSetting){
                        $shopSetting = '1';
                    }
                    Session::del('WIB_CATE');
                    Session::set('WIB_CATE', $shopSetting);
                    
                } catch (\Exception $e) {
                    echo json_encode($e);
                }
                break;
            case '/goods/goods_search':
                try {
                    
                    $mallKey = Request::get()->get('mallKey');
                    
                    if($mallKey && $mallKey == '1'){
                        $shopNum = "1";
                        Session::del('WIB_SHOP_NUM');
                        Session::set('WIB_SHOP_NUM', $shopNum);
                    }else if($mallKey && $mallKey == '2'){
                        $shopNum = "2";
                        Session::del('WIB_SHOP_NUM');
                        Session::set('WIB_SHOP_NUM', $shopNum);
                    }else if($mallKey && $mallKey == '3'){
                        $shopNum = "3";
                        Session::del('WIB_SHOP_NUM');
                        Session::set('WIB_SHOP_NUM', $shopNum);
                    }
                    
                } catch (\Exception $e) {
                    echo json_encode($e);
                }
                break;
            default :
                if ($getURI == "/") {
                    try {

                        $shopNum = "1";
                        Session::del('WIB_SHOP_NUM');
                        Session::set('WIB_SHOP_NUM', $shopNum);
                        
                    } catch (\Exception $e) {
                        echo json_encode($e);
                    }
                }
                break;
        }
        
        $controller->setData('shopNum', $shopNum);
    }

    private function _setPageName($pageName) {
        // . 을 기준으로 배열
        $parts = explode('.', $pageName);

        if (count($parts) <= 1) {
            return $parts[0];
        }

        // 마지막 배열 버림
        array_pop($parts);

        return implode('.', $parts);
    }
}

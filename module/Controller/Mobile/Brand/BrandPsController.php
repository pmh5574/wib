<?php

namespace Controller\Mobile\Brand;

use Request;
use Component\Wib\WibBrand;
use Framework\Debug\Exception\AlertRedirectException;
use Session;

class BrandPsController extends \Controller\Mobile\Controller
{
    public function index()
    {
        $wibBrand = new WibBrand();
        $postValue = Request::post()->toArray();
        
        if (!Request::isAjax()) {
            throw new AlertRedirectException('ajax 전용 페이지 입니다.', 0, null, '/');
        }

        switch ($postValue['mode']){
            case 'brandLike':
                $result = $wibBrand->setBrandLike(Session::get('member.memNo'), $postValue['brandCd']);
                $this->json([
                   'code' => $result 
                ]);
                break;
            case 'get_brand':
                try {

                    $cateNm = $postValue['brand'];
                    $search = $postValue['search'];
                    $getData = $wibBrand->getBrandCodeInfo(null, 4, $cateNm, false, null, $search);

                    echo json_encode($getData);
                    exit;
                } catch (Exception $e) {
                    echo json_encode(array('message' => $e->getMessage()));
                }
                break;
            default :
                break;
        }
        
        exit();
    }
}


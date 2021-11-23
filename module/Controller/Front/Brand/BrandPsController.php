<?php

namespace Controller\Front\Brand;

use Request;
use Component\Wib\WibBrand;
use Framework\Debug\Exception\AlertRedirectException;
use Session;

class BrandPsController extends \Controller\Front\Controller
{
    public function index()
    {
        $wibBrand = new WibBrand();
        $postValue = Request::post()->toArray();
        
        if (!Request::isAjax()) {
            throw new AlertRedirectException('ajax 전용 페이지 입니다.', 0, null, '/');
            exit();
        }

        switch ($postValue['mode']){
            case 'brandLike':
                $wibBrand->setBrandLike(Session::get('member.memNo'), $postValue['brandCd']);
                break;
            default :
                break;
        }
        
        exit();
    }
}


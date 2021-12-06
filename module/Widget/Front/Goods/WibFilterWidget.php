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

use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\Framework\Debug\Exception;
use Message;
use Globals;
use Request;
use Cookie;

class WibFilterWidget extends \Widget\Front\Widget
{
    
    public function index()
    {
        $getValue = Request::get()->all();
        
        if($getValue['cateCd']){
            $getValue['cateType'] = 'category';
        }else{
            $getValue['cateType'] = 'brand';
        }
        
        $quickConfig = gd_policy('search.quick');

        if(in_array('category',$getValue['cateType']) || in_array('brand',$getValue['cateType'])) {

            if(in_array('category',$quickConfig['searchType'])) {
                $cate = \App::load('\\Component\\Category\\Category');
                $cateDisplay = $cate->getMultiCategoryBox('quickCateGoods',null,null,true);
                $this->setData('cateDisplay', gd_isset($cateDisplay));
            }

            if(in_array('brand',$quickConfig['searchType'])) {
                $brand = \App::load('\\Component\\Category\\Brand');
                $brandDisplay = $brand->getMultiCategoryBox('quickBrandGoods',null,null,true);
                $this->setData('brandDisplay', gd_isset($brandDisplay));
            }
        }
        
        $goods = \App::load('\\Component\\Goods\\Goods');
        $goodsColorList = $goods->getGoodsColorList();
        $this->setData('goodsColorList', gd_isset($goodsColorList));

        $this->setData('quickConfig',gd_isset($quickConfig));
    }
}
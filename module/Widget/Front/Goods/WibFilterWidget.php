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

use Component\Wib\WibGoods;
use Request;


class WibFilterWidget extends \Widget\Front\Widget
{
    
    public function index()
    {
        $wibGoods = new WibGoods();
        
        $getValue = Request::get()->all();
        
        if($getValue['cateCd']){
            $getValue['cateType'] = 'category';
        }else{
            $getValue['cateType'] = 'brand';
        }

        $cateDisplay = $wibGoods->getCategoryList($getValue['cateCd']);

        $this->setData('cateDisplay', gd_isset($cateDisplay['list']));
        $this->setData('cateDisplayNm', gd_isset($cateDisplay['cateNm']));
        $this->setData('cateParentLink', gd_isset($cateDisplay['parentCateCd']));
        
        
        $goods = \App::load('\\Component\\Goods\\Goods');
        $goodsColorList = $goods->getGoodsColorList();
        $this->setData('goodsColorList', gd_isset($goodsColorList));

        $this->setData('getValue', $getValue);
        
    }
}
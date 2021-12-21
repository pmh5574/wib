<?php

namespace Component\Wib;

use Component\Wib\WibSql;
use Session;

class WibWish
{
    public $wibSql;
    
    public function __construct() 
    {
        $this->wibSql = new WibSql();   
    }
    /**
     * 회원번호로 찜 리스트 sno 값 가져오기
     */
    public function getWishList($memNo, $goodsNo)
    {
        
        $query = "SELECT sno FROM es_wish WHERE memNo = '{$memNo}' AND goodsNo = '{$goodsNo}'";
        $sno = $this->wibSql->WibNobind($query)['sno'];
        
        return $sno;
    }
    
    /**
     * 상품 리스트에 위시 정보 추가
     */
    public function getGoodsWishList($goodsList, $memNo)
    {
        foreach ($goodsList as $key => $value) {
            foreach ($value as $k => $val) {
                $sno = $this->getWishList($memNo, $val['goodsNo']);
                if($sno){
                    $goodsList[$key][$k]['wishCheck'] = 'on';
                    $goodsList[$key][$k]['wishSno'] = $sno;
                }else{
                    $goodsList[$key][$k]['wishCheck'] = '';
                }
            }
        }
        
        return $goodsList;
    }
}

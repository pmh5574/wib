<?php

namespace Component\Wib;

use Session;
use Request;

class WibGoods
{
    public $wibSql;
    
    public function __construct() 
    {
        $this->wibSql = new WibSql();
    }
    
    public function getAdminListGoods()
    {
        // --- 검색 설정
        $getValue = Request::post()->toArray();
        
        $arrWhere[] = "g.delFl = 'n' AND g.applyFl = 'y'";
        $arrWhere[] = "gi.imageKind = 'list'";
        $arrWhere[] = "g.".$getValue['key']." LIKE '%".$getValue['keyword']."%'";
        
        $data['result'] = '0';
        $query = "SELECT g.goodsNo, g.goodsNm, g.imagePath, gi.imageName FROM es_goods g LEFT JOIN es_goodsImage gi ON g.goodsNo = gi.goodsNo WHERE ". implode(' AND ', $arrWhere). " ORDER BY goodsNo DESC";
        $data['data'] = $this->wibSql->WibAll($query);
        
        foreach ($data['data'] as $key => $value) {
            $sql = "SELECT cateCd FROM es_goodsLinkCategory WHERE goodsNo = {$value['goodsNo']}";
            $cateCd = $this->wibSql->WibAll($sql);
            $data['data'][$key]['linkCateCd'] = $cateCd;
        }
        
        if(!$data['data'][0]){
            $data['result'] = 1;
        }
        return $data;
        
    }
}


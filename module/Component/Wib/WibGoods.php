<?php

namespace Component\Wib;

use Session;
use Request;
use Component\Wib\WibSql;

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
        $postValue = Request::post()->toArray();
        
        $arrWhere[] = "g.delFl = 'n' AND g.applyFl = 'y'";
        $arrWhere[] = "gi.imageKind = 'list'";
        $arrWhere[] = "g.".$postValue['key']." LIKE '%".$postValue['keyword']."%'";
        if($postValue['sort'] != 'g.goodsNo desc'){
            $sort = $postValue['sort']. ', g.goodsNo DESC';
        }else{
            $sort = $postValue['sort'];
        }
        
        $data['result'] = 0;
        $query = "SELECT g.goodsNo, g.goodsNm, g.imagePath, gi.imageName FROM es_goods g LEFT JOIN es_goodsImage gi ON g.goodsNo = gi.goodsNo WHERE ". implode(' AND ', $arrWhere). " ORDER BY ".$sort;
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
    
    /**
     * 상품리스트 필터 카테고리 리스트 
     * $cateCd 없으면 브랜드에서 전체 카테고리 1, 2뎁스
     */
    public function getCategoryList($cateCd = null)
    {
        
        if(Request::isMobile()){
            $cateDisplayMode = "cateDisplayMobileFl";
        }else{
            $cateDisplayMode = "cateDisplayFl";
        }
        
        $userWhere = " AND {$cateDisplayMode} = 'y' ";
        
        $cateLength = strlen($cateCd);
        
        //첫 뎁스 제외 상위 카테고리
        if($cateLength > 3){
            $data['parentCateCd'] = substr($cateCd, 0, ($cateLength-3));
        }
        
        $parentCateLength = $cateLength+3;
        
        $sql = "SELECT COUNT(*) cnt FROM es_categoryGoods WHERE length(cateCd) = {$parentCateLength} AND cateCd LIKE '{$cateCd}%'";
        $lastDepthCheck = $this->wibSql->WibNobind($sql)['cnt'];
        
        //마지막뎁스 아니면 하위뎁스
        if($lastDepthCheck > 0){
            $cateLength += 3;
            $whereStr = "cateCd LIKE '{$cateCd}%' AND length(cateCd) = '{$cateLength}' AND divisionFl = 'n'" . gd_isset($userWhere);
        }else{
            $lastWhere = " AND cateCd != {$cateCd}";
            $whereStr = "cateCd LIKE '{$data['parentCateCd']}%' AND length(cateCd) = '{$cateLength}' AND divisionFl = 'n'" . gd_isset($userWhere) . gd_isset($lastWhere);
        }
        
        
        
        $query = "SELECT sno, cateNm, cateCd FROM es_categoryGoods WHERE {$whereStr} ORDER BY cateCd";
        $result = $this->wibSql->WibAll($query);
        
        if($cateCd){
            $sql = "SELECT cateNm FROM es_categoryGoods WHERE cateCd = '{$cateCd}'";
            $res = $this->wibSql->WibNobind($sql);
            $data['cateNm'] = $res['cateNm'];
        }
        
        if(count($result) > 0){
            $data['list'] = $result;
        }
        
        return $data;
    }
    
    /**
     * 상품리스트 필터 카테고리 리스트 
     * $cateCd 없으면 브랜드에서 전체 카테고리 1, 2뎁스
     */
    public function getBrandList($brandNm = null, $orderBy)
    {
        if($brandNm){
            $where = " AND cateNm LIKE '%{$brandNm}%' OR cateKrNm LIKE '%{$brandNm}%' ";
        }
        
        $cateNm = 'cateNm ASC';
        if($orderBy){
            $cateNm = 'CASE WHEN cateKrNm Is null Then 1 WHEN cateKrNm = "" Then 1 Else 0 END, cateKrNm ASC, cateNm ASC';
        }
        
        $query = "SELECT sno, cateNm, cateKrNm, cateCd FROM es_categoryBrand WHERE length(cateCd) = 3 {$where} ORDER BY {$cateNm} ";
        $data = $this->wibSql->WibAll($query);
        
        $code = 0;
        
        if(count($data) == 0){
            $code = 1;
        }
        
        return json_encode(['code' => $code,'data' => $data]);
    }
    
    public function setGoodsPmsNna() 
    {
        $query = "SELECT goodsNo, goodsNm, shopSetting FROM es_goods WHERE (shopSetting != '1' AND shopSetting != '2') OR shopSetting Is Null";
        $data = $this->wibSql->WibAll($query);
        
        foreach ($data as $key => $value) {
            if(strpos($value['goodsNm'], 'PMS') !== false){
                unset($sql);
                $sql = [
                    'es_goods',
                    ['shopSetting' => ['2', 's']],
                    ['goodsNo' => [$value['goodsNo'], 'i']]
                ];
                $this->wibSql->WibUpdate($sql);

                unset($sql);
                
                $sql = [
                    'es_goodsSearch',
                    ['shopSetting' => ['2', 's']],
                    ['goodsNo' => [$value['goodsNo'], 'i']]
                ];
                $this->wibSql->WibUpdate($sql);
            }
            
            if(strpos($value['goodsNm'], 'NNA') !== false){
                unset($sql);
                $sql = [
                    'es_goods',
                    ['shopSetting' => ['3', 's']],
                    ['goodsNo' => [$value['goodsNo'], 'i']]
                ];
                
                $this->wibSql->WibUpdate($sql);
                
                unset($sql);
                
                $sql = [
                    'es_goodsSearch',
                    ['shopSetting' => ['3', 's']],
                    ['goodsNo' => [$value['goodsNo'], 'i']]
                ];
                $this->wibSql->WibUpdate($sql);
            }
        }
        
        return count($data);
    }
    
    public function getShopNo($goodsNo)
    {
        $query = "SELECT shopSetting FROM es_goods WHERE goodsNo = {$goodsNo}";
        $data = $this->wibSql->WibNobind($query);
        
        return $data['shopSetting'];
    }
}


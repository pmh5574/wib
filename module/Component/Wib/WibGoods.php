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
     * 
     * @param string $cateCd 카테고리 번호
     * 
     * @return array 카테고리 리스트
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
    
    //카테고리별 가지고 있는 컬러만 노출
    public function getColorList($cateCd, $cateType)
    {
        
        $goodsNoList = [];
        //2차 카테고리 기준
//        $cateCd = substr($cateCd,0,6);
        
        $cbNm = 'Category';
        if($cateType == 'brand'){
            $cbNm = 'Brnad';
        }
        
        //해당 카테고리에 전체에 해당하는 goodsNo를 새롭게 배열로 만듬
        $query = "SELECT goodsNo FROM es_goodsLink{$cbNm} WHERE cateCd = {$cateCd}";
        $result = $this->wibSql->WibAll($query);
        
        

        foreach ($result as $value){
            
            //다시 그 카테고리 전체에 해당하는 goodsNo에 color값을 호출
            $query = "SELECT goodsColor FROM es_goods WHERE goodsNo = {$value['goodsNo']} AND goodsDisplayFl = 'y'";
            $res = $this->wibSql->WibNobind($query);
            
            if(strpos($res['goodsColor'],'^|^') !== false){
                
                //컬러값 여러개면 체크해서 잘라서 넣어주기
                $_color = explode('^|^',$res['goodsColor']);
                foreach ($_color as $val){

                    $goodsNoList[$val] = $val;
                }
                
            }else{
                if($res['goodsColor']){
                    
                    //key값을 color값으로 해서 중복되는것도 한번만 넣어줌
                    $goodsNoList[$res['goodsColor']] = $res['goodsColor'];

                }
            }
            
            
        }
        
        $colorList = [];
        
        //해당 컬러칩을 다시 배열 한개당 하나씩 만듬
        foreach($goodsNoList as $value){
            $colorList[]['code'] = $value;
        }
        
        return $colorList;
    }
    
    /**
     * 상품리스트 필터 카테고리 리스트 
     * $cateCd 없으면 브랜드에서 전체 카테고리 1, 2뎁스
     * 
     * @param string $brandNm 브랜드 이름
     * @param string $orderByCheck 정렬순
     * @param string $cateCd 카테고리 코드
     * 
     * @return json 브랜드 정보
     */
    public function getBrandList($brandNm = null, $orderByCheck = null, $cateCd = null)
    {
        if($brandNm){
            $where = " AND cateNm LIKE '%{$brandNm}%' OR cateKrNm LIKE '%{$brandNm}%' ";
        }
        
        $orderBy = 'cateNm ASC';
        if($orderByCheck){
            $orderBy = 'CASE WHEN cateKrNm Is null Then 1 WHEN cateKrNm = "" Then 1 Else 0 END, cateKrNm ASC, cateNm ASC';
        }
        
        if($cateCd){
            //해당 카테고리에 전체에 해당하는 goodsNo를 새롭게 배열로 만듬
            $sql = "SELECT g.brandCd FROM  es_goods g LEFT JOIN es_goodsLinkCategory glc ON glc.goodsNo = g.goodsNo WHERE glc.cateCd = '{$cateCd}' GROUP BY g.brandCd";
            $result = $this->wibSql->WibAll($sql);
            
            $brandCdArr = [];
            
            foreach ($result as $value) {
                $brandCdArr[] = " cateCd = '{$value['brandCd']}'";
            }
            $brandCdWhere = implode(' OR ', $brandCdArr);
            
            $query = "SELECT sno, cateNm, cateKrNm, cateCd FROM es_categoryBrand WHERE length(cateCd) = 3 AND ({$brandCdWhere}) {$where} ORDER BY {$orderBy} ";
            $data = $this->wibSql->WibAll($query);
            
            $code = 0;

            if(count($data) == 0){
                $code = 1;
            }

            return json_encode(['code' => $code,'data' => $data]);
            
        }else{
            $query = "SELECT sno, cateNm, cateKrNm, cateCd FROM es_categoryBrand WHERE length(cateCd) = 3 {$where} ORDER BY {$orderBy} ";
            $data = $this->wibSql->WibAll($query);

            $code = 0;

            if(count($data) == 0){
                $code = 1;
            }

            return json_encode(['code' => $code,'data' => $data]);
        }
        
        
    }
    
    /**
     * 상품검색페이지 필터 카테고리 리스트 
     * $cateCd 없으면 브랜드에서 전체 카테고리 1, 2뎁스
     * 
     * @param string $orderByCheck 정렬순
     * 
     * @return json 브랜드 정보
     */
    public function getSearchBrandList($orderByCheck = null, $goodsNm)
    {
        $code = 1;
        print_r($goodsNm);
        print_r('qweqeqweqwe');
//        return json_encode(['code' => $code,'data' => $goodsNm]);
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


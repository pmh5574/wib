<?php

namespace Component\Wib;

use Session;
use Request;
use Component\Member\Util\MemberUtil;
use Exception;
use Component\Wib\WibSql;

class WibBrand
{
    public $wibSql;
    
    public function __construct() 
    {
        $this->wibSql = new WibSql();
    }
    
    /*
     * $memNo 회원번호, $brandCd 브랜드 코드
     * 
     */
    public function setBrandLike($memNo, $brandCd)
    {
        //회원번호와 브랜드코드로 이미 좋아요 눌렀는지 체크
        $query = "SELECT sno FROM wib_memberBrand WHERE memberNo = '{$memNo}' AND brandCd = '{$brandCd}'";
        $result = $this->wibSql->WibNobind($query);
        
        // 좋아요 삭제
        if($result['sno']){
            $sql = "DELETE FROM wib_memberBrand WHERE sno = {$result['sno']}";
            $this->wibSql->WibNobind($sql);
            return 'off';
        }else{ // 좋아요
            $data = [
                'wib_memberBrand',
                [
                    'memberNo' => [$memNo, 'i'],
                    'brandCd' => [$brandCd, 's'],
                    'regDt' => [date('Y-m-d H:i:s'), 's']
                ]
            ];
            $this->wibSql->WibInsert($data);
            return 'on';
        }
        
    }
    
    /**
     * 마이페이지 찜 브랜드 리스트
     */
    public function getBrandWishData()
    {
        $getValue = Request::get()->get('page');
        
        $memNo = Session::get('member.memNo');
        
        // 회원 로그인 체크
        if (gd_is_login() === true) {
            $arrWhere[] = "mb.memberNo = '{$memNo}'";
        }

        if(Request::isMobile()){
            $arrWhere[] = "cb.cateDisplayMobileFl = 'y'";
        }else{
            $arrWhere[] = "cb.cateDisplayFl = 'y'";
        }
        
        $getValue['page'] = $getValue['page'] ? $getValue['page'] : 1;
        
        $page = \App::load('\\Component\\Page\\Page', $getValue['page']);
        $page->page['list'] = 4; // 페이지당 리스트 수
        $page->block['cnt'] = Request::isMobile() ? 5 : 10; // 블록당 리스트 개수
        $page->setPage();
        $page->setUrl(\Request::getQueryString());
        
        $query = "
            SELECT 
                cb.cateNm, cb.cateCd, mb.memberNo, cb.smallBrandImg 
            FROM 
                wib_memberBrand mb 
            LEFT JOIN 
                es_categoryBrand cb 
            ON 
                mb.brandCd = cb.cateCd 
            LEFT JOIN 
                es_member m 
            ON 
                mb.memberNo = m.memNo 
            WHERE 
                " . implode(' AND ', $arrWhere) . " 
            ORDER BY mb.regDt DESC 
            LIMIT {$page->recode['start']}, 4";
        $data = $this->wibSql->WibAll($query);
        
        foreach ($data as $key => $value) {
            $sql = "SELECT COUNT(*) cnt FROM wib_memberBrand WHERE brandCd = '{$value['cateCd']}'";
            $res = $this->wibSql->WibNobind($sql);
            
            $data[$key]['likeCnt'] = $res['cnt'];
        }
        
        $query = "
            SELECT 
                COUNT(*) totalCnt
            FROM 
                wib_memberBrand mb 
            LEFT JOIN 
                es_categoryBrand cb 
            ON 
                mb.brandCd = cb.cateCd 
            LEFT JOIN 
                es_member m 
            ON 
                mb.memberNo = m.memNo 
            WHERE 
                " . implode(' AND ', $arrWhere) . " 
            ORDER BY mb.regDt DESC 
            ";
        $dataCount = $this->wibSql->WibNoBind($query);
        
        // 검색 레코드 수
        $page->recode['total'] = $dataCount['totalCnt']; //검색 레코드 수
        $page->setPage();
        
        return $data;
    }
    
    /**
     * 전체 찜 브랜드 리스트
     */
    public function getAllBrandWishData()
    {
        $memNo = Session::get('member.memNo');
        
        // 회원 로그인 체크
        if (gd_is_login() === true) {
            $arrWhere[] = "mb.memberNo = '{$memNo}'";
        }else{
            return false;
        }

        if(Request::isMobile()){
            $arrWhere[] = "cb.cateDisplayMobileFl = 'y'";
        }else{
            $arrWhere[] = "cb.cateDisplayFl = 'y'";
        }
        
        $query = "
            SELECT 
                cb.cateNm, cb.cateCd, mb.memberNo, cb.bigBrandImg, cb.smallBrandImg, cb.whiteBrandImg, cb.blackBrandImg  
            FROM 
                wib_memberBrand mb 
            LEFT JOIN 
                es_categoryBrand cb 
            ON 
                mb.brandCd = cb.cateCd 
            LEFT JOIN 
                es_member m 
            ON 
                mb.memberNo = m.memNo 
            WHERE 
                " . implode(' AND ', $arrWhere) . " 
            ORDER BY mb.regDt DESC 
            ";
        $data = $this->wibSql->WibAll($query);
        
        return $data;
    }
    
    /**
     * 
     * 메인페이지 브랜드 리스트
     */
    public function getBrandData()
    {
        if(Request::isMobile()){
            $arrWhere = "cateDisplayMobileFl = 'y'";
        }else{
            $arrWhere = "cateDisplayFl = 'y'";
        }
        
        $query = "SELECT cateNm, cateCd, sortType, sortAutoFl, cateHtml1, cateHtml1Mobile, cateKrNm, bigBrandImg, whiteBrandImg, blackBrandImg FROM es_categoryBrand WHERE {$arrWhere} AND length(cateCd) = 3 ORDER BY cateSort DESC LIMIT 20";
        $data = $this->wibSql->WibAll($query);
        
        // 회원 로그인 체크
        if (gd_is_login() === true) {
            
            $memNo = Session::get('member.memNo');
            
            $arrWhere = " AND memberNo = '{$memNo}'";
            
            foreach ($data as $key => $value) {
                $sql = "SELECT sno FROM wib_memberBrand WHERE brandCd = '{$value['cateCd']}' {$arrWhere}";
                $sno = $this->wibSql->WibNobind($sql)['sno'];

                if($sno){
                    $data[$key]['brandLike'] = 'on';
                }else{
                    $data[$key]['brandLike'] = 'off';
                }

            }
        }
        
        
        
        return $data;
    }
    
    /**
     * 브랜드 추가 정보
     */
    public function getBrandNm($brandCd)
    {
        
        $query = "SELECT cateNm, cateKrNm, bigBrandImg, smallBrandImg, whiteBrandImg, blackBrandImg FROM es_categoryBrand WHERE cateCd = '{$brandCd}'";
        $brandNm = $this->wibSql->WibNobind($query);
        
        $sno = $this->getWishBrandList($brandCd);
        
        $cnt = $this->getWishBrandCnt($brandCd);
        
        if($sno){
            $brandNm['brandLike'] = 'on';
        }else{
            $brandNm['brandLike'] = 'off';
        }
        
        if($cnt > 0){
            $brandNm['cnt'] = $cnt;
        }
  
        return $brandNm;
    }
    
    /**
     * 찜 브랜드 체크
     */
    public function getWishBrandList($brandCd)
    {
        $memNo = Session::get('member.memNo');
        $where = "";
        
        if($memNo){
            $where = " AND memberNo = '{$memNo}'";
        }else{
            return false;
        }
        $sql = "SELECT sno FROM wib_memberBrand WHERE brandCd = '{$brandCd}' {$where}";
        $sno = $this->wibSql->WibNobind($sql)['sno'];

        return $sno;
    }
    
    /**
     * 찜 브랜드 개수
     */
    public function getWishBrandCnt($brandCd)
    {
        $sql = "SELECT COUNT(*) cnt FROM wib_memberBrand WHERE brandCd = '{$brandCd}'";
        $cnt = $this->wibSql->WibNobind($sql)['cnt'];

        return $cnt;
    }
}


<?php

namespace Component\Wib;

use Session;
use Request;
use Component\Member\Util\MemberUtil;
use Exception;

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
                    'brandCd' => [$brandCd, 's']
                ]
            ];
            $this->wibSql->WibInsert($data);
            return 'on';
        }
        
    }
    
    /**
     * 
     * 마이페이지 위시 브랜드 리스트
     */
    public function getBrandWishData()
    {
        $memNo = Session::get('member.memNo');
        
        // 회원 로그인 체크
        if (gd_is_login() === true) {
            $arrWhere[] = "mb.memberNo = '{$memNo}'";
        } else {
            MemberUtil::logoutGuest();
            $moveUrl = URI_HOME . 'member/login.php?returnUrl=' . urlencode(Request::getReturnUrl());
            throw new AlertRedirectException(null, null, null, $moveUrl);
        }

        if(Request::isMobile()){
            $arrWhere[] = "cb.cateDisplayMobileFl = 'y'";
        }else{
            $arrWhere[] = "cb.cateDisplayFl = 'y'";
        }
        
        $query = "
            SELECT 
                cb.cateNm, cb.cateCd, mb.memberNo
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
            LIMIT 0, 4";
        $data = $this->wibSql->WibAll($query);
        
        foreach ($data as $key => $value) {
            $sql = "SELECT COUNT(*) cnt FROM wib_memberBrand WHERE brandCd = '{$value['cateCd']}'";
            $res = $this->wibSql->WibNobind($sql);
            
            $data[$key]['likeCnt'] = $res['cnt'];
        }
        
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
        
        $query = "SELECT cateNm, cateCd, sortType, sortAutoFl, cateHtml1, cateHtml1Mobile, cateKrNm, bigBrandImg, smallBrandImg, whiteBrandImg, blackBrandImg FROM es_categoryBrand WHERE {$arrWhere} AND length(cateCd) = 3 ORDER BY cateSort ASC";
        $data = $this->wibSql->WibAll($query);
        
        return $data;
    }
    
    
}


<?php

namespace Component\Wib;

use Session;

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
}


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
 * @link      http://www.godo.co.kr
 */
namespace Component\Database;

/**
 * DB Table 기본 Field 클래스 - DB 테이블의 기본 필드를 설정한 클래스 이며, prepare query 생성시 필요한 기본 필드 정보임
 * @package Component\Database
 * @static  tableConfig
 */
class DBTableField extends \Bundle\Component\Database\DBTableField
{
    
    public static function tableScmManage()
    {
        $arrField = parent::tableScmManage();
        
        //211112 디자인위브 mh 협력사 정보 추가
        
        $arrField[] = ['val' => 'scmCountry', 'typ' => 's', 'def' => null]; // 협력사 관부과세
        $arrField[] = ['val' => 'scmVat', 'typ' => 'i', 'def' => 0]; // 협력사 관부과세
        $arrField[] = ['val' => 'scmUrl', 'typ' => 's', 'def' => null]; // 협력사 홈페이지
        $arrField[] = ['val' => 'scmEmail', 'typ' => 's', 'def' => null]; // 협력사 이메일
        $arrField[] = ['val' => 'scmPhone', 'typ' => 's', 'def' => null]; // 협력사 연락처
        $arrField[] = ['val' => 'scmCredit', 'typ' => 'i', 'def' => 0]; // 협력사 크레딧
        $arrField[] = ['val' => 'scmDeposit', 'typ' => 'i', 'def' => 0]; // 협력사 보증금
        $arrField[] = ['val' => 'scmUnit', 'typ' => 's', 'def' => null]; // 협력사 화폐
        $arrField[] = ['val' => 'scmWeeks', 'typ' => 's', 'def' => null]; // 협력사 갱신주기
        $arrField[] = ['val' => 'scmMessenger', 'typ' => 's', 'def' => null]; // 협력사 메신저
        $arrField[] = ['val' => 'scmMessengerId', 'typ' => 's', 'def' => null]; // 협력사 메시전 ID
        $arrField[] = ['val' => 'scmApiStart', 'typ' => 's', 'def' => null]; // 협력사 API 시작 경로
        $arrField[] = ['val' => 'scmApiEnd', 'typ' => 's', 'def' => null]; // 협력사 API 종료 경로
        $arrField[] = ['val' => 'scmGoodsUrl', 'typ' => 's', 'def' => null]; // 협력사 상품 URL 연동
        $arrField[] = ['val' => 'scmGoodsUrlFl', 'typ' => 's', 'def' => null]; // 협력사 URL 연동방식
        $arrField[] = ['val' => 'scmImageUrl', 'typ' => 's', 'def' => null]; // 협력사 이미지 연동
        $arrField[] = ['val' => 'scmImageUrlFl', 'typ' => 's', 'def' => null]; // 협력사 이미지 연동 방식
        
        return $arrField;
    }
    
    public static function tableCategoryBrand()
    {
        $arrField = parent::tableCategoryBrand();
        
        //211206 디자인위브 mh 브랜드 카테고리 추가
        
        $arrField[] = ['val' => 'cateKrNm', 'typ' => 's', 'def' => null]; // 한글 브랜드명
        $arrField[] = ['val' => 'bigBrandImg', 'typ' => 's', 'def' => null]; // 큰 브랜드 이미지(pc)
        $arrField[] = ['val' => 'smallBrandImg', 'typ' => 's', 'def' => null]; // 작은 브랜드 이미지(pc)
        $arrField[] = ['val' => 'commonBrandImgMo', 'typ' => 's', 'def' => null]; // 브랜드 모바일 이미지
        $arrField[] = ['val' => 'whiteBrandImg', 'typ' => 's', 'def' => null]; // 화이트 로고 이미지
        $arrField[] = ['val' => 'blackBrandImg', 'typ' => 's', 'def' => null]; // 블랙 로고 이미지
        
        return $arrField;
    }
    
    public static function tableGoods($conf = null)
    {
        $arrField = parent::tableGoods($conf);
        
        // 211216 디자인위브 mh 추가
//        $arrField[] = ['val' => 'shopSetting', 'typ' => 's', 'def' => null,]; //상점 구분 2:프리미엄샵 3:뉴니아
//        $arrField[] = ['val' => 'shopSettingFl', 'typ' => 's', 'def' => 'n',]; //상점 구분 사용 여부
//        $arrField[] = ['val' => 'partnersGoodsNo', 'typ' => 's', 'def' => null]; // 협력사 상품 번호
//        $arrField[] = ['val' => 'partnersCategory', 'typ' => 's', 'def' => null]; // API 카테고리
//        $arrField[] = ['val' => 'partnersFixedPrice', 'typ' => 's', 'def' => null]; // 공급가(외화)
//        $arrField[] = ['val' => 'partnersGoodsPrice', 'typ' => 's', 'def' => null]; // 협력사 판매금액
//        $arrField[] = ['val' => 'partnersExchangeSno', 'typ' => 's', 'def' => null]; // 환율 정보 sno
//        $arrField[] = ['val' => 'partnersExchangeRate', 'typ' => 's', 'def' => null]; // 적용 환율
//        $arrField[] = ['val' => 'partnersExchangeRateDt', 'typ' => 's', 'def' => null]; // 적용 환율 기준일
//        $arrField[] = ['val' => 'modDtFl', 'typ' => 's', 'def' => null]; // 수정 확인 여부
        
        return $arrField;
    }
    public static function tableGoodsSearch()
    {
        $arrField = parent::tableGoodsSearch();
        
        // 211216 디자인위브 mh 추가
//        $arrField[] = ['val' => 'shopSetting', 'typ' => 's', 'def' => null,]; //상점 구분 2:프리미엄샵 3:뉴니아
//        $arrField[] = ['val' => 'shopSettingFl', 'typ' => 's', 'def' => 'n',]; //상점 구분 사용 여부
//        $arrField[] = ['val' => 'partnersGoodsNo', 'typ' => 's', 'def' => null]; // 협력사 상품 번호
//        $arrField[] = ['val' => 'partnersCategory', 'typ' => 's', 'def' => null]; // API 카테고리
//        $arrField[] = ['val' => 'partnersFixedPrice', 'typ' => 's', 'def' => null]; // 공급가(외화)
//        $arrField[] = ['val' => 'partnersGoodsPrice', 'typ' => 's', 'def' => null]; // 협력사 판매금액
//        $arrField[] = ['val' => 'partnersExchangeSno', 'typ' => 's', 'def' => null]; // 환율 정보 sno
//        $arrField[] = ['val' => 'partnersExchangeRate', 'typ' => 's', 'def' => null]; // 적용 환율
//        $arrField[] = ['val' => 'partnersExchangeRateDt', 'typ' => 's', 'def' => null]; // 적용 환율 기준일
//        $arrField[] = ['val' => 'modDtFl', 'typ' => 's', 'def' => null]; // 수정 확인 여부
        
        return $arrField;
    }
}
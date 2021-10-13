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
namespace Component\Goods;

use Component\PlusShop\PlusReview\PlusReviewConfig;
use Component\Database\DBTableField;
use Component\ExchangeRate\ExchangeRate;
use Component\Member\Group\Util;
use Component\Validator\Validator;
use Cookie;
use Exception;
use Framework\Utility\ArrayUtils;
use Framework\Utility\SkinUtils;
use Framework\Utility\StringUtils;
use Globals;
use Request;
use Session;
use UserFilePath;
use Framework\Utility\DateTimeUtils;

/**
 * 상품 class
 */
class Goods extends \Bundle\Component\Goods\Goods
{
    /**
     * 상품 정보 출력 (상품 리스트)
     *
     * @param string $cateCd 카테고리 코드
     * @param string $cateMode 카테고리 모드 (category, brand)
     * @param int $pageNum 페이지 당 리스트수 (default 10)
     * @param string $displayOrder 상품 기본 정렬 - 'sort asc', Category::getSort() 참고
     * @param string $imageType 이미지 타입 - 기본 'main'
     * @param boolean $optionFl 옵션 출력 여부 - true or false (기본 false)
     * @param boolean $soldOutFl 품절상품 출력 여부 - true or false (기본 true)
     * @param boolean $brandFl 브랜드 출력 여부 - true or false (기본 true)
     * @param boolean $couponPriceFl 쿠폰가격 출력 여부 - true or false (기본 false)
     * @param integer $imageViewSize 이미지 크기 (기본 "0" - 0은 원래 크기)
     * @param integer $displayCnt 상품 출력 갯수 - 기본 10개
     * @return array 상품 정보
     * @throws Exception
     */
    public function getBrandGoodsList($cateCd, $brandCateCd, $cateMode = 'category', $pageNum = 10, $displayOrder = 'sort asc', $imageType = 'list', $optionFl = false, $soldOutFl = true, $brandFl = false, $couponPriceFl = false, $imageViewSize = 0, $displayCnt = 10)
    {

        $delivery = \App::load('\\Component\\Delivery\\Delivery');

        $display = \App::load('\\Component\\Display\\DisplayConfigAdmin');
        $displayNavi = $display->getDateNaviDisplay();

        gd_isset($this->goodsTable,DB_GOODS);
        $mallBySession = SESSION::get(SESSION_GLOBAL_MALL);
        // Validation - 상품 코드 체크
        if (Validator::required($cateCd, true) === false) {
            throw new Exception(self::ERROR_VIEW . self::TEXT_NOT_EXIST_CATECD);
        }

        $getValue = Request::get()->toArray();

        // --- 정렬 설정
        if (gd_isset($getValue['sort'])) {
            $dSort = $getValue['sort'];
            if (method_exists($this, 'getSortMatch')) {
                $dSort = $this->getSortMatch($dSort);
            }

            // 품절상품 정렬 추가 (정렬시 최우선)
            if (strpos($dSort, "soldOut asc") === false) {
                $dSort = 'soldOut asc, '.$dSort;
            }

            $sort[] = $dSort;
        } else {

            if ($displayOrder) {
                if (is_array($displayOrder)) $sort[] = implode(",", $displayOrder);
                else $sort[] = $displayOrder;

            } else {
                $sort[] = "gl.goodsSort desc";
            }
        }

        $sort = implode(',', $sort);
        if($sort) {
            if(strpos($sort, "regDt") !== false) $sort = str_replace("regDt","goodsNo",$sort);
            if(strpos($sort, "goodsNo") === false) $sort = $sort.', goodsNo desc ';
        }

        if(strpos($displayOrder, "soldOut") !== false) $addField = ",( if (g.soldOutFl = 'y' , 'y', if (g.stockFl = 'y' AND g.totalStock <= 0, 'y', 'n') ) ) as soldOut";

        // --- 페이지 기본설정
        gd_isset($getValue['page'], 1);

        // 배수 설정
        $getData['multiple'] = range($displayCnt, $displayCnt * 4, $displayCnt);

        $page = \App::load('\\Component\\Page\\Page', $getValue['page']);
        $page->page['list'] = $pageNum; // 페이지당 리스트 수
        $page->block['cnt'] = Request::isMobile() ? 5 : 10; // 블록당 리스트 개수
        $page->setPage();
        $page->setUrl(\Request::getQueryString());

        // 카테고리 종류에 따른 설정
        if ($cateMode == 'category') {
            $dbTable = DB_GOODS_LINK_CATEGORY;
            $viewName="goods";
        } else {
            $dbTable = DB_GOODS_LINK_BRAND;
            $viewName="brand";
        }

        // 조인 설정
        $arrJoin[] = ' INNER JOIN '.$this->goodsTable.' g ON gl.goodsNo = g.goodsNo ';

        // 조건절 설정
        $this->db->bind_param_push($this->arrBind, 's', $cateCd);
        $this->arrWhere[] = 'gl.cateCd = ?';
        if ($cateMode == 'category' || ($cateMode == 'brand' && $displayNavi['data']['brand']['linkUse'] != 'y')) {
            $this->arrWhere[] = 'gl.cateLinkFl = \'y\'';
        }
        $this->arrWhere[] = 'g.delFl = \'n\'';
        $this->arrWhere[] = 'g.applyFl = \'y\'';
        $this->arrWhere[] = 'g.' . $this->goodsDisplayFl . ' = \'y\'';
        $this->arrWhere[] = '(UNIX_TIMESTAMP(g.goodsOpenDt) IS NULL  OR UNIX_TIMESTAMP(g.goodsOpenDt) = 0 OR UNIX_TIMESTAMP(g.goodsOpenDt) < UNIX_TIMESTAMP())';

        //접근권한 체크
        if (gd_check_login()) {
            $this->arrWhere[] = '(g.goodsAccess !=\'group\'  OR (g.goodsAccess=\'group\' AND FIND_IN_SET(\''.Session::get('member.groupSno').'\', REPLACE(g.goodsAccessGroup,"'.INT_DIVISION.'",","))) OR (g.goodsAccess=\'group\' AND !FIND_IN_SET(\''.Session::get('member.groupSno').'\', REPLACE(g.goodsAccessGroup,"'.INT_DIVISION.'",",")) AND g.goodsAccessDisplayFl =\'y\'))';
        } else {
            $this->arrWhere[] = '(g.goodsAccess=\'all\' OR (g.goodsAccess !=\'all\' AND g.goodsAccessDisplayFl =\'y\'))';
        }

        //성인인증안된경우 노출체크 상품은 노출함
        if (gd_check_adult() === false) {
            $this->arrWhere[] = '(onlyAdultFl = \'n\' OR (onlyAdultFl = \'y\' AND onlyAdultDisplayFl = \'y\'))';
        }

        if ($soldOutFl === false) { // 품절 처리 여부
            $this->arrWhere[] = 'NOT(g.stockFl = \'y\' AND g.totalStock = 0) AND NOT(g.soldOutFl = \'y\')';
        }

        // 필드 설정
        $this->setGoodsListField(); // 상품 리스트용 필드
        if($this->goodsDivisionFl) {
            $this->db->strField = 'STRAIGHT_JOIN g.goodsNo'.gd_isset($addField);
        } else {
            $this->db->strField = 'STRAIGHT_JOIN ' . $this->goodsListField . gd_isset($addField);
        }

        $this->db->strJoin = implode('', $arrJoin);
        $this->db->strWhere = implode(' AND ', gd_isset($this->arrWhere));
        $this->db->strOrder = $sort;
        $this->db->strLimit = $page->recode['start'] . ',' . $pageNum;

        $query = $this->db->query_complete();
		if($brandCateCd){
			$strSQL = ' SELECT goodsNo, soldOutFl as soldOut FROM es_goods WHERE brandCd = '.$cateCd.' AND cateCd LIKE "'.$brandCateCd.'%"';
		} else {
			$strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . $dbTable . ' gl ' . implode(' ', $query);
		}

        $data = $this->db->slave()->query_fetch($strSQL, $this->arrBind);

        if($data) {
            if($this->goodsDivisionFl) {
                /* 상품 테이블에서 정보 가져옴 */
                $strGoodsSQL = 'SELECT ' . $this->goodsListField . ' FROM ' . DB_GOODS . ' g WHERE goodsNo IN ("' . implode('","', array_column($data, 'goodsNo')) . '") ORDER BY FIELD(g.goodsNo,' . implode(',', array_column($data, 'goodsNo')) . ')';
                $data = $this->db->query_fetch($strGoodsSQL);
            }

            /* 검색 count 쿼리 */
            $totalCountSQL =  ' SELECT COUNT(gl.goodsNo) AS totalCnt FROM ' . $dbTable . ' as gl '.implode('', $arrJoin).'  WHERE '.implode(' AND ', $this->arrWhere);
            $dataCount = $this->db->slave()->query_fetch($totalCountSQL, $this->arrBind,false);
            unset($this->arrBind, $this->arrWhere);

            // 검색 레코드 수
            $page->recode['total'] = $dataCount['totalCnt']; //검색 레코드 수
            $page->setPage();

            // 상품 정보 세팅
            if (empty($data) === false) {
                $this->setGoodsListInfo($data, $imageType, $optionFl, $couponPriceFl, $imageViewSize,$viewName,$brandFl);
            }
        }

        unset($this->arrBind, $this->arrWhere);

        // 배송비 유형 설정
        //        foreach($data as $key => $goodInfo) {
        //            $goodsView = $this->getGoodsView($goodInfo['goodsNo']);
        //            $deliveryInfo = $delivery->getDeliveryType($goodsView['deliverySno']);
        //            $data[$key]['deliveryType'] = $deliveryInfo['deliveryType'];
        //            $data[$key]['deliveryMethod'] = $deliveryInfo['method'];
        //            $data[$key]['deliveryDes'] = $deliveryInfo['description'];
        //        }

        // 각 데이터 배열화
        $getData['listData'] = gd_htmlspecialchars_stripslashes(gd_isset($data));
        $getData['listSort'] = $displayOrder;
        $getData['listSearch'] = gd_htmlspecialchars($this->search);
        unset($this->search);
        return $getData;
    }
}
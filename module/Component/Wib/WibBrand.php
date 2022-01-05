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
    protected $db;
    protected $cateLength;                // 카테고리 기본 노드당 길이
    protected $cateTable;                    // 카테고리 기본 테이블명
    
    public function __construct() 
    {
        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
        
        $this->cateLength = DEFAULT_LENGTH_BRAND;
        $this->cateTable = DB_CATEGORY_BRAND;
        
        $this->wibSql = new WibSql();
    }
    
    /**
     * 카테고리 정보
     *
     * @param string  $cateCd     카테고리 코드
     * @param integer $depth      출력 depth
     * @param boolean $cateNm     카테고리 이름
     * @param boolean $tree       배열형태 출력 여부
     * @param boolean $orderBy    order by 조건절 추가
     * @param boolean $search     조건절 추가
     *
     * @return string 카테고리 정보
     */
    public function getBrandCodeInfo($cateCd = null,$depth = 4,$cateNm = null, $tree = true, $orderBy = null, $search = false)
    {
        $mallBySession = SESSION::get(SESSION_GLOBAL_MALL);
        gd_isset($mallBySession['sno'],DEFAULT_MALL_NUMBER);

        $arrWhere = $arrBind = [];
        $arrCateCd = null;

        if (Request::isMobile()) {
            $displayField = "cateDisplayMobileFl";
        } else {
            $displayField = "cateDisplayFl";
        }

        $arrWhere[] = "FIND_IN_SET(".$mallBySession['sno'].",mallDisplay) AND ".$displayField." = 'y'";
        $globalMall = false;
        if($mallBySession['sno'] == '2') { //영문몰 체크
            $arrWhere[] = "g.mallSno = '" . $mallBySession['sno'] . "'";
            $globalMall = true;
        }
        if($cateNm) {
            
            if($search) {
                if($globalMall === true) {
                    $arrWhere[] = '(g.cateNm LIKE concat(?,\'%\') or g.cateKrNm LIKE concat(?,\'%\'))';
                    $this->db->bind_param_push($arrBind, 's', strtolower($cateNm));
                    $this->db->bind_param_push($arrBind, 's', strtolower($cateNm));
                }
                else{
                    $arrWhere[] = '(cateNm LIKE concat(\'%\',?,\'%\') or cateKrNm LIKE concat(\'%\',?,\'%\'))';
                    $this->db->bind_param_push($arrBind, 's', strtolower($cateNm));
                    $this->db->bind_param_push($arrBind, 's', strtolower($cateNm));
                }
            } else {
                
                if (preg_match("/[\xA1-\xFE\xA1-\xFE]/", $cateNm)) {
                    if ($globalMall === true) {
                        $searchCateNm = 'g.cateKrNm';
                    }
                    else {
                        $searchCateNm = 'cateKrNm';
                    }
                    
                    if($orderBy == null && $globalMall !== true){
                        $orderBy = 'cateKrNm ASC';
                    }
                    switch ($cateNm)    //TODO:GLOBAL 초성검색
                    {
                        case 'ㄱ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^(ㄱ|ㄲ)' OR ( ".$searchCateNm." >= '가' AND ".$searchCateNm." < '나' )) ";
                            break;
                        case 'ㄴ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^ㄴ' OR ( ".$searchCateNm." >= '나' AND ".$searchCateNm." < '다' )) ";
                            break;
                        case 'ㄷ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^(ㄷ|ㄸ)' OR ( ".$searchCateNm." >= '다' AND ".$searchCateNm." < '라' )) ";
                            break;
                        case 'ㄹ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^ㄹ' OR ( ".$searchCateNm." >= '라' AND ".$searchCateNm." < '마' )) ";
                            break;
                        case 'ㅁ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^ㅁ' OR ( ".$searchCateNm." >= '마' AND ".$searchCateNm." < '바' )) ";
                            break;
                        case 'ㅂ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^ㅂ' OR ( ".$searchCateNm." >= '바' AND ".$searchCateNm." < '사' )) ";
                            break;
                        case 'ㅅ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^(ㅅ|ㅆ)' OR ( ".$searchCateNm." >= '사' AND ".$searchCateNm." < '아' )) ";
                            break;
                        case 'ㅇ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^ㅇ' OR ( ".$searchCateNm." >= '아' AND ".$searchCateNm." < '자' )) ";
                            break;
                        case 'ㅈ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^(ㅈ|ㅉ)' OR ( ".$searchCateNm." >= '자' AND ".$searchCateNm." < '차' )) ";
                            break;
                        case 'ㅊ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^ㅊ' OR ( ".$searchCateNm." >= '차' AND ".$searchCateNm." < '카' )) ";
                            break;
                        case 'ㅋ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^ㅋ' OR ( ".$searchCateNm." >= '카' AND ".$searchCateNm." < '타' )) ";
                            break;
                        case 'ㅌ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^ㅌ' OR ( ".$searchCateNm." >= '타' AND ".$searchCateNm." < '파' )) ";
                            break;
                        case 'ㅍ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^ㅍ' OR ( ".$searchCateNm." >= '파' AND ".$searchCateNm." < '하' )) ";
                            break;
                        case 'ㅎ':
                            $arrWhere[] = "(".$searchCateNm." RLIKE '^ㅎ' OR ( ".$searchCateNm." >= '하')) ";
                            break;
                        default:
                    }

                } else if ($cateNm == 'etc') {
                    if($globalMall === true) {
                        $arrWhere[] = "g.cateNm  < '가' AND g.cateNm NOT REGEXP  '^[a-zA-Z]'";
                    }else {
                        $arrWhere[] = "cateNm  < '가' AND cateNm NOT REGEXP  '^[a-zA-Z]'";
                    }
                } else {                   
                    if($globalMall === true) {
                        $arrWhere[] = '(g.cateNm LIKE concat(?,\'%\'))';
                    }
                    else {
                        $arrWhere[] = '(cateNm LIKE concat(?,\'%\'))';
                    }
                    $this->db->bind_param_push($arrBind, 's', strtolower($cateNm));
                }
            }

            if (is_null($depth) === false && is_numeric($depth)) {
                $depth = min($depth, 4); //출력Depth가 4차를 넘지 않도록 설정
                if ($globalMall === true) {
                    $arrWhere[] = 'length( g.cateCd ) <= ' . (($depth * $this->cateLength));
                }
                else {
                    $arrWhere[] = 'length( cateCd ) <= ' . (($depth * $this->cateLength));
                }
            }

            //성인인증안된경우 노출체크 상품은 노출함
            if (gd_check_adult() === false) {
                $arrWhere[] = '(cateOnlyAdultFl = \'n\' OR (cateOnlyAdultFl = \'y\' AND cateOnlyAdultDisplayFl = \'y\'))';
            }

            //접근권한 체크
            if (gd_check_login()) {
                $arrWhere[] = '(catePermission !=\'2\'  OR (catePermission=\'2\' AND FIND_IN_SET(\''.Session::get('member.groupSno').'\', REPLACE(catePermissionGroup,"'.INT_DIVISION.'",","))) OR (catePermission=\'2\' AND !FIND_IN_SET(\''.Session::get('member.groupSno').'\', REPLACE(catePermissionGroup,"'.INT_DIVISION.'",",")) AND catePermissionDisplayFl =\'y\'))';
            } else {
                $arrWhere[] = '(catePermission IS NULL OR catePermission=\'all\' OR (catePermission !=\'all\' AND catePermissionDisplayFl =\'y\'))';
            }

            $this->db->strWhere = implode(' AND ', $arrWhere);
            if ($globalMall === true) {
                $this->db->strJoin = ' LEFT JOIN ' . DB_CATEGORY_BRAND_GLOBAL . ' AS g ON cate.cateCd = g.cateCd';
            }
            if($orderBy == null && $globalMall !== true) {
               $orderBy = 'cateCd ASC';
            }
            $this->db->strOrder = $orderBy;
            if ($globalMall === true) {
                $cateField = ' g.cateCd,';
            }
            else{
                $cateField = ' cateCd,';
            }

            $getData = $this->getCategoryInfo(null,$cateField.$displayField.' as cateDisplay',$arrBind,true);

            foreach($getData as $k => $v) {
                $arrCateCd[] = $v['cateCd'];
                $cateDepth = (strlen($v['cateCd']) / $this->cateLength);
                for ($i = 1; $i < $cateDepth; $i++) {
                    $arrCateCd[] = substr($v['cateCd'], 0, ($i * $this->cateLength));
                }
            }

            $arrCateCd = array_unique($arrCateCd);

            unset($this->db->strWhere);
        }

        if(($cateNm && $arrCateCd) || $cateNm == null)  {
            $getData =  $this->getCategoryData($arrCateCd, null, 'cateCd, cateNm,cateKrNm,cateOverImg,cateImg,cateSort',$arrWhere[0]." AND divisionFl = 'n'", $orderBy);
            foreach ($getData as $key => $value) {
                $sno = $this->getWishBrandList($value['cateCd']);
                if($sno){
                    $getData[$key]['brandLike'] = 'on';
                }else{
                    $getData[$key]['brandLike'] = 'off';
                }
            }
            

            
        }

        if (empty($getData) === false) {
            if($tree) {
                return $this->getTreeArray($getData, false);
            } else {
                return $this->getSortArray($getData);
            }
        } else {
            return false;
        }
    }
    
    /**
     * 카테고리 정보 출력
     * 완성된 쿼리문은 $db->strField , $db->strJoin , $db->strWhere , $db->strGroup , $db->strOrder , $db->strLimit 멤버 변수를
     * 이용할수 있습니다.
     *
     * @param string $cateCd 카테고리 코드 번호 (기본 null)
     * @param string $cateField 출력할 필드명 (기본 null)
     * @param array $arrBind bind 처리 배열 (기본 null)
     * @param bool|string $dataArray return 값을 배열처리 (기본값 false)
     * @param null $search 검색 조건 [field : 찾는 필드 , value : 찾는 값]
     * @return array 카테고리 정보
     */
    public function getCategoryInfo($cateCd = null, $cateField = null, $arrBind = null, $dataArray = false,$search = null)
    {
        if (is_null($arrBind)) {
            // $arrBind = array();
        }

        if ($cateCd) {

            // 상품 코드가 배열인 경우
            if(is_array($cateCd) === true) {
                $arrWhere = "cateCd IN ('" . implode("','", $cateCd) . "')";
                // 상품 코드가 하나인경우
            } else {
                $arrWhere  =" cate.cateCd = ?";
                $this->db->bind_param_push($arrBind, 'i', $cateCd);
            }

            if ($this->db->strWhere) {
                $this->db->strWhere = $arrWhere." AND " . $this->db->strWhere;
            } else {
                $this->db->strWhere = $arrWhere;
            }
        }

        if($search){
            $validSearchField = ['cateNm'];
            if(in_array($search['field'],$validSearchField)){
                $this->db->strWhere .= $search['field']."  LIKE concat('%',?,'%') " ;
                $this->db->bind_param_push($arrBind, 's', $search['keyword']);
            }
        }

        if ($cateField) {
            if ($this->db->strField) {
                $this->db->strField = $cateField . ', ' . $this->db->strField;
            } else {
                $this->db->strField = $cateField;
            }
        }
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . $this->cateTable . ' as cate ' . implode(' ', $query);
        $getData = $this->db->query_fetch($strSQL, $arrBind);
        if (count($getData) == 1 && $dataArray === false) {
            return gd_htmlspecialchars_stripslashes($getData[0]);
        }

        return gd_htmlspecialchars_stripslashes($getData);
    }
    
    /**
     * 카테고리 정보 출력 - categoryGoods, categorySpecial 테이블의 정보만 출력함
     *
     * @param string $cateCd     카테고리 코드
     * @param string $cateCdLike 카테고리 코드 (Like 검색)
     * @param string $cateField  카테고리 테이블의 필드 (기본 *)
     * @param string $setWhere   where 문
     * @param string $setOrderBy order by 문
     * @param string $debug      query문을 출력, true 인 경우 결과를 return 과 동시에 query 출력 (기본 false)
     *
     * @return array 상품 정보
     */
    public function getCategoryData($cateCd = null, $cateCdLike = null, $cateField = '*', $setWhere = null, $setOrderBy = null, $debug = false)
    {
        $mallBySession = SESSION::get(SESSION_GLOBAL_MALL);
        gd_isset($mallBySession['sno'],DEFAULT_MALL_NUMBER);

        $whereArr = $orderByArr = $getData = $arrBind = [];
        $whereStr = $orderByStr = null;

        if($mallBySession && !Session::has('manager.managerId')) {
            $whereArr[] = 'FIND_IN_SET('.$mallBySession['sno'].',mallDisplay)';
        }
        if ($cateCd) {
            if(is_array($cateCd)) {
                $tmpCateCd = [];
                foreach ($cateCd as $key => $val) {
                    $tmpCateCd[] = '?';
                    $this->db->bind_param_push($arrBind, 's', $val);
                }
                $whereArr[] = " cateCd IN (" . implode(',', $tmpCateCd) . ") ";
                unset($tmpCateCd);
            }
            else {
                $whereArr[] = " cateCd = ? ";
                $this->db->bind_param_push($arrBind, 's', $cateCd);
            }

        }
        if ($cateCdLike) {
            $whereArr[] = ' cateCd LIKE concat(?,\'%\') ';
            $this->db->bind_param_push($arrBind, 's', $cateCdLike);
        }
        if ($setWhere) {
            $whereArr[] = $setWhere;
        }

        //관리자가 아닌경우 실행
        if (Request::getSubdomainDirectory() !== 'admin') {
            //성인인증안된경우 노출체크 상품은 노출함
            if (gd_check_adult() === false) {
                $whereArr[] = '(cateOnlyAdultFl = \'n\' OR (cateOnlyAdultFl = \'y\' AND cateOnlyAdultDisplayFl = \'y\'))';
            }

            //접근권한 체크
            if (gd_check_login()) {
                $whereArr[] = '(catePermission !=\'2\'  OR (catePermission=\'2\' AND FIND_IN_SET(\''.Session::get('member.groupSno').'\', REPLACE(catePermissionGroup,"'.INT_DIVISION.'",","))) OR (catePermission=\'2\' AND !FIND_IN_SET(\''.Session::get('member.groupSno').'\', REPLACE(catePermissionGroup,"'.INT_DIVISION.'",",")) AND catePermissionDisplayFl =\'y\'))';
            } else {
                $whereArr[] = '(catePermission IS NULL  OR catePermission=\'0\' OR (catePermission !=\'0\' AND catePermissionDisplayFl =\'y\'))';
            }
        }

        if ($setOrderBy) {
            $orderByArr[] = $setOrderBy;
        } else {
            $orderByArr[] = " cateCd ASC ";
        }
        if (count($whereArr) > 0) {
            $whereStr = " WHERE " . implode(' AND ', $whereArr);
        }
        $orderByStr = " ORDER BY " . implode(' , ', $orderByArr);

        if($cateField!='*' && strpos($cateField, 'cateCd') === false ) {
            $cateField .= ",cateCd";
        }

        $strSQL = "SELECT " . $cateField . " FROM " . $this->cateTable . $whereStr . $orderByStr;
        $getData = $this->db->slave()->query_fetch($strSQL, $arrBind);
        unset($arrBind);


        if($mallBySession) {
            if($mallBySession['sno'] != '1') { // 카테고리글로벌에서 mallSno 1 값은 제외
                $strSQLGlobal = "SELECT cateNm,cateCd FROM " . $this->cateTable . "Global  WHERE cateCd IN ('" . implode("','", array_column($getData, 'cateCd')) . "') AND mallSno = '" . $mallBySession['sno'] . "'";
                $tmpData = $this->db->query_fetch($strSQLGlobal);
                $globalData = array_combine(array_column($tmpData, 'cateCd'), $tmpData);
                if ($globalData) {
                    $getData = array_combine(array_column($getData, 'cateCd'), $getData);
                    $getData = array_values(array_replace_recursive($getData, $globalData));
                }
            }
        }


        if ($debug === true) echo $strSQL;

        return gd_htmlspecialchars_stripslashes($getData);
    }

    /**
     * 출력된 카테고리 정보로 a~z, ㄱ~ㅎ 순서대로 정렬 재정렬
     * 
     * @param array $data 카테고리 정보
     * 
     * @return array 카테고리 정보 
     */
    private function getSortArray($data){
        $return = [];
        $english_alphabet= range('A', 'Z');
        $kored_alphabet = array('ㄱ','ㄴ','ㄷ','ㄹ','ㅁ','ㅂ','ㅅ','ㅇ','ㅈ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ');
        foreach($data as $key => $val) {
            $tmp = $this->check_uniord($val['cateNm']);
            $tmpK = $this->check_uniord($val['cateKrNm']);
            if(in_array($tmpK, $kored_alphabet)) {
                $return['korean'][$tmpK][$val['cateKrNm']] = $val;
            }
            if(in_array($tmp, $english_alphabet)) {
                $return['english'][$tmp][$val['cateNm']] = $val;
            }
        }
        return $return;
    }
    
    /**
     * 아스키 코드 값으로 해당하는 알파벳 정보 출력
     * 
     * @param string $c 카테고리 이름
     * 
     * @return string 해당하는 알파벳 정보
     */
    private function check_uniord($c) {
        $h = $this->uniord($c);
        if($h>=44032 && $h<=45207) return "ㄱ";
        if($h>=45208 && $h<=45795) return "ㄴ";
        if($h>=45796 && $h<=46971) return "ㄷ";
        if($h>=46972 && $h<=47559) return "ㄹ";
        if($h>=47560 && $h<=48147) return "ㅁ";
        if($h>=48148 && $h<=49323) return "ㅂ";
        if($h>=49324 && $h<=50499) return "ㅅ";
        if($h>=50500 && $h<=51087) return "ㅇ";
        if($h>=51088 && $h<=52263) return "ㅈ";
        if($h>=52264 && $h<=52851) return "ㅊ";
        if($h>=52852 && $h<=53439) return "ㅋ";
        if($h>=53440 && $h<=54027) return "ㅌ";
        if($h>=54028 && $h<=54615) return "ㅍ";
        if($h>=54616 && $h<=55203) return "ㅎ";
        if($h==65 || $h==97) return "A";
        if($h==66 || $h==98) return "B";
        if($h==67 || $h==99) return "C";
        if($h==68 || $h==100) return "D";
        if($h==69 || $h==101) return "E";
        if($h==70 || $h==102) return "F";
        if($h==71 || $h==103) return "G";
        if($h==72 || $h==104) return "H";
        if($h==73 || $h==105) return "I";
        if($h==74 || $h==106) return "J";
        if($h==75 || $h==107) return "K";
        if($h==76 || $h==108) return "L";
        if($h==77 || $h==109) return "M";
        if($h==78 || $h==110) return "N";
        if($h==79 || $h==111) return "O";
        if($h==80 || $h==112) return "P";
        if($h==81 || $h==113) return "Q";
        if($h==82 || $h==114) return "R";
        if($h==83 || $h==115) return "S";
        if($h==84 || $h==116) return "T";
        if($h==85 || $h==117) return "U";
        if($h==86 || $h==118) return "V";
        if($h==87 || $h==119) return "W";
        if($h==88 || $h==120) return "X";
        if($h==89 || $h==121) return "Y";
        if($h==90 || $h==122) return "Z";
        return "ETC";
    }
    
    /**
     * 아스키 코드로 변환
     * 
     * @param string $c 카테고리 이름
     * 
     * @return string, boolean 시작 이름이 해당하는 아스키 코드값
     */
    private function uniord($c) {
        $h = ord($c{0});
        if ($h <= 0x7F) {
            return $h;
        } else if ($h < 0xC2) {
            return false;
        } else if ($h <= 0xDF) {
            return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
        } else if ($h <= 0xEF) {
            return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
                | (ord($c{2}) & 0x3F);
        } else if ($h <= 0xF4) {
            return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
                | (ord($c{2}) & 0x3F) << 6
                | (ord($c{3}) & 0x3F);
        } else {
            return false;
        }
    }
    
    /**
     * 카테고리 정보를 배열 형태로 출력
     *
     * @param array $data 카테고리 정보
     *
     * @return string 배열 형태의 카테고리 트리 정보
     */
    public function getTreeArray($data, $goodsCntFl = false,$selectboxImgFl = true)
    {
        if(Request::request()->has('imageFl')  && Request::request()->get('imageFl') =='n') $imageFl = false;
        else $imageFl = true;

        $jsonVar = [];
        $jsonArr = [];
        $cateLength = $this->cateLength;
        foreach ($data as $key => $val) {
            $jsonArr['cateCd'] = $val['cateCd'];
            $jsonArr['cateOverImg'] = $val['cateOverImg'];
            $jsonArr['divisionFl'] = $val['divisionFl'];
            if(Request::isMobile())  $val['cateImg'] = $val['cateImgMobile'];
            if ($goodsCntFl === true) {
                $jsonArr['goodsCnt'] = gd_isset($val['goodsCnt'], 0);
            }
            if (!$val['cateNm']) {
                $val['cateNm'] = '_no_name_';
            }
            
            if($val['cateKrNm']){
                $jsonArr['cateKrNm'] = $val['cateKrNm'];
            }else{
                $jsonArr['cateKrNm'] = '';
            }
            
            if($val['cateImg'] && $imageFl && $selectboxImgFl) {
                if($val['cateOverImg']) $jsonArr['cateNm'] = "<img data-other-src='/data/category/".$val['cateOverImg']."' src='/data/category/".$val['cateImg']."' class='gd_menu_over'>";
                else $jsonArr['cateNm'] = "<img src='/data/category/".$val['cateImg']."' alt='".$val['cateNm']."'>";
            } else {
                if($val['cateOverImg'] && $imageFl)  $jsonArr['cateNm'] = "<span  data-other-src='/data/category/".$val['cateOverImg']."' data-other-text='".strip_tags(stripcslashes($val['cateNm']))."' class='gd_menu_over'>".strip_tags(stripcslashes($val['cateNm']))."</span>";
                else $jsonArr['cateNm'] = strip_tags(stripcslashes($val['cateNm']));
            }

            $tmp['Length'] = strlen($val['cateCd']);            // 현재 카티고리 길이

            if ($key == 0) {
                if ($tmp['Length'] == $this->cateLength) {
                    $cateLength = $this->cateLength;
                } else {
                    $cateLength = $tmp['Length'];
                }
            }

            // 1차 카테고리 인경우
            if ($tmp['Length'] == $cateLength) {
                $tmp['Info'][1] = &$jsonVar[$val['cateSort']][];
                $tmp['Info'][1] = $jsonArr;
                $tmp['Node'][1] = $val['cateCd'];
                // 1차 이상의 카테고리 인경우
            } else {
                $tmp['Chk1'] = ($tmp['Length'] - $cateLength) / $this->cateLength;
                $tmp['Chk2'] = $tmp['Chk1'] + 1;
                if (isset($tmp['Info'][$tmp['Chk1']]) === true && isset($tmp['Node'][$tmp['Chk1']]) === true) {
                    if ($tmp['Info'][$tmp['Chk1']]['cateCd'] == $tmp['Node'][$tmp['Chk1']]) {
                        $tmp['Info'][$tmp['Chk2']] = &$tmp['Info'][$tmp['Chk1']]['children'][$val['cateSort']][];
                        $tmp['Info'][$tmp['Chk2']] = $jsonArr;
                    }
                }
                $tmp['Node'][$tmp['Chk2']] = $val['cateCd'];
            }
        }

        return $this->sortCategoryJson($jsonVar);
    }
    
    /**
     * JSON 형식으로 카테고리 정렬
     *
     * @param array $arrData 카테고리 정보
     *
     * @return array JSON 형태의 카테고리 트리 정보
     */
    protected function sortCategoryJson($arrData)
    {
        // 카테고리 정보가 없는경우 리턴
        if (empty($arrData) === true || is_array($arrData) === false) {
            return;
        }

        $arrData = $this->sortCategoryTree($arrData);

        foreach ($arrData as $key => $val) {
            if (gd_isset($val['children'])) {
                $arrData[$key]['children'] = self::sortCategoryJson($val['children']);
            }
        }

        return $arrData;
    }

    /**
     * 카테고리 배열 순서 재정의
     *
     * @param array $arrData 카테고리 정보
     *
     * @return array 재정의된 카테고리 정보
     */
    protected function sortCategoryTree($arrData)
    {
        ksort($arrData);
        foreach ($arrData as $val) {
            foreach ($val as $tVal) {
                $data[] = $tVal;
            }
        }

        return $data;
    }
    
    /*
     * 브랜드 찜하기 기능(삭제, 저장)
     * 
     * @param integer $memNo 회원번호
     * @param string $brandCd 브랜드 코드
     * 
     * @return string
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
     * 회원별 찜 브랜드 리스트(마이페이지용)
     * 
     * @return array 재정의된 카테고리 정보
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
     * 회원이 찜한 브랜드 리스트(햄버거 메뉴용)
     * 
     * @return array, boolean
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
     * 메인페이지 브랜드 리스트(너무 많이 나오면 상품이랑 같이 나와서 로딩이 길어져서 20개로 처리 협의 후 다시 수정 예정)
     * 
     * @return array 브랜드 정보
     */
    public function getBrandData()
    {
        if(Request::isMobile()){
            $arrWhere = "cateDisplayMobileFl = 'y'";
        }else{
            $arrWhere = "cateDisplayFl = 'y'";
        }
        
        $query = "SELECT cateNm, cateCd, sortType, sortAutoFl, cateHtml1, cateHtml1Mobile, cateKrNm, bigBrandImg, commonBrandImgMo, whiteBrandImg, blackBrandImg FROM es_categoryBrand WHERE {$arrWhere} AND length(cateCd) = 3 ORDER BY cateSort DESC LIMIT 20";
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
     * 
     * @param string $brandCd 브랜드 코드
     */
    public function getBrandNm($brandCd)
    {
        
        $query = "SELECT cateNm, cateKrNm, bigBrandImg, smallBrandImg, commonBrandImgMo, whiteBrandImg, blackBrandImg FROM es_categoryBrand WHERE cateCd = '{$brandCd}'";
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
     * 
     * @param string $brandCd 브랜드 코드
     * 
     * @return integer sno값
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
     * 해당 브랜드 찜 개수
     * @param string $brandCd 브랜드 코드
     * 
     * @return integer 개수
     */
    public function getWishBrandCnt($brandCd)
    {
        $sql = "SELECT COUNT(*) cnt FROM wib_memberBrand WHERE brandCd = '{$brandCd}'";
        $cnt = $this->wibSql->WibNobind($sql)['cnt'];

        return $cnt;
    }
}


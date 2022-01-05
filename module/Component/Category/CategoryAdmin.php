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

namespace Component\Category;

use Component\Member\Group\Util as GroupUtil;
use Component\Storage\Storage;
use Component\Database\DBTableField;
use Component\Validator\Validator;
use Framework\Utility\ArrayUtils;
use Request;
use Globals;
/**
 * 카테고리 class
 * 카테고리 관련 관리자 Class
 *
 * @author artherot
 * @version 1.0
 * @since 1.0
 * @copyright ⓒ 2016, NHN godo: Corp.
 */
class CategoryAdmin extends \Bundle\Component\Category\CategoryAdmin
{
    public function saveInfoCategoryModify($arrData)
    {
        $filesValue = Request::files()->toArray();

        // 카테고리 코드 체크
        if (Validator::required(gd_isset($arrData['cateCd'])) === false) {
            throw new \Exception(__('카테고리 코드 필수 항목이 존재하지 않습니다.'),500);
        }

        // 카테고리명 체크
        if (Validator::required(gd_isset($arrData['cateNm'])) === false) {
            throw new \Exception('카테고리명 은(는) 필수 항목 입니다.',500);
        }

        //상단꾸미기 영역
        if ($arrData['cateHtml1SameFl'] =='y')  $arrData['cateHtml1Mobile']  = $arrData['cateHtml1'] ;
        if ($arrData['cateHtml2SameFl'] =='y')  $arrData['cateHtml2Mobile']  = $arrData['cateHtml2'] ;
        if ($arrData['cateHtml3SameFl'] =='y')  $arrData['cateHtml3Mobile']  = $arrData['cateHtml3'] ;

        //추천상품 수동진열
       if($arrData['recomSortAutoFl'] =='n' && gd_isset($arrData['goodsNoData'])) {
           if (is_array($arrData['goodsNoData'])) {
               $arrData['recomGoodsNo'] = implode(INT_DIVISION, $arrData['goodsNoData']);
           }

       }

        //추천상품 자동진열
        if($arrData['recomSortAutoFl'] =='y' && gd_isset($arrData['recomGoodsNo'])) {
            if (is_array($arrData['recomGoodsNo'])) {
                $arrData['recomGoodsNo'] = implode(INT_DIVISION, $arrData['recomGoodsNo']);
            }
        }

        // 회원등급
        if (gd_isset($arrData['memberGroupNo'])) {
            if (is_array($arrData['memberGroupNo'])) {
                $arrData['catePermissionGroup'] = implode(INT_DIVISION, $arrData['memberGroupNo']);
            }
        }

        if ($arrData['cateImgDel'] == 'y') {
            $this->storage()->delete($arrData['cateImg']);
            $arrData['cateImg'] = '';
        }

        if ($arrData['cateOverImgDel'] == 'y') {
            $this->storage()->delete($arrData['cateOverImg']);
            $arrData['cateOverImg'] = '';
        }

        if ($arrData['cateImgMobileDel'] == 'y') {
            $this->storage()->delete($arrData['cateImgMobile']);
            $arrData['cateImgMobile'] = '';
        }
        
        //211210 디자인위브 이미지 삭제 기능 추가
        if ($arrData['bigBrandImgDel'] == 'y') {
            $this->storage()->delete($arrData['bigBrandImg']);
            $arrData['bigBrandImg'] = '';
        }
        
        if ($arrData['smallBrandImgDel'] == 'y') {
            $this->storage()->delete($arrData['smallBrandImg']);
            $arrData['smallBrandImg'] = '';
        }
        
        if ($arrData['commonBrandImgMoDel'] == 'y') {
            $this->storage()->delete($arrData['commonBrandImgMo']);
            $arrData['commonBrandImgMo'] = '';
        }
        
        if ($arrData['whiteBrandImgDel'] == 'y') {
            $this->storage()->delete($arrData['whiteBrandImg']);
            $arrData['whiteBrandImg'] = '';
        }
        
        if ($arrData['blackBrandImgDel'] == 'y') {
            $this->storage()->delete($arrData['blackBrandImg']);
            $arrData['blackBrandImg'] = '';
        }

        /*
        if ($this->cateType = 'goods') {
            $storageName = Storage::PATH_CODE_CATEGORY;
        } else {
            $storageName = Storage::PATH_CODE_BRAND;
        } */

        $storageName = Storage::PATH_CODE_CATEGORY;

        if ($filesValue) {
            foreach ($filesValue as $k => $v) {
                $fileDate = $v;
                if ($fileDate['name']) {
                    if (gd_file_uploadable($fileDate, 'image') === true) {  // 이미지 업로드
                        $imageExt = strrchr($v['name'], '.');
                        $arrData[$k] = $arrData['cateCd'] . '_' . $k .'_'.$this->cateType. $imageExt; // 이미지명 공백 제거
                        $targetImageFile = $arrData[$k];
                        $tmpImageFile = $v['tmp_name'];
                        Storage::disk($storageName)->upload($tmpImageFile, $targetImageFile);
                    } else {
                        throw new \Exception(__('이미지파일만 가능합니다.'));
                    }
                }

            }
        }

        if ($arrData['cateImgMobileFl'] == 'y') {
            $arrData['cateImgMobile']  = $arrData['cateImg'] ;
        } else {
            $arrData['cateImgMobileFl'] = "n";
        }


        if (gd_isset($arrData['cateDisplayFl']) == '') $arrData['cateDisplayFl'] = 'n';
        if (gd_isset($arrData['cateDisplayMobileFl']) == '') $arrData['cateDisplayMobileFl'] = 'n';
        if (gd_isset($arrData['recomDisplayFl']) == '') $arrData['recomDisplayFl'] = 'n';
        if (gd_isset($arrData['recomDisplayMobileFl']) == '') $arrData['recomDisplayMobileFl'] = 'n';
        if (gd_isset($arrData['recomSubFl']) == '') $arrData['recomSubFl'] = 'n';
        if (gd_isset($arrData['catePermissionSubFl']) == '') $arrData['catePermissionSubFl'] = 'n';
        if (gd_isset($arrData['catePermissionDisplayFl']) == '') $arrData['catePermissionDisplayFl'] = 'n';
        if (gd_isset($arrData['cateOnlyAdultSubFl']) == '') $arrData['cateOnlyAdultSubFl'] = 'n';
        if (gd_isset($arrData['cateOnlyAdultDisplayFl']) == '') $arrData['cateOnlyAdultDisplayFl'] = 'n';

        if($this->gGlobal['isUse']) {
            if(in_array('all',$arrData['mallDisplay']))  $arrData['mallDisplay'] = implode(",",array_keys($this->gGlobal['useMallList'] ));
            else  $arrData['mallDisplay'] = implode(",",$arrData['mallDisplay'] );
        }
        if (empty($arrData['mallDisplaySubFl']) === true) $arrData['mallDisplaySubFl'] = 'n'; //글로벌 노출상점 하위카테고리 동일 적용 사용 여부

        $seoTagData = $arrData['seoTag'];
        $seoTag = \App::load('\\Component\\Policy\\SeoTag');
        if (empty($arrData['seoTagFl']) === false) {
            $seoTagData['sno'] = $arrData['seoTagSno'];
            $pageGroup = $this->cateType == 'goods' ? 'category' : 'brand';
            $seoTagData['pageCode'] = $arrData['cateCd'];
            $arrData['seoTagSno'] = $seoTag->saveSeoTagEach($pageGroup,$seoTagData);
        }

        // 카테고리 정보 저장
        $funcName = $this->cateFuncNm;
        $arrBind = $this->db->get_binding(DBTableField::$funcName(), $arrData, 'update');

        $this->db->bind_param_push($arrBind['bind'], 's', $arrData['cateCd']);
        $this->db->set_update_db($this->cateTable, $arrBind['param'], 'cateCd = ?', $arrBind['bind']);
        unset($arrBind);

        if($this->gGlobal['isUse']) {
            $arrBind = [];
            //삭제후 신규 등록
            $this->db->bind_param_push($arrBind, 's', $arrData['cateCd']);
            $this->db->set_delete_db($this->cateTable."Global", 'cateCd = ?', $arrBind);
            unset($arrBind);

            $funcName = $this->cateFuncNm."Global";

            //노출상점설정
            if(in_array('all',$arrData['mallDisplay'])) { //전체인경우
                foreach($this->gGlobal['useMallList'] as $k => $v) {
                    if($v['standardFl'] =='n' && $arrData['global'][$v]) {
                        $globalData = $arrData['global'][$v];
                        $globalData['mallSno'] = $v['sno'];
                        $globalData['cateCd'] = $arrData['cateCd'];

                        $arrBind = $this->db->get_binding(DBTableField::$funcName(), $globalData, 'insert');
                        $this->db->set_insert_db($this->cateTable."Global", $arrBind['param'], $arrBind['bind'], 'y');
                        unset($arrBind);
                    }
                }
            } else {
                foreach($arrData['globalData'] as $k => $v) {
                    $globalData = $v;
                    $globalData['mallSno'] = $k;
                    $globalData['cateCd'] = $arrData['cateCd'];
                    if (gd_isset($v['cateNmGlobalFl'])) {
                        $globalData['cateNmGlobalFl'] = 'y';
                        $globalData['cateNm'] = $arrData['cateNm'];
                    }
                    else{
                        $globalData['cateNmGlobalFl'] = 'n';
                        $globalData['cateNm'] = $v['cateNm'];
                    }
                    $arrBind = $this->db->get_binding(DBTableField::$funcName(), $globalData, 'insert');
                    $this->db->set_insert_db($this->cateTable."Global", $arrBind['param'], $arrBind['bind'], 'y');
                    unset($arrBind);
                }
            }
        }

        if ($arrData['sortAutoFl'] != $arrData['sortAutoFlChk']) {

            // 카테고리 종류에 따른 설정
            if ($this->cateType == 'goods') {
                $dbTable = DB_GOODS_LINK_CATEGORY;
            } else {
                $dbTable = DB_GOODS_LINK_BRAND;
            }

            if ($arrData['sortAutoFl'] == 'y') {
                $strSQL = 'UPDATE ' . $dbTable . ' SET goodsSort = 0 where cateCd=\'' . $arrData['cateCd'] . '\'';
                $this->db->query($strSQL);
            } else {
                $strSetSQL = 'SET @newSort := 0;';
                $this->db->query($strSetSQL);

                $strSQL = 'UPDATE ' . $dbTable . ' SET goodsSort = ( @newSort := @newSort+1 ) WHERE cateCd=\'' . $arrData['cateCd'] . '\' ORDER BY goodsSort DESC;';
                $this->db->query($strSQL);
            }
        }

        if($arrData['catePermissionSubFl'] =='y') {
            $arrUpdate[] = "catePermission = '".$arrData['catePermission']."'";
            $arrUpdate[] = "catePermissionGroup = '".$arrData['catePermissionGroup']."'";
            $arrUpdate[] = "catePermissionDisplayFl = '".$arrData['catePermissionDisplayFl']."'";
            $this->db->set_update_db($this->cateTable, $arrUpdate,  "cateCd LIKE '".$arrData['cateCd']."%' ");
            unset($arrUpdate);
        }

        if($arrData['cateOnlyAdultSubFl'] =='y') {
            $arrUpdate[] = "cateOnlyAdultFl = '".$arrData['cateOnlyAdultFl']."'";
            $arrUpdate[] = "cateOnlyAdultDisplayFl = '".$arrData['cateOnlyAdultDisplayFl']."'";
            $this->db->set_update_db($this->cateTable, $arrUpdate,  "cateCd LIKE '".$arrData['cateCd']."%' ");
            unset($arrUpdate);
        }


        if($arrData['mallDisplaySubFl'] =='y') {
            $arrUpdate[] = "mallDisplay = '".$arrData['mallDisplay']."'";
            $this->db->set_update_db($this->cateTable, $arrUpdate,  "cateCd LIKE '".$arrData['cateCd']."%' ");
            unset($arrUpdate);
        }

        $this->setUseCntThemeConfig();

        return true;
    }

}
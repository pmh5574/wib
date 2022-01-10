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

use Component\Database\DBTableField;
use Framework\Utility\SkinUtils;
use Globals;
use LogHandler;
use Request;
use Session;

/**
 * 추가 상품 관련 클래스
 * @author
 * 220110 디자인위브 mh $data값 여러개로 변경
 */
class RecommendGoods extends \Bundle\Component\Goods\RecommendGoods
{
    public function getGoodsDataUser($display = 'front')
    {
        if(\Session::has(SESSION_GLOBAL_MALL)) {
            return;
        }

        $goods = \App::load('\\Component\\Goods\\Goods');
        $config = gd_policy('goods.recom');
        $mileage = gd_policy('member.mileageGive');
        $mileageBasic = gd_policy('member.mileageBasic');
        $trunc = Globals::get('gTrunc');
        $goodsPriceDisplayFl = gd_policy('goods.display')['priceFl']; //상품 가격 노출 관련
        //품절상품 설정
        if(Request::isMobile()) {
            $soldoutDisplay = gd_policy('soldout.mobile');
        } else {
            $soldoutDisplay = gd_policy('soldout.pc');
        }

        if (($display == 'front' && $config['pcDisplayFl'] == 'n') || ($display == 'mobile' && $config['mobileDisplayFl'] == 'n')) {
            return;
        }

        $arrField = DBTableField::setTableField('tableGoods', ['goodsNo', 'imagePath', 'imageStorage', 'goodsPrice', 'goodsPriceString', 'totalStock', 'stockFl', 'soldOutFl', 'makerNm', 'goodsNm', 'shortDescription', 'fixedPrice', 'goodsModelNo', 'mileageFl', 'mileageGoodsUnit', 'mileageGoods','goodsPermissionPriceStringFl','goodsPermission','goodsPermissionGroup','goodsPermissionPriceString','onlyAdultFl','onlyAdultImageFl','goodsColor', 'goodsDiscountFl', 'goodsDiscountGroup', 'goodsDiscountGroupMemberInfo', 'goodsDiscountUnit', 'goodsDiscount', 'exceptBenefit', 'exceptBenefitGroupInfo', 'exceptBenefitGroup', 'goodsBenefitSetFl'], null, 'g');

        $arrField[] = "( if (g.soldOutFl = 'y' , 'y', if (g.stockFl = 'y' AND g.totalStock <= 0, 'y', 'n') ) ) as soldOut";

        $arrJoin[] = ' INNER JOIN ' . DB_RECOMMEND_GOODS . ' as rg ON rg.goodsNo = g.goodsNo ';
        $arrJoin[] = ' LEFT JOIN ' . DB_CATEGORY_BRAND . ' cb ON g.brandCd = cb.cateCd';
        $arrJoin[] = ' LEFT JOIN ' . DB_GOODS_IMAGE . ' gi ON g.goodsNo = gi.goodsNo AND gi.imageKind = ? ';

        $arrBind = [];
        $this->db->bind_param_push($arrBind, 's', $config['imageCd']);
        if (gd_is_plus_shop(PLUSSHOP_CODE_TIMESALE) === true) {
            if (\Request::isMobile()) {
                $arrJoin[] = ' LEFT JOIN ' . DB_TIME_SALE . ' ts ON FIND_IN_SET(g.goodsNo, REPLACE(ts.goodsNo,"'.INT_DIVISION.'",",")) AND UNIX_TIMESTAMP(ts.startDt) < UNIX_TIMESTAMP() AND  UNIX_TIMESTAMP(ts.endDt) > UNIX_TIMESTAMP() AND ts.mobileDisplayFl=? ';
            } else {
                $arrJoin[] = ' LEFT JOIN ' . DB_TIME_SALE . ' ts ON FIND_IN_SET(g.goodsNo, REPLACE(ts.goodsNo,"'.INT_DIVISION.'",",")) AND UNIX_TIMESTAMP(ts.startDt) < UNIX_TIMESTAMP() AND  UNIX_TIMESTAMP(ts.endDt) > UNIX_TIMESTAMP() AND ts.pcDisplayFl=? ';
            }
            $this->db->bind_param_push($arrBind, 's', 'y');
            $arrField[] = 'ts.mileageFl as timeSaleMileageFl,ts.couponFl as timeSaleCouponFl,ts.benefit as timeSaleBenefit,ts.sno as timeSaleSno,ts.goodsPriceViewFl as timeSaleGoodsPriceViewFl';
        }

        if ($display == 'front') {
            $arrWhere[] = 'g.goodsDisplayFl = ?';
            $this->db->bind_param_push($arrBind, 's', 'y');
        } else {
            $arrWhere[] = 'g.goodsDisplayMobileFl = ?';
            $this->db->bind_param_push($arrBind, 's', 'y');
        }
        if ($config['soldOutFl'] == 'n') {
            $arrWhere[] = 'g.soldOutFl != ?';
            $this->db->bind_param_push($arrBind, 's', 'y');
            $arrWhere[] = '(g.stockFl != ? OR (g.stockFl = ? AND g.totalStock > ?))';
            $this->db->bind_param_push($arrBind, 's', 'y');
            $this->db->bind_param_push($arrBind, 's', 'y');
            $this->db->bind_param_push($arrBind, 'i', 0);
        }
        $arrWhere[] = 'g.delFl = ?';
        $this->db->bind_param_push($arrBind, 's', 'n');
        
        /**
         * 211121 디자인위브 mh 상품 샵 조건 추가
         */
        $shopNum = Session::get('WIB_SHOP_NUM');
        
        if($shopNum != '1' && $shopNum){
            $arrWhere[] = "((g.shopSettingFl = 'y' AND g.shopSetting = '{$shopNum}') or g.shopSettingFl = 'n')";
        }


        //접근권한 체크
        if (gd_check_login()) {
            $arrWhere[] = '(g.goodsAccess !=\'group\'  OR (g.goodsAccess=\'group\' AND FIND_IN_SET(\''.\Session::get('member.groupSno').'\', REPLACE(g.goodsAccessGroup,"'.INT_DIVISION.'",","))) OR (g.goodsAccess=\'group\' AND !FIND_IN_SET(\''.\Session::get('member.groupSno').'\', REPLACE(g.goodsAccessGroup,"'.INT_DIVISION.'",",")) AND g.goodsAccessDisplayFl =\'y\'))';
        } else {
            $arrWhere[] = '(g.goodsAccess=\'all\' OR (g.goodsAccess !=\'all\' AND g.goodsAccessDisplayFl =\'y\'))';
        }

        //성인인증안된경우 노출체크 상품은 노출함
        if (gd_check_adult() === false) {
            $arrWhere[] = '(onlyAdultFl = \'n\' OR (onlyAdultFl = \'y\' AND onlyAdultDisplayFl = \'y\'))';
        }

        $this->db->strField = implode(', ', $arrField) . ', gi.imageName, gi.imageSize, cb.cateNm';
        $this->db->strJoin = implode('', $arrJoin);
        $this->db->strWhere = implode(' AND ', gd_isset($arrWhere));
        $this->db->strOrder = 'RAND()';
//        $this->db->strLimit = 1;

        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_GOODS . ' g ' . implode(' ', $query);
        $data = $this->db->query_fetch($strSQL, $arrBind, false);
        
        if($data) {
            foreach($data as $key => $value){
                $GoodsBenefit = \App::load('\\Component\\Goods\\GoodsBenefit');
                $data[$key] = $GoodsBenefit->goodsDataFrontConvert($value);

                //기본적으로 가격 노출함
                $data[$key]['goodsPriceDisplayFl'] = 'y';

                // 상품 이미지
                if ($value['onlyAdultFl'] == 'y' && gd_check_adult() === false && $value['onlyAdultImageFl'] =='n') {
                    if (Request::isMobile()) {
                        $data[$key]['img'] = "/data/icon/goods_icon/only_adult_mobile.png";
                    } else {
                        $data[$key]['img'] = "/data/icon/goods_icon/only_adult_pc.png";
                    }
                } else {
                    $data[$key]['img'] = SkinUtils::imageViewStorageConfig($data[$key]['imageName'], $data[$key]['imagePath'], $data[$key]['imageStorage'], 150, 'goods')[0];
                }

                // 마일리지
                if (in_array('mileage', $config['displayField']) === true) {
                    if ($mileage['giveFl'] == 'y') {

                        //상품 마일리지
                        if ($data[$key]['mileageFl'] == 'c') {
                            $mileagePercent = $mileage['goods'] / 100;
                            // 상품 기본 마일리지 정보
                            $data[$key]['mileageBasicGoods'] = gd_number_figure($data[$key]['goodsPrice'] * $mileagePercent, $trunc['mileage']['unitPrecision'], $trunc['mileage']['unitRound']);

                        // 개별 설정인 경우 마일리지 설정
                        } else if ($data[$key]['mileageFl'] == 'g') {
                            $mileagePercent = $data[$key]['mileageGoods'] / 100;

                            // 상품 기본 마일리지 정보
                            if ($data[$key]['mileageGoodsUnit'] === 'percent') {
                                $data[$key]['mileageBasicGoods'] = gd_number_figure($data[$key]['goodsPrice'] * $mileagePercent, $trunc['mileage']['unitPrecision'], $trunc['mileage']['unitRound']);
                            } else {
                                // 정액인 경우 해당 설정된 금액으로
                                $data[$key]['mileageBasicGoods'] = gd_number_figure($data[$key]['mileageGoods'], $trunc['mileage']['unitPrecision'], $trunc['mileage']['unitRound']);
                            }

                        }

                        $member = \App::Load(\Component\Member\Member::class);
                        $memInfo = $member->getMemberInfo();

                        // 회원 그룹별 추가 마일리지
                        if ($memInfo['mileageLine'] <= $data[$key]['goodsPrice']) {
                            if ($memInfo['mileageType'] === 'percent') {
                                $memberMileagePercent = $memInfo['mileagePercent'] / 100;
                                $data[$key]['mileageBasicMember'] = gd_number_figure($data[$key]['goodsPrice'] * $memberMileagePercent, $trunc['mileage']['unitPrecision'], $trunc['mileage']['unitRound']);
                            } else {
                                $data[$key]['mileageBasicMember'] = $memInfo['mileagePrice'];
                            }
                        }
                        $data[$key]['mileage'] = ($data[$key]['mileageBasicGoods'] + $data[$key]['mileageBasicMember']) . $mileageBasic['unit'];
                    }
                }

                //구매불가 대체 문구 관련
                if($data[$key]['goodsPermissionPriceStringFl'] =='y' && $data[$key]['goodsPermission'] !='all' && (($data[$key]['goodsPermission'] =='member'  && gd_is_login() === false) || ($data[$key]['goodsPermission'] =='group'  && !in_array(\Session::get('member.groupSno'),explode(INT_DIVISION,$data[$key]['goodsPermissionGroup']))))) {
                    $data[$key]['goodsPriceString'] = $data[$key]['goodsPermissionPriceString'];
                }

                // 상품금액
                $data[$key]['timeSaleFl'] = false;
                if (gd_is_plus_shop(PLUSSHOP_CODE_TIMESALE) === true) {
                    $strScmSQL = 'SELECT ts.mileageFl as timeSaleMileageFl,ts.couponFl as timeSaleCouponFl,ts.benefit as timeSaleBenefit,ts.sno as timeSaleSno,ts.goodsPriceViewFl as timeSaleGoodsPriceViewFl, ts.endDt as timeSaleEndDt, ts.leftTimeDisplayType, ts.pcDisplayFl as timeSalePC, ts.mobileDisplayFl as timeSaleMobile FROM ' . DB_TIME_SALE .' as ts WHERE FIND_IN_SET('.$data[$key]['goodsNo'].', REPLACE(ts.goodsNo,"'.INT_DIVISION.'",",")) AND UNIX_TIMESTAMP(ts.startDt) < UNIX_TIMESTAMP() AND  UNIX_TIMESTAMP(ts.endDt) > UNIX_TIMESTAMP() ';
                    $tmpScmData = $this->db->query_fetch($strScmSQL,null,false);

                    if($tmpScmData) {
                        //타임세일 노출 여부 (디바이스에 따라)
                        if ($tmpScmData['timeSalePC'] === 'y' && !Request::isMobile()) {
                            $data[$key]['timeSaleFl'] = true;
                        }
                        if ($tmpScmData['timeSaleMobile'] === 'y' && Request::isMobile()) {
                            $data[$key]['timeSaleFl'] = true;
                        }
                    }

                    if ($data[$key]['timeSaleMileageFl'] == 'n') unset($data[$key]['mileage']);
                    if ($data[$key]['timeSaleCouponFl'] == 'n') unset($data[$key]['couponPrice']);
                    if ($data[$key]['goodsPrice'] && $data['timeSaleBenefit'] > 0) $data[$key]['goodsPrice'] = gd_number_figure($data[$key]['goodsPrice'] - (($data[$key]['timeSaleBenefit'] / 100) * $data[$key]['goodsPrice']), $trunc['goods']['unitPrecision'], $trunc['goods']['unitRound']);
                }

                // 쿠폰 정보
                $couponConfig = gd_policy('coupon.config');
                if ($data[$key]['timeSaleCouponFl'] == 'n') $couponConfig['couponUseType'] = 'n';

                // 쿠폰가 회원만 노출
                if ($couponConfig['couponDisplayType'] == 'member') {
                    if (gd_check_login()) {
                        $couponPriceYN = true;
                    } else {
                        $couponPriceYN = false;
                    }
                } else {
                    $couponPriceYN = true;
                }

                // 혜택제외 체크 (쿠폰)
                $exceptBenefit = explode(STR_DIVISION, $data[$key]['exceptBenefit']);
                $exceptBenefitGroupInfo = explode(INT_DIVISION, $data[$key]['exceptBenefitGroupInfo']);
                if (in_array('coupon', $exceptBenefit) === true && ($data[$key]['exceptBenefitGroup'] == 'all' || ($data[$key]['exceptBenefitGroup'] == 'group') && in_array(\Session::get('member.memNo'), $exceptBenefitGroupInfo) === true)) {
                    $couponPriceYN = false;
                }

                // 쿠폰 할인 금액
                if ($couponConfig['couponUseType'] == 'y' && $couponPriceYN  && $data[$key]['goodsPrice'] > 0 && empty($data[$key]['goodsPriceString']) === true) {
                    // 쿠폰 모듈 설정

                    $coupon = \App::load('\\Component\\Coupon\\Coupon');
                    // 해당 상품의 모든 쿠폰
                    $couponArrData = $coupon->getGoodsCouponDownList($data[$key]['goodsNo']);

                    // 해당 상품의 쿠폰가
                    $data[$key]['couponDcPrice'] = $couponSalePrice = $coupon->getGoodsCouponDisplaySalePrice($couponArrData, $data[$key]['goodsPrice']);
                    if ($couponSalePrice) {
                        $data[$key]['couponPrice'] = $data[$key]['goodsPrice'] - $couponSalePrice;
                        if ($data[$key]['couponPrice'] < 0) {
                            $data[$key]['couponPrice'] = 0;
                        }
                    }
                }

                // 구매 가능여부 체크
                if ($data[$key]['soldOut'] == 'y' && $goodsPriceDisplayFl =='n' && $soldoutDisplay['soldout_price'] !='price') {
                    if($soldoutDisplay['soldout_price'] =='text')   $data[$key]['goodsPriceString'] = $soldoutDisplay['soldout_price_text'];
                    $data[$key]['goodsPriceDisplayFl'] = 'n';
                }

                if (empty($data[$key]['goodsPriceString']) === false && $goodsPriceDisplayFl =='n') {
                    $data[$key]['goodsPriceDisplayFl'] = 'n';
                }

                // 상품 대표색상 치환코드 추가
                if ($data[$key]['goodsColor']) {
                    $goodsColorList = $goods->getGoodsColorList(true);
                    $goodsColor = (Request::isMobile()) ? "<div class='color_chip'>" : "<div class='color'>";
                    if ($data[$key]['goodsColor']) $data[$key]['goodsColor'] = explode(STR_DIVISION, $data[$key]['goodsColor']);

                    if (is_array($data[$key]['goodsColor'])) {
                        foreach(array_unique($data[$key]['goodsColor']) as $k => $v) {
                            if (!in_array($v,$goodsColorList) ) {
                                continue;
                            }
                            $goodsColorData = array_flip($goodsColorList)[$v];
                            $goodsColor .= ($v == 'FFFFFF') ? "<div style='background-color:#{$v} !important;' title='{$goodsColorData}'></div>" : "<div style='background-color:#{$v} !important; border-color:#{$v} !important;' title='{$goodsColorData}'></div>";
                        }
                        $goodsColor .= "</div>";
                        unset($data[$key]['goodsColor']);
                        $data[$key]['goodsColor'] = $goodsColor;
                    }
                }

                //할인가 기본 세팅
                $data[$key]['goodsDcPrice'] = $goods->getGoodsDcPrice($data[$key]);

                if (in_array('goodsDiscount', $config['displayField']) === true) {
                    if (empty($config['goodsDiscount']) === false) {
                        if (in_array('goods', $config['goodsDiscount']) === true) $data[$key]['dcPrice'] += $data[$key]['goodsDcPrice'];
                        if (in_array('coupon', $config['goodsDiscount']) === true) $data[$key]['dcPrice'] += $data[$key]['couponDcPrice'];
                    }
                }

                if ($data[$key]['dcPrice'] >= $data[$key]['goodsPrice']) {
                    $data[$key]['dcPrice'] = 0;
                }

                if (in_array('dcRate', $config['displayAddField']) === true) {
                    $data[$key]['goodsDcRate'] = round((100 * gd_isset($data[$key]['dcPrice'], 0)) / $data[$key]['goodsPrice']);
                    $data[$key]['couponDcRate'] = round((100 * $data[$key]['couponDcPrice']) / $data[$key]['goodsPrice']);
                }
            }
            
        }

        $setData['data'] = $data;
        $setData['config'] = $config;

        return $setData;
    }
}

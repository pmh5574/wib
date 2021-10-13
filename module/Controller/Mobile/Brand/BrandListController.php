<?php
namespace Controller\Mobile\Brand;

use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\Framework\Debug\Exception;
use Message;
use Globals;
use Request;
use Cookie;

class BrandListController extends \Controller\Mobile\Controller
{
	/**
	 * 상품목록
	 * @author  Dong-Gi Kim <zeumad@naver.com>
	 */
    public function index()
    {
        $checkParameter = ['cateCd', 'brandCd'];
        $getValue = Request::get()->length($checkParameter)->toArray();
		
		$cateInfo['displayType'] = '02'; //테마 지정

        // 모듈 설정
        $goods = \App::load('\\Component\\Goods\\Goods');
        $cate = \App::load('\\Component\\Category\\Brand');

        try {
			$cateCd = $getValue['brandCd'];
			$brandCateCd = $getValue['cateCd'];
			$cateType = "brand";
			$cateMode = "brand";
			$naviDisplay = gd_policy('display.navi_brand');			

            $cateInfo = $cate->getCategoryGoodsList($cateCd,'y');
            $goodsCategoryList = $cate->getCategories($cateCd,'y');
            $goodsCategoryListNm = array_column($goodsCategoryList, 'cateNm');

			//$brandCateCd 카테고리 깊이 별로 구분 나눔
			$brandDiv = [];
			for($i = 1; $i <= strlen($brandCateCd)/DEFAULT_LENGTH_CATE; $i++)
			{
				array_push($brandDiv, $i*DEFAULT_LENGTH_CATE);
			}
			
			//$brandCateCd 카테고리 깊이 별로 선택된 값을 구함
			foreach ($brandDiv as $key => $val) {
				$brandSelectedCate[$key] = $cate->getBrandSelectedCate(substr($brandCateCd,0,$val),'location');
			}
			$brandCate = $cate->getBrandSelectedCate($cateCd,'brand');
			
			if(count($brandDiv) == 4){
				$listNum = 3; //마지막 카테고리
			} else {
				$listNum = count($brandDiv);
			}
			$brandlistData = $cate->getBrandCate($cateCd,$brandCateCd);
			$brandlistCate = $brandlistData[$listNum];

            if(gd_isset($cateInfo['themeCd']) ===null) {
                throw new \Exception(__('상품의 테마 설정을 확인해주세요.'));
            }

            // 마일리지 정보
            $mileage = gd_mileage_give_info();

            $this->setData('gPageName', $goodsCategoryList[$cateCd]['cateNm']);
            $this->setData('cateInfo', gd_isset($cateInfo));

            Request::get()->set('page',$getValue['page']);
            Request::get()->set('sort',$getValue['sort']);

            if($cateInfo['recomDisplayMobileFl'] =='y' && $cateInfo['recomGoodsNo'])
            {
                $recomTheme = $cateInfo['recomTheme'];
                if ($recomTheme['detailSet']) {
                    $recomTheme['detailSet'] = unserialize($recomTheme['detailSet']);
                }

                gd_isset($recomTheme['lineCnt'],4);
                $imageType		= gd_isset($recomTheme['imageCd'],'list');						// 이미지 타입 - 기본 'main'
                $soldOutFl		= $recomTheme['soldOutFl'] == 'y' ? true : false;			// 품절상품 출력 여부 - true or false (기본 true)
                $brandFl		= in_array('brandCd',array_values($recomTheme['displayField']))  ? true : false;	// 브랜드 출력 여부 - true or false (기본 false)
                $couponPriceFl	= in_array('coupon',array_values($recomTheme['displayField']))  ? true : false;		// 쿠폰가격 출력 여부 - true or false (기본 false)
                $optionFl = in_array('option',array_values($recomTheme['displayField']))  ? true : false;

                if($cateInfo['recomSortAutoFl'] =='y') $recomOrder = $cateInfo['recomSortType'].",g.goodsNo desc";
                else $recomOrder = "FIELD(g.goodsNo," . str_replace(INT_DIVISION, ",", $cateInfo['recomGoodsNo']) . ")";
                if ($recomTheme['soldOutDisplayFl'] == 'n') $recomOrder = "soldOut asc," . $recomOrder;
                $recomTheme['goodsDiscount'] = explode(',', $recomTheme['goodsDiscount']);
                $recomTheme['priceStrike'] = explode(',', $recomTheme['priceStrike']);
                $recomTheme['displayAddField'] = explode(',', $recomTheme['displayAddField']);

                $goods->setThemeConfig($recomTheme);
                $goodsRecom	= $goods->goodsDataDisplay('goods', $cateInfo['recomGoodsNo'], (gd_isset($recomTheme['lineCnt']) * gd_isset($recomTheme['rowCnt'])), $recomOrder, $imageType, $optionFl, $soldOutFl, $brandFl, $couponPriceFl);

                if($goodsRecom) $goodsRecom = array_chunk($goodsRecom,$recomTheme['lineCnt']);

                $this->setData('widgetGoodsList', gd_isset($goodsRecom));
                $this->setData('widgetTheme', $recomTheme);
            }


            if($cateInfo['soldOutDisplayFl'] =='n')  $displayOrder[] = "soldOut asc";

            if ($cateInfo['sortAutoFl'] == 'y') $displayOrder[] = "gl.fixSort desc," . gd_isset($cateInfo['sortType'], 'gl.goodsNo desc');
            else $displayOrder[] = "gl.fixSort desc,gl.goodsSort desc";

            // 상품 정보
            $displayCnt = gd_isset($cateInfo['lineCnt']) * gd_isset($cateInfo['rowCnt']);
            $pageNum = gd_isset($getValue['pageNum'],$displayCnt);
            $optionFl = in_array('option',array_values($cateInfo['displayField']))  ? true : false;
            $soldOutFl = (gd_isset($cateInfo['soldOutFl']) == 'y' ? true : false); // 품절상품 출력 여부
            $brandFl =  in_array('brandCd',array_values($cateInfo['displayField']))  ? true : false;
            $couponPriceFl =in_array('coupon',array_values($cateInfo['displayField']))  ? true : false;	 // 쿠폰가 출력 여부
            $goods->setThemeConfig($cateInfo);
            $goodsData = $goods->getBrandGoodsList($cateCd, $brandCateCd, $cateMode, $pageNum,$displayOrder, gd_isset($cateInfo['imageCd']), $optionFl, $soldOutFl, $brandFl, $couponPriceFl,null,$displayCnt);

            if($goodsData['listData']) $goodsList = array_chunk($goodsData['listData'],$cateInfo['lineCnt']);
            $page = \App::load('\\Component\\Page\\Page'); // 페이지 재설정
            unset($goodsData['listData']);
            //품절상품 설정
            $soldoutDisplay = gd_policy('soldout.mobile');

            if ($soldoutDisplay['soldout_icon_img']) {
                $fileSplit = explode(DIRECTORY_SEPARATOR, $soldoutDisplay['soldout_icon_img']);
                $soldout_icon_img = array_splice($fileSplit, -1, 1, DIRECTORY_SEPARATOR);
                $soldoutDisplay['soldout_icon_img_filename'] = $soldout_icon_img[0];
            }

            if ($soldoutDisplay['soldout_price_img']) {
                $fileSplit = explode(DIRECTORY_SEPARATOR, $soldoutDisplay['soldout_price_img']);
                $soldout_price_img = array_splice($fileSplit, -1, 1, DIRECTORY_SEPARATOR);
                $soldoutDisplay['soldout_price_img_filename'] = $soldout_price_img[0];
            }

            // 카테고리 노출항목 중 상품할인가
            if (in_array('goodsDcPrice', $cateInfo['displayField'])) {
                foreach ($goodsList as $key => $val) {
                    foreach ($val as $key2 => $val2) {
                        $goodsList[$key][$key2]['goodsDcPrice'] = $goods->getGoodsDcPrice($val2);
                    }
                }
            }

            // 장바구니 설정
            if ($cateInfo['displayType'] == '11') {
                $cartInfo = gd_policy('order.cart');
                $this->setData('cartInfo', gd_isset($cartInfo));
            }


            // 웹취약점 개선사항 카테고리 에디터 업로드 이미지 alt 추가
            if ($cateInfo['cateHtml1Mobile']) {
                $tag = "title";
                preg_match_all( '@'.$tag.'="([^"]+)"@' , $cateInfo['cateHtml1Mobile'], $match );
                $titleArr = array_pop($match);

                foreach ($titleArr as $title) {
                    $cateInfo['cateHtml1Mobile'] = str_replace('title="'.$title.'"', 'title="'.$title.'" alt="'.$title.'"', $cateInfo['cateHtml1Mobile']);
                }
            }

            if ($cateInfo['cateHtml2Mobile']) {
                $tag = "title";
                preg_match_all( '@'.$tag.'="([^"]+)"@' , $cateInfo['cateHtml2Mobile'], $match );
                $titleArr = array_pop($match);

                foreach ($titleArr as $title) {
                    $cateInfo['cateHtml2Mobile'] = str_replace('title="'.$title.'"', 'title="'.$title.'" alt="'.$title.'"', $cateInfo['cateHtml2Mobile']);
                }
            }

            if ($cateInfo['cateHtml3Mobile']) {
                $tag = "title";
                preg_match_all( '@'.$tag.'="([^"]+)"@' , $cateInfo['cateHtml3Mobile'], $match );
                $titleArr = array_pop($match);

                foreach ($titleArr as $title) {
                    $cateInfo['cateHtml3Mobile'] = str_replace('title="'.$title.'"', 'title="'.$title.'" alt="'.$title.'"', $cateInfo['cateHtml3Mobile']);
                }
            }

			$this->setData('brandlistCate', gd_isset($brandlistCate));//list_item_category 메뉴
			$this->setData('brandSelectedCate', gd_isset($brandSelectedCate));//navi_g 카테고리
			$this->setData('brandCate', gd_isset($brandCate));
			$this->setData('brandCateCd', $brandCateCd);
            $this->setData('cateCd', $cateCd);
            $this->setData('goodsCategoryListNm', gd_isset($goodsCategoryListNm));
            $this->setData('brandCd', $getValue['brandCd']);
            $this->setData('cateType', $cateType);
            $this->setData('themeInfo', gd_isset($cateInfo));
            $this->setData('goodsList', gd_isset($goodsList));
            $this->setData('page', gd_isset($page));
            $this->setData('naviDisplay', gd_isset($naviDisplay));
            $this->setData('soldoutDisplay', gd_isset($soldoutDisplay));
            $this->setData('mileageData', gd_isset($mileage['info']));
            $this->setData('currency', Globals::get('gCurrency'));

            if($getValue['mode'] == 'data') {
                $this->getView()->setPageName('goods/list/list_'.$getValue['displayType']);
            } else {
                $this->getView()->setDefine('goodsTemplate', 'goods/list/list_'.$cateInfo['displayType'].'.html');
            }

        } catch (\Exception $e) {
            throw new AlertRedirectException($e->getMessage(),null,null,"/");
        }
    }
}
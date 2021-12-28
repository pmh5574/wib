<?php

namespace Controller\Admin\Partners;

use Component\Order\OrderAdmin;
use Exception;
use Globals;
use Request;
use Component\Wib\WibScmAdmin;


class PartnersOrderListController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            // --- 메뉴 설정
            $this->callMenu('partners', 'partners', 'partners_order_list');
            $this->addScript(
                [
                    'jquery/jquery.multi_select_box.js',
                    'sms.js'
                ]
            );

            // --- 모듈 호출
            $order = new OrderAdmin();

            /* 운영자별 검색 설정값 */
            $searchConf = \App::load('\\Component\\Member\\ManagerSearchConfig');
            $searchConf->setGetData();
            $isOrderSearchMultiGrid = gd_isset(\Session::get('manager.isOrderSearchMultiGrid'), 'n');
            $this->setData('isOrderSearchMultiGrid', $isOrderSearchMultiGrid);

            // --- 리스트 설정
            $getValue = Request::get()->toArray();

            //주문리스트 그리드 설정
            $orderAdminGrid = \App::load('\\Component\\Order\\OrderAdminGrid');
            $getValue['orderAdminGridMode'] = $orderAdminGrid->getOrderAdminGridMode($getValue['view']);
            $this->setData('orderAdminGridMode', $getValue['orderAdminGridMode']);

            $getData = $order->getOrderListForAdmin($getValue, 6);
            
            $scmAdmin = new WibScmAdmin();
            $scmList = $scmAdmin->getScmAdminList();
            $this->setData('scmList', $scmList['data']);
            
            $getData['search']['combineSearch'] = [
                'memId' => '아이디',
                'orderName' => '주문자 이름',
                'orderNo' => '주문 번호',
                'orderCode' => '상품 코드'
            ];
            
            $getData['orderGridConfigList'] = [
                'check' => 'check',
                'no' => '번호',
                'orderStatus' => '상태',
                'goodsImage' => '이미지',
                'scmNm' => '업체명',
                'orderNo' => '주문번호',
                'regDt' => '주문일자',
                'paymentDt' => '결제일자', //보이고 안보이고?
                'modDt' => '수정일', //보이고 안보이고?
                'orderName' => '주문자명/ID',
                'orderGoodsNm' => '상품명',
                'orderOptionNm' => '옵션',
                'orderGoodsModel' => '상품 코드',
                'orderCnt' => '수량',
                'address' => '배송지', //보이고 안보이고?
                'receiverCellPhone' => '연락처1', //보이고 안보이고?
                'receiverPhone' => '연락처2', //보이고 안보이고?
                'orderMemo' => '배송 메세지', //보이고 안보이고?
                'orderPlusDeliveryPriceFr' => '운송비(해외)', //보이고 안보이고?
                'orderPlusDeliveryPriceKr' => '운송비(국내)', //보이고 안보이고?
                'goodsVolume' => '중량', //보이고 안보이고?
                'goodsWeight' => '부피', //보이고 안보이고?
                'goodsNo' => '협력사 상품 번호',
                'scmUnit' => '화폐',
                'fixedPrice' => '공급가',
                'scmAdjustPrice' => '정산 금액', //보이고 안보이고?
                'salePrice' => '할인 금액', //보이고 안보이고?
                'saleStatus' => '할인 방식', //보이고 안보이고?
                'goodsPrice' => '판매 금액',
                'settlePrice' => '결제 금액',
                'exchangeRate' => '적용 환율', //보이고 안보이고?
                'exchangeRateDt' => '적용 환율 기준일', //보이고 안보이고?
                'a' => '품목', //보이고 안보이고?
                'b' => '관세', //보이고 안보이고?
                'c' => '개별소비세', //보이고 안보이고?
                'd' => '교육세', //보이고 안보이고?
                'scmVat' => '부가세', //보이고 안보이고?
                'e' => '세금 합계', //보이고 안보이고?
                'orderMargin' => '마진율', //보이고 안보이고?
                'orderDeliveryFr' => '운송장 번호(해외)',
                'orderDeliveryKr' => '운송장 번호(국내)',
                'orderDeliveryCnt' => '배송 횟수',
                'f' => 'FTA 적용 여부',
                'memberOrderFl' => '구매 동의', //보이고 안보이고?
                'g' => '분쟁 조정', //보이고 안보이고?
                'h' => '협력사 알림', //보이고 안보이고?
                'orderTypeFl' => '수기 등록 여부', //보이고 안보이고?
                'i' => '정산 여부', //보이고 안보이고?
                'j' => '증빙',
                'scmDescription' => '비고'
            ];
            $statusPolicy = $order->statusPolicy;
            
            $orderStatus = [
                'o1',
                'p1',
                'd1',
                'd1',
                'd2',
                's1',
                'b1',
                'b4',
                'c1',
                'e1',
                'e5',
                'r1',
                'r3'
            ];
            
            $statusList = [];
            
            foreach($statusPolicy as $aVal) {
                foreach($aVal as $key => $value) {
                    if($value['admin'] && in_array($key, $orderStatus)) {
                        $statusList[$key] = $value['admin'];
                    }
                }
            }
            
            $this->setData('statusList', $statusList);
            $this->setData('data', gd_isset($getData['data']));
            $this->setData('search', $getData['search']);
            $this->setData('checked', $getData['checked']);
            $this->setData('orderGridConfigList', $getData['orderGridConfigList']);
            //복수배송지를 사용하여 리스트 데이터 배열의 키를 체인지한 데이터인지 체크
            $this->setData('useMultiShippingKey', $getData['useMultiShippingKey']);

            // 페이지 설정
            $page = \App::load('Component\\Page\\Page');
            $this->setData('total', count($getData['data']));
            $this->setData('page', gd_isset($page));
            $this->setData('pageNum', gd_isset($pageNum));

            // 정규식 패턴 view 파라미터 제거
            $pattern = '/view=[^&]+$|searchFl=[^&]+$|view=[^&]+&|searchFl=[^&]+&/';//'/[?&]view=[^&]+$|([?&])view=[^&]+&/';

            // view 제거된 쿼리 스트링
            $queryString = preg_replace($pattern, '', Request::getQueryString());
            $this->setData('queryString', $queryString);

            // --- 주문 일괄처리 셀렉트박스
            foreach ($order->getOrderStatusAdmin() as $key => $val) {
                if (in_array($key, $order->statusListExclude) === false && in_array(substr($key, 0, 1), $order->statusExcludeCd) === false && substr($key, 0, 1) != 'o') {
                    $selectBoxOrderStatus[$key] = $val;
                }
            }
            $this->setData('selectBoxOrderStatus', $selectBoxOrderStatus);

            // 메모 구분
            $orderAdmin = \App::load('\\Component\\Order\\OrderAdmin');
            $tmpMemo = $orderAdmin->getOrderMemoList(true);
            $arrMemoVal = [];
            foreach($tmpMemo as $key => $val){
                $arrMemoVal[$val['itemCd']] = $val['itemNm'];
            }
            $this->setData('memoCd', $arrMemoVal);

            // --- 템플릿 정의
            $this->getView()->setDefine('layoutOrderSearchForm', Request::getDirectoryUri() . '/layout_order_search_form.php');// 검색폼

            
            $this->getView()->setDefine('layoutOrderList', Request::getDirectoryUri() . '/layout_order_list.php');// 리스트폼

            // --- 템플릿 변수 설정
            $this->setData('statusStandardNm', $order->statusStandardNm);
            $this->setData('statusStandardCode', $order->statusStandardCode);
            $this->setData('statusListCombine', $order->statusListCombine);
            $this->setData('statusListExclude', $order->statusListExclude);
            $this->setData('type', $order->getOrderType());
            $this->setData('channel', $order->getOrderChannel());
            $this->setData('settle', $order->getSettleKind());
            $this->setData('formList', $order->getDownloadFormList());
            $this->setData('statusExcludeCd', $order->statusExcludeCd);
            $this->setData('statusSearchableRange', $order->getOrderStatusAdmin());

            //        $this->setData('statusPreCode', $statusPreCode);
            //        $this->setData('search', gd_isset($getData['search']));
            //        $this->setData('checked', $checked = gd_isset($getData['checked']));
            //        $this->setData('statusMode', gd_isset($getValue['statusMode']));

            // 공급사와 동일한 페이지 사용
            $this->getView()->setPageName('partners/partners_order_list.php');

        } catch (Exception $e) {
            throw $e;
        }
    }
}

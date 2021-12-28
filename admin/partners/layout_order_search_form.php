<?php
/**
 * 주문리스트내 검색 폼 레이아웃
 *
 * @author Jong-tae Ahn <qnibus@godo.co.kr>
 */

//주문번호별/상품주문번호별 탭 이동 시 검색결과개수 초기화
parse_str($queryString,$arrQueryString);
unset($arrQueryString['__total']);
unset($arrQueryString['__amount']);
unset($arrQueryString['__totalPrice']);
$queryString = http_build_query($arrQueryString);
?>

<!-- 검색을 위한 form -->
<form id="frmSearchOrder" method="get" class="js-form-enter-submit">
    <input type="hidden" name="detailSearch" value="<?= $search['detailSearch']; ?>"/>

    <div class="table-title <?=isset($currentUserHandleMode) ? '' : 'gd-help-manual'?>">
        주문 검색
    </div>

    <div class="search-detail-box">
        <table class="table table-cols">
            <colgroup>
                <col class="width-sm">
                <col>
                <col class="width-sm">
                <col>
            </colgroup>
            <tbody>
                <?php if($isOrderSearchMultiGrid == 'y') { ?>
                <tr>
                    <th>검색어</th>
                    <td colspan="3">
                        <div class="keywordDiv fir mgb8">
                            <div class="form-inline mgb5">
                                <?= gd_select_box('key1', 'key[]', $search['combineSearch'], null, $search['key'][0], null, 'data-target="fir"', 'form-control '); ?>
                                <button type="button" class="btn btn-sm btn-black" onclick="keywordAdd()">추가</button>
                            </div>
                            <textarea name="keyword[]" class="form-control width100p" placeholder="Enter 또는 ‘,’로 구분하여 최대 10개까지  복수 검색이 가능합니다."><?= $search['keyword'][0]; ?></textarea>
                        </div>
                        <div class="keywordDiv sec <?= $search['keyword'][1] ? '' : 'display-none'?>">
                            <div class="form-inline mgb5">
                                <?= gd_select_box('key2', 'key[]', $search['combineSearch'], null, $search['key'][1], null, 'data-target="sec"', 'form-control '); ?>
                                <button type="button" class="btn btn-sm btn-white" onclick="keywordDel('sec')">삭제</button>
                            </div>
                            <textarea name="keyword[]" class="form-control width100p" placeholder="Enter 또는 ‘,’로 구분하여 최대 10개까지  복수 검색이 가능합니다."><?= $search['keyword'][1]; ?></textarea>
                        </div>
                        <div class="keywordDiv thr <?= $search['keyword'][2] ? '' : 'display-none'?>">
                            <div class="form-inline mgb5">
                                <?= gd_select_box('key3', 'key[]', $search['combineSearch'], null, $search['key'][2], null, 'data-target="thr"', 'form-control '); ?>
                                <button type="button" class="btn btn-sm btn-white" onclick="keywordDel('thr')">삭제</button>
                            </div>
                            <textarea name="keyword[]" class="form-control width100p" placeholder="Enter 또는 ‘,’로 구분하여 최대 10개까지  복수 검색이 가능합니다."><?= $search['keyword'][2]; ?></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>기간검색</th>
                    <td colspan="3">
                        <div class="form-inline">
                            <?= gd_select_box('treatDateFl', 'treatDateFl', $search['combineTreatDate'], null, $search['treatDateFl'], null, null, 'form-control '); ?>
                            <?= gd_select_box('searchPeriod', 'searchPeriod', ["1" => "어제", "0" => "오늘", "2" => "3일", "6" => "7일", "14" => "15일", "29" => "1개월", "89" => "3개월", "179" => "6개월",], null, gd_isset($search['searchPeriod'], 6), "=직접선택=", 'data-target-name="treatDate[]"', 'form-control js-select-box-dateperiod'); ?>
                            <div class="input-group js-datepicker">
                                <input type="text" name="treatDate[]" value="<?= $search['treatDate'][0]; ?>" class="form-control width-xs">
                                <span class="input-group-addon">
                                <span class="btn-icon-calendar"></span>
                            </span>
                            </div>
                            <?= gd_select_box_by_search_time('treatTime[0]', 'treatTime[]', ':00:00', $search['treatTime'][0]); ?>
                            ~
                            <div class="input-group js-datepicker">
                                <input type="text" name="treatDate[]" value="<?= $search['treatDate'][1]; ?>" class="form-control width-xs">
                                <span class="input-group-addon">
                                <span class="btn-icon-calendar"></span>
                            </span>
                            </div>
                            <?= gd_select_box_by_search_time('treatTime[1]', 'treatTime[]', ':59:59', $search['treatTime'][1]); ?>
                            <span class="btn-group">
                            <button type="button" class="btn btn-sm btn-white js-treat-time">
                                <img src="/admin/gd_share/img/checkbox_login_on.png"/>시간 설정
                            </button>
                            <input type="checkbox" name="treatTimeFl" value="y" <?= gd_isset($checked['treatTimeFl']['y']); ?> style="display:none;"/>
                        </span>
                        </div>
                    </td>

                </tr>
                <?php } else { ?>
                <tr>
                    <th>검색어</th>
                    <td>
                        <div class="form-inline">
                            <?= gd_select_box('key', 'key', $search['combineSearch'], null, $search['key'], null, null, 'form-control '); ?>
                            <?= gd_select_box('searchKind', 'searchKind', $search['searchKindArray'], null, $search['searchKind'], null, null, 'form-control '); ?>
                            <input type="text" name="keyword" value="<?= $search['keyword']; ?>" class="form-control width300"/>
                            <div class="notice-danger notice-search-kind">
                                “검색어 부분포함“으로 검색 시 검색량에 따라 로딩 속도가 느릴 수 있으며, 검색 중 페이지가 멈출 수 있습니다.<br/>
                                안전한 검색을 위해서 “검색어 전체일치＂로 검색할 것을 권고 드립니다.
                            </div>
                        </div>
                    </td>
                    <th>FTA 적용 여부</th>
                    <td>
                        <div class="form-inline search-input mgb8">
                            <select class="form-control">
                                <option value="A">전체</option>
                                <option value="Y">동의</option>
                                <option value="N">미동의</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>기간검색</th>
                    <td>
                        <div class="form-inline">
                            <?= gd_select_box('treatDateFl', 'treatDateFl', $search['combineTreatDate'], null, $search['treatDateFl'], null, null, 'form-control '); ?>
                            <div class="input-group js-datepicker">
                                <input type="text" name="treatDate[]" value="<?= $search['treatDate'][0]; ?>" class="form-control width-xs">
                                    <span class="input-group-addon">
                                        <span class="btn-icon-calendar">
                                        </span>
                                    </span>
                            </div>
                            ~
                            <div class="input-group js-datepicker">
                                <input type="text" name="treatDate[]" value="<?= $search['treatDate'][1]; ?>" class="form-control width-xs">
                                    <span class="input-group-addon">
                                        <span class="btn-icon-calendar">
                                        </span>
                                    </span>
                            </div>

                            <?= gd_search_date(gd_isset($search['searchPeriod'], 6), 'treatDate[]', false) ?>
                        </div>
                    </td>
                    <th>구매동의여부</th>
                    <td>
                        <div class="form-inline search-input mgb8">
                            <select class="form-control">
                                <option value="A">전체</option>
                                <option value="Y">동의</option>
                                <option value="N">미동의</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>협력사</th>
                    <td>
                        <div class="form-inline">
                            <select class="form-control">
                                <option value="">전체</option>
                                <?php 
                                foreach ($scmList as $key => $value) {
                                    echo '<option value="'.$value["sno"].'">'.$value["companyNm"].'</option>';  
                                } 
                                ?>
                            </select>
                        </div>
                    </td>
                    <th>분쟁조정여부</th>
                    <td>
                        <div class="form-inline">
                            <select class="form-control">
                                <option value="A">전체</option>
                                <option value="Y">분쟁</option>
                                <option value="N">미분쟁</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>상태</th>
                    <td colspan="3">
                        <div class="form-inline">
                            <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                <input type="checkbox" name="" value=""> 전체
                            </label>
                            <?php 
                            foreach ($statusList as $key => $value) {
                            ?>
                            <label class="checkbox-inline" style="margin: 0 10px 0 0;">
                                <input type="checkbox" name="orderStatus[]" value="<?= $key; ?>"> <?= $value; ?>
                            </label>
                            <?php
                            } 
                            ?>
                        </div>
                    </td>
                </tr>
            </tbody>
            <?php } ?>
        </table>
    </div>
    <div class="table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black">
    </div>

    <?php if ($isUserHandle) { ?>
    <ul class="nav nav-tabs mgb30" role="tablist">
        <li role="presentation" <?=$search['view'] == 'exchange' ? 'class="active"' : ''?>>
            <a href="../order/order_list_user_exchange.php?view=exchange&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">교환신청 관리 (<strong>전체 <?=$userHandleCount['exchangeAll']?></strong> | <strong class="text-danger">신청 <?=$userHandleCount['exchangeRequest']?></strong> | <strong class="text-info">처리완료 <?=$userHandleCount['exchangeAccept']?>)</strong></a>
        </li>
        <li role="presentation" <?=$search['view'] == 'back' ? 'class="active"' : ''?>>
            <a href="../order/order_list_user_exchange.php?view=back&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">반품신청 관리 (<strong>전체 <?=$userHandleCount['backAll']?></strong> | <strong class="text-danger">신청 <?=$userHandleCount['backRequest']?></strong> | <strong class="text-info">처리완료 <?=$userHandleCount['backAccept']?>)</strong></a>
        </li>
        <li role="presentation" <?=$search['view'] == 'refund' ? 'class="active"' : ''?>>
            <a href="../order/order_list_user_exchange.php?view=refund&<?=$queryString ? 'searchFl=y&' . $queryString : ''?>">환불신청 관리 (<strong>전체 <?=$userHandleCount['refundAll']?></strong> | <strong class="text-danger">신청 <?=$userHandleCount['refundRequest']?></strong> | <strong class="text-info">처리완료 <?=$userHandleCount['refundAccept']?>)</strong></a>
        </li>
    </ul>

    <div class="table-sub-title">
        <?php
        switch ($search['view']) {
            case 'exchange':
                echo '교환신청 관리';
                break;
            case 'back':
                echo '반품신청 관리';
                break;
            case 'refund':
                echo '환불신청 관리';
                break;
        }
        ?>
    </div>
    <?php } ?>

    <?php
    if (!$isUserHandle && in_array(substr($currentStatusCode, 0, 1), ['','o','p','g','d','s'])) {
        if (isset($search['view'])) {
            $tableHeaderClass = 'table-header-tab';
            if (in_array($currentStatusCode, ['','o'])) {
                $actionClass = 'order';
            } elseif (in_array(substr($currentStatusCode,0, 1), ['p','g','d','s'])) {
                $actionClass = 'orderGoodsSimple';
            }
    ?>
        
    <?php
        }
    }
    ?>

    <div class="table-header <?=$tableHeaderClass?>">
        <div class="pull-left">
            검색 <strong class="text-danger"><?= number_format(gd_isset($page->recode['total'], 0)); ?></strong>개 /
            전체 <strong class="text-danger"><?= number_format(gd_isset($page->recode['amount'], 0)); ?></strong>개
            <!--( 검색된 주문 총 <?php if (!$isProvider) { ?>결제<?php } ?>금액 : <?= gd_currency_symbol() ?><span class="text-danger"><?=gd_money_format($page->recode['totalPrice'])?></span><?=gd_currency_string()?>-->
            <?php if (false && !$isProvider) { // 아직 환불에 대한 처리방법 결정되지 않음 ?>
            | 총 실결제금액 : <?= gd_currency_symbol() ?><span class="text-danger"><?=gd_money_format($page->recode['totalGoodsPrice'] + $page->recode['totalDeliveryPrice'])?></span><?=gd_currency_string()?>
                - <small>테스트확인용 상품금액: <?=number_format($page->recode['totalGoodsPrice'])?>/
                배송비: <?=number_format($page->recode['totalDeliveryPrice'])?></small>
            <?php } ?>
            <!--)-->
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-dark-gray js-partners-toggle">접기/펼치기</button>
        </div>
    </div>
    <input type="hidden" name="view" value="<?=$search['view']?>"/>
    <input type="hidden" name="searchFl" value="y">
    <input type="hidden" name="applyPath" value="<?=gd_php_self()?>?view=<?=$search['view']?>">
</form>
<!-- // 검색을 위한 form -->

<!-- 프린트 출력을 위한 form -->
<form id="frmOrderPrint" name="frmOrderPrint" action="" method="post" class="display-none">
    <input type="hidden" name="orderPrintCode" value=""/>
    <input type="hidden" name="orderPrintMode" value=""/>
</form>
<!-- // 프린트 출력을 위한 form -->

<?php if($isOrderSearchMultiGrid == 'y') { ?>

<script type="text/javascript">
    $(document).ready(function () {
        var oldKey = '';
        $('select[name="key[]"]').on('focus', function(){
            oldKey = $(this).val();
        }).on('change', function(){
            var id = $(this).data('target');
            var v = $(this).val();
            var fir = $('.keywordDiv.fir select').val();
            var sec = $('.keywordDiv.sec').is(':visible') ? $('.keywordDiv.sec select').val() : false;
            var thr = $('.keywordDiv.thr').is(':visible') ? $('.keywordDiv.thr select').val() : false;
            if((id == 'fir' && (v == sec || v == thr)) || (id == 'sec' && (v == fir || v == thr)) || (id == 'thr' && (v == fir || v == sec))) {
                alert('이미 선택된 검색어 입니다.');
                $(this).val(oldKey);
            } else {
                oldKey = $(this).val();
            }
        });

        var oldGoods = $('input[name=goodsText]').val();
        $('input[name="goodsText"]').on('focus', function(){
            oldGoods = $(this).val();
        }).on('keyup', function(){
            var v = $(this).val();
            if(!v || v != oldGoods) {
                $('input[name=goodsNo]').val('');
                oldGoods = $(this).val();
            }
        });

        $('#goodsKey').on('change', function(){
            var msg = '';
            if($(this).val() == 'og.goodsNm') msg = '상품명에 포함된 내용을 입력하세요.';
            else msg = $(this).find('option:selected').text() + ' 전체를 정확하게 입력하세요.';
            $('input[name=goodsText]').attr('placeholder', msg);
            $('input[name=goodsNo]').val('');
        });

        $('.js-treat-time').click(function(e) {
            var click = $('input[name=treatTimeFl]').is(':checked');
            if (e.isTrigger == undefined) click = (click ? false : true);
            if(click) {
                $(this).addClass('active').addClass('btn-black').removeClass('btn-white');
                $('input[name=treatTimeFl]').prop('checked', true);
                $('select[name="treatTime[]"]').show();
            } else {
                $(this).removeClass('active').removeClass('btn-black').addClass('btn-white');
                $('input[name=treatTimeFl]').prop('checked', false);
                $('select[name="treatTime[]"]').hide();


            }
        });

        function init() {
            $('#goodsKey').trigger('change');
            $('.js-treat-time').trigger('click');
            keywordDisplay();
        }
        init();
    });

    function keywordAdd() {
        var l = $('.keywordDiv:visible').length;
        if(l >= 3) {
            alert('검색어는 최대 3개까지 선택 가능합니다.');
            return false;
        } else {
            var fir = $('.keywordDiv.fir select option:selected').index();
            var sec = $('.keywordDiv.sec select option:selected').index();
            var thr = $('.keywordDiv.thr select option:selected').index();
            if($('.keywordDiv.sec').is(':visible')) {
                for(var i = 0; i < 3; i++) {
                    if(i != fir && i != sec) {
                        $('.keywordDiv.thr select option:eq('+i+')').prop('selected', 'selected');
                        break;
                    }
                }
                $('.keywordDiv.thr').removeClass('display-none');
            } else {
                for(var i = 0; i < 3; i++) {
                    if(!$('.keywordDiv.thr').is(':visible') && i != fir || i != fir && i != thr) {
                        $('.keywordDiv.sec select option:eq('+i+')').prop('selected', 'selected');
                        break;
                    }
                }
                $('.keywordDiv.sec').removeClass('display-none');
            }
        }
        keywordDisplay();
    }

    function keywordDel(classNm) {
        $('.keywordDiv.'+classNm).addClass('display-none');
        $('.keywordDiv.'+classNm).find('textarea').val('');
        keywordDisplay();
    }

    function keywordDisplay() {
        var l = $('.keywordDiv.display-none').length;
        if(l == 2) $('.keywordDiv').css({'width': '100%', 'margin-right': '0'});
        else if(l == 1) $('.keywordDiv').css({'width': '49%', 'margin-right': '1%'});
        else $('.keywordDiv').css({'width': '32.5%', 'margin-right': '0.5%'});
    }

    function layer_register(typeStr) {
        var layerFormID		= 'searchGoodsForm';

        // 레이어 창
        if (typeStr == 'goods') {
            var layerTitle = '상품 선택';
            var mode =  'multiSearch';
            var dataInputNm = 'goodsNo';
            var parentFormID = 'frmSearchOrder';
        }

        var addParam = {
            "mode": mode,
            "layerFormID": layerFormID,
            "dataInputNm": dataInputNm,
            "layerTitle": layerTitle,
            "parentFormID": parentFormID,
        };
        console.log(addParam);

        if(typeStr == 'goods'){
            addParam['scmFl'] = $('input[name="scmFl"]:checked').val();
            addParam['scmNo'] = $('input[name="scmNo"]').val();
        }

        layer_add_info(typeStr,addParam);
    }
</script>
<?php } ?>

<?php if (isset($statusSearchableRange['r3']) !== false) { ?>
<script type="text/javascript">
    $(document).ready(function () {
        // '차지백 서비스건만 보기' 체크하면 '환불완료' 자동선택
        $('input:checkbox[name*=\'pgChargeBack\']:not(:disabled)').click(function() {
            if (this.checked === true) {
                $('input:checkbox[name*=\'orderStatus[]\'][value*=\'r3\']:not(:disabled)').prop('checked', this.checked);
                $('input:checkbox[name*=\'orderStatus[]\']:not(:disabled):eq(0)').prop('checked', false);
            }
        });
        // '환불완료' 해제하면 '차지백 서비스건만 보기' 자동해제
        $('input:checkbox[name*=\'orderStatus[]\'][value*=\'r3\']:not(:disabled)').click(function() {
            if (this.checked === false) {
                $('input:checkbox[name*=\'pgChargeBack\']:not(:disabled)').prop('checked', false);
            }
        });
        // '전체' 해제하면 '차지백 서비스건만 보기' 자동해제
        $('input:checkbox[name*=\'orderStatus[]\']:not(:disabled):eq(0)').click(function() {
            if (this.checked === true) {
                $('input:checkbox[name*=\'pgChargeBack\']:not(:disabled)').prop('checked', false);
            }
        });
    });
</script>
<?php } ?>

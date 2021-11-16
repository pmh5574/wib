<style>
    .partners-tg.dn{display:none;}
</style>
<div class="page-header js-affix">
    <h3><?php echo end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <a href="../partners/partners_regist.php" class="btn btn-red-line">협력사 등록</a>
    </div>
</div>

<form id="frmSearchScm" method="get" class="js-form-enter-submit">
    <div class="table-title">
        협력사 검색
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
                <tr>
                    <th>협력사</th>
                    <td class="form-inline">
                        <input type="text" name="scmNmSearch" class="form-control width-lg" value="" placeholder="공급사 명을 입력해주세요.">
                        <input type="button" value="검색" class="btn btn-dark-gray js-artners-check" data-type="scm" data-mode="search" data-scm-commission-set="p">
                    </td>
                    <th>정산 확인 여부</th>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" name="" value="" />전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="" value="a" />확인
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="" value="c" />미확인
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>검색어</th>
                    <td>
                        <div class="form-inline">
                            
                            <?= gd_select_box('key', 'key', $combineSearch, null, $search['key']); ?>
                            <input type="text" name="keyword" value="<?php echo $search['keyword']; ?>" class="form-control"/>
                        </div>
                    </td>
                    <th>크레딧 발행 여부</th>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" name="" value="" />전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="" value="y" />발행
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="" value="x" />미발행
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>화폐</th>
                    <td>
                        <div class="form-inline">
                            <select class="form-control">
                                <option value="">전체</option>
                                <option value="e">EUR</option>
                                <option value="u">USD</option>
                            </select>
                        </div>
                    </td>
                    <th>API 연결 상태</th>
                    <td>
                        <label class="radio-inline">
                            <input type="radio" name="scmPermissionModify" value="" <?= $checked['scmPermissionModify']['']; ?>/>전체
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="scmPermissionModify" value="a" <?= $checked['scmPermissionModify']['a']; ?>/>연결
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="scmPermissionModify" value="c" <?= $checked['scmPermissionModify']['c']; ?>/>미등록
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="scmPermissionModify" value="c" <?= $checked['scmPermissionModify']['c']; ?>/>실패
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-center table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black">
    </div>

    <div class="table-header">
        <div class="pull-left">
            협력사 리스트 (검색결과
            <strong><?= number_format($page->recode['total'], 0); ?></strong>건, 전체<strong><?= number_format($page->recode['amount'], 0); ?></strong>건)
        </div>
        <div class="pull-right">
            <div class="form-inline">
                <button type="button" class="btn btn-dark-gray js-partners-toggle">접기/펼치기</button>
                <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort']); ?>
                <?php echo gd_select_box(
                    'pageNum', 'pageNum', gd_array_change_key_value(
                    [
                        10,
                        20,
                        30,
                        40,
                        50,
                        60,
                        70,
                        80,
                        90,
                        100,
                        200,
                        300,
                        500,
                    ]
                ), '개 보기', Request::get()->get('pageNum'), null
                ); ?>
            </div>
        </div>
    </div>
</form>

<form id="frmScmList" name="frmScmList" action="../scm/scm_ps.php" method="post">
    <input type="hidden" name="mode" value="deleteScmList"/>
    <div class="table-responsive">
        <table class="table table-rows">
            <thead>
            <tr>
                <th><input type="checkbox" class="js-checkall" data-target-name="chkScm[]"/></th>
                <th>상태</th>
                <th>업체명</th>
                <th class="partners-tg dn">담당자</th>
                <th>아이디</th>
                <th>업체코드</th>
                <th>국가</th>
                <th>관부가세</th>
                <th>홈페이지</th>
                <th>이메일</th>
                <th>연락처</th>
                <th class="partners-tg dn">메신저</th>
                <th class="partners-tg dn">메신저 ID</th>
                <th class="partners-tg dn">API 시작 경로</th>
                <th class="partners-tg dn">API 종료 경로</th>
                <th class="partners-tg dn">상품 URL 연동</th>
                <th class="partners-tg dn">URL 연동방식</th>
                <th class="partners-tg dn">이미지 연동</th>
                <th class="partners-tg dn">이미지 연동 방식</th>
                <th>api 연결상태</th>
                <th>전체 상품 수</th>
                <th>등록 상품 수</th>
                <th>화폐</th>
                <th>크레딧</th>
                <th>보증금</th>
                <th>비고</th>
                <th class="partners-tg dn">갱신주기</th>
                <th class="partners-tg dn">최근 주문일</th>
                <th class="partners-tg dn">등록일</th>
                <th>카테고리 매핑</th>
                <th>수정</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (empty($data) === false && is_array($data)) {
                foreach ($data as $row) {
                    if ($row['scmType'] == 'y') {
                        $row['scmType'] = '운영';
                        $disabled = 'disabled="disabled"';
                    } else if ($row['scmType'] == 'n') {
                        $row['scmType'] = '일시정지';
                        $disabled = '';
                    } else if ($row['scmType'] == 'x') {
                        $row['scmType'] = '탈퇴';
                        $disabled = '';
                    }
                    if ($row['scmKind'] == 'c') {
                        $row['scmKind'] = '본사';
                    } else if ($row['scmKind'] == 'p') {
                        $row['scmKind'] = '협력사';
                    }
                    if ($row['scmPermissionInsert'] == 'a') {
                        $row['scmPermissionInsert'] = '자동승인';
                    } else if ($row['scmPermissionInsert'] == 'c') {
                        $row['scmPermissionInsert'] = '관리자승인';
                    }
                    if ($row['scmPermissionModify'] == 'a') {
                        $row['scmPermissionModify'] = '자동승인';
                    } else if ($row['scmPermissionModify'] == 'c') {
                        $row['scmPermissionModify'] = '관리자승인';
                    }
                    if ($row['scmPermissionDelete'] == 'a') {
                        $row['scmPermissionDelete'] = '자동승인';
                    } else if ($row['scmPermissionDelete'] == 'c') {
                        $row['scmPermissionDelete'] = '관리자승인';
                    }
                    $addScmCommission = '';
                    $addScmCommissionDelivery = '';
                    $sellCnt = 1;
                    $deliveryCnt = 1;
                    $sellDivision = '';
                    $deliveryDivision = '';
                    //추가 수수료
                    foreach ($row['addCommissionData']  as $key => $val) {
                        if ($sellCnt != 1) { $sellDivision = ' / '; }
                        if ($val['commissionType'] == 'sell' && $val['commissionValue'] != 0.00) {
                            $addScmCommission .= $sellDivision.$val['commissionValue'].'%';
                            $sellCnt++;
                        }
                        if ($deliveryCnt != 1) { $deliveryDivision = ' / '; }
                        if ($val['commissionType'] == 'delivery' && $val['commissionValue'] != 0.00) {
                            $addScmCommissionDelivery .= $deliveryDivision.$val['commissionValue'].'%';
                            $deliveryCnt++;
                        }
                    }
                    //판매수수료 동일 적용
                    if ($row['scmSameCommission']) {
                        $scmCommissionDeliveryTd = $row['scmSameCommission'];
                    } else {
                        $scmCommissionDeliveryTd = $row['scmCommissionDelivery'].'%(기본)<br/>'.$addScmCommissionDelivery;
                    }

                    //담당자 이름
                    $staffNameArr = json_decode(gd_htmlspecialchars_stripslashes($row['staff']), true);
                    $staffName = '';
                    foreach ($staffNameArr as $key => $value) {
                        $staffName = $value['staffName'];
                    }
                    
                    //화폐
                    $scmUnit = '';
                    $scmUnitFl = '';
                    if($row['scmUnit'] == 'e'){
                        $scmUnit = 'EUR';
                        $scmUnitFl = '€';
                    }else if($row['scmUnit'] == 'u'){
                        $scmUnit = 'USD';
                        $scmUnitFl = '$';
                    }
                    
                    //갱신 주기 요일
                    $scmWeeks = '';
                    switch($row['scmWeeks']){
                        case '0':
                            $scmWeeks = '일요일';
                            break;
                        case '1':
                            $scmWeeks = '월요일';
                            break;
                        case '2':
                            $scmWeeks = '화요일';
                            break;
                        case '3':
                            $scmWeeks = '수요일';
                            break;
                        case '4':
                            $scmWeeks = '목요일';
                            break;
                        case '5':
                            $scmWeeks = '금요일';
                            break;
                        case '6':
                            $scmWeeks = '토요일';
                            break;
                        default:
                            $scmWeeks = '';
                            break;
                    }
                    ?>
                    <tr class="text-center">
                        <td>
                            <input type="checkbox" name="chkScm[]" value="<?= $row['scmNo']; ?>" data-type="<?= $row['scmType']; ?>" <?= $disabled; ?> />
                        </td>
                        <td><?= $row['scmType']; ?></td>
                        <td><?= $row['companyNm']; ?></td>
                        <td class="partners-tg dn"><?= $staffName; ?></td>
                        <td><?= $row['managerId'].$row['deleteText']; ?></td>
                        <td><?= $row['scmCode']; ?></td>
                        <td><?= $row['scmCountry']; ?></td>
                        <td>기본(<?= $row['scmVat']; ?>)%</td>
                        <td><?= $row['scmUrl']; ?></td>
                        <td><?= $row['scmEmail']; ?></td>
                        <td><?= $row['scmPhone']; ?></td>
                        <td class="partners-tg dn"><?= $row['scmMessenger']; ?></td>
                        <td class="partners-tg dn"><?= $row['scmMessengerId']; ?></td>
                        <td class="partners-tg dn"><?= $row['scmApiStart']; ?></td>
                        <td class="partners-tg dn"><?= $row['scmApiEnd']; ?></td>
                        <td class="partners-tg dn"><?= $row['scmGoodsUrl']; ?></td>
                        <td class="partners-tg dn"><?= $row['scmGoodsUrlFl']; ?></td>
                        <td class="partners-tg dn"><?= $row['scmImageUrl']; ?></td>
                        <td class="partners-tg dn"><?= $row['scmImageUrlFl']; ?></td>
                        <td><?= ''; ?></td>
                        <td><?= ''; ?></td>
                        <td><?= ''; ?></td>
                        <td><?= $scmUnit; ?></td>
                        <td><?= $scmUnitFl; ?><?= $row['scmCredit']; ?></td>
                        <td><?= $scmUnitFl; ?><?= $row['scmDeposit']; ?></td>
                        <td><?= ''; ?></td>
                        <td class="partners-tg dn"><?= $scmWeeks; ?></td>
                        <td class="partners-tg dn"><?= ''; ?></td>
                        <td class="partners-tg dn"><?= gd_date_format('Y-m-d', $row['regDt']); ?></td>
                        <td><?= ''; ?></td>
                        <td><a href="../partners/partners_regist.php?scmno=<?= $row['scmNo']; ?>" class="btn btn-dark-gray btn-sm">수정</a></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="13" class="no-data">
                        검색된 협력사가 없습니다.
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="table-action">
        <div class="pull-left">
            <button type="button" class="btn btn-white js-scm-delete">선택 삭제</button>
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel js-excel-download" data-target-form="frmSearchScm" data-target-list-form="frmScmList" data-target-list-sno="chkScm" data-search-count="<?=$page->recode['total']?>" data-total-count="<?=$page->recode['amount']?>">엑셀다운로드</button>
        </div>
    </div>
</form>

<div class="center"><?= $page->getPage(); ?></div>

<script type="text/javascript">
    <!--
    $(document).ready(function () {
        $('#frmScmList').validate({
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                form.submit();
            },
            rules: {
                'chkScm[]': 'required',
            },
            messages: {
                'chkScm[]': {
                    required: "삭제할 협력사를 선택해 주세요.",
                }
            }
        });

        $('.js-scm-delete').click(function (e) {
            if ($('#frmScmList').valid()) {
                BootstrapDialog.confirm({
                    type: BootstrapDialog.TYPE_DANGER,
                    title: '협력사삭제',
                    message: '선택된 ' + $('input[name*=chkScm]:checked').length + '개의 협력사를 정말로 삭제 하시겠습니까?<br />삭제 시 정보는 복구 되지 않습니다.',
                    closable: false,
                    callback: function (result) {
                        if (result) {
                            $('#frmScmList').submit();
                        }
                    }
                });
            }
        });
        
        $('.js-partners-toggle').click(function(e){
            if($('.partners-tg').hasClass('dn')){
                $('.partners-tg').removeClass('dn');
            }else{
                $('.partners-tg').addClass('dn');
            }
            
        });
    });
    //-->
</script>


<?php if($goodsColorList) {?>
    <script>
        <!--
        function selectColor(val,target,name) {
            var color = $(val).data('color');
            var title = $(val).data('content');

            if($(target+" #"+name+color).length == '0') {
                var addHtml = "<div id='"+name+ color + "' class='btn-group btn-group-xs'>";
                addHtml += "<input type='hidden' name='goodsColor[]' value='" + color + "'>";
                addHtml += "<button type='button' class='btn btn-default js-popover' data-html='true' data-content='"+title+"' data-placement='bottom' style='background:#" + color + ";'>&nbsp;&nbsp;&nbsp;</button>";
                addHtml += "<button type='button' class='btn btn-icon-delete' data-toggle='delete' data-target='#"+name+ color + "'>삭제</button></div>";
            }
            $(target+" #selectColorLayer").append(addHtml);

            $('.js-popover').popover({trigger: 'hover',container: '#content',});
        }
        //-->
    </script>
<?php } ?>


<form id="frmSearchGoods" name="frmSearchGoods" method="get" class="js-form-enter-submit">
    <div class="table-title gd-help-manual">
        <?php if($search['delFl'] =='y') { echo "삭제 "; } ?>상품 검색
        <?php if(empty($searchConfigButton) && $searchConfigButton != 'hide'){?>
        <span class="search"><button type="button" class="btn btn-sm btn-black" onclick="set_search_config(this.form)">검색설정저장</button></span>
        <?php }?>
    </div>

    <div class="search-detail-box">
        <input type="hidden" name="detailSearch" value="<?=$search['detailSearch']; ?>"/>
        <input type="hidden" name="delFl" value="<?=$search['delFl']; ?>"/>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tbody>
                <?php if(gd_use_provider() === true) { ?>
                <?php if(gd_is_provider() === false) { ?>
                <tr>
                    <th>공급사 구분</th>
                    <td colspan="3">
                        <div class="form-inline">
                            <input type="hidden" name="scmFl" value="y" />
                            <input type="hidden" name="managerId" value="<?= gd_isset($search['managerId']); ?>">
                            <input type="text" name="scmNmSearch" class="form-control width-lg" value="<?php if($search['scmFl'] != 'all') echo $search['scmNoNm'][0]; ?>" placeholder="협력사 명을 입력해주세요.">
                            <input type="button" value="검색" class="btn btn-dark-gray js-artners-check" data-type="scm" data-mode="search" data-scm-commission-set="p">
                        </div>
                    </td>
                </tr>
                <?php } ?>
                <?php } ?>
                <tr>
                    <th>검색어</th>
                    <td colspan="3">
                        <div class="form-inline">
                            <?=gd_select_box('key', 'key', $search['combineSearch'], null, $search['key'], null); ?>
                            <input type="text" name="keyword" value="<?=$search['keyword']; ?>" class="form-control"/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>기간검색</th>
                    <td colspan="3">
                        <div class="form-inline">
                            <select name="searchDateFl" class="form-control">
                                <?php if($search['delFl'] =='y') { ?>
                                    <option value="delDt" <?=gd_isset($selected['searchDateFl']['delDt']); ?>>삭제일</option>
                                <?php } ?>
                                <option value="regDt" <?=gd_isset($selected['searchDateFl']['regDt']); ?>>등록일</option>
                                <option value="modDt" <?=gd_isset($selected['searchDateFl']['modDt']); ?>>수정일</option>
                            </select>

                            <div class="input-group js-datepicker">
                                <input type="text" class="form-control width-xs" name="searchDate[]" value="<?=$search['searchDate'][0]; ?>" />
                                <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                            </div>
                            ~
                            <div class="input-group js-datepicker">
                                <input type="text" class="form-control width-xs" name="searchDate[]" value="<?=$search['searchDate'][1]; ?>" />
                                <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                            </div>
                            <?= gd_search_date($search['searchPeriod']) ?>
                        </div>
                    </td>
                </tr>
            </tbody>

        </table>
        
    </div>

    <div class="table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black">
    </div>


    <div class="table-header">
        <div class="pull-left">
            검색 <strong><?=number_format($page->recode['total']);?></strong>개 /
            전체 <strong><?=number_format($page->recode['amount']);?></strong>개
            <?php
            if($stateCount) {
            ?>
            | 품절 <?=number_format($stateCount['soldOutCnt']);?>개
            | 노출 : PC <?=number_format($stateCount['pcDisplayCnt']);?>개 / 모바일 <?=number_format($stateCount['mobileDisplayCnt']);?>개
            | 미노출 : PC <?=number_format($stateCount['pcNoDisplayCnt']);?>개 / 모바일 <?=number_format($stateCount['mobileNoDisplayCnt']);?>개
            <?php
            }
            ?>
        </div>
        <div class="pull-right form-inline">
            <button type="button" class="btn btn-dark-gray js-partners-toggle">접기/펼치기</button>
            <?=gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort'], null); ?>
            <?=gd_select_box('pageNum', 'pageNum', gd_array_change_key_value([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 500]), '개 보기', Request::get()->get('pageNum'), null); ?>
            <?php
            if($goodsBatchStockAdminGridMode == 'goods_batch_stock_list'){
                ?><button type="button" class="js-layer-register btn btn-sm btn-black" style="height: 27px !important;" data-type="goods_batch_stock_grid_config" data-goods-batch-stock-grid-mode="<?=$goodsBatchStockAdminGridMode?>" data-title="조회항목 설정">조회항목설정</button><?php
            }
            ?>
        </div>
    </div>
    <input type="hidden" name="searchFl" value="y">
    <input type="hidden" name="applyPath" value="<?=gd_php_self()?>">
</form>
<script>
function brand_del(){
    $('input[name=brandCdNm]').val('');
}
</script>

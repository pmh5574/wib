<div class="page-header js-affix">
    <h3><?php echo end($naviMenu->location); ?>
        <small>취소/환불/반품/교환을 포함한 전체 주문리스트입니다.</small>
    </h3>
    <?php if (!isset($isProvider) && $isProvider != true) { ?>
        <div class="btn-group">
            <a href="order_write.php" class="btn btn-red-line">수기주문 등록</a>
        </div>
    <?php } ?>
</div>

<?php include $layoutOrderSearchForm;// 검색 및 프린트 폼 ?>

<form id="frmOrderStatus" action="./order_ps.php" method="post">
    <input type="hidden" name="mode" value="combine_status_change"/>
    <input type="hidden" id="orderStatus" name="changeStatus" value=""/>
    <div class="table-action-dropdown">
        <div class="table-action mgt0 mgb0">
            <?php if ($search['view'] !== 'order') { ?>
                <div class="pull-left form-inline">
                    <span class="action-title">선택한 주문을</span>
                    <?php echo gd_select_box('orderStatusTop', null, $selectBoxOrderStatus, null, null, '=주문상태='); ?>
                    <button type="submit" class="btn btn-white js-order-status"/>
                    일괄처리</button>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php include $layoutOrderList;// 주문리스트 ?>

    <div class="table-action">
        <?php if ($search['view'] !== 'order') { ?>
            <div class="pull-left form-inline">
                <span class="action-title">선택한 주문을</span>
                <?php echo gd_select_box('orderStatusBottom', 'orderStatusBottom', $selectBoxOrderStatus, null, null, '=주문상태='); ?>
                <button type="submit" class="btn btn-white js-order-status"/>
                일괄처리</button>
            </div>
        <?php } ?>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel js-excel-download" data-target-form="frmSearchOrder" data-search-count="<?= $page->recode['total'] ?>" data-total-count="<?= $page->recode['amount'] ?>"
                    data-state-code="<?= $currentStatusCode ?>" data-target-list-form="frmOrderStatus" data-target-list-sno="statusCheck">엑셀다운로드
            </button>
        </div>
    </div>
</form>
<div class="text-center"><?= $page->getPage(); ?></div>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/orderList.js?ts=<?= time(); ?>"></script>


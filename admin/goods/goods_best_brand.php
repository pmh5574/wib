<script type="text/javascript">
        

    //211108 디자인위브 mh 추가 상품 관리
    function goods_search_popup() {
        var addParam = {
            "mode": 'checkbox',
        };
        
        best_layer_add_info('best_brand', addParam);
    }
    
    function best_layer_add_info(fileCd, addParam) {
        if ($.type(addParam) != 'object') var addParam = {};

        if (addParam['layerFormID'] == undefined) addParam['layerFormID'] = 'addSearchForm';
        if (addParam['dataFormID'] == undefined) addParam['dataFormID'] = 'info_' + fileCd;
        if (addParam['parentFormID'] == undefined) addParam['parentFormID'] = fileCd + 'Layer';
        if (addParam['dataInputNm'] == undefined) addParam['dataInputNm'] = '';

        var loadChk = $('#' + addParam['layerFormID']).length;
        var title = '';
        var dataInputNm = addParam['dataInputNm'];

        switch (fileCd) {

            case 'best_brand':
                title = "브랜드";
                addParam['size'] = "wide";
                addParam['dataInputNm'] = dataInputNm != '' ? dataInputNm : fileCd + "Cd";
                break;
        }
        if (addParam['layerTitle'] == undefined) {
            addParam['layerTitle'] = title;
        }

        $.ajax({
            url: '../share/layer_' + fileCd + '.php',
            type: 'get',
            data: addParam,
            async: false,
            success: function (data) {
                if (loadChk == 0) {
                    data = '<div id="' + addParam['layerFormID'] + '">' + data + '</div>';
                }
                var layerForm = data;
                var configure = {
                    title: addParam['layerTitle'],
                    size: get_layer_size(addParam['size']),
                    message: $(layerForm),
                    closable: true
                };
                if (typeof addParam['events'] == 'object') {
                    BootstrapDialog.show($.extend({}, configure, addParam['events']));
                } else {
                    BootstrapDialog.show(configure);
                }

            }
        });
    }
    
    function delete_option() {

        var chkCnt = $('input[name="layer_brand[]"]:checked').length;
        if (chkCnt == 0) {
            alert('선택된 브랜드가 없습니다.');
            return;
        }
        if (confirm('선택한 ' + chkCnt + '개 브랜드를 삭제하시겠습니까?')) {
            $('input[name="layer_brand[]"]:checked').each(function () {
                field_remove('tbl_brand_' + $(this).val());
            });

            var cnt = $('input[name="layer_brand[]"]').length;

            $('input[name="layer_brand[]"]').each(function (idx) {
                $("num_cnt_" + $(this).val()).html(idx+1);
            });

        }
    }

</script>
<form id="frmGoods" name="frmGoods" action="./goods_best_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
    <input type="hidden" name="mode" value="best_goods_update"/>
    

    <div class="page-header js-affix">
        <h3><?=end($naviMenu->location); ?> 수정</h3>
        <div class="btn-group">
            <input type="submit" value="저장" class="btn btn-red" />
        </div>
    </div>

    
    <div class="table-title">
        인기 브랜드 설정
    </div>
    <div id="best_brandLayer" class="js-excludeGroup">
        <table cellpadding="0" cellpadding="0" width="100%" id="tbl_add_goods_set" class="table table-rows">
            <thead>
                <tr id="goodsRegisteredTrArea">
                    <th class="width2p"><input type="checkbox" id="allCheck" value="y" class="js-checkall" data-target-name="itemGoodsNo[]"/></th>
                    <th class="width2p">번호</th>
                    <th class="">브랜드명</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>

        <div class="table-action">
            <div class="pull-left">
                <button class="btn btn-white checkDelete" type="button" onclick="delete_option()">선택 삭제</button>
            </div>
            <div class="pull-right">
                <button class="btn btn-white checkRegister" type="button" onclick="goods_search_popup()">브랜드 선택</button>
            </div>
        </div>
    </div>


</form>



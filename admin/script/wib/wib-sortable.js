/**
 * pid setTimeOut용
 * positions 몇번째에 add_goods_fix값이 있는지
 * add_goods_fix el 배열 값
 * goodsArrList 상품번호 배열 리스트
 */
var goodsArrList = [];
var goodsChoice = new GoodsChoiceController();
$(function () {
   
    var pid,
            positions,
            freezed;

    //sortTable
    var el = document.getElementById('goods_result');

    var sortable = new Sortable(el, {
        group: {
            name: 'shared', // 공유 옵션,
            pull: false
        },
        scroll: true,
        animation: 150, // 속도
        filter: '.add_goods_fix', // sort 안될 클래스
        onStart: function (evt) {

            //freezed에 add_goods_fix를 배열로 만들어서 넣기
            freezed = [].slice.call(this.el.querySelectorAll('.add_goods_fix'));

            //몇번째 인덱스에 있는지 넣기
            positions = freezed.map(function (el) {
                return Sortable.utils.index(el);
            });
        },
        onMove: function (evt, originalEvent) {
            var vector,
                    freeze = false;

            if($('.moveEventImg').length == 0){
                $(evt.dragged).append('<td><span class="moveEventImg"></span></td>');
            }
            
            clearTimeout(pid);
            

            //뒤에 실행 2
            pid = setTimeout(function () {
                var list = evt.to;
                for(var j=0; j<freezed.length; j++){
                    freezed.forEach(function (el, i) {
                        var idx = positions[i];

                        //freeazed(add_goods_fix)배열안에 엘리먼트값이랑 현재 리스트 엘리먼트 값이 위치가 다를경우
                        if (list.children[idx] !== el) {
                            var realIdx = Sortable.utils.index(el);

                            //(realIdx < idx) true면 1, false면 0을 더함 앞으로 이동된건지 뒤로 이동된건지 체크
                            list.insertBefore(el, list.children[idx + (realIdx < idx)]);
                        }
                    });
                }
                

            }, 0);

            //먼저 실행 1
            freezed.forEach(function (el, i) {
                if (el === evt.related) {
                    freeze = true;
                }

                if (evt.related.nextElementSibling === el && evt.relatedRect.top < evt.draggedRect.top) {
                    vector = -1;
                }
            });

            //freeze가 true면 위치를 바꾸려는 게 add_goods_fix라 false반환
            return freeze ? false : vector;
        },
        onEnd : function(evt){
            $(evt.item).find('.moveEventImg').closest('td').remove();
        }
    });

    var el2 = document.getElementById('goods_sub_result');

    //검색 sortable
    var sortableSearch = new Sortable(el2, {
        group: {
            name: 'shared',
            put: false,
            pull: 'clone'
        },
        sort: false,
        animation: 150,
        draggable: '.add_goods_free',
        onStart: function (evt) {
            //freezed에 sortable에 add_goods_fix를 배열로 만들어서 넣기
            freezed = [].slice.call(sortable.el.querySelectorAll('.add_goods_fix'));
            
            if($('#goods_result tr').length == 0){
                $('#tbl_add_goods_result').css('height', '500px');

            }else{
                $('#tbl_add_goods_result').css('height', '');
                $('#tbl_add_goods_result .moveEventImg').css('height', '100%');
            }
            
            positions = freezed.map(function (el) {
                return Sortable.utils.index(el);
            });
        },
        onMove: function (evt, originalEvent) {
            var vector,
                    freeze = false;
    
            if($('#goods_result tr').length == '0'){
                $('#tbl_add_goods_result').css('height', '');
            }
            if($('.searchWall').length == 0){
                $('#goods_sub_result').closest('#frmList').append('<div class="searchWall"></div>');
            }
            
            if($('.moveEventImg').length == 0){
                $(evt.dragged).append('<td><span class="moveEventImg"></span></td>');
            }

            sortableSearch.disabled = false;
            
            if (evt.to === evt.from) {
                evt.to.append(evt.dragged);

                return false;
            }

            clearTimeout(pid);
            pid = setTimeout(function () {
                var list = evt.to;
                for(var j=0; j<freezed.length; j++){
                    if (evt.to === evt.from) {
                        evt.to.append(evt.dragged);
                        return false;
                    }

                    freezed.forEach(function (el, i) {
                        var idx = positions[i];

                        if (list.children[idx] !== el) {
                            var realIdx = Sortable.utils.index(el);

                            list.insertBefore(el, list.children[idx + (realIdx < idx)]);
                        }
                    });
                }
                
            }, 0);


            freezed.forEach(function (el, i) {
                if (el === evt.related) {
                    freeze = true;
                }

                if (evt.related.nextElementSibling === el && evt.relatedRect.top < evt.draggedRect.top) {
                    vector = -1;
                }
            });

            return freeze ? false : vector;
        },
        onEnd: function (evt) {

            setGoodsArrList();
            setShareSort();
            
            if($('#goods_result tr').length == 1){
//                $('#tbl_add_goods_result').css('margin-bottom', '20px');   
            }
            
            $(evt.item).find('.moveEventImg').closest('td').remove();
            $('.searchWall').remove();
            
            var goodsNo = parseInt(evt.item.dataset.goodsNo);
            
            
            //해당 goodsNo값이 있으면 추가된걸로 판단
            if (goodsArrList.indexOf(goodsNo) != -1) {

                evt.clone.classList.remove('add_goods_free');
                $(evt.clone).prepend('<div class="status"><span>진열중</span></div>');
            }
        }
    });

    //저장
    $('.save').click(function () {
        $('#listFrm').submit();
    });

    $('input[name="keyword"]').keydown(function () {
        if (window.event.keyCode == 13) {
            $('.js-search').trigger('click');
        }
    });
    
    $('#sort').change(function(){
        $('.js-search').trigger('click');
    });
    
    $('.allCheck').change(function(){
       
       if($('#goods_sub_result tr').length > 0){
           
       }else{
           $(this).prop('checked', false); 
       }
    });

    setGoodsArrList();
    
    //고정값 사용 여부
    $(document).on('change', '.gallery_button input[type="checkbox"]', function () {
        if ($(this).prop('checked')) {
            $(this).closest('li').removeClass('add_goods_free').addClass('add_goods_fix');
        } else {
            $(this).closest('li').removeClass('add_goods_fix').addClass('add_goods_free');
        }
    });

    //엘리먼트 삭제
    $(document).on('click', '#delGoods', function () {
        
        var _this = this;
        
        var goodsNo = [];

        $('#goods_result input[name="itemGoodsNo[]"]').each(function(){
            goodsNo.push($(this).val());
        });
        
        
        freezed = [].slice.call(sortable.el.querySelectorAll('.add_goods_fix'));
        var list = document.getElementById('goods_result');

        positions = freezed.map(function (el) {
            return Sortable.utils.index(el);
        });
        
        if(goodsArrList.length > 0){

            for(var i=0;i<goodsArrList.length;i++){
                
                if(goodsNo.indexOf(goodsArrList[i]) == -1){
                    
                    $('#goods_sub_result tr input[name="itemGoodsNo[]"]').each(function(){

                        if($(this).val() == goodsArrList[i]){

                            $(this).closest('tr').addClass('add_goods_free');
                            $(this).closest('tr').find('.status').remove();
                        }
                    });

                }
            }

        }


        setGoodsArrList();

        $('#goods_sub_result tr input[name="itemGoodsNo[]"]').each(function(){
            var goods_no = $(this).val();

            if(goods_no == goodsNo){

            }
        });
            

        clearTimeout(pid);
        pid = setTimeout(function () {

            freezed.forEach(function (el, i) {
                var idx = positions[i];

                if (list.children[idx] !== el) {

                    var realIdx = Sortable.utils.index(el);

                    //삭제시키는 li는 제외
                    if(_this.closest('li') !== el){
                        list.insertBefore(el, list.children[idx + (realIdx < idx)]);
                    }

                }
            });
        }, 0);

        
        
    });
});



// goodsArrList 세팅
function setGoodsArrList()
{
    goodsArrList = [];
    $('#goods_result tr').each(function () {
        goodsArrList.push(parseInt($(this).find('input[name="itemGoodsNo[]"]').val()));
    });

    $('#goods_sub_result tr input[name="itemGoodsNo[]"]').each(function(){

        if(goodsArrList.indexOf(parseInt($(this).val())) != -1){
            if($(this).closest('tr').hasClass('add_goods_free')){
                $(this).closest('tr').removeClass('add_goods_free');
                $(this).closest('tr').append('<div class="status"><span>진열중</span></div>');
            }
            
        }
    });
    
}

function setShareSort()
{
    var goodsChoiceIframeID = 'iframe_goodsChoiceList'; //상품선택 iframe ID
    var registeredTableID = 'tbl_add_goods_result';
    var searchedTableID = 'tbl_add_goods';
    var fixDataArr = new Array();
    var countCheckGoods = $('#registeredCheckedGoodsCountMsg').html($("#tbl_add_goods_result").find('input[name="itemGoodsNo[]"]:checked').closest('tr').length);
    
    var tblGoods = $("#" + goodsChoiceIframeID).contents();
        var duplicateCnt = 0;
        var addCnt = 0;
        var registerCount = $("#" + registeredTableID).find('input[name="itemGoodsNo[]"]').length;
        var $this = this;

        tblGoods.find('.add_goods_free.sortable-chosen input[name="itemGoodsNo[]"]').each(function () {

            var sel_id = $(this).attr('id');    // 상품코드
            var sel_row = $(this);
            var row = sel_row.closest("tr");
            var table = sel_row.closest("table");

            if ($("#" + registeredTableID).find('#' + sel_id).length == 0) {

                row.detach();
                if(registerCount > 0 ) $("#" + registeredTableID).prepend(row);
                else $("#" + registeredTableID).append(row);
                $('#'+sel_id).on('click', countCheckGoods);

                addCnt++;

            } else duplicateCnt++;


            $("#" + registeredTableID).find('#' + sel_id).prop('checked', false);

            //itemGoodsNo값을 체크하여 선택 리스트에 추가함
            if (typeof $('#selectedGoodsList').val() !== "undefined") {
                goodsChoice.setSelectedGoodsList($(this).val(), false);
            }

        });


        if (duplicateCnt > 0 && addCnt > 0) alert('중복된 데이터' + duplicateCnt + '건을 제외한 ' + addCnt + '건의 데이터가 추가되었습니다.');
        else if (duplicateCnt > 0 && addCnt == 0) alert('중복된 데이터' + duplicateCnt + '건이 있습니다.');

        goodsChoice.getGoodsReSort();
}
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
        multiDrag: true, // Enable multi-drag
	selectedClass: 'selected', // The class applied to the selected items
        sort: false,
        animation: 150,
        draggable: '.add_goods_free',
        onStart: function (evt) {
            //freezed에 sortable에 add_goods_fix를 배열로 만들어서 넣기
            freezed = [].slice.call(sortable.el.querySelectorAll('.add_goods_fix'));

            positions = freezed.map(function (el) {
                return Sortable.utils.index(el);
            });
        },
        onMove: function (evt, originalEvent) {
            var vector,
                    freeze = false;
            
            $('#goods_sub_result').before('<div class="searchWall"></div>');
            
            if (evt.to === evt.from) {
                evt.to.append(evt.dragged);

                return false;
            }

            clearTimeout(pid);
            pid = setTimeout(function () {
                var list = evt.to;
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

    //검색시
    $('.js-search').click(function () {

        if (!$('#searchFrm input[name="keyword"]').val()) {
            alert('상품명을 입력해 주세요.');
            $('input[name="keyword"]').focus();
            return false;
        }

        var dataList = $('#searchFrm').serialize();

        $.ajax({
            url: '../share/layer_drag_and_drop_ps.php',
            type: 'post',
            data: dataList,
            dataType: 'json',
            success: function (result) {

                if (result.result == '0') {
                    
                    var data = result.data;

                    var _html = '';
                    for (var i = 0; i < data.length; i++) {
                        var _checked = '';
                        if (data[i].fixSort > 0) {
                            var addClassText = ' class="add_goods_fix" ';
                            _checked = 'checked';
                        } else {
                            var addClassText = ' class="add_goods_free" ';
                        }
                        
                        var moreHtml = '';

                        if (goodsArrList.indexOf(parseInt(data[i].goodsNo)) != -1) {
                            
                            moreHtml += '<div class="status"><span>진열중</span></div>';
                            addClassText = '';
                        }

                        _html += '<li id="tbl_add_goods_' + data[i].goodsNo + '"' + addClassText + ' data-goods-no="'+data[i].goodsNo+'">';
                        _html += moreHtml;
                        _html += '<div class="gallery_thumbnail">';
                        _html += '<img src="/data/goods/' + data[i].imagePath + data[i].imageName + '" alt="' + data[i].goodsNm + '" tile="' + data[i].goodsNm + '">';
                        _html += '</div>';
                        _html += '<div class="gallery_description">';
                        _html += '<div class="inner">';
                        _html += '<div class="info">';
                        _html += '<span class="code">' + data[i].goodsNo + '</span>';
                        _html += '</div>';
                        _html += '<strong class="product">' + data[i].goodsNm + '</strong>';
                        _html += '</div>';
                        _html += '<div class="gallery_button">';
                        _html += '<input type="checkbox" value="a" ' + _checked + '>';
                        _html += '<button type="button" class="delete_button">x</button>';
                        _html += '</div>';
                        _html += '</div>';
                        _html += '</li>';

                    }

                    $('#searchGoodsList ul').html(_html);

                } else if (result.result == '1') {
                    $('#searchGoodsList ul').html('검색하신 상품이 없습니다.');
                }

            }
        });
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
    $(document).on('click', '.delete_button', function () {
        var _this = this;
        
        freezed = [].slice.call(sortable.el.querySelectorAll('.add_goods_fix'));
        var list = document.getElementById('goods_result');

        positions = freezed.map(function (el) {
            return Sortable.utils.index(el);
        });
        
        var goodsNo = parseInt($(this).closest('li').data('goods-no'));
        $(this).closest('li').remove();
        setGoodsArrList();

        if(goodsArrList.indexOf(goodsNo) == -1){
            $('#searchGoodsList li').each(function(){
                var goods_no = $(this).data('goods-no');
                if(goods_no == goodsNo){
                    $(this).addClass('add_goods_free');
                    $(this).find('.status').remove();
                }
            });
        }

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
    console.log(goodsArrList);
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
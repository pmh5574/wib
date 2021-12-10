/**
 * pid setTimeOut용
 * positions 몇번째에 add_goods_sort_fix값이 있는지
 * add_goods_sort_fix el 배열 값
 * goodsArrList 상품번호 배열 리스트
 */
var goodsArrList = [];
$(function () {
    var pid,
        positions,
        freezed;

    //sortTable
    var el = document.getElementById('goods_result');

    var sortable = new Sortable(el, {
        animation: 150, // 속도
        handle: '.add_goods_free',
        filter: '.add_goods_sort_fix', // sort 안될 클래스
        onStart: function (evt) {

            //freezed에 add_goods_sort_fix를 배열로 만들어서 넣기
            freezed = [].slice.call(this.el.querySelectorAll('.add_goods_sort_fix, .add_goods_fix'));

            //몇번째 인덱스에 있는지 넣기
            positions = freezed.map(function (el) {
                return Sortable.utils.index(el);
            });

            this._currentOrder = this.toArray();

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

                for(var j=0; j<freezed.length;j++){
                    freezed.forEach(function (el, i) {
                        var idx = positions[i];

                        //freeazed(add_goods_sort_fix)배열안에 엘리먼트값이랑 현재 리스트 엘리먼트 값이 위치가 다를경우
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

            //freeze가 true면 위치를 바꾸려는 게 add_goods_sort_fix라 false반환
            return freeze ? false : vector;
        },

        onEnd : function(evt){
            goodsChoiceFunc.reSort();  
            $(evt.item).find('.moveEventImg').closest('td').remove();
        }
    });
    
    $('.allCheck').change(function(){
       
       if($('#goods_sub_result li').length > 0){
           
       }else{
           $(this).prop('checked', false); 
       }
    });
    
    $('.goodsChoice_fixUpBtn').click(function(){
        
        $("#goods_result tr").removeClass('add_goods_sort_fix');
    });


    setGoodsArrList();
    
    //고정값 사용 여부
    $(document).on('click', '#goods_result tr', function () {
        
        if (!$(this).hasClass('add_goods_sort_fix') && !$(this).hasClass('add_goods_fix')) {
            $(this).closest('tr').removeClass('add_goods_free').addClass('add_goods_sort_fix');
        } else if(!$(this).hasClass('add_goods_fix')){
            $(this).closest('tr').removeClass('add_goods_sort_fix').addClass('add_goods_free');
        }
    });
    
});


// goodsArrList 세팅
function setGoodsArrList()
{
    goodsArrList = [];
    $('#goods_result li').each(function () {
        goodsArrList.push(parseInt($(this).data('goods-no')));
    });

}
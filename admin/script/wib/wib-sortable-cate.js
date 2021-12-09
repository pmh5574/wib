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
        freezed,
        thEl;

    //sortTable
    var el = document.getElementById('goods_result');

    var sortable = new Sortable(el, {
        animation: 150, // 속도
        filter: '.add_goods_sort_fix', // sort 안될 클래스
        preventOnFilter : false,
        dataIdAttr : 'data-sort'
        // Called when creating a clone of element
	
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
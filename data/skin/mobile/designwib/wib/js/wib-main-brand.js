$(function(){
    //뉴니아, 프리미엄 멀티샵 브랜드 상품 더보기 클릭시
    $('.brand_best_goods').click(function(e){
        e.preventDefault();
        var _this = this;
        
        var page = $(_this).data('page');
        var brandCd = $(_this).data('brancd');
        
        $(this).data('page', parseInt(page)+1);
        
        $.ajax({
            url : '../goods/goods_main_brand_list.php',
            type : 'get',
            data : {
                'page' : page,
                'brandCd' : brandCd
            },
            success : function(data){
                if($(data).find('.no_bx').length > 0){
                    alert('더이상 상품이 없습니다');
                    $(_this).hide();
                }else{
                    $('.main_wrap_best_brand_list_'+brandCd).append(data);
                }
            }
        });
        
    });
});
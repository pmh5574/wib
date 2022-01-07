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
                if($(data).find('.goods_no_data').length > 0){
                    alert('더이상 상품이 없습니다');
                    $(_this).hide();
                }else{
                    $('.main_wrap_'+brandCd).append(data);
                }
            }
        });
        
    });
    
    setTimeout(console.log.bind(console,"%cDESIGN BY %c디자인위브 %c1566-6681 ", "color:#4ab8ff;", "color:#4ab8ff;font-size:24px;font-weight:bold;font-family:Noto Sans KR, sans-serif;", "color:#4ab8ff;"),0);
});
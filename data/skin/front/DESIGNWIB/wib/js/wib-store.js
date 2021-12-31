$(function(){
    
    //버튼 클릭시 해당하는 페이지 ajax로 불러오기
    $('.board_list ul.total_list li').on('click',function(){

        var sno = $(this).data('sno');

        $.ajax({
            url : '/board/layer_view_list.php',
            type : 'post',
            data : {
               'sno' : sno
            } ,
            success : function(data){
                $('.morePage').hide();
                $('#board_detail .cont_box').html('');
                $('#board_detail .cont_box').html(data);
                $('#board_detail .cont_box').addClass('on');
                $(".store_list").addClass('fix');
                
                $('.map .point').removeClass('on');
                $('.map .point').css('z-index', 0);
                $('.map .point').each(function(){
                    if($(this).data('sno') == sno){
                        $(this).addClass('on');
                        $(this).css('z-index', 10);
                    }
                });
                
                var img;
                var imgPop;
                if($(data).find('.attach').length > 0){
                    img = new Swiper(".cont_all .attach", {
                        slidesPerView: 1,
                        allowTouchMove: true,
                        observer: true,
                        observeParents: true,
                        loop:true,
                        pagination: {
                          el: ".swiper-pagination",
                          type: "progressbar"
                        },
                        navigation: {
                          nextEl: ".cont_all .nextBtn",
                          prevEl: ".cont_all .prevBtn"
                        },
                    });

                    imgPop = new Swiper(".img_pop .attach", {
                            speed: 800,
                            observer: true,
                            observeParents: true,
                            loop:true,
                            navigation: {
                              nextEl: ".img_pop .nextBtn",
                              prevEl: ".img_pop .prevBtn"
                            },
                    });

                    img.controller.control = imgPop;
                    imgPop.controller.control = img;
                }


                function cursorMove(e){
                        var $cursor = $(".pop_close");
                        TweenMax.to(
                                $cursor, 0.5, {
                                x: e.clientX - 15,
                                y: e.clientY - 20,
                                ease: Power3.easeOut
                        });
                }	

                $("#img_box").mousemove(cursorMove);

            }

			
        });
    });  
    
    //map 포인트 클릭시 상세보기 동일하게
    $('.map .point').click(function(){
        if($(this).hasClass('on')){
            $('#board_detail .cont_box .back_btn').trigger('click');
        }else{
            var sno = $(this).data('sno');
        
            $('.board_list ul.total_list li').each(function(){
                if($(this).data('sno') == sno){
                    $(this).trigger('click');
                }
            });
        }
        
    });
    
    //마우스 오버시 z-index값 변경
    $('.map .point').mouseenter(function(){
        $(this).css('z-index', 11);
    });
    
    //마우스 leave z-index값 변경
    $('.map .point').mouseleave(function(){
        $(this).css('z-index', 0);
        
        //상세보기 켜져있으면 해당 매장 z-index값 유지
        if($('.cont_box').hasClass('on')){
            $('.map .point.on').css('z-index', 10);
        }
    });
	
});



//이미지 팝업
$(document).on('click', '#board_detail .cont_box .cont_all .attach img', function(e){
	e.preventDefault();
	$('.sub_content').css('z-index','60');
    $('.dark_bg, .img_pop, .img_pop_wrap').addClass('on');
});

//이미지 팝업 닫기
$(document).on('click', '#img_box', function(e){
   if(!$(e.target).hasClass("attach")){
	   if(!$(e.target).hasClass("swiper-btn")){
			if(!$(e.target).hasClass("swiper-slide")){
				$('.dark_bg, .img_pop, .img_pop_wrap').removeClass('on');
				$('.sub_content').css('z-index','10');
			}
	   }
	} 
});

//지역검색
$(document).on('change', 'select[name="storeSearch"]', function(){
    $('select[name="searchWord"]').val('');
    $('#frmList').submit();
});

//매장검색
$(document).on('change', 'select[name="searchWord"]', function(){
    $('#frmList').append('<input type="hidden" name="searchField" value="subject">');
    $('#frmList').submit();
});

//상세보기에서 뒤로가기
$(document).on('click', '#board_detail .cont_box .back_btn', function(){

    $(".store_list").removeClass('fix');
    $("#board_detail .cont_box").removeClass('on');
    $(".map .point").removeClass('on');
    $(".map .point").css('z-index', 0);
    
});








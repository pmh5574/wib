$(function(){
    
    $('.board_list ul.total_list li').on('click',function(){

        var sno = $(this).data('sno');
        var title = $(this).data('title');
        var address = $(this).data('address');
        var addressSub = $(this).data('addresssub');
        var number = $(this).data('number');

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


$(document).on('change', 'select[name="storeSearch"]', function(){
    $('#frmList').submit();
});

$(document).on('change', 'select[name="searchWord"]', function(){
    $('#frmList').append('<input type="hidden" name="searchField" value="subject">');
    
    $('#frmList').submit();
});

$(document).on('click', '#board_detail .cont_box .back_btn', function(){

    $(".store_list").removeClass('fix');
    $("#board_detail .cont_box").removeClass('on');

});








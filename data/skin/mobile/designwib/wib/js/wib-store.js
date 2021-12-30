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

				$('#board_detail .cont_box').html('');
				$('#board_detail .cont_box').html(data);
				$('#board_detail .cont_box').addClass('on');
				$(".store_list").addClass('fix');
				

            }

			
        });
    });    

});





//이미지 팝업
$(document).on('click', '#board_detail .cont_box .cont_all .attach img', function(){

    $('.dark_bg, .img_pop').addClass('on');
});

$(document).on('click', '#board_detail .cont_box .back_btn', function(){

    $(".store_list").removeClass('fix');
    $("#board_detail .cont_box").removeClass('on');

});



//이미지 팝업 닫기
$(document).on('click', '.img_pop .pop_close', function(){
    $('.dark_bg, .img_pop').removeClass('on');
});


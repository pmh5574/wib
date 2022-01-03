$(function(){
    
    //버튼 클릭시 해당하는 페이지 ajax로 불러오기
//    $('.board_list ul.total_list li').on('click',function(){
//
//        var sno = $(this).data('sno');
//
//        $.ajax({
//            url : '/board/layer_view_list.php',
//            type : 'post',
//            data : {
//               'sno' : sno
//            } ,
//            success : function(data){
//                $('.morePage').hide();
//                $('#board_detail .cont_box').html('');
//                $('#board_detail .cont_box').html(data);
//                $('#board_detail .cont_box').addClass('on');
//                $(".store_list").addClass('fix');
//                
//                $('.map .point').removeClass('on');
//                $('.map .point').css('z-index', 0);
//                $('.map .point').each(function(){
//                    if($(this).data('sno') == sno){
//                        $(this).addClass('on');
//                        $(this).css('z-index', 10);
//                    }
//                });
//                
//                
//
//            }
//
//			
//        });
//    });  
	
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








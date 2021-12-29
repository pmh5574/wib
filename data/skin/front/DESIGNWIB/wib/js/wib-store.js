var retitle = '';
var readdress = '';
var renumber = '';
var readdresssub = '';
var didScroll = false;
window.onscroll = function(e) {
    didScroll = true;
    
};
$(function(){
    
    
    moReSize();
    setMap();
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
               mapSearch(title,address,addressSub,number);
               
               moSlide();
            }
        });
    });
    
    $('.morePage').on('click',function(e){
        var page = Number($(this).find('.pageNum').html());
        var pageCount = Number($(this).find('.pageCount').html());
        var pageNum = 0;
        
        if(pageCount != '1'){
            if(pageCount>page){
           
                pageNum = Number(page)+1;
                $(this).find('.pageNum').html(pageNum);
             
            }else if(pageCount <= page){

                pageNum = 1;
                $(this).find('.pageNum').html(pageNum);

            }
            pagination(page,pageNum);
            
        }
        
        
    });
    
    //debounce가 붙으면 리사이즈가 끝나고 떼면 실행됨
    $(window).on('resize', _.debounce(function() {
        
        //스크롤시 리사이징돼서
        if(!didScroll){
            moSlide();
            moReSize();
            //리사이즈시 값 가지고 있으면 해당 값만
            if(retitle && readdress && renumber){
                mapSearch(retitle, readdress, readdresssub, renumber);
            }else{
                setMap();
            }
        }

    }, 100));

});

function moSlide()
{
    var attachSlide = $('#board_detail .cont_box .cont_all .attach');
    var slideOption = {
            dots: true,
            infinite: true,
            fade:true,
            //autoplay: true,
            autoplaySpeed: 3000,
    };
    var screenWidth = $(window).width();
    if(screenWidth < 769){
        if(attachSlide.hasClass('slick-slider') == false){
            attachSlide.slick(slideOption);
        }
    }else{
        if(attachSlide.hasClass('slick-slider')){
            attachSlide.slick('unslick');
        }
    }
}

//맵값 하나만 보여주기
function mapSearch(title, address, addressSub, number) 
{

    //기본값 초기화
    $('.map').html('');

    var container = document.getElementsByClassName('map')[0]; //지도를 담을 영역의 DOM 레퍼런스
    var options = { //지도를 생성할 때 필요한 기본 옵션
        center: new kakao.maps.LatLng(37.3595953, 127.1053971), //지도의 중심좌표.
        level: 3 //지도의 레벨(확대, 축소 정도)
    };

    var map = new kakao.maps.Map(container, options); //지도 생성 및 객체 리턴
    
    var imageSrc = '/data/skin/front/r_jw03/wib/img/icon/map_kakao.png', // 마커이미지의 주소입니다    
    imageSize = new kakao.maps.Size(22, 30); // 마커이미지의 크기입니다

    var markerImage = new kakao.maps.MarkerImage(imageSrc, imageSize);

    // 주소-좌표 변환 객체를 생성합니다
    var geocoder = new kakao.maps.services.Geocoder();

    geocoder.addressSearch(address, function(result, status) {

        // 정상적으로 검색이 완료됐으면 
        if (status === kakao.maps.services.Status.OK) {

            var coords = new kakao.maps.LatLng(result[0].y, result[0].x);

            // 결과값으로 받은 위치를 마커로 표시합니다
            var marker = new kakao.maps.Marker({
                position: coords,
                image: markerImage // 마커이미지 설정 
            });

            // 마커가 지도 위에 표시되도록 설정합니다
            marker.setMap(map);

            var kakaoContents = '<div id="map_store_info_layer">';
            kakaoContents += '<h2 class="mapnm">'+title+'</h2>';
            kakaoContents += '<div class="mapaddress">'+address+', '+addressSub+'</div>';
            kakaoContents += '<div class="mapnum">T.'+number+'</div>';
            kakaoContents += '</div>';

            var position = new kakao.maps.LatLng(result[0].y, result[0].x);

            // 커스텀 오버레이를 생성합니다
            var customOverlay = new kakao.maps.CustomOverlay({
                map: map,
                position: position,
                content: kakaoContents,
                yAnchor: 1 
            });

            // 지도의 중심을 결과값으로 받은 위치로 이동시킵니다
            map.setCenter(coords);

            reSizeData(title, address, addressSub, number);
        } 
    });
}

//전역변수 설정
function reSizeData(title, address, addressSub, number)
{
    retitle = title;
    readdress = address;
    readdresssub = addressSub;
    renumber = number;
}

//전역변수 삭제
function delData()
{
    retitle = '';
    readdress = '';
    readdresssub = '';
    renumber = '';
    
}

function pagination(page,pageNum){
    
    if(!($('.page_list'+page).hasClass('hide'))){
        $('.page_list'+page).addClass('hide');
    }
    
        
    if($('.page_list'+pageNum).hasClass('hide')){
                
        $('.page_list'+pageNum).removeClass('hide');
    }

}

function moReSize(){
    var screenWidth = $(window).width();
    if(screenWidth < 769){

        $('.page_list1').removeClass('hide');

    }else{
        $('.total_list').each(function(){
            if(!($(this).hasClass('hide'))){
                $(this).addClass('hide');
            }

        });

    }
}


//이미지 팝업
$(document).on('click', '#board_detail .cont_box .cont_all .attach img', function(){
    
    var _src = $(this).attr('src');

    $('.img_pop .img').html('');
    $('.img_pop .img').html('<img src="'+_src+'">');
    $('.dark_bg, .img_pop').addClass('on');
});

$(document).on('click', '#board_detail .cont_box .back_btn', function(){
    $('.morePage').show();
    $(".store_list").removeClass('fix');
    $("#board_detail .cont_box").removeClass('on');
    $('.map').html('');
    setMap();
    delData();
});



//이미지 팝업 닫기
$(document).on('click', '.img_pop .pop_close', function(){
    $('.dark_bg, .img_pop').removeClass('on');
});


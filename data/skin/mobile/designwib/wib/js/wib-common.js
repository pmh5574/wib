jQuery(document).ready(function() {
	var bodyOffset = jQuery('header .header-area .bottom-menu .inner .left .category').offset();
        if(bodyOffset){
            jQuery(window).scroll(function() {
		if (jQuery(document).scrollTop() > bodyOffset.top) {
			jQuery('header .header-area .bottom-menu .inner .left .category').addClass('scroll');
		} else {
			jQuery('header .header-area .bottom-menu .inner .left .category').removeClass('scroll');
		}
            });
        }
	
});





$(document).ready(function(){
	var $header = $("header"),
		$body = $("body"),
		$dim = $(".wibDim"),
		$csBtn = $("header .header-area .top-menu .link > ul > li:last-of-type a"),				// 헤더 CS 버튼
		$searchBtn = $("a.search-toggle"),																						// 헤더 검색 버튼
		$rankMoreView = $("a.popular-more"),																			// 인기검색어 전체보기
		$searchArea = $(".search-area"),																							// 검색창
		$searchOpenBtn = $("a.search-toggle"),																			// 검색창 열기
		$searchCloseBtn = $("a.search-close"),																				// 검색창 닫기
		$depth1 = $(".all-menu .contents-wrap .category > ul > li"),										// 뎁스 1
		$depth2Open = $(".all-menu .contents-wrap .category > ul > li > span"),				// 뎁스1 우측 select 버튼
		$depth2Area = $(".all-menu .contents-wrap .category > ul > li > ul"),						// 뎁스2 영역
		$allMenuOpen = $("header .header-area .bottom-menu .inner .left a.menu"),	// 전체메뉴열기 버튼
		$allMenuClose = $(".all-menu .top a.all-menu-close"),													// 전체메뉴닫기 버튼
		$allMenu = $(".all-menu"),																										// 전체메뉴

		
	// 헤더 고객센터 슬라이드다운 메뉴
	csToggle = function(){
		$csBtn.click(function(){
			$(this).toggleClass("on");
		});
	}();

	// 헤더 인기검색어
	popularSearch = function(){
		// 인기검색어 랭킹
		var popular = $(".popular-rank");
		popular.each(function(){
			var popularIdx = $(this).index();
			$(this).attr("data-index", popularIdx + 1);
		});
		// COPY
		$(".popular-area li").clone().appendTo(".rank-view ul");
		// 인기검색어 슬라이드
		var popularSearch = $("ul.popular-area").bxSlider({
			auto: true,
			slideMargin: 5,
			pause: 5000,
			speed:800,
			controls:true,
			pager:false,
			mode:'vertical',
			//preventDefaultSwipeY:true,
		});
		// 인기검색어 전체보기
		$rankMoreView.on("click", function(){
			if ($(this).closest("dl").hasClass("on"))
			{
				$(this).closest("dl").removeClass("on");
			} else{
				$(this).closest("dl").addClass("on");
			}
		});
		// 해당 영역 외 클릭시 닫혀짐
		$("html").click(function(e) {
			if(!$(".rank-view").has(e.target).length && !$(".popular-search").has(e.target).length){
				$(".popular-search dl").removeClass("on");
			}
		});
	}();

	// 검색영역
	search = function(){
		$searchOpenBtn.on("click", function(){
			$searchArea.stop().fadeIn();
			$body.css("overflow", "hidden");
		});
		$searchCloseBtn.on("click", function(){
			$searchArea.stop().hide();
			$body.css("overflow", "visible");
		});
	}();


	$(".all-menu-open").click(function(){
		$("#nav").addClass("on");
		$("body").css("overflow", "hidden");
	});

	$(".nav-close").click(function(){
		$("#nav").removeClass("on");
		$("body").css("overflow", "visible");
	});

	$(".nav_tabmenu_box .nav_tabmenu li").click(function(){
		var tabIdx = $(this).index();
		$(this).addClass("on").siblings("li").removeClass("on");
		$(".nav_category .contents-wrap > div").eq(tabIdx).stop().fadeIn().siblings().hide();

	});
});

var runCheck = false;

// 211123 디자인위브 mh 브랜드 좋아요 기능 
function brandLike(brandCd, obj, name)
{
    var _this = obj;
    
    if(runCheck == true){
        return false;
    }
    
    runCheck = true;
    
    $.ajax({
        url : '../brand/brand_ps.php',
        type : 'post',
        data : {
            mode : 'brandLike',
            brandCd : brandCd
        },
        dataType : 'json',
        success : function(data){
            if(name == 'mypage'){
                location.reload();
            }else{
                if(data.code == 'on'){
                    $(_this).addClass('on');
                    alert('브랜드 찜 리스트에 추가 됐습니다.');
                }else if(data.code == 'off'){
                    $(_this).removeClass('on');
                    alert('브랜드 찜 리스트에서 삭제 됐습니다.');
                }  
            }
            
            runCheck = false;
        }
    });
}

function brandLikeNoMember()
{
    alert("로그인하셔야 본 서비스를 이용하실 수 있습니다.");
    document.location.href = "../member/login.php";
    return false;
}



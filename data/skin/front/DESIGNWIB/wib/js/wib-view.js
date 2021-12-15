$(function(){
	var $benefitViewBtn = $("a.benefit-view"),									// 구매 혜택보기 버튼
		  $benefitPopup = $(".benefit-popup"),										// 구매 혜택보기 팝업
		  $benefitPopupClose = $(".benefit-popup-close"),					// 구매혜택 팝업 닫기 버튼
		  $cardBenefitOpenBtn = $(".card-benefit"),								// 무이자 할부혜택 팝업보기 버튼
		  $cardBenefitPopup = $(".installment-popup"),							// 무이자 할부 혜택
		  $popupDim = $(".popup-dim"),
		  $popupCloseBtn = $(".popup-close"),										// 팝업 닫기 버튼
		  $openblockToggle = $(".openblock .openblock-header h3"),	// 상품필수 정보, 배송 교환 환불 안내 토글
		  $deliOpen = $(".deli-type-open"),												// 배송유형팝업 열기 버튼
		  $deliPopup = $(".delitype-popup"),											// 배송유형팝업
		  $detailTab = $(".detail-tab");													// 탭

	$benefitViewBtn.on("click", function(){
		$benefitPopup.stop().fadeIn();
	});

	$benefitPopupClose.on("click", function(){
		$benefitPopup.stop().fadeOut();
	});

	$cardBenefitOpenBtn.on("click", function(){
		$cardBenefitPopup.stop().fadeIn();
		$popupDim.stop().show();
		$("body").css("overflow", "hidden");
	});

	$deliOpen.on("click", function(){
		$deliPopup.stop().fadeIn();
		$popupDim.stop().show();
		$("body").css("overflow", "hidden");
	});

	$(".popup-dim, .popup-close").on("click", function(){
		$cardBenefitPopup.stop().fadeOut();
		$deliPopup.stop().fadeOut();
		$popupDim.stop().hide();
		$("body").css("overflow", "visible");
	});

	$openblockToggle.on("click", function(){
		if ($(this).closest(".openblock").hasClass("on"))
		{
			$(this).closest(".openblock").removeClass("on");
			$(this).closest(".openblock").find(".openblock-content").stop().slideUp();
		} else{
			$(this).closest(".openblock").addClass("on");
			$(this).closest(".openblock").find(".openblock-content").stop().slideDown();
		}
	});

	$(".detail_explain_box .item_gallery_type").addClass("swiper-container");
	$(".detail_explain_box .item_gallery_type > ul").addClass("swiper-wrapper");
	$(".detail_explain_box .item_gallery_type > ul > li").addClass("swiper-slide");
	var explainSlider = new Swiper(".detail_explain_box .swiper-container", {
		slidesPerView: "5",
		spaceBetween: 30,
		// Rrefresh option
		observer : true,
		observeParents: true,
		scrollbar: {
			el: ".detail_explain_box .swiper-scrollbar",
		},
		navigation: {
			prevEl: ".detail_explain_box .prev-slide",
			nextEl: ".detail_explain_box .next-slide",
		},
	});

	// 스크롤시 헤더 픽스
	$(window).on("load scroll", function(){
		var tabOffsetTop = $(".item_goods_sec").offset().top;
		
		if ( $( document ).scrollTop() > tabOffsetTop - 120) {
			$detailTab.addClass("scroll");
			$("body").addClass("scroll");
		}
		else {
			$detailTab.removeClass("scroll");
			$("body").removeClass("scroll");
		}
	});

	// 탭 클릭시 해당 위치로 이동
	$(".detail-tab ul li a").click(function(){
		var tabAttr = $(this).attr("data-class");
		var tabPosition = $(tabAttr).offset().top - $(".detail-tab").outerHeight() - $("header .bottom-menu").outerHeight();

		$(this).closest("li").addClass("on").siblings("li").removeClass("on");

		console.log(tabPosition);
		$("html, body").animate({
			scrollTop : tabPosition
		});
	});

	
	$(window).on("load scroll", function(){	
		var go01off = $(".go01").offset().top - $(".detail-tab").outerHeight() - $("header .bottom-menu").outerHeight() - 1;
		var go02off = $(".go02").offset().top - $(".detail-tab").outerHeight() - $("header .bottom-menu").outerHeight() - 1;
		var go03off = $(".go03").offset().top - $(".detail-tab").outerHeight() - $("header .bottom-menu").outerHeight() - 1;
                
                //관심상품이 없는 경우도 있어서
                if($('.go04').length > 0){
                    var go04off = $(".go04").offset().top - $(".detail-tab").outerHeight() - $("header .bottom-menu").outerHeight() - 1;
                }
		
		var $position = $(window).scrollTop();
		if ($position > go01off)
		{	
			$(".detail-tab ul li").eq(0).addClass("on").siblings("li").removeClass("on");
		}
		if ($position > go02off)
		{	
			$(".detail-tab ul li").eq(1).addClass("on").siblings("li").removeClass("on");
		}
		if ($position > go03off)
		{	
			$(".detail-tab ul li").eq(2).addClass("on").siblings("li").removeClass("on");
		}
		if ($position > go04off)
		{	
			$(".detail-tab ul li").eq(3).addClass("on").siblings("li").removeClass("on");
		}
	});


	$(window).on("load scroll", function(){

		//현재 스크롤 위치값
		var winPosition = $(window).scrollTop();

		// 문서 높이
		var docH = $(document).height();

		// 픽스박스 높이
		var boxHeight = $("body.scroll .item_info_box .info_inner").outerHeight();

		// 컨텐츠 끝 위치
		var contentBottomPosition = $(".sub_content > .content_box").height();

		// 컨텐츠 시작점
		var contentTopPosition = $(".sub_content > .content_box").offset().top;

		// 여백 [윈도우 높이값 - 컨텐츠시작점값]
		var fixMargin = $(window).height() - contentTopPosition;

		// 여백2 [윈도우 높이값 - 픽스박스 높이값]
		var fixMargin2 = $(window).height() - boxHeight;

		// 픽스 시킬 포인트
		var pointPosition = contentBottomPosition + fixMargin -  boxHeight - fixMargin2;

		if ( winPosition >= pointPosition )
		{
			$("body").addClass("fixbottom");
		} else{
			$("body").removeClass("fixbottom");
		}
	});

});
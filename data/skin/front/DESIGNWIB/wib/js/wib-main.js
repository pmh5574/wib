$(document).ready(function(){

	var mvSlider = new Swiper(".mcon01 .swiper-container", {
		slidesPerView: "1",
		loop : true,
		autoplay : { 
			delay : 6000, 
		},
		speed: 1200,
		navigation: {
			prevEl: ".mcon01 .swiper-button-prev",
			nextEl: ".mcon01 .swiper-button-next",
		},
		creativeEffect: {
			prev: {
				shadow: true,
				translate: ["-20%", 0, -1],
			},
			next: {
				translate: ["100%", 0, 0],
			},
		},
		pagination: {
			el: ".mcon01 .swiper-pagination",
        },
	});

	var mcon03Slider = new Swiper(".mcon03 .swiper-container", {
		slidesPerView: "5",
		spaceBetween: 30,
		// Rrefresh option
		observer : true,
		observeParents: true,
		scrollbar: {
			el: ".mcon03 .swiper-scrollbar",
		},
		navigation: {
			prevEl: ".mcon03 .prev-slide",
			nextEl: ".mcon03 .next-slide",
		},
	});

	$(".mcon04 .banner").mouseenter(function(){
		$(this).addClass("hover").removeClass("non-hover");
		$(this).siblings(".banner").addClass("non-hover").removeClass("hover");
	});

	$(".mcon04 .banner").mouseleave(function(){
		$(".mcon04 .banner").removeClass("hover non-hover");
	});


	/* 상품 이미지 슬라이드 */
	$(".mcon05 .brand-list > div").slick({
		// vertical: true,
		asNavFor: ".brand-product-form .inner",
		focusOnSelect: true,
		centerMode: true,
		variableWidth: true,
		slidesToShow: 9,
		loop: true,
		// pauseOnHover: false, 
		// cssEase: 'cubic-bezier(.76,.09,.215,1)'
	});

	//상품 상세이미지 슬라이드
	$(".brand-product-form .inner").slick({
		loop: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		fade: true,
		asNavFor: ".mcon05 .brand-list > div",
		dots: true,
	});
	
	tab(".mcon07 .tab ul li");

	
	var liveSlider = new Swiper(".mcon08 .swiper-container", {
		loop: true,
		effect: "coverflow",
		lazy : {
			loadPrevNext : true,		//swiper내에서 lazy loading 사용 시
			loadPrevNextAmount : 5
		},
		grabCursor: true,
		centeredSlides: true,
		slidesPerView: "auto",
		speed: 800,
		coverflowEffect: {
			rotate: 15,
			stretch: 0,
			depth: 25,
			modifier: 1,
			slideShadows: false,
		},
		observer: true,
		observeParents: true,
		touchEventsTarget: "wrapper",
	});


	var lookbookSlider =  new Swiper(".mcon10 .left .swiper-container", {
		grabCursor: true,
		slidesPerView: 1,
		effect: "creative",
		speed: 800,
		creativeEffect: {
			prev: {
				shadow: true,
				translate: [0, 0, -400],
			},
			next: {
				translate: ["100%", 0, 0],
			},
		},
		allowTouchMove : false,
		loop : true,
	});


	var lookbookBig = new Swiper(".mcon10 .swiper-container.big", {
		grabCursor: true,
		effect: "creative",
		slidesPerView: 1,
		speed: 800,
		creativeEffect: {
			prev: {
				shadow: true,
				translate: ["-20%", 0, -1],
			},
			next: {
				translate: ["100%", 0, 0],
			},
		},
		controller: {
			control: ".mcon10 .swiper-container",
		},
		loop : true,
		navigation: {
			prevEl: ".mcon10 .left .swiper-button-prev",
			nextEl: ".mcon10 .left .swiper-button-next",
		},
	});

	var lookbookSmall = new Swiper(".mcon10 .swiper-container.small", {
		grabCursor: true,
		slidesPerView: 1,
		effect: "creative",
		speed: 800,
		creativeEffect: {
			prev: {
				shadow: true,
				translate: ["-20%", 0, -1],
			},
			next: {
				translate: ["100%", 0, 0],
			},
		},
		controller: {
			control: ".mcon10 .swiper-container",
		},
		allowTouchMove : false,
		loop: true,
	});



	lookbookBig.controller.control = lookbookSmall;
	lookbookSmall.controller.control = lookbookSlider;










});

function tab(tab){
	$(tab).click(function(){
		var tabIdx = $(this).index();
		$(this).addClass("on").siblings("li").removeClass("on");
		$(this).closest(".tab-area").find(".contents-wrap").find(".content").hide();
		$(this).closest(".tab-area").find(".contents-wrap").find(".content").eq(tabIdx).stop().fadeIn();
	});
}

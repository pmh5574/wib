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



	var swiper = new Swiper(".brand-list .swiper-container", {
		slidesPerView: 'auto',
		preventClicks: true,
		preventClicksPropagation: false,
		observer: true, // 리프레쉬
		observeParents: true,
		touchRatio: 0, // 드래그 금지
		
	});
	var $snbSwiperItem = $('.brand-list .swiper-container .swiper-slide a');
	$snbSwiperItem.click(function(){
		var target = $(this).parent();
		$snbSwiperItem.parent().removeClass('on')
		target.addClass('on');
		muCenter(target);
	})

	$snbSwiperItem.parent().eq(4).addClass("on").siblings("li").removeClass("on");

	function muCenter(target){
		var snbwrap = $('.brand-list .swiper-wrapper');
		var targetPos = target.position();
		var box = $('.brand-list');
		var boxHarf = box.width()/2;
		var pos;
		var listWidth=0;
		
		snbwrap.find('.swiper-slide').each(function(){ listWidth += $(this).outerWidth(); })
		
		var selectTargetPos = targetPos.left + target.outerWidth()/2;
		if (selectTargetPos <= boxHarf) { // left
			pos = 0;
		}else if ((listWidth - selectTargetPos) <= boxHarf) { //right
			pos = listWidth-box.width();
		}else {
			pos = selectTargetPos - boxHarf;
		}
		
		setTimeout(function(){snbwrap.css({
			"transform": "translate3d("+ (pos*-1) +"px, 0, 0)",
			"transition-duration": "500ms"
		})}, 200);
	}


	

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

$(function(){
	$(".sub_top h2 em").click(function(){
		if ($(this).hasClass("on"))
		{
			$(this).removeClass("on");
			$(".depth1-menu").stop().fadeOut();
			$(".black-dim").stop().hide();
		} else{
			$(this).addClass("on");
			
			$(".depth1-menu").stop().fadeIn();
			$(".black-dim").stop().fadeIn();
		}
	});


	var listCate = new Swiper(".goods_list_category .swiper", {
		freeMode: true,
		observer : true,
		observeParents: true,
		slidesPerView: 'auto',
		slidesOffsetBefore: 15,
		slidesOffsetAfter: 15,
	});


	$(".filter-area .contents-wrap .content > div h1").click(function(){
		if ($(this).closest("div").hasClass("on"))
		{
			$(this).closest("div").removeClass("on");
			$(this).closest("div").find(".toggle").stop().slideUp();
		} else{
			$(this).closest("div").addClass("on");
			$(this).closest("div").find(".toggle").stop().slideDown();
		}
	});

	// 필터 함수 호출
	tab(".filter-area .tab ul li", ".filter-area .contents-wrap .content");

	$(".filter-area .contents-wrap .content > div .toggle h2").click(function(){
		if ($(this).closest(".toggle").hasClass("on"))
		{
			$(this).closest(".toggle").removeClass("on");
			$(this).closest(".toggle").find("ul").stop().slideUp();
		} else{
			$(this).closest(".toggle").addClass("on");
			$(this).closest(".toggle").find("ul").stop().slideDown();
		}
		
	});

	$(".filter-button").click(function(){
		$(".filter-area").stop().fadeIn();
	});
	

});

// 필터 탭
function tab(a, b){
	$(a).click(function(){
		var tabIdx = $(this).index();
		$(this).addClass("on").siblings().removeClass("on");
		$(b).eq(tabIdx).stop().fadeIn().siblings().hide();
	});
}
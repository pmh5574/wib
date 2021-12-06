var wibFilter = {
    
    ajaxUrl : '',
    cateCd : '',
    page : '',
    sort : '',
    filterSize : [],
    filterColor : [],
    filterfeatures : [],
    filterline :  [],
    filtergender :  [],
    filtercollaboration :  [],
    
    init : function(option){
        var _this = this;

        this.ajaxUrl = option.url;
        this.cateCd = option.cateCd;
        this.page = option.page;
        this.sort = option.sort;
        
        
        $('.f_size span.check_box_img').on('click', function(e){
            
            e.preventDefault();
            _this.setSize($(this));
        });
        
        $('.f_features span.check_box_img').on('click', function(e){
            
            e.preventDefault();
            _this.setFeatures($(this));
        });
        
        $('.f_gender span.check_box_img').on('click', function(e){
            
            e.preventDefault();
            _this.setGender($(this));
        });
        
        
        $('.f_collaboration span.check_box_img').on('click', function(e){
            
            e.preventDefault();
            _this.setCollaboration($(this));
        });
        
        $('.f_color .f_depth1 li span').on('click', function(){
            _this.setColor($(this));
        });
        
        $(document).on('click', '.filter-paging .pagination ul li a', function(e){
            e.preventDefault();
            _this.setPage($(this));
        });
    },
    
    getList : function(){
        var _this = this;
        if(this.page == null){
            var paging = this.ajaxUrl+'?cateCd='+this.cateCd;
        }else if(this.sort && this.page == null){
            var paging = this.ajaxUrl+'?cateCd='+this.cateCd+'&sort='+this.sort;
        }else if(this.sort && this.page){
            var paging = this.ajaxUrl+'?page='+this.page+'&cateCd='+this.cateCd+'&sort='+this.sort;
        }else{
            var paging = this.ajaxUrl+'?page='+this.page+'&cateCd='+this.cateCd;
        }
        
        $.ajax({
            'url' : paging,
            'method' : 'post',
            data : {
                'cateCd' : _this.cateCd,
                'filterSize' : _this.filterSize,
                'filterFeatures' : _this.filterFeatures,
                'filterGender' :  _this.filterGender,
                'filterCollaboration' :  _this.filterCollaboration,
                'filterColor' : _this.filterColor,
                'page' : this.page
            },
            success : function(e){
                
                var _split = e.split('<!--개수-->');
                var _numSplit = _split[1].split('<!--//개수-->');

                $('.pick_list_num strong').html(_numSplit[0]);
                $('.cont_list .filter_goods_list').empty().append(e);
                
                _this.checkClass();
            }
        });
    },
    
    numberWithCommas : function(x){
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    
    checkClass : function(){
        
        var _check = 0;
        
        $('.filter_result .filterVal').each(function(){
            if(!($(this).find('p').html() == '' || $(this).find('p').html() == undefined)){
                _check++;
            }
        });
        
        if(_check > 0){
            if(!$('.filter_result').hasClass('filterOn')){
                $('.filter_result').addClass('filterOn');
            }
        }else{
            if($('.filter_result').hasClass('filterOn')){
                $('.filter_result').removeClass('filterOn');
            }
        }
    },
    
    delColor : function(code){
        var _this = this;
        _this.page = 1;
        
        $('.cl_'+code).remove();
        $('.clpa_'+code).removeClass('on');
        _this.filterColor = [];
        $('.hiddenColor > input').each(function(){
            var eThis = $(this).val();
            _this.filterColor.push(eThis);
        });
        _this.getList();
        
    },
    
    setColor : function(obj){
        var _this = this;
        _this.page = 1;
        
        var colorCode = obj.data('color');
        var colorPa = obj.parent();
        $('.hiddenColor').append('<input type="hidden" class="cl_'+colorCode+'" value="'+colorCode+'">');

        if(colorPa.hasClass('on')){
            _this.delColor(colorCode);
            colorPa.removeClass('on');
            return false;
        }

        colorPa.addClass('on clpa_'+colorCode);
        var appendColor = '';
        _this.filterColor = [];
        $('.hiddenColor > input').each(function(){
            var eThis = $(this).val();
            appendColor += '<div class="cl_'+eThis+'">';
            appendColor += '<p class="f_color" style="background:#'+eThis+'">color1</p>';
            appendColor += '<button type="button" class="filter_del" onclick="wibFilter.delColor(\''+eThis+'\')"></button>';
            appendColor += '</div>';
            _this.filterColor.push(eThis);
        });
        $('.filter_result .fColor').empty().append(appendColor).css('display', 'inline-block');
        $('.filter_result .fColor').show();
        _this.getList();
        
    },
    
    delSize : function(code){
        var _this = this;
        _this.page = 1;
        
        $('.sz_'+code).remove();
        $('.szpa_'+code).removeClass('on');
        $('.szpa_'+code+' .check_box_img').removeClass('on');
        _this.filterSize = [];
        $('.hiddenSize > input').each(function(){
            var eThis = $(this).val();
            _this.filterSize.push(eThis);
        });
        _this.getList();
    },
    
    setSize : function(obj){
        var _this = this;
        _this.page = 1;

        var sizeCode = obj.data('size');
        
        //만다리나덕 띄어쓰기랑 소수점때문에 추가
        if(typeof sizeCode == 'number'){
            var sizeCodeClass = (sizeCode).toFixed(0);
            
        }else if(typeof sizeCode == 'string'){
            var sizeCodeClass = sizeCode.replace(/ /g,"");
            //~를 클래스로 쓰면 제대로 동작을 못함
            if(sizeCodeClass.indexOf('~')!= -1){
                sizeCodeClass = sizeCodeClass.replace('~','-');
            }
        }
        

        $('.hiddenSize').append("<input type='hidden' class='sz_"+sizeCodeClass+"' value='"+sizeCode+"'>");
        
        if(obj.parent().hasClass('on')){
            _this.delSize(sizeCodeClass);
            return false;
        }
        
        obj.addClass('on');
        obj.parent().addClass('on szpa_'+sizeCodeClass);
        
        var appendSize = '';
        _this.filterSize = [];
        $('.hiddenSize > input').each(function(){
            var eThis = $(this).val();
            //만다리나덕 띄어쓰기랑 소수점때문에 추가
            var eSizeClass = $(this).attr('class').split('sz_');

            appendSize += '<div class="sz_'+eSizeClass[1]+'">';
            appendSize += '<p>'+eThis+'</p>';
            appendSize += '<button type="button" class="filter_del" onclick="wibFilter.delSize(\''+eSizeClass[1]+'\')"></button>';
            appendSize += '</div>';
            _this.filterSize.push(eThis);
        });
        
        
        $('.filter_result .fSize').empty().append(appendSize).css('display', 'inline-block');
        $('.filter_result .fSize').show();
        
        _this.getList();
    },
    
    delFeatures : function(code){
        var _this = this;
        _this.page = 1;

        $('.fu_'+code).remove();
        $('.fupa_'+code).removeClass('on');
        $('.fupa_'+code+' .check_box_img').removeClass('on');
        
        _this.filterFeatures = [];
        $('.hiddenFeatures > input').each(function(){
            var eThis = $(this).val();
            _this.filterFeatures.push(eThis);
        });
        _this.getList();
    },
    
    setFeatures : function(obj){
        var _this = this;
        var FeaturesCode = obj.data('features');
        _this.page = 1;
        
        //만다리나덕 띄어쓰기랑 소수점때문에 추가
        if(typeof FeaturesCode == 'number'){
            var FeaturesCodeClass = (FeaturesCode).toFixed(0);
            
        }else if(typeof FeaturesCode == 'string'){
            var FeaturesCodeClass = FeaturesCode.replace(/ /g,"");
        }
        

        $('.hiddenFeatures').append("<input type='hidden' class='fu_"+FeaturesCodeClass+"' value='"+FeaturesCode+"'>");
        
        if(obj.parent().hasClass('on')){
            _this.delFeatures(FeaturesCodeClass);
            return false;
        }
        
        obj.addClass('on');
        obj.parent().addClass('on fupa_'+FeaturesCodeClass);
        
        var appendFeatures = '';
        _this.filterFeatures = [];
        $('.hiddenFeatures > input').each(function(){
            var eThis = $(this).val();
            //만다리나덕 띄어쓰기랑 소수점때문에 추가
            var eFeaturesClass = $(this).attr('class').split('fu_');

            appendFeatures += '<div class="fu_'+eFeaturesClass[1]+'">';
            appendFeatures += '<p>'+eThis+'</p>';
            appendFeatures += '<button type="button" class="filter_del" onclick="wibFilter.delFeatures(\''+eFeaturesClass[1]+'\')"></button>';
            appendFeatures += '</div>';
            _this.filterFeatures.push(eThis);
        });
        
        
        $('.filter_result .fFeatures').empty().append(appendFeatures).css('display', 'inline-block');
        $('.filter_result .fFeatures').show();
        
        _this.getList();
    },
    
    delGender : function(code){
        var _this = this;
        _this.page = 1;
        
        $('.gd_'+code).remove();
        $('.gdpa_'+code).removeClass('on');
        $('.gdpa_'+code+' .check_box_img').removeClass('on');
        _this.filterGender = [];
        $('.hiddenGender > input').each(function(){
            var eThis = $(this).val();
            _this.filterGender.push(eThis);
        });
        _this.getList();
    },
    
    setGender : function(obj){
        var _this = this;
        var GenderCode = obj.data('gender');
        _this.page = 1;
        
        //만다리나덕 띄어쓰기랑 소수점때문에 추가
        if(typeof GenderCode == 'number'){
            var GenderCodeClass = (GenderCode).toFixed(0);
            
        }else if(typeof GenderCode == 'string'){
            var GenderCodeClass = GenderCode.replace(/ /g,"");
        }
        

        $('.hiddenGender').append("<input type='hidden' class='gd_"+GenderCodeClass+"' value='"+GenderCode+"'>");
        
        if(obj.parent().hasClass('on')){
            _this.delGender(GenderCodeClass);
            return false;
        }
        
        obj.addClass('on');
        obj.parent().addClass('on gdpa_'+GenderCodeClass);
        var appendGender = '';
        _this.filterGender = [];
        $('.hiddenGender > input').each(function(){
            var eThis = $(this).val();
            //만다리나덕 띄어쓰기랑 소수점때문에 추가
            var eGenderClass = $(this).attr('class').split('gd_');

            appendGender += '<div class="gd_'+eGenderClass[1]+'">';
            appendGender += '<p>'+eThis+'</p>';
            appendGender += '<button type="button" class="filter_del" onclick="wibFilter.delGender(\''+eGenderClass[1]+'\')"></button>';
            appendGender += '</div>';
            _this.filterGender.push(eThis);
        });
        
        
        $('.filter_result .fGender').empty().append(appendGender).css('display', 'inline-block');
        $('.filter_result .fGender').show();
        
        _this.getList();
    },
    
    delCollaboration : function(code){
        var _this = this;
        _this.page = 1;

        $('.cb_'+code).remove();
        $('.cbpa_'+code).removeClass('on');
        $('.cbpa_'+code+' .check_box_img').removeClass('on');
        _this.filterCollaboration = [];
        $('.hiddenCollaboration > input').each(function(){
            var eThis = $(this).val();
            _this.filterCollaboration.push(eThis);
        });
        _this.getList();
    },
    
    setCollaboration : function(obj){
        var _this = this;
        var collaborationCode = obj.data('collaboration');
        _this.page = 1;
        
        //만다리나덕 띄어쓰기랑 소수점때문에 추가
        if(typeof collaborationCode == 'number'){
            var collaborationCodeClass = (collaborationCode).toFixed(0);
            
        }else if(typeof collaborationCode == 'string'){
            var collaborationCodeClass = collaborationCode.replace(/ /g,"");
            
        }
        

        $('.hiddenCollaboration').append("<input type='hidden' class='cb_"+collaborationCodeClass+"' value='"+collaborationCode+"'>");
        
        if(obj.parent().hasClass('on')){
            _this.delCollaboration(collaborationCodeClass);
            return false;
        }
        
        obj.addClass('on');
        obj.parent().addClass('on cbpa_'+collaborationCodeClass);
        var appendCollaboration = '';
        _this.filterCollaboration = [];
        $('.hiddenCollaboration > input').each(function(){
            var eThis = $(this).val();
            //만다리나덕 띄어쓰기랑 소수점때문에 추가
            var eCollaborationClass = $(this).attr('class').split('cb_');

            appendCollaboration += '<div class="cb_'+eCollaborationClass[1]+'">';
            appendCollaboration += '<p>'+eThis+'</p>';
            appendCollaboration += '<button type="button" class="filter_del" onclick="wibFilter.delCollaboration(\''+eCollaborationClass[1]+'\')"></button>';
            appendCollaboration += '</div>';
            _this.filterCollaboration.push(eThis);
        });
        
        
        $('.filter_result .fCollaboration').empty().append(appendCollaboration).css('display', 'inline-block');
        $('.filter_result .fCollaboration').show();
        
        _this.getList();
    },
    
    setPage : function(obj){
        
        var _this = this;
        var _href = obj.attr('href');
        var _split = _href.split('?page=');
        var filterPage = _split[1].split('&');            
        
        _this.page = filterPage[0];
        
        _this.getList();
    }
    
};

$(document).ready(function(){
    
    var _url = location.search;
    
    if(_url.indexOf('page') !== -1){
        
        var cateCcd = _url.split('&cateCd=');
        var ppage = cateCcd[0].split('?page=');
        var cateCd = cateCcd[1];
        var _page = ppage[1];

    }else{
        
        
        if(_url.indexOf('sort') !== -1){
            var _split = _url.split('?cateCd=');
            var cateCcd = _split[1].split('&sort=');
            var cateCd = cateCcd[0];
            var sort = cateCcd[1];
            
        }else{
            var cateCcd = _url.split('?cateCd=');
            var cateCd = cateCcd[1];
            var sort = null;
        }
        var _page = null;
    }
    
    wibFilter.init({
        'url' : '/goods/filter_goods.php',
        'cateCd' : cateCd,
        'page' : _page,
        'sort' : sort
    });
});

$(function(){
	$(".goods-list-wrap .side_memu .side_menu_all h1").click(function(){
		if ($(this).hasClass("on"))
		{
			$(this).removeClass("on");
			$(this).next("ul").stop().slideUp();
		} else{
			$(this).addClass("on");
			$(this).next("ul").stop().slideDown();
		}
	});
	$(".goods_pick_list .filter-toggle").click(function(){
		if ($(this).hasClass("on"))
		{
			$(this).removeClass("on");
			$(".goods-list-wrap .side_memu").stop().hide();
		} else{
			$(this).addClass("on");
			$(".goods-list-wrap .side_memu").stop().fadeIn();
		}
	});

	$(".filter-close").click(function(){
		$(".goods_pick_list .filter-toggle").removeClass("on");
		$(".goods-list-wrap .side_memu").stop().hide();
	});
});
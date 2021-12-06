var wibFilter = {
    
    ajaxUrl : '',
    cateCd : '',
    page : '',
    sort : '',
    filterBrand : '',
    filterColor : [],
    
    init : function(option){
        var _this = this;

        this.ajaxUrl = option.url;
        this.cateCd = option.cateCd;
        this.page = option.page;
        this.sort = option.sort;
        
        //브랜드 필터 정렬기준
        $('.filter_brand .brand_order_by span').on('click', function(e){
            e.preventDefault();
            
            $('.filter_brand .brand_order_by span').removeClass('on');
            $(this).addClass('on');

            _this.getBrand();
        });
        
        //브랜드 필터 인풋에서 엔터
        $('.filter_brand #filterBrandNm').on('keyup', function(e){
            if(event.keyCode == 13){
                _this.getBrand();
            }
        });
        
        //브랜드 필터 클릭
        $(document).on('click', '.filter_brand .filterBrandList li span', function(e){
            e.preventDefault();
            _this.setBrand($(this));
        });
        
        //컬러 필터 클릭
        $('.filter_color ul li span').on('click', function(e){
            e.preventDefault();
            
            _this.setColor($(this));
        });

    },
    
    getBrand : function(){  
        $.ajax({
            url : '../goods/goods_filter_ps.php',
            type : 'post',
            dataType : 'json',
            data : {
                'mode' : 'getBrand',
                'brandNm' : $('#filterBrandNm').val(),
                'orderBy' : $('.filter_brand .brand_order_by span.on').data('order-by')
            },
            success : function(result){
                
                var data = result.data;
                
                if(result.code == '0'){
                    $('.filterBrandList').html('검색된 브랜드가 없습니다.');
                }else{
                    var _html = '';
                    var brandKrNm = '';
                    
                    for(var i=0; i<data.length; i++){
                        
                        if(data[i]['cateKrNm']){
                            brandKrNm = '<span class="brandKrNm">'+brandKrNm+'</span>';
                        }else{
                            brandKrNm = '';
                        }
                        
                        _html += '<li><span class="check_box_img" data-brand="'+data[i]['cateCd']+'">'+data[i]['cateNm']+brandKrNm+'</span></li>';
                    }
                    
                    $('.filterBrandList').html(_html);
                }
            }
        });
    },
    
    getList : function(){
        var _this = this;
        var sort =  $('input[name="sort"]:checked').val();
        var pageNum = $('select[name="pageNum"]').val();
        
        $.ajax({
            url : _this.ajaxUrl+'?cateCd='+_this.cateCd,
            type : 'post',
            data : {
                'sort' : sort,
                'pageNum' : pageNum,
                'cateCd' : _this.cateCd,
                'page' : _this.page,
                'filterBrand' : _this.filterBrand,
                'filterColor' : _this.filterColor,
            },
            success : function(result){
                console.log(result);
            }
        });
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
//        $('.filter_result .fColor').empty().append(appendColor).css('display', 'inline-block');
//        $('.filter_result .fColor').show();
        _this.getList();
        
    },
    
    delBrand : function(code){
        var _this = this;
        
        _this.filterBrand = '';
        
        _this.getList();
    },
    
    setBrand : function(obj){
        var _this = this;
        
        var dataCode = obj.data('brand');
        var pa = obj.parent();
        
        if(pa.hasClass('on')){
            _this.delBrand(dataCode);
            pa.removeClass('on');
            return false;
        }
        
        obj.closest('li').addClass('on');
        _this.filterBrand = dataCode;
        
        _this.getList();
    },
    
    numberWithCommas : function(x){
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    
    
    
};

$(function(){
    var _url = location.search;
    
    var cateCcd = _url.split('cateCd=')[1];
    var cateCd = cateCcd.split('&')[0];
    
    wibFilter.init({
        'url' : '/goods/filter_goods.php',
        'cateCd' : cateCd,
        'page' : 1
    });

    wibFilter.getBrand();
});
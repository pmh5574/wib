var wibFilter = {
    
    ajaxUrl : '',
    cateCd : '',
    page : '',
    sort : '',
    brandCheck : '',
    filterBrand : [],
    filterColor : [],
    
    init : function(option){
        var _this = this;

        this.ajaxUrl = option.url;
        this.cateCd = option.cateCd;
        this.page = option.page;
        this.sort = option.sort;
        this.brandCheck = option.brandCheck;
        
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
        
        $(document).on('click', '.btn_box button.more_btn', function(e){
            e.preventDefault();
            _this.setPage($(this));
            
        });

    },
    
    getBrand : function(){
        
        var _this = this;
        var orderBy = $('.filter_brand .brand_order_by span.on').data('order-by');
        
        $.ajax({
            url : '../goods/goods_filter_ps.php',
            type : 'post',
            dataType : 'json',
            data : {
                'mode' : 'getBrand',
                'brandNm' : $('#filterBrandNm').val(),
                'orderBy' : orderBy
            },
            success : function(result){
                
                var data = result.data;
                
                if(result.code == '1'){
                    $('.filterBrandList').html('검색된 브랜드가 없습니다.');
                }else{
                    var _html = '';
                    var brandKrNm = '';
                    var brandEnNm = '';
                    
                    for(var i=0; i<data.length; i++){
                        
                        //한글 브랜드명일때는 한글명이 위로
                        if(orderBy == 'kr'){
                            if(data[i]['cateNm']){
                                brandEnNm = '<span class="brandEnNm">'+data[i]['cateNm']+'</span>';
                            }else{
                                brandEnNm = '';
                            }
                            
                            if(data[i]['cateKrNm']){
                                brandKrNm = data[i]['cateKrNm'];
                            }else{
                                brandKrNm = '';
                            }

                            if(_this.filterBrand.indexOf(data[i]['cateCd']) != -1){
                                _html += '<li class="on brpa_'+data[i]['cateCd']+'"><span class="check_box_img" data-brand="'+data[i]['cateCd']+'">'+brandKrNm+brandEnNm+'</span></li>';
                            }else{
                                _html += '<li><span class="check_box_img" data-brand="'+data[i]['cateCd']+'">'+brandKrNm+brandEnNm+'</span></li>';
                            }
                        }else{
                            if(data[i]['cateKrNm']){
                                brandKrNm = '<span class="brandKrNm">'+data[i]['cateKrNm']+'</span>';
                            }else{
                                brandKrNm = '';
                            }

                            if(_this.filterBrand.indexOf(data[i]['cateCd']) != -1){
                                _html += '<li class="on brpa_'+data[i]['cateCd']+'"><span class="check_box_img" data-brand="'+data[i]['cateCd']+'">'+data[i]['cateNm']+brandKrNm+'</span></li>';
                            }else{
                                _html += '<li><span class="check_box_img" data-brand="'+data[i]['cateCd']+'">'+data[i]['cateNm']+brandKrNm+'</span></li>';
                            }
                        }
                        
                        
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
            url : _this.ajaxUrl+'?'+_this.brandCheck+_this.cateCd,
            type : 'post',
            data : {
                'sort' : sort,
                'pageNum' : pageNum,
                'cateCd' : _this.cateCd,
                'page' : _this.page,
                'filterBrand' : _this.filterBrand,
                'filterColor' : _this.filterColor,
            },
            success : function(data){
                
                
                if($(data).filter("li.no_bx").length && _this.page != 1) {
                    alert("더이상 상품이 없습니다");
                    $('.more_btn').hide();
                    

                }else if($(data).filter("li.no_bx").length && _this.page == 1){
                    $('.goods_product_list').html(data);
                    $('.more_btn').hide();
                    
                }else {
                    
                    if(_this.page == 1){
                        $('.goods_product_list').html(data);
                        $('.more_btn').show();

                    }else{
                        $('.goods_product_list').append(data);

                    }

                    $('.btn_box button.more_btn').data('page',parseInt(_this.page)+1);
                    

                }

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
        _this.page = 1;
        
        $('.br_'+code).remove();
        $('.brpa_'+code).removeClass('on');
        
        _this.filterBrand = [];
        
        $('.filterBrand > input').each(function(){
            var eThis = $(this).val();
            _this.filterBrand.push(eThis);
        });
        
        _this.getList();
    },
    
    setBrand : function(obj){
        
        var _this = this;
        _this.page = 1;
        
        var code = obj.data('brand');
        var paCode = obj.parent();
        $('.hiddenBrand').append('<input type="hidden" class="br_'+code+'" value="'+code+'">');
        
        if(paCode.hasClass('on')){
            _this.delBrand(code);
            paCode.removeClass('on');
            return false;
        }

        paCode.addClass('on brpa_'+code);
        var appendData = '';
        _this.filterBrand = [];
        
        $('.hiddenBrand > input').each(function(){
            var eThis = $(this).val();
            _this.filterBrand.push(eThis);
        });

        _this.getList();
    },
    
    numberWithCommas : function(x){
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    
    setPage : function(obj){
        
        var _this = this;
        var filterPage = obj.data('page');

        _this.page = filterPage;
        
        _this.getList();
    }
    
};

$(function(){
    
    var cateCd = getParameterByName('cateCd');
    var brandCd = 'cateCd=';
    
    if(!cateCd){
        cateCd = getParameterByName('brandCd');
        brandCd = 'brandCd=';
    }
    
    wibFilter.init({
        'url' : '/goods/filter_goods.php',
        'cateCd' : cateCd,
        'brandCheck' : brandCd,
        'page' : 1
    });

    wibFilter.getBrand();
});

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"), results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
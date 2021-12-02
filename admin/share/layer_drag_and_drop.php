<style>
    /*
    * 상품 정렬 리스트 
    */
    #div_goods_result{font-size:11px;}
    #div_goods_result ul{width: 1089px;margin: 0 auto;padding-bottom: 100px;box-sizing: border-box;display: flex;flex-wrap: wrap;align-content: flex-start;}
    #div_goods_result ul li{position: relative; width: 150px;height: 150px;margin: 4px 0 0 4px;}
    #div_goods_result ul li .gallery_description{opacity: 0;position: absolute;top: 0;left: 0;width: 100%;height: 100%;box-sizing: border-box;letter-spacing: -1px;background: rgba(255,255,255,.95);-webkit-transition: .3s ease-out;transition: .3s ease-out;}
    #div_goods_result ul li:hover .gallery_description{opacity: 1;}
    /*#div_goods_result ul li.sortable-chosen:after { content:""; position:absolute; top:0; left:0; display:block; width:213px; height:230px; }*/
    #div_goods_result ul .sortable-chosen.sortable-ghost:hover .gallery_description{ opacity:0 !important; display:none !important; }
    #div_goods_result ul li .gallery_thumbnail{position: relative;overflow: hidden;width: 100%;box-sizing: border-box;height: 150px;}
    #div_goods_result ul li .gallery_thumbnail img{width: 100%; max-height: 150px;}
    #div_goods_result ul li .gallery_button{position: absolute;top: 9px;left: 11px;z-index: 2;width: 120px;height: 22px;}
    #div_goods_result ul li .gallery_button .delete_button{width: 14px;height: 14px;vertical-align: middle;float: right;}
    
    
    /*
    * 상품 검색 리스트 
    */
    #goodsChoiceList{position: relative;float: left;width: 210px;height: 100%;}
    #searchGoodsList{border:1px solid #888888;}
    
    #searchGoodsList ul li{position: relative; width: 150px;height: 150px;margin: 4px 0 0 4px;}
    #searchGoodsList ul li .gallery_description{opacity: 0;position: absolute;top: 0;left: 0;width: 100%;height: 100%;box-sizing: border-box;letter-spacing: -1px;background: rgba(255,255,255,.95);-webkit-transition: .3s ease-out;transition: .3s ease-out;}
    #searchGoodsList ul li:hover .gallery_description{opacity: 1;}
    #searchGoodsList ul li .gallery_thumbnail{position: relative;overflow: hidden;width: 100%;box-sizing: border-box;height: 230px;}
    #searchGoodsList ul li .gallery_thumbnail img{width: 100%; max-height: 150px;}
    #searchGoodsList ul li .gallery_button{display:none;}
    #searchGoodsList .status{display: table;position: absolute;top: 0;left: 0;z-index: 3;width: 100%;height: 100%;background: rgba(56,64,77,.5);}
    #searchGoodsList .status span{display: table-cell;position: relative;z-index: 3;vertical-align: middle;font-weight: normal;text-align: center;color: #fff;}
    
</style>
<button type="button" class="btn btn-red save">저장</button>
<form id="searchFrm" name="searchFrm" method="post">
    <input type="hidden" name="mode" value="searchGoods">
    <input type="hidden" name="cateCd" value="<?= $cateCd; ?>">
<div id="goodsChoiceList">
    <div>
        <div>진열할 상품 찾기</div>
        <div>검색분류</div>
        <div>
            <div class="form-inline">
                <select name="key" class="form-control">
                    <option value="goodsNm">상품명</option>
                    <option value="goodsNo">상품번호</option>
                </select>
                <input name="keyword" class="form-control">
            </div>
        </div>
    </div>
    <div>
        <div class="form-inline">
            <button type="button" class="btn btn-gray js-search">추가 상품 검색</button>
        </div>
    </div>
    <div id="searchGoodsList">
        <ul id="goods_sub_result">검색하신 상품이 없습니다.</ul>
    </div>
</div>
</form>
<div id="div_goods_result">
    <ul id="goods_result">
    <?php
    if($data){
        foreach ($data as $key => $value) {
            ?>
            <li id="tbl_add_goods_<?= $value['goodsNo']; ?>" <?php if($value['fixSort'] > 0) { echo "class='add_goods_fix'"; } else { echo 'class="add_goods_free"'; } ?>>
                <div class="gallery_thumbnail">
                    <img src="/data/goods/<?= $value['imagePath']; ?><?= $value['imageName']; ?>" alt="<?= $value['goodsNm']; ?>" tile="<?= $value['goodsNm']; ?>">
                </div>
                <div class="gallery_description">
                    <div class="inner">
                        <div class="info">
                            <span class="code"><?= $value['goodsNo']; ?></span>
                        </div> 
                        <strong class="product"><?= $value['goodsNm']; ?></strong>
                    </div>
                    <div class="gallery_button">
                        <input type="checkbox" value="a" <?php if($value['fixSort'] > 0) { echo "checked"; } ?>>
                        <button type="button" class="delete_button">x</button>
                    </div>
                </div>
                
            </li>
    <?php
        }
    }
    ?>
    </ul>
</div>


<script type="text/javascript" src="../admin/script/Sortable.js"></script>
<script>
    /**
    * pid setTimeOut용
    * positions 몇번째에 add_goods_fix값이 있는지
    * add_goods_fix el 배열 값
    * goodsArrList 상품번호 배열 리스트
    */
    var goodsArrList = [];
    $(function(){
        var pid,
            positions,
            freezed;

        //sortTable
        var el = document.getElementById('goods_result');

        var sortable = new Sortable(el, {
            group: {
                name: 'shared', // 공유 옵션,
                pull: 'clone'
            },
            animation: 150, // 속도
            filter : '.add_goods_fix', // sort 안될 클래스
            onStart: function (evt) {
                
                //freezed에 add_goods_fix를 배열로 만들어서 넣기
                freezed = [].slice.call(this.el.querySelectorAll('.add_goods_fix'));
                
                //몇번째 인덱스에 있는지 넣기
                positions = freezed.map(function (el) {
                    return Sortable.utils.index(el); 
                });
            },
            onMove: function (evt, originalEvent) {
                var vector,
                    freeze = false;
                
                clearTimeout(pid);
                //뒤에 실행 2
                pid = setTimeout(function () {
                    var list = evt.to;
                    
                    freezed.forEach(function (el, i) {
                        var idx = positions[i];
                        
                        //freeazed(add_goods_fix)배열안에 엘리먼트값이랑 현재 리스트 엘리먼트 값이 위치가 다를경우
                        if (list.children[idx] !== el) {
                            var realIdx = Sortable.utils.index(el);
                            
                            //(realIdx < idx) true면 1, false면 0을 더함 앞으로 이동된건지 뒤로 이동된건지 체크
                            list.insertBefore(el, list.children[idx + (realIdx < idx)]);
                        }
                    });

                }, 0);
                
                //먼저 실행 1
                freezed.forEach(function (el, i) {
                    if (el === evt.related) {
                        freeze = true;
                    }

                    if (evt.related.nextElementSibling === el && evt.relatedRect.top < evt.draggedRect.top) {
                        vector = -1;
                    }
                });
                
                //freeze가 true면 위치를 바꾸려는 게 add_goods_fix라 false반환
                return freeze ? false : vector;
            }
        });
        
        var el2 = document.getElementById('goods_sub_result');
        
        //검색 sortable
        var sortableSearch = new Sortable(el2, {
            group: {
                name: 'shared',
                put: false ,
                pull: 'clone'
            },
            sort: false,
            animation: 150,
            draggable: '.add_goods_free',
            onStart: function (evt) {
                
                //freezed에 sortable에 add_goods_fix를 배열로 만들어서 넣기
                freezed = [].slice.call(sortable.el.querySelectorAll('.add_goods_fix'));
                
                positions = freezed.map(function (el) {
                    return Sortable.utils.index(el); 
                });
            },
            onMove: function (evt, originalEvent) {
                var vector,
                    freeze = false;
                
                clearTimeout(pid);
                pid = setTimeout(function () {
                    var list = evt.to;

                    freezed.forEach(function (el, i) {
                        var idx = positions[i];

                        if (list.children[idx] !== el) {
                            var realIdx = Sortable.utils.index(el);

                            list.insertBefore(el, list.children[idx + (realIdx < idx)]);
                        }
                    });
                }, 0);
    
    
                freezed.forEach(function (el, i) {
                    if (el === evt.related) {
                        freeze = true;
                    }

                    if (evt.related.nextElementSibling === el && evt.relatedRect.top < evt.draggedRect.top) {
                        vector = -1;
                    }
                });
                
                return freeze ? false : vector;
            },
            onEnd: function(evt){
                
                setGoodsArrList();
                
                var goodsNo = evt.item.getElementsByClassName('code')[0].innerHTML;
                console.log(goodsArrList);
                //해당 goodsNo값이 있으면 추가된걸로 판단
                if(goodsArrList.indexOf(goodsNo) != -1){
                    console.log('qqqq');
                    //evt는 옮겨진 값..
                    evt.item.classList.remove('add_goods_free');
                }
                
                
                
            }
        });
        
        //저장
        $('.save').click(function(){
            
        });
        
        $('input[name="keyword"]').keydown(function(){
            if(window.event.keyCode == 13){
                $('.js-search').trigger('click');
            }
        });
        
        //검색시
        $('.js-search').click(function(){
            
            if(!$('#searchFrm input[name="keyword"]').val()){
                alert('상품명을 입력해 주세요.');
                $('input[name="keyword"]').focus();
                return false;
            }
            
            var dataList = $('#searchFrm').serialize();
            
            $.ajax({
                url : '../share/layer_drag_and_drop_ps.php',
                type : 'post',
                data : dataList,
                dataType : 'json',
                success: function(result){
                    
                    if(result.result == '0'){
                        
                        var data = result.data;
                        
                        var _html = '';
                        for(var i=0; i<data.length; i++){
                            var _checked = '';
                            if(data[i].fixSort > 0){
                                var addClassText = ' class="add_goods_fix" ';
                                _checked = 'checked';
                            }else{
                                var addClassText = ' class="add_goods_free" ';
                            }
                            var moreHtml = '';
                            if(goodsArrList.indexOf(data[i].goodsNo) != -1){
                                moreHtml += '<div class="status"><span>진열중</span></div>';
                                addClassText = '';
                            }
                            
                            _html += '<li id="tbl_add_goods_'+data[i].goodsNo+'"' +addClassText+'>';
                            _html += moreHtml;
                            _html += '<div class="gallery_thumbnail">';
                            _html += '<img src="/data/goods/'+data[i].imagePath+data[i].imageName+'" alt="'+data[i].goodsNm+'" tile="'+data[i].goodsNm+'">';
                            _html += '</div>';
                            _html += '<div class="gallery_description">';
                            _html += '<div class="inner">';
                            _html += '<div class="info">';
                            _html += '<span class="code">'+data[i].goodsNo+'</span>';
                            _html += '</div>';
                            _html += '<strong class="product">'+data[i].goodsNm+'</strong>';
                            _html += '</div>';
                            _html += '<div class="gallery_button">';
                            _html += '<input type="checkbox" value="a" '+_checked+'>';
                            _html += '<button type="button" class="delete_button">x</button>';
                            _html += '</div>';
                            _html += '</div>';
                            _html += '</li>';

                        }

                        $('#searchGoodsList ul').html(_html);
                        
                    }else if(result.result == '1'){
                        $('#searchGoodsList ul').html('검색하신 상품이 없습니다.');
                    }
                   
                }
            });
        });
        
        setGoodsArrList();
    });
    
    //고정값 사용 여부
    $(document).on('change', '.gallery_button input[type="checkbox"]', function(){
        if($(this).prop('checked')){
            $(this).closest('li').removeClass('add_goods_free').addClass('add_goods_fix');
        }else{
            $(this).closest('li').removeClass('add_goods_fix').addClass('add_goods_free');
        }
    });
    
    //엘리먼트 삭제
    $(document).on('click', '.delete_button', function(){
        $(this).closest('li').remove();
        setGoodsArrList();
    });
    
    // goodsArrList 세팅
    function setGoodsArrList()
    {
        goodsArrList = [];
        $('#goods_result .gallery_description .code').each(function(){
            goodsArrList.push($(this).html());
        });
        
    }
    
    function searchRemoveClass()
    {
        if(goodsArrList.indexOf()){
        }
    }
</script>
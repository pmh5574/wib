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
    #div_goods_result ul li .gallery_thumbnail{position: relative;overflow: hidden;width: 100%;box-sizing: border-box;height: 230px;}
    #div_goods_result ul li .gallery_thumbnail img{width: 100%; max-height: 150px;}
    #div_goods_result ul li .gallery_button{position: absolute;top: 9px;left: 11px;z-index: 2;width: 191px;height: 22px;}
    
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
    #searchGoodsList ul li .gallery_button{position: absolute;top: 9px;left: 11px;z-index: 2;width: 191px;height: 22px;}
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
                </div>
                <div class="gallery_button">
                    <input type="checkbox" value="a" <?php if($value['fixSort'] > 0) { echo "checked"; } ?>>
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
    $(function(){
        //sortTable
        var el = document.getElementById('goods_result');

        var sortable = new Sortable(el, {
            group: {
                name: 'shared', // 공유 옵션,

            },

            swapThreshold: 1, // 전체가 아닌 절반쯤 갔을때 바뀌게
            animation: 150, // 속도
            filter : '.add_goods_fix', // sort 안될 클래스
            draggable : '.add_goods_free', // sort될 클래스
            onClone: function (evt) {
		var origEl = evt.item;
		var cloneEl = evt.clone;
                origEl.before(cloneEl);
//                console.log(origEl);
//                console.log(cloneEl);
            },
            onMove: function (evt, originalEvent) {
                console.log(evt);
                console.log(originalEvent);
                
               
//                console.log(evt.dragged);
//                console.log(evt.draggedRect);
//                console.log(evt.related);
//                console.log(evt.relatedRect);
//                console.log(evt.willInsertAfter);
                
 
                // return false; — for cancel
//                 return -1;// — insert before target
                // return 1; — insert after target
                // return true; — keep default insertion point based on the direction
                // return void; — keep default insertion point based on the direction
            },
            onChange: function(evt) {
		console.log(evt); // most likely why this event is used is to get the dragging element's current index
		// same properties as onEnd
            }
//            fallbackOnBody : true
//            swap : true
        });
        
        var el2 = document.getElementById('goods_sub_result');
        
        var sortable = new Sortable(el2, {
            group: {
                name: 'shared',
                put: false ,
                clone: true
            },
//            fallbackOnBody : true,
            swapThreshold: 1,
            sort: false,
            animation: 150,
            filter : '.add_goods_fix',
            draggable : '.add_goods_free'
        });
        
        $('.save').click(function(){
            
        });
        
        $('input[name="keyword"]').keydown(function(){
            if(window.event.keyCode == 13){
                $('.js-search').trigger('click');
            }
        });
        
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
                            
                            _html += '<li id="tbl_add_goods_'+data[i].goodsNo+'"' +addClassText+'>';
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
                            _html += '</div>';
                            _html += '<div class="gallery_button">';
                            _html += '<input type="checkbox" value="a" '+_checked+'>';
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
        
    });
    
    $(document).on('change', '.gallery_button input[type="checkbox"]', function(){
        if($(this).prop('checked')){
            $(this).closest('li').removeClass('add_goods_free').addClass('add_goods_fix');
        }else{
            $(this).closest('li').removeClass('add_goods_fix').addClass('add_goods_free');

        }
    });
</script>
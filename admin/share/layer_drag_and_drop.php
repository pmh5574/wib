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
    
    .searchWall{position: absolute;width: 100%;height: 100%; z-index: 9999;}
    
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
            <li id="tbl_add_goods_<?= $value['goodsNo']; ?>" <?php if($value['fixSort'] > 0) { echo "class='add_goods_fix'"; } else { echo 'class="add_goods_free"'; } ?> data-goods-no="<?= $value['goodsNo']; ?>">
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
<script type="text/javascript" src="../admin/script/wib/wib-sortable.js"></script>
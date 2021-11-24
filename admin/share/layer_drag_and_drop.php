<style>
    #div_goods_result ul{width: 438px;margin: 0 auto;padding-bottom: 100px;box-sizing: border-box;display: flex;flex-wrap: wrap;align-content: flex-start;}
    #div_goods_result ul li{position: relative; width: 213px;height: 230px;margin: 4px 0 0 4px;}
    #div_goods_result ul li .gallery_description{opacity: 0;position: absolute;top: 0;left: 0;width: 100%;height: 100%;box-sizing: border-box;letter-spacing: -1px;background: rgba(255,255,255,.95);-webkit-transition: .3s ease-out;transition: .3s ease-out;}
    #div_goods_result ul li:hover .gallery_description{opacity: 1;}
    /*#div_goods_result ul li.sortable-chosen:after { content:""; position:absolute; top:0; left:0; display:block; width:213px; height:230px; }*/
    #div_goods_result ul .sortable-chosen.sortable-ghost:hover .gallery_description{ opacity:0 !important; display:none !important; }
    #div_goods_result ul li .gallery_thumbnail{position: relative;overflow: hidden;width: 100%;box-sizing: border-box;height: 230px;}
    #div_goods_result ul li .gallery_thumbnail img{min-width: 100%;min-height: 100%;max-width: 250px;max-height: 250px;}
    #div_goods_result ul li .gallery_button{position: absolute;top: 9px;left: 11px;z-index: 2;width: 191px;height: 22px;}
    
</style>
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
    </tbody>
</div>


<script type="text/javascript" src="../admin/script/Sortable.js"></script>
<script>
    $(function(){
        var el = document.getElementById('goods_result');

//        var sortable = Sortable.create(el);
        var sortable = new Sortable(el, {
            swapThreshold: 1,
            animation: 150,
            filter : '.add_goods_fix',
            draggable : '.add_goods_free',
            swap : true
        });

        
        $('.gallery_button input[type="checkbox"]').change(function(){
            if($(this).prop('checked')){
                $(this).closest('li').removeClass('add_goods_free').addClass('add_goods_fix');
            }else{
                $(this).closest('li').removeClass('add_goods_fix').addClass('add_goods_free');

            }
        });
    });
</script>
<?php

namespace Component\Wib;

use Component\Wib\WibSql;

class WibBoard
{
    public $wibSql;
    
    public function __construct() 
    {
        $this->wibSql = new WibSql();   
    }
    
    public function adminGetList($bdList)
    {
        
        foreach ($bdList['list'] as $key => $value) {
            $query = "SELECT mainListFl FROM es_bd_goodsreview WHERE sno = {$value['sno']}";
            $mainListFl = $this->wibSql->WibNobind($query)['mainListFl'];
            $bdList['list'][$key]['mainListFl'] = $mainListFl;

        }
        
        $query = "SELECT count(*) cnt FROM es_bd_goodsreview WHERE mainListFl = 'y'";
        $cnt = $this->wibSql->WibNobind($query)['cnt'];

        $bdList['mainListFlCnt'] = $cnt;
        
        return $bdList;
    }
    
    public function adminSetReview($req)
    {
        $data = [
            'es_bd_goodsreview',
            ['mainListFl' => [$req['mainListFl'], 's']],
            ['sno' => [$req['sno'], 'i']]
        ];
        
        $this->wibSql->WibUpdate($data);
        
    }
    
    /**
     * 메인페이지 리뷰 리스트
     * @return array 리뷰 정보
     */
    public function getMainReview()
    {
        $query = "SELECT bgr.*, g.goodsNm, g.goodsPrice, g.fixedPrice, cb.cateNm AS brandNm, g.imagePath, gi.imageName "
                . "FROM es_bd_goodsreview bgr "
                . "LEFT JOIN es_goods g ON g.goodsNo = bgr.goodsNo "
                . "LEFT JOIN es_goodsImage gi ON gi.goodsNo = bgr.goodsNo "
                . "LEFT JOIN es_categoryBrand cb ON cb.cateCd = g.brandCd "
                . "WHERE bgr.mainListFl = 'y' AND bgr.isDelete = 'n' AND gi.imageKind = 'main' "
                . "ORDER BY bgr.sno DESC "
                . "LIMIT 20";
        $result = $this->wibSql->WibAll($query);
        
        //이미지 두개이상일때 하나만 보여주기
        $reviews = $this->setThumbNail($result);
        
        return $reviews;
    }
    
    /**
     * 메인 페이지 리뷰 리스트 이미지 하나만, contents에 이미지 제거
     * @param array $reviews 리뷰 데이터
     * @return array 리뷰 데이터
     */
    public function setThumbNail($reviews)
    {
        foreach($reviews as $key => $value){
            if(strpos($value['saveFileNm'],'^|^')){
                $saveFileNm = explode('^|^', $value['saveFileNm']);
                $reviews[$key]['saveFileNm'] = $saveFileNm[0];
            }
        }
        
        return $reviews;
    }
    
    
}

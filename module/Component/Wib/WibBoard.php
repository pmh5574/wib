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
    
    public function updateStoreData($req)
    {   
        $sno = 0;
        
        if($req['sno']){
            $sno = $req['sno'];
        }else{
            //방금 저장된 sno가져오기
            $sno = $this->bdStoreGetSno();
        }

        $data = [
            'es_bd_store',
            array(
                'storeSearch' => [$req['storeSearch'],'s'],
                'storePhoneNum' => [$req['storePhoneNum'],'s'],
                'storeOpenTime' => [$req['storeOpenTime'],'s'],
                'storeHoliday' => [$req['storeHoliday'],'s'],
                'address' => [$req['address'],'s'],
                'addressSub' => [$req['addressSub'],'s'],
                'addressLat' => [$req['addressLat'],'s'],
                'addressLng' => [$req['addressLng'],'s']
            ),
            array('sno' => [$sno,'i'])
        ];
        
        $this->wibSql->WibUpdate($data);
        
    }
    
    public function bdStoreGetSno()
    {
        
        $wql = "SELECT sno FROM es_bd_store ORDER BY sno DESC LIMIT 1";
        $lastSno = $this->wibSql->WibNobind($wql);
        
        return $lastSno['sno'];
    }
    
    public function setBdList($bdList,$req)
    {
        $wibSql = new WibSql();
        
        foreach ($bdList['list'] as $key => $value){
                
            //db 추가정보
            $query = "SELECT storeSearch, storePhoneNum, storeOpenTime, storeHoliday, address, addressSub, addressLat, addressLng FROM es_bd_store WHERE sno = {$value['sno']} LIMIT 999";
            $result = $wibSql->WibNobind($query);

            if($result){

                $bdList['list'][$key]['storeSearch'] = $result['storeSearch'];
                $bdList['list'][$key]['storePhoneNum'] = $result['storePhoneNum'];
                $bdList['list'][$key]['storeOpenTime'] = $result['storeOpenTime'];
                $bdList['list'][$key]['storeHoliday'] = $result['storeHoliday'];
                $bdList['list'][$key]['address'] = $result['address'];
                $bdList['list'][$key]['addressSub'] = $result['addressSub'];
                $bdList['list'][$key]['addressLat'] = $result['addressLat'];
                $bdList['list'][$key]['addressLng'] = $result['addressLng'];
            }


            $kakaoContents = $result["address"];


            $bdList['list'][$key]['wibkakao'] = $kakaoContents;


            $saveFileNm = explode('^|^', $value['saveFileNm']);

            if($saveFileNm[0]){

                $arr = [];
                //이미지 여러개 뿌리기
                foreach ($saveFileNm as $k => $val){
                    $arr[$k]['imgList'] = '/data/board/'.$value['bdUploadPath'].$val;
                }

                $bdList['list'][$key]['saveFileNmList'] = $arr;
            }
            
            //검색관련
            if($req['storeSearch'] && $req['searchWord']){
                
                if($result['storeSearch'] != $req['storeSearch']){
                    unset($bdList['list'][$key]);
            
                    
                }else if(strpos($bdList['list'][$key]['subject'],$req['searchWord']) === false && strpos($bdList['list'][$key]['address'],$req['searchWord']) === false && strpos($bdList['list'][$key]['addressSub'],$req['searchWord']) === false){

                    unset($bdList['list'][$key]);
                }
                    
                    
                
            }else if($req['storeSearch'] && !($req['searchWord'])){
                   
                if($req['storeSearch'] != $result['storeSearch']){
                    unset($bdList['list'][$key]);
                }
            }else if(!($req['storeSearch']) && $req['searchWord']){
                if(strpos($bdList['list'][$key]['subject'],$req['searchWord']) === false && strpos($bdList['list'][$key]['address'],$req['searchWord']) === false && strpos($bdList['list'][$key]['addressSub'],$req['searchWord']) === false){
                    unset($bdList['list'][$key]);
                }
            }

        }
        return $bdList;
    }
    
}

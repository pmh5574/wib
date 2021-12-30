<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Front\Board;

use Component\Board;
use Component\Board\BoardList;

class ListController extends \Bundle\Controller\Front\Board\ListController
{
    public function post()
    {
        $req = $this->getData('req');   
        $this->req = $req;
        if($req['bdId'] == 'store'){
            $bdList = $this->getData('bdList');
            
            $db = \App::load('DB');
            $arrBind = [];
            $query = "SELECT sno,address,addresssub,addressLat,addressLng,storePhoneNum FROM es_bd_store";
            
            // result = db에서 구해온 $query 의 값
            $result = $db->query_fetch($query, $arrBind, false);
            //gd_debug($bdList['list']);
            //gd_debug($result);
            for($i=0; $i<count($bdList['list']); $i++){

                for($j=0; $j<count($result); $j++){
                    //gd_debug($result['sno']);
                    if($result[$j]['sno']==$bdList['list'][$i]['sno']){
                        
                        $bdList['list'][$i]['address']=$result[$j]['address'];
                        $bdList['list'][$i]['addresssub']=$result[$j]['addresssub'];
                        $bdList['list'][$i]['addressLat']=$result[$j]['addressLat'];
                        $bdList['list'][$i]['addressLng']=$result[$j]['addressLng'];
                        $bdList['list'][$i]['storePhoneNum']=$result[$j]['storePhoneNum'];
                        //gd_debug("123");
                    }
                }
                //gd_debug($asd);
                //$ass = array_push($bdList);
            }
            gd_debug($bdList['list']);
            /*
            foreach($bdList['list'] as $key => $value){
                $query = "SELECT sno,address,addresssub,addressLat, addressLng FROM es_bd_store WHERE sno = {$value['sno']}";
                $result = $db->query_fetch($query, $arrBind, false);
            }
            */
           //gd_debug($bdList);
            
            
            $this->setData("result",$result);
            $this->setData("bdList",$bdList);
            //echo json_encode($_POST,JSON_FORCE_OBJECT);
            //gd_debug($bdList);
        }
    }
}

<?php

namespace Controller\Front\Board;

use Component\Board;
use Component\Board\BoardList;

use Request;

class TestViewController extends \Controller\Front\Controller
{

    // storePhoneNum, storeOpenTime, storeHoliday, address
    public function index()
    {
        $db = \App::load('DB');
        $arrbind = [];
        $query  = "SELECT subject,storePhoneNum, storeOpenTime, storeHoliday, address FROM " . 'es_bd_store' . " where sno = ?";
        $field1 = 1;
        $db->bind_param_push($arrBind, 'i', $field1);

        
        $result = $db->query_fetch($query, $arrBind, false);


        

        gd_debug($result);
        $this->setData("result",$result);
        
    
    }
    
}
<?php

namespace Component\Wib;

use Component\Wib\WibSql;
use Request;


class WibMember
{
    public $wibSql;
    
    public function __construct() 
    {
        $this->wibSql = new WibSql;
    }
    
    public function getMemberOrderCount($member) 
    {
        $query = "SELECT COUNT(*) cnt FROM es_order WHERE memNo = {$member} AND orderStatus NOT LIKE 'f%' ";
        $data = $this->wibSql->WibNobind($query)['cnt'];
        
        return $data;
    }
}


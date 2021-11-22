<?php

namespace Component\Wib;

class WibBrand
{
    public $wibSql;
    
    public function __construct() 
    {
        $this->wibSql = new WibSql();
    }
    
    public function setBrandLike()
    {
        
    }
}


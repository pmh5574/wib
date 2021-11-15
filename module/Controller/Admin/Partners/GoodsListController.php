<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Admin\Partners;

use Exception;
use Framework\Debug\Exception\LayerException;

class GoodsListController extends \Controller\Admin\Controller
{
    public function index()
    {
        // --- 메뉴 설정
        $this->callMenu('partners', 'partners', 'goods_list');
        
       
    }
}

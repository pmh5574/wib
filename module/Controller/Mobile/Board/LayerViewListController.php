<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Mobile\Board;

use Request;
use Component\Wib\WibBoard;

class LayerViewListController extends \Controller\Mobile\Controller
{
    public function index()
    {
        $wibBoard = new WibBoard();
        
        $sno = Request::post()->get('sno');
        
        if($sno){
            
            $data = $wibBoard->getStoreBoardData($sno);

            $this->setData('data',$data);
            
        }else{
            $this->js('alert("' . __('잘못된 접근입니다.') . '");');
            $this->redirect('../main/index');
        }
    }
}

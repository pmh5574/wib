<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Mobile\Board;

use Component\Wib\WibBoard;

class ListController extends \Bundle\Controller\Mobile\Board\ListController
{
    public function post()
    {
        $wibBoard = new WibBoard();
        
        $bdList = $this->getData('bdList');
        $req = $this->getData('req');
        
        if($req['bdId'] == 'store'){
            $bdList = $wibBoard->setBdList($bdList,$req);

            $this->setData('bdList', $bdList);
        }
        
    }
}

<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Controller\Admin\Board;

use Component\Wib\WibBoard;

class ArticleListController extends \Bundle\Controller\Admin\Board\ArticleListController
{
    public function post()
    {
        
        $wibBoard = new WibBoard();
        
        $bdList = $this->getData('bdList');
        $req  = $this->getData('req');
        
        if($req['bdId'] == 'goodsreview'){
            $bdList = $wibBoard->adminGetList($bdList);
            
        }
        
        $this->setData('bdList', $bdList);
        
    }
}

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

use Request;
use Component\Wib\WibBoard;

class GoodsReviewPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $wibBoard = new WibBoard();
        
        $postValue = Request::post()->all();
        
        switch ($postValue['mode']) {
            case 'main':
                
                $wibBoard->adminSetReview($postValue);
                $result = [
                    'result' => 'ok'
                ];
                $this->json($result);
                
                break;
            default:
                break;
        }
        
        exit;
    }
}
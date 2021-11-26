<?php
namespace Controller\Admin\Share;

use Exception;
use Framework\Utility\ArrayUtils;
use Globals;
use Request;
use Component\Wib\WibGoods;

class LayerDragAndDropPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $postValue = Request::post()->toArray();
        
        switch($postValue['mode']){
            case 'searchGoods':
                $goods = new WibGoods();
                $result = $goods->getAdminListGoods();
                $this->json(
                    [
                        'result' => $result['result'],
                        'data' => $result['data']
                    ]
                );
                break;
            case '':
                break;
            default :
                break;
        }
     
        exit();
    }
}
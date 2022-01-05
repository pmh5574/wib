<?php
namespace Controller\Front\Service;

use Component\Wib\WibSql;
use Component\Wib\WibBoard;
use Component\Board\Board;
use Component\Board\BoardList;

use Request;
// 각 배열에서  sno 뽑아서 넣기
class CustomerserviceController extends \Controller\Front\Controller
{
    protected $db = null;

    public function index()
    {
        $wibBoard = new WibBoard();
        $WibSql = new WibSql();

        $asd = $wibBoard->bdStoreGetSno();
        
        $qwe = $wibBoard->getStoreBoardData($asd);

        $bdId = Request::get()->get('bdId');

        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }

        $file = \Request::files($result[1]['uploadFileNm']);

        $arrBind = [];
        $query = "SELECT sno,subject, address, addressSub, storePhoneNum, storeHoliday, storeOpenTime FROM es_bd_store order by sno desc";
        $result = $this->db->query_fetch($query, $arrBind, false);
        gd_debug($result[1]['sno']);

        $this->setData("result",$result);
        
        
        $bdList = $this->getData('bdList');
        $req = $this->getData('req');
        
        if($req['bdId'] == 'store'){
            
            $bdList = $wibBoard->setBdList($bdList,$req);
           
            $subjectList = $wibBoard->getSubjectList($req['storeSearch']);

            gd_debug($wibBoard);
            $this->setData('subjectList', $subjectList);
            
            $this->setData('totalCnt', count($bdList['list']));
            $this->setData('bdList', $bdList);
        }
    }
}
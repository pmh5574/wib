<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Front\Proc;

use Component\Board\Board;
use Component\Board\BoardList;
use Component\Goods\GoodsCate;
use Component\Page\Page;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\RedirectLoginException;
use Framework\Debug\Exception\RequiredLoginException;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\Strings;
use Request;
use View\Template;
use Component\Wib\WibBoard;

class TestBoardController extends \Controller\Front\Controller
{
    public function index()
    {
        try {
            $locale = \Globals::get('gGlobal.locale');
            $this->addCss([
                'plugins/bootstrap-datetimepicker.min.css',
                'plugins/bootstrap-datetimepicker-standalone.css',
            ]);
            $this->addScript([
                'gd_board_common.js',
                'moment/moment.js',
                'moment/locale/' . $locale . '.js',
                'jquery/datetimepicker/bootstrap-datetimepicker.js',
            ]);

            $req = Request::get()->toArray();

            //마이페이지에서 디폴트 기간노출 7일
            if($req['memNo']>0 && ($req['bdId'] == Board::BASIC_QA_ID || $req['bdId'] == Board::BASIC_GOODS_QA_ID)) {
                $rangDate = \Request::get()->get(
                    'rangDate', [
                        DateTimeUtils::dateFormat('Y-m-d', '-6 days'),
                        DateTimeUtils::dateFormat('Y-m-d', 'now'),
                    ]
                );
                $req['rangDate'] = $rangDate;
            }

            $boardList = new BoardList($req);
            $boardList->checkUsePc();
            $getData = $boardList->getList();
            $bdList['cfg'] = $boardList->cfg;
            $bdList['cnt'] = $getData['cnt'];
            $bdList['list'] = $getData['data'];
            $bdList['noticeList'] = $getData['noticeData'];
            $bdList['categoryBox'] = $boardList->getCategoryBox($req['category'], ' onChange="this.form.submit();" ');
            $bdList['pagination'] = $getData['pagination']->getPage();
            gd_isset($req['memNo'], 0);
        } catch (RequiredLoginException $e) {
            if ($req['noheader'] == 'y') {
                throw new AlertBackException($e->getMessage());
            }
            throw new RedirectLoginException();
        } catch (\Exception $e) {
            throw new AlertBackException($e->getMessage());
        }

        if (gd_isset($req['noheader'], 'n') != 'n') {
            $this->getView()->setDefine('header', 'outline/_share_header.html');
            $this->getView()->setDefine('footer', 'outline/_share_footer.html');
        }
            
        $wibBoard = new WibBoard();
        $bdList = $wibBoard->setBdList($bdList,$req);

        $listCount = ceil(count($bdList['list'])/5);
        if($listCount == 0){
            $listCount = 1;
        }

        $this->setData('listCount',$listCount);
            
        $this->setData('isMemNo', $req['memNo']);
        $this->setData('bdList', $bdList);
        $this->setData('req', gd_htmlspecialchars($boardList->req));
    }
    
    
}

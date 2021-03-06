<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Front\Board;

use Component\Wib\WibBoard;
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

class ListController extends \Bundle\Controller\Front\Board\ListController
{
    public function index()
    {
        $bdId = Request::get()->get('bdId');
        
        $wibBoard = new WibBoard();
        
        if($bdId == 'store'){
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
                
                if($req['bdId'] == 'store'){
                    if(!$req['storeSearch'] && !$req['searchWord']){
                        $req['searchWord'] = '';
                        Request::get()->del('searchWord');
                    }else if(!$req['storeSearch'] && $req['searchWord']){
                        $storeSearch = $wibBoard->getStoreSearchList($req['searchWord'])['storeSearch'];

                        Request::get()->set('storeSearch', $storeSearch);
                        $req['storeSearch'] = $storeSearch;

                    }
                }
                

                //????????????????????? ????????? ???????????? 7???
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
                $getData = $boardList->getList(true, 255);
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
            $this->setData('isMemNo', $req['memNo']);
            $this->setData('bdList', $bdList);
            $this->setData('req', gd_htmlspecialchars($boardList->req));
            $path = 'board/skin/' . $bdList['cfg']['themeId'] . '/list.html';
            $this->getView()->setDefine('list', $path);
        }else{
            parent::index();
        }
    }
    
    public function post()
    {
        $wibBoard = new WibBoard();
        
        $bdList = $this->getData('bdList');
        $req = $this->getData('req');
        
        if($req['bdId'] == 'store'){
            
            $bdList = $wibBoard->setBdList($bdList,$req);
           
            $subjectList = $wibBoard->getSubjectList($req['storeSearch']);
            $this->setData('subjectList', $subjectList);
            
            $this->setData('totalCnt', count($bdList['list']));
            $this->setData('bdList', $bdList);
        }
        
    }
}

<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Mobile\Board;

use Component\Board\Board;
use Component\Board\BoardList;
use Component\Page\Page;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\RedirectLoginException;
use Framework\Debug\Exception\RequiredLoginException;
use Request;
use View\Template;
use Framework\Utility\Strings;
use Session;
use Component\Wib\WibBoard;

class ListController extends \Bundle\Controller\Mobile\Board\ListController
{
    public function index()
    {
        $bdId = Request::get()->get('bdId');
        
        $wibBoard = new WibBoard();
        
        if($bdId == 'store'){
            try {
                if(Request::get()->get('mypageFl') == 'y'){
                    if (gd_is_login() === false) {
                        throw new RequiredLoginException();
                    }
                }

                $wibBoard = new WibBoard();

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
                    
                    $arrWhere[] = "b.storeSearch = '{$req['storeSearch']}'";
                }


                if ($req['mypageFl'] == 'y') {
                    $req['memNo'] = Session::get('member.memNo');
                }

                $boardList = new BoardList($req);
                $boardList->checkUseMobile();
                $getData = $boardList->getList(true,$boardList->cfg['bdListCnt'], 0, $arrWhere);
                $bdList['cfg'] = $boardList->cfg;
                $bdList['cnt'] = $getData['cnt'];
                $bdList['list'] = $getData['data'];
                $bdList['noticeList'] = $getData['noticeData'];
                $bdList['categoryBox'] = $boardList->getCategoryBox($req['category'], ' onChange="this.form.submit();" ');
                $getData['pagination']->setBlockCount(Board::PAGINATION_MOBILE_BLOCK_COUNT);
                //$getData['pagination']->setBlockCount(Board::PAGINATION_MOBILE_COUNT);
                $getData['pagination']->setPage();
                $bdList['pagination'] = $getData['pagination']->getPage();

                // 웹취약점 개선사항 상단 에디터 업로드 이미지 alt 추가
                if ($bdList['cfg']['bdHeader']) {
                    $tag = "title";
                    preg_match_all( '@'.$tag.'="([^"]+)"@' , $bdList['cfg']['bdHeader'], $match );
                    $titleArr = array_pop($match);

                    foreach ($titleArr as $title) {
                        $bdList['cfg']['bdHeader'] = str_replace('title="'.$title.'"', 'title="'.$title.'" alt="'.$title.'"', $bdList['cfg']['bdHeader']);
                    }
                }

                // 웹취약점 개선사항 하단 에디터 업로드 이미지 alt 추가
                if ($bdList['cfg']['bdFooter']) {
                    $tag = "title";
                    preg_match_all( '@'.$tag.'="([^"]+)"@' , $bdList['cfg']['bdFooter'], $match );
                    $titleArr = array_pop($match);

                    foreach ($titleArr as $title) {
                        $bdList['cfg']['bdFooter'] = str_replace('title="'.$title.'"', 'title="'.$title.'" alt="'.$title.'"', $bdList['cfg']['bdFooter']);
                    }
                }
            } catch(RequiredLoginException $e) {
                throw new RedirectLoginException($e->getMessage());
            }
            catch (\Exception $e)
            {
                if($req['gboard'] == 'y') {
                    throw new AlertCloseException($e->getMessage());
                }
                throw new AlertBackException($e->getMessage());
            }

            if(gd_isset($req['noheader'],'n') != 'n') {
                $this->getView()->setDefine('header', 'outline/_share_header.html');
                $this->getView()->setDefine('footer', 'outline/_share_footer.html');
            }
            $this->setData('bdList', $bdList);
            $this->setData('req', gd_htmlspecialchars($boardList->req));
            $this->setData('gPageName', __($bdList['cfg']['bdNm']));
            $path = 'board/skin/'.$bdList['cfg']['themeId'].'/list.html';
            $this->getView()->setDefine('list',$path);
            $this->getView()->setDefine('tpl', 'board/list.html');
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

<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Mobile\Proc;

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

class TestBoardController extends \Controller\Mobile\Controller
{
    public function index()
    {
        try {
            if(Request::get()->get('mypageFl') == 'y'){
                if (gd_is_login() === false) {
                    throw new RequiredLoginException();
                }
            }

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

            if ($req['mypageFl'] == 'y') {
                $req['memNo'] = Session::get('member.memNo');
            }

            $boardList = new BoardList($req);
            $boardList->checkUseMobile();
            $getData = $boardList->getList(true,$boardList->cfg['bdListCnt']);
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
        
        $wibBoard = new WibBoard();
        $bdList = $wibBoard->setBdList($bdList,$req);

        $listCount = ceil(count($bdList['list'])/5);
        if($listCount == 0){
            $listCount = 1;
        }
        
        $this->setData('bdList', $bdList);
        $this->setData('req', gd_htmlspecialchars($boardList->req));
        $this->setData('gPageName', __($bdList['cfg']['bdNm']));

    }
}

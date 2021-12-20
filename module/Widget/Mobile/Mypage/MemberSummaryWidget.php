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
namespace Widget\Mobile\Mypage;

use Session;
use Component\Wib\WibMember;
/**
 *
 * @author Lee Seungjoo <slowj@godo.co.kr>
 */
class MemberSummaryWidget extends \Bundle\Widget\Mobile\Mypage\MemberSummaryWidget
{
    public function post()
    {
        $wibMember = new WibMember();
        $cnt = $wibMember->getMemberOrderCount(Session::get('member.memNo'));
        
        $this->setData('memberOrderCnt', ($cnt > 0) ? $cnt : 0);
    }
}
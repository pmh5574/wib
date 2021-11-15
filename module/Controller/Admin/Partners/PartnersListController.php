<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Admin\Partners;

use Exception;
use Framework\Debug\Exception\LayerException;

class PartnersListController extends \Controller\Admin\Controller
{
    public function index()
    {
        // --- 메뉴 설정
        $this->callMenu('partners', 'partners', 'partners_list');
        
        // --- 모듈 호출
        try {
            $scmAdmin = \App::load(\Component\Wib\WibScmAdmin::class);
            $scmCommission = \App::load(\Component\Scm\ScmCommission::class);
            $getData = $scmAdmin->getScmAdminList();
            foreach ($getData['data'] as $key => &$val) {
                $val['scmSameCommission'] = '';
                $commissionSameFl = $scmCommission->compareWithScmCommission($val['addCommissionData']);
                if ($commissionSameFl && $val['scmCommission'] == $val['scmCommissionDelivery']) {
                    $val['scmSameCommission'] = '판매수수료 동일 적용';
                }
            }
            $page = \App::load('\\Component\\Page\\Page'); // 페이지 재설정
        } catch (Exception $e) {
            throw new LayerException($e->getMessage());
        }
       

        // --- 관리자 디자인 템플릿
        $this->setData('data', gd_isset($getData['data']));
        $this->setData('list', gd_isset($getData['list']));
        $this->setData('search', $getData['search']);
        $this->setData('checked', $getData['checked']);
        $this->setData('page', $page);
    }
}
